'use strict';

/* ============================================================
   CONFIGURACIÓN
============================================================ */
const API_URL       = '../api/products.php';
const ORDERS_URL    = '../api/orders.php';
const FAVORITOS_URL = '../api/favoritos.php';

/* ============================================================
   CATÁLOGO EN MEMORIA
============================================================ */
let PRODUCTOS = {};
let CATALOGO_COMPLETO = {};

/* ============================================================
   MAPA DE CATEGORÍAS & NOTAS DE CATA — Experiencia Specialty Coffee & Food
============================================================ */
const CATEGORIAS_NOMBRE = {
    'todos':           'Todos los Productos',
    'cafes-todos':     'Todos los Cafés',
    'tueste-claro':    'Tueste Claro',
    'tueste-medio':    'Tueste Medio',
    'tueste-oscuro':   'Tueste Oscuro',
    'capsulas':        'Cápsulas & Drip',
    'origen-especial': 'Origen Especial',
    'reposteria':      'Tortas & Repostería',
    'panaderia':       'Panadería Artesanal',
    'desayunos':       'Desayunos & Brunch',
};

const CATEGORIAS_CAFES = ['tueste-claro', 'tueste-medio', 'tueste-oscuro', 'capsulas', 'origen-especial'];

const NOTAS_CATA_POR_CATEGORIA = {
    'tueste-claro':    ['Jazmín & Cítricos', 'Panela & Miel', 'Cuerpo Sedoso', '1.850 msnm'],
    'tueste-medio':    ['Chocolate Suave', 'Avellana & Nuez', 'Caramelo Toffee', 'Equilibrado'],
    'tueste-oscuro':   ['Cacao 85%', 'Notas Ahumadas', 'Especias Dulces', 'Intensidad Alta'],
    'capsulas':        ['Frutos Rojos', 'Vainilla Bourbon', 'Extracción Perfecta', 'Biodegradable'],
    'origen-especial': ['Geisha / Bourbon Rosa', 'Proceso Honey/Natural', 'Puntuación 88+ SCA', 'Micro-lote'],
    'reposteria':      ['Mantequilla 100% Pura', 'Cacao Fino de Aroma', 'Receta de la Casa', 'Fresco del Día'],
    'panaderia':       ['Masa Madre Natural', 'Fermentación 24-48h', 'Corteza Crujiente', 'Harinas Seleccionadas'],
    'desayunos':       ['Preparado al Instante', 'Huevos de Campo', 'Ingredientes Huilenses', 'Café Ilimitado'],
};

/* ============================================================
   ESTADO VISTA PREVIA (MODAL QUICK VIEW)
============================================================ */
let previewProductoActual = null;
let previewCantidadActual = 1;

/* ============================================================
   ESTADO DEL CARRITO
============================================================ */
let carrito = {};

/* ============================================================
   FAVORITOS EN MEMORIA
============================================================ */
let favoritosActivos = new Set();

/* ============================================================
   SEGURIDAD — Sanitización XSS
============================================================ */
function sanitizar(str) {
    if (typeof str !== 'string') return '';
    const mapa = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#x27;' };
    return str.replace(/[&<>"']/g, c => mapa[c]).trim();
}

/* ============================================================
   FORMATO DE PRECIO
============================================================ */
function formatearPrecio(n) {
    return '$' + Math.round(n).toLocaleString('es-CO');
}

/* ============================================================
   RESOLVER RUTA DE IMAGEN (HTTP Remota vs Relativa)
============================================================ */
function resolverRutaImagen(img) {
    if (!img || typeof img !== 'string') return '';
    if (img.startsWith('http://') || img.startsWith('https://') || img.startsWith('//') || img.startsWith('data:')) {
        return img;
    }
    return '../' + img.replace(/^\/+/, '');
}

/* ============================================================
   ESTRELLAS
============================================================ */
function generarEstrellas(valor) {
    const llenas = Math.round(parseFloat(valor) || 5);
    let s = '';
    for (let i = 1; i <= 5; i++) s += i <= llenas ? '★' : '☆';
    return s;
}

/* ============================================================
   PERSISTENCIA — localStorage
============================================================ */
const LS_KEY = 'coffeecol_carrito_v2';

function guardarCarritoLocal() {
    try {
        localStorage.setItem(LS_KEY, JSON.stringify(carrito));
        // Disparar evento para que otros scripts (como encabezado.php) se enteren al instante
        window.dispatchEvent(new Event('storage'));
    } catch (_) {}
}

function cargarCarritoLocal() {
    try {
        const saved = localStorage.getItem(LS_KEY);
        if (saved) carrito = JSON.parse(saved) || {};
    } catch (_) { carrito = {}; }
}

/* ============================================================
   FAVORITOS — CARGAR DESDE SERVIDOR
============================================================ */
async function cargarFavoritosUsuario() {
    try {
        const res = await fetch(FAVORITOS_URL + '?todos=1');
        if (!res.ok) return; // no logueado → silencioso
        const data = await res.json();
        if (data.success && Array.isArray(data.ids)) {
            favoritosActivos = new Set(data.ids);
        }
    } catch (_) {}
}

/* ============================================================
   FAVORITOS — TOGGLE EN SERVIDOR
============================================================ */
async function toggleFavoritoServidor(productoId) {
    try {
        const res = await fetch(FAVORITOS_URL, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ producto_id: productoId })
        });

        if (res.status === 401) {
            mostrarToast('Inicia sesión para guardar favoritos', 'fa-user');
            setTimeout(() => { window.location.href = '/cafe/includes/loginu.php'; }, 1400);
            return null;
        }

        const data = await res.json();
        return data.success ? data : null;
    } catch (_) {
        return null;
    }
}

/* ============================================================
   CARGA DINÁMICA DE PRODUCTOS DESDE LA API
============================================================ */
async function cargarProductos() {
    const grid = document.getElementById('productosGrid');
    if (!grid) return;

    grid.innerHTML = generarEsqueletos(4);

    try {
        console.log('[CoffeeCol] Cargando productos desde:', API_URL);

        const res = await fetch(API_URL);

        if (!res.ok) {
            console.error('[CoffeeCol] HTTP error al cargar productos. Status:', res.status, res.statusText);
            throw new Error('HTTP ' + res.status);
        }

        let data;
        try {
            data = await res.json();
        } catch (jsonErr) {
            console.error('[CoffeeCol] La respuesta de products.php no es JSON válido:', jsonErr);
            throw new Error('JSON inválido en products.php');
        }

        if (!data.success) {
            console.error('[CoffeeCol] products.php devolvió error:', data.error);
            throw new Error(data.error || 'Error de API');
        }

        const productos = data.productos || [];
        console.log('[CoffeeCol] Productos cargados:', productos.length);

        PRODUCTOS = {};
        CATALOGO_COMPLETO = {};
        productos.forEach(p => {
            const pId = parseInt(p.id, 10);
            PRODUCTOS[pId] = { nombre: p.nombre, precio: parseFloat(p.precio) || 0, icono: p.icono, imagen: p.imagen };
            CATALOGO_COMPLETO[pId] = p;
        });

        renderizarProductos(productos);

        const contador = document.getElementById('contadorProductos');
        if (contador) {
            contador.textContent = `Mostrando ${productos.length} productos y especialidades`;
        }

        inicializarFiltros();

        // Cargar favoritos del servidor ANTES de inicializar los botones
        await cargarFavoritosUsuario();
        inicializarFavoritos();

        inicializarAnimaciones();
        reconstruirCarritoDesdeLocal();

    } catch (err) {
        console.error('[CoffeeCol] Error cargando productos:', err);
        grid.innerHTML = `
            <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:#47060E;">
                <i class="fas fa-exclamation-circle" style="font-size:2rem;opacity:.4;display:block;margin-bottom:12px;"></i>
                <strong>No se pudieron cargar los productos.</strong><br>
                <small style="color:#888;">Verifica la conexión con la base de datos. Detalle: ${sanitizar(err.message)}</small>
            </div>`;
    }
}

function reconstruirCarritoDesdeLocal() {
    const idsValidos = Object.keys(PRODUCTOS).map(Number);
    Object.keys(carrito).forEach(id => {
        if (!idsValidos.includes(parseInt(id, 10))) delete carrito[id];
    });
    guardarCarritoLocal();
    actualizarUI();
}

/* ============================================================
   RENDER DE TARJETAS DE PRODUCTO
============================================================ */
function renderizarProductos(productos) {
    const grid = document.getElementById('productosGrid');
    if (!grid) return;

    if (!productos.length) {
        grid.innerHTML = `
            <div class="catalogo-vacio">
                <i class="fas fa-mug-hot"></i>
                <strong>No hay productos disponibles en este momento</strong>
                <p>Pronto añadiremos nuevas cosechas y delicias.</p>
            </div>`;
        return;
    }

    grid.innerHTML = productos.map(p => {
        const pId = parseInt(p.id, 10);
        const badgeHTML = p.badge && p.badge_tipo
            ? `<div class="producto-badge ${sanitizar(p.badge_tipo)}">${sanitizar(p.badge)}</div>`
            : '';

        const precioAntesHTML = p.precio_antes
            ? `<span class="precio-antes">${formatearPrecio(p.precio_antes)}</span>`
            : '';

        const categoriaNombre = CATEGORIAS_NOMBRE[p.categoria] || p.categoria;
        const notasCata       = NOTAS_CATA_POR_CATEGORIA[p.categoria] || ['Especial', 'Artesanal'];
        const estrellas       = generarEstrellas(p.rating_valor);
        const rutaImagen      = resolverRutaImagen(p.imagen);

        const tagsHTML = `
            <div class="producto-tags">
                <span class="producto-tag origen"><i class="fas fa-location-dot" style="font-size:8.5px;margin-right:3px;"></i>Huila</span>
                ${notasCata.slice(0, 3).map(nota => `<span class="producto-tag">${sanitizar(nota)}</span>`).join('')}
            </div>`;

        const mediaHTML = rutaImagen
            ? `<img
                    src="${sanitizar(rutaImagen)}"
                    alt="${sanitizar(p.nombre)}"
                    class="producto-img-real"
                    loading="lazy"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
               >
               <div class="producto-img-fallback" style="display:none;">
                   <i class="fas ${sanitizar(p.icono || 'fa-mug-hot')}"></i>
               </div>`
            : `<div class="producto-img-fallback">
                   <i class="fas ${sanitizar(p.icono || 'fa-mug-hot')}"></i>
               </div>`;

        return `
        <div class="producto-card" data-categoria="${sanitizar(p.categoria)}" data-id="${pId}">
            ${badgeHTML}
            <button class="btn-favorito" aria-label="Agregar a favoritos">
                <i class="far fa-heart"></i>
            </button>
            <div class="producto-img-wrap" onclick="abrirVistaPrevia(${pId})" title="Ver detalles y vista previa">
                ${mediaHTML}
            </div>
            <div class="producto-info">
                <div class="producto-categoria">${sanitizar(categoriaNombre)}</div>
                <h3 class="producto-nombre" onclick="abrirVistaPrevia(${pId})" title="Ver detalles">${sanitizar(p.nombre)}</h3>
                ${tagsHTML}
                <p class="producto-descripcion" onclick="abrirVistaPrevia(${pId})" title="Ver detalles">${sanitizar(p.descripcion)}</p>
                <div class="producto-rating">
                    <span class="estrellas">${estrellas}</span>
                    <span class="rating-num">${p.rating_valor || 4.9} (${p.rating_cantidad || 120})</span>
                </div>
                <div class="producto-footer">
                    <div class="producto-precio">
                        ${precioAntesHTML}
                        <span class="precio-actual">${formatearPrecio(p.precio)}</span>
                        <span class="precio-unidad">${sanitizar(p.unidad || 'Unidad')}</span>
                    </div>
                    <button class="btn-agregar" onclick="agregarAlCarrito(${pId})">
                        <i class="fas fa-bag-shopping"></i><span>Agregar</span>
                    </button>
                </div>
            </div>
        </div>`;
    }).join('');
}

/* ============================================================
   CONTROLADOR DE VISTA PREVIA RÁPIDA (QUICK VIEW MODAL)
============================================================ */
function abrirVistaPrevia(productoId) {
    const id = parseInt(productoId, 10);
    const p = CATALOGO_COMPLETO[id] || (PRODUCTOS[id] ? { ...PRODUCTOS[id], id } : null);
    if (!p) return;

    previewProductoActual = p;
    previewCantidadActual = 1;

    const overlay = document.getElementById('modalPreviewOverlay');
    if (!overlay) return;

    // Elementos DOM del modal
    const imgEl        = document.getElementById('previewImg');
    const imgFallback  = document.getElementById('previewImgFallback');
    const badgeWrap    = document.getElementById('previewBadgeWrap');
    const catEl        = document.getElementById('previewCategoria');
    const nomEl        = document.getElementById('previewNombre');
    const estEl        = document.getElementById('previewEstrellas');
    const ratEl        = document.getElementById('previewRatingText');
    const preAntEl     = document.getElementById('previewPrecioAntes');
    const preActEl     = document.getElementById('previewPrecioActual');
    const uniEl        = document.getElementById('previewUnidad');
    const descEl       = document.getElementById('previewDescripcion');
    const tagsList     = document.getElementById('previewTagsList');
    const cantNum      = document.getElementById('previewCantidadNum');
    const favBtn       = document.getElementById('btnPreviewFav');

    // Poblado de imagen
    const rutaImagen = resolverRutaImagen(p.imagen);
    if (imgEl && imgFallback) {
        if (rutaImagen) {
            imgEl.src = rutaImagen;
            imgEl.alt = p.nombre || '';
            imgEl.style.display = 'block';
            imgFallback.style.display = 'none';
        } else {
            imgEl.style.display = 'none';
            imgFallback.style.display = 'flex';
        }
    }

    // Badge
    if (badgeWrap) {
        badgeWrap.innerHTML = p.badge && p.badge_tipo
            ? `<span class="preview-badge ${sanitizar(p.badge_tipo)}">${sanitizar(p.badge)}</span>`
            : '';
    }

    // Textos y precios
    if (catEl) catEl.textContent = CATEGORIAS_NOMBRE[p.categoria] || p.categoria || 'Especialidad';
    if (nomEl) nomEl.textContent = p.nombre || '';
    if (estEl) estEl.textContent = generarEstrellas(p.rating_valor || 5);
    if (ratEl) ratEl.textContent = `${p.rating_valor || 4.9} (${p.rating_cantidad || 120} valoraciones)`;

    if (preAntEl) {
        if (p.precio_antes && parseFloat(p.precio_antes) > parseFloat(p.precio)) {
            preAntEl.textContent = formatearPrecio(p.precio_antes);
            preAntEl.style.display = 'inline';
        } else {
            preAntEl.textContent = '';
            preAntEl.style.display = 'none';
        }
    }

    if (preActEl) preActEl.textContent = formatearPrecio(p.precio);
    if (uniEl) uniEl.textContent = p.unidad ? `/ ${p.unidad}` : '';
    if (descEl) descEl.textContent = p.descripcion || 'Producto artesanal de alta calidad elaborado con los mejores estándares.';

    // Tags y notas sensoriales
    if (tagsList) {
        const notas = NOTAS_CATA_POR_CATEGORIA[p.categoria] || ['Especialidad de la Casa', 'Ingredientes de Origen', 'Elaboración Artesanal'];
        tagsList.innerHTML = notas.map(n => `
            <span class="preview-tag-item">
                <i class="fas fa-check" style="font-size:9px;margin-right:5px;color:var(--gold-primary);"></i>${sanitizar(n)}
            </span>
        `).join('');
    }

    // Reset de cantidad a 1
    if (cantNum) cantNum.textContent = '1';

    // Sincronización de botón de favoritos dentro del modal
    if (favBtn) {
        const esFavorito = favoritosActivos.has(id);
        favBtn.classList.toggle('activo', esFavorito);
        const icon = favBtn.querySelector('i');
        if (icon) {
            icon.classList.toggle('far', !esFavorito);
            icon.classList.toggle('fas', esFavorito);
        }

        favBtn.onclick = async function (e) {
            e.stopPropagation();
            const eraActivo = favBtn.classList.contains('activo');
            favBtn.classList.toggle('activo', !eraActivo);
            if (icon) {
                icon.classList.toggle('far', eraActivo);
                icon.classList.toggle('fas', !eraActivo);
            }

            const res = await toggleFavoritoServidor(id);
            if (!res) {
                favBtn.classList.toggle('activo', eraActivo);
                if (icon) {
                    icon.classList.toggle('far', !eraActivo);
                    icon.classList.toggle('fas', eraActivo);
                }
                return;
            }

            if (res.favorito) {
                favoritosActivos.add(id);
                mostrarToast('Guardado en favoritos ❤️', 'fa-heart');
            } else {
                favoritosActivos.delete(id);
                mostrarToast('Eliminado de favoritos', 'fa-heart');
            }

            // Sincronizar tarjeta correspondiente en el grid principal
            const cardFav = document.querySelector(`[data-id="${id}"] .btn-favorito`);
            if (cardFav) {
                cardFav.classList.toggle('activo', favoritosActivos.has(id));
                const cardIcon = cardFav.querySelector('i');
                if (cardIcon) {
                    cardIcon.classList.toggle('far', !favoritosActivos.has(id));
                    cardIcon.classList.toggle('fas', favoritosActivos.has(id));
                }
            }
        };
    }

    overlay.classList.add('activo');
    document.body.style.overflow = 'hidden';
}

function cerrarVistaPrevia() {
    const overlay = document.getElementById('modalPreviewOverlay');
    if (overlay) {
        overlay.classList.remove('activo');
    }
    document.body.style.overflow = '';
    previewProductoActual = null;
    previewCantidadActual = 1;
}

function cambiarCantidadPreview(delta) {
    previewCantidadActual += delta;
    if (previewCantidadActual < 1) previewCantidadActual = 1;
    if (previewCantidadActual > 50) previewCantidadActual = 50;

    const cantNum = document.getElementById('previewCantidadNum');
    if (cantNum) cantNum.textContent = previewCantidadActual;
}

function agregarDesdePreview() {
    if (!previewProductoActual) return;
    const id = parseInt(previewProductoActual.id, 10);
    const cantidad = previewCantidadActual;

    if (carrito[id]) {
        carrito[id].cantidad += cantidad;
    } else {
        carrito[id] = {
            nombre:  previewProductoActual.nombre,
            precio:  parseFloat(previewProductoActual.precio) || 0,
            icono:   previewProductoActual.icono,
            imagen:  previewProductoActual.imagen,
            cantidad: cantidad
        };
    }

    guardarCarritoLocal();
    actualizarUI();
    mostrarToast(`${previewProductoActual.nombre} (${cantidad}) agregado al carrito`, 'fa-bag-shopping');

    // Animación rebote del botón flotante
    const flotante = document.querySelector('.btn-carrito-flotante');
    if (flotante) {
        flotante.classList.remove('bounce');
        void flotante.offsetWidth;
        flotante.classList.add('bounce');
        setTimeout(() => flotante.classList.remove('bounce'), 500);
    }

    // Feedback visual en el botón del modal
    const btnPreview = document.getElementById('btnPreviewAgregar');
    if (btnPreview) {
        const textoOriginal = btnPreview.innerHTML;
        btnPreview.innerHTML = '<i class="fas fa-check"></i> <span>¡Agregado con éxito!</span>';
        btnPreview.style.background = '#2E7D32';
        btnPreview.style.borderColor = '#2E7D32';

        setTimeout(() => {
            btnPreview.innerHTML = textoOriginal;
            btnPreview.style.background = '';
            btnPreview.style.borderColor = '';
            cerrarVistaPrevia();
        }, 850);
    }
}

// Exponer funciones globales para interactividad inline
window.abrirVistaPrevia       = abrirVistaPrevia;
window.cerrarVistaPrevia      = cerrarVistaPrevia;
window.cambiarCantidadPreview = cambiarCantidadPreview;
window.agregarDesdePreview    = agregarDesdePreview;

/* Esqueletos de carga */
function generarEsqueletos(n) {
    const card = `
        <div class="producto-card" style="pointer-events:none;">
            <div class="producto-img-wrap" style="background:linear-gradient(90deg,#f0ebe0 25%,#e8e3d8 50%,#f0ebe0 75%);background-size:200% 100%;animation:shimmer 1.4s infinite;"></div>
            <div class="producto-info">
                <div style="height:10px;border-radius:4px;background:#e8e3d8;margin-bottom:10px;width:50%;"></div>
                <div style="height:18px;border-radius:4px;background:#e8e3d8;margin-bottom:8px;width:80%;"></div>
                <div style="height:12px;border-radius:4px;background:#f0ebe0;margin-bottom:6px;"></div>
                <div style="height:12px;border-radius:4px;background:#f0ebe0;width:70%;"></div>
            </div>
        </div>`;

    if (!document.getElementById('shimmer-style')) {
        const st = document.createElement('style');
        st.id = 'shimmer-style';
        st.textContent = '@keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}';
        document.head.appendChild(st);
    }
    return Array(n).fill(card).join('');
}

/* ============================================================
   CARRITO — OPERACIONES CRUD
============================================================ */
function agregarAlCarrito(productoId) {
    const id = parseInt(productoId, 10);
    if (isNaN(id) || !PRODUCTOS[id]) return;

    if (carrito[id]) {
        carrito[id].cantidad += 1;
    } else {
        carrito[id] = { ...PRODUCTOS[id], cantidad: 1 };
    }

    guardarCarritoLocal();
    actualizarUI();
    mostrarToast(sanitizar(PRODUCTOS[id].nombre) + ' agregado al carrito', 'fa-bag-shopping');

    // Micro-interacción: Rebote del botón flotante
    const flotante = document.querySelector('.btn-carrito-flotante');
    if (flotante) {
        flotante.classList.remove('bounce');
        void flotante.offsetWidth; // forzar reflow
        flotante.classList.add('bounce');
        setTimeout(() => flotante.classList.remove('bounce'), 500);
    }

    const btn = document.querySelector(`[data-id="${id}"] .btn-agregar`);
    if (btn) {
        btn.classList.add('agregado');
        const textoOriginal = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i><span>¡Listo!</span>';
        setTimeout(() => { btn.classList.remove('agregado'); btn.innerHTML = textoOriginal; }, 1400);
    }
}

function cambiarCantidad(id, delta) {
    id = parseInt(id, 10);
    if (!carrito[id]) return;
    carrito[id].cantidad += delta;
    if (carrito[id].cantidad <= 0) delete carrito[id];
    guardarCarritoLocal();
    actualizarUI();
}

function eliminarItem(id) {
    id = parseInt(id, 10);
    delete carrito[id];
    guardarCarritoLocal();
    actualizarUI();
    mostrarToast('Producto eliminado', 'fa-trash-alt');
}

function vaciarCarrito() {
    if (!Object.keys(carrito).length) return;
    if (!confirm('¿Estás seguro de que deseas vaciar tu carrito de compras?')) return;
    carrito = {};
    guardarCarritoLocal();
    actualizarUI();
    mostrarToast('Carrito vaciado', 'fa-trash');
}

/* ============================================================
   CARRITO — ACTUALIZACIÓN DE LA INTERFAZ & SINCRONIZACIÓN
============================================================ */
function actualizarUI() {
    const ids        = Object.keys(carrito);
    const totalItems = ids.reduce((s, id) => s + (carrito[id].cantidad || 0), 0);
    const subtotal   = ids.reduce((s, id) => s + (carrito[id].precio * (carrito[id].cantidad || 0)), 0);
    const META_ENVIO = 150000;
    const envioGratis = subtotal >= META_ENVIO;
    const costoEnvio  = subtotal > 0 ? (envioGratis ? 0 : 12000) : 0;
    const total       = subtotal + costoEnvio;

    // Badges en botón flotante y navbars
    const badgeFlotante = document.getElementById('carritoBadge');
    if (badgeFlotante) {
        badgeFlotante.textContent = totalItems;
        badgeFlotante.classList.toggle('oculto', totalItems === 0);
    }

    const badgeDesktop = document.getElementById('nav-badge-desktop');
    const badgeMobile  = document.getElementById('nav-badge-mobile');
    [badgeDesktop, badgeMobile].forEach(b => {
        if (b) {
            b.textContent = totalItems;
            b.classList.toggle('oculto', totalItems === 0);
        }
    });

    const headerCount = document.getElementById('carritoContadorHeader');
    if (headerCount) headerCount.textContent = totalItems === 1 ? '1 artículo' : `${totalItems} artículos`;

    // ── Barra dinámica de progreso para Envío Gratis VIP ──
    const progresoBar   = document.getElementById('envioProgresoBar');
    const progresoTexto = document.getElementById('envioProgresoTexto');
    if (progresoBar && progresoTexto) {
        if (subtotal === 0) {
            progresoBar.style.width = '0%';
            progresoBar.classList.remove('completado');
            progresoTexto.innerHTML = '<i class="fas fa-truck-fast"></i> <span>Envío gratis VIP a partir de $150.000</span>';
        } else if (subtotal < META_ENVIO) {
            const pct = Math.min(100, Math.round((subtotal / META_ENVIO) * 100));
            const falta = META_ENVIO - subtotal;
            progresoBar.style.width = pct + '%';
            progresoBar.classList.remove('completado');
            progresoTexto.innerHTML = `<i class="fas fa-truck-fast"></i> <span>Te faltan <strong>${formatearPrecio(falta)}</strong> para Envío Gratis VIP</span>`;
        } else {
            progresoBar.style.width = '100%';
            progresoBar.classList.add('completado');
            progresoTexto.innerHTML = '<i class="fas fa-gift"></i> <span>✨ <strong>¡Felicidades!</strong> Tu pedido califica para Envío Gratis VIP</span>';
        }
    }

    const elVacio  = document.getElementById('carritoVacio');
    const elLista  = document.getElementById('carritoItemsList');
    const elFooter = document.getElementById('carritoFooter');

    if (!ids.length) {
        if (elVacio)  elVacio.style.display  = 'flex';
        if (elLista)  elLista.style.display  = 'none';
        if (elFooter) elFooter.style.display = 'none';
        return;
    }

    if (elVacio)  elVacio.style.display  = 'none';
    if (elLista)  elLista.style.display  = 'flex';
    if (elFooter) elFooter.style.display = 'flex';

    if (elLista) {
        elLista.innerHTML = ids.map(id => {
            const item = carrito[id];
            const rutaImg = resolverRutaImagen(item.imagen);
            const miniatura = rutaImg
                ? `<img src="${sanitizar(rutaImg)}" alt="${sanitizar(item.nombre)}"
                        onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                   <div style="display:none;width:100%;height:100%;align-items:center;justify-content:center;">
                       <i class="fas ${sanitizar(item.icono || 'fa-mug-hot')}"></i>
                   </div>`
                : `<i class="fas ${sanitizar(item.icono || 'fa-mug-hot')}"></i>`;

            return `
            <div class="carrito-item" id="ci${id}">
                <div class="carrito-item-img">${miniatura}</div>
                <div class="carrito-item-info">
                    <div class="carrito-item-nombre" title="${sanitizar(item.nombre)}">${sanitizar(item.nombre)}</div>
                    <div class="carrito-item-precio">${formatearPrecio(item.precio)}</div>
                    <div class="carrito-item-controles">
                        <button class="btn-cantidad" onclick="cambiarCantidad(${id},-1)" aria-label="Reducir">−</button>
                        <span class="cantidad-num">${item.cantidad}</span>
                        <button class="btn-cantidad" onclick="cambiarCantidad(${id},1)" aria-label="Aumentar">+</button>
                        <button class="btn-eliminar-item" onclick="eliminarItem(${id})" aria-label="Eliminar" title="Eliminar producto">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    const elSubtotal = document.getElementById('subtotalCarrito');
    const elEnvio    = document.getElementById('envioCarrito');
    const elTotal    = document.getElementById('totalCarrito');
    if (elSubtotal) elSubtotal.textContent = formatearPrecio(subtotal);
    if (elEnvio)    elEnvio.textContent    = envioGratis ? 'Gratis 🎉' : formatearPrecio(costoEnvio);
    if (elTotal)    elTotal.textContent    = formatearPrecio(total);
}

/* ============================================================
   CARRITO — DRAWER
============================================================ */
function toggleCarrito() {
    const drawer = document.getElementById('carritoDrawer');
    if (!drawer) return;
    const estaAbierto = drawer.classList.contains('abierto') || drawer.classList.contains('open');
    estaAbierto ? cerrarCarrito() : abrirCarrito();
}

function abrirCarrito() {
    const drawer  = document.getElementById('carritoDrawer');
    const overlay = document.getElementById('carritoOverlay');
    if (drawer) {
        drawer.classList.add('abierto', 'open');
    }
    if (overlay) {
        overlay.classList.add('visible', 'active');
    }
    document.body.style.overflow = 'hidden';
}

function cerrarCarrito() {
    const drawer  = document.getElementById('carritoDrawer');
    const overlay = document.getElementById('carritoOverlay');
    if (drawer) {
        drawer.classList.remove('abierto', 'open');
    }
    if (overlay) {
        overlay.classList.remove('visible', 'active');
    }
    document.body.style.overflow = '';
}

/* ============================================================
   TOAST NOTIFICACIONES
============================================================ */
function mostrarToast(mensaje, icono = 'fa-check') {
    const contenedor = document.getElementById('toastContainer');
    if (!contenedor) return;
    const toast = document.createElement('div');
    toast.className = 'toast-notif';
    toast.innerHTML = `<i class="fas ${sanitizar(icono)}"></i> <span>${sanitizar(mensaje)}</span>`;
    contenedor.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('saliendo');
        setTimeout(() => toast.remove(), 300);
    }, 2600);
}

/* ============================================================
   CHECKOUT — ABRIR / CERRAR
============================================================ */
function abrirCheckout() {
    if (!Object.keys(carrito).length) {
        mostrarToast('El carrito está vacío', 'fa-exclamation-circle');
        return;
    }

    const ids        = Object.keys(carrito);
    const subtotal   = ids.reduce((s, id) => s + (carrito[id].precio * carrito[id].cantidad), 0);
    const envioGratis = subtotal >= 150000;
    const costoEnvio  = envioGratis ? 0 : 12000;
    const total       = subtotal + costoEnvio;

    const modalResumen = document.getElementById('modalResumen');
    if (modalResumen) {
        modalResumen.innerHTML = `
            <div class="resumen-titulo-modal">Resumen de tu pedido</div>
            ${ids.map(id => `
            <div class="resumen-item-modal">
                <span>${sanitizar(carrito[id].nombre)} × ${carrito[id].cantidad}</span>
                <span>${formatearPrecio(carrito[id].precio * carrito[id].cantidad)}</span>
            </div>`).join('')}
            <div class="resumen-item-modal">
                <span>Envío ${envioGratis ? 'VIP' : 'Nacional'}</span>
                <span style="${envioGratis ? 'color:#27ae60;font-weight:700;' : ''}">${envioGratis ? 'Gratis 🎉' : formatearPrecio(costoEnvio)}</span>
            </div>
            <div class="resumen-total-modal">
                <span>Total a pagar</span>
                <span style="color:#C6A76B;font-size:16px;">${formatearPrecio(total)}</span>
            </div>`;
    }

    // Autocompletar datos del usuario si ha iniciado sesión en Firebase / sessionStorage
    try {
        const userSaved = JSON.parse(sessionStorage.getItem('cc_usuario') || 'null');
        if (userSaved) {
            const nombreInput   = document.getElementById('co-nombre');
            const apellidoInput = document.getElementById('co-apellido');
            const emailInput    = document.getElementById('co-email');
            if (emailInput && !emailInput.value && userSaved.email) emailInput.value = userSaved.email;
            if (nombreInput && !nombreInput.value && userSaved.nombre) {
                const partes = userSaved.nombre.trim().split(' ');
                nombreInput.value = partes[0] || '';
                if (apellidoInput && !apellidoInput.value && partes.length > 1) {
                    apellidoInput.value = partes.slice(1).join(' ');
                }
            }
        }
    } catch (_) {}

    document.getElementById('modalFormContent')?.classList.remove('oculto');
    document.getElementById('modalSuccess')?.classList.remove('visible');
    limpiarErrores();
    document.getElementById('modalCheckoutOverlay')?.classList.add('visible', 'active');
    cerrarCarrito();
    document.body.style.overflow = 'hidden';
}

function cerrarCheckout() {
    document.getElementById('modalCheckoutOverlay')?.classList.remove('visible', 'active');
    document.body.style.overflow = '';
}

function cerrarTodo() {
    cerrarCheckout();
    carrito = {};
    guardarCarritoLocal();
    actualizarUI();
}

/* ============================================================
   CHECKOUT — VALIDACIÓN
============================================================ */
function limpiarErrores() {
    document.querySelectorAll('.form-error-msg').forEach(el => el.classList.remove('visible'));
    document.querySelectorAll('.form-grupo').forEach(el => el.classList.remove('error'));
    document.querySelectorAll('.form-grupo input, .form-grupo select').forEach(el => el.classList.remove('error'));
}

function marcarError(campoId, errorId) {
    const campo = document.getElementById(campoId);
    if (campo) {
        campo.classList.add('error');
        campo.closest('.form-grupo')?.classList.add('error');
    }
    document.getElementById(errorId)?.classList.add('visible');
}

function validarFormulario() {
    limpiarErrores();
    let valido = true;

    const nombre    = document.getElementById('co-nombre')?.value.trim()    || '';
    const apellido  = document.getElementById('co-apellido')?.value.trim()  || '';
    const email     = document.getElementById('co-email')?.value.trim()     || '';
    const telefono  = document.getElementById('co-telefono')?.value.trim()  || '';
    const ciudad    = document.getElementById('co-ciudad')?.value           || '';
    const direccion = document.getElementById('co-direccion')?.value.trim() || '';

    const soloLetras = /^[a-zA-ZÀ-ÿ\s'\-]{2,50}$/;
    const emailRx    = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~\-]+@[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?)*\.[a-zA-Z]{2,}$/;

    if (!soloLetras.test(nombre))   { marcarError('co-nombre',    'err-nombre');    valido = false; }
    if (!soloLetras.test(apellido)) { marcarError('co-apellido',  'err-apellido');  valido = false; }
    if (!emailRx.test(email) || email.length > 254) { marcarError('co-email', 'err-email'); valido = false; }
    if (!/^[0-9]{7,15}$/.test(telefono.replace(/[\s\-\(\)\+]/g, ''))) {
        marcarError('co-telefono', 'err-telefono'); valido = false;
    }
    if (!ciudad)              { marcarError('co-ciudad',    'err-ciudad');    valido = false; }
    if (direccion.length < 5 || direccion.length > 200) { marcarError('co-direccion', 'err-direccion'); valido = false; }

    return valido;
}

/* ============================================================
   CHECKOUT — ENVÍO AL BACKEND
============================================================ */
async function enviarPedidoAlServidor(datosCliente, btn) {
    const ids        = Object.keys(carrito);
    const subtotal   = ids.reduce((s, id) => s + (carrito[id].precio * carrito[id].cantidad), 0);
    const envioGratis = subtotal >= 150000;
    const costoEnvio  = envioGratis ? 0 : 12000;
    const total       = subtotal + costoEnvio;

    const items = ids.map(id => ({
        producto_id:     parseInt(id, 10),
        cantidad:        carrito[id].cantidad,
        precio_unitario: carrito[id].precio
    }));

    const payload = { ...datosCliente, total, items };

    console.log('[CoffeeCol] Enviando pedido al servidor:', payload);

    try {
        const res = await fetch(ORDERS_URL, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(payload)
        });

        console.log('[CoffeeCol] Respuesta HTTP de orders.php:', res.status, res.statusText);

        let data;
        try {
            data = await res.json();
        } catch (jsonErr) {
            console.error('[CoffeeCol] orders.php no devolvió JSON válido:', jsonErr);
            throw new Error('El servidor no devolvió una respuesta válida.');
        }

        console.log('[CoffeeCol] Respuesta del servidor:', data);

        if (!res.ok || !data.success) {
            const errorMsg = data?.error || `Error HTTP ${res.status}`;
            console.error('[CoffeeCol] El servidor rechazó el pedido:', errorMsg);
            throw new Error(errorMsg);
        }

        console.log('[CoffeeCol] ✅ Pedido guardado correctamente. ID:', data.pedido_id);

        // Vaciar el carrito en memoria y almacenamiento local
        carrito = {};
        guardarCarritoLocal();
        actualizarUI();

        mostrarExitoCheckout(btn, data.pedido_id, data);

    } catch (err) {
        console.error('[CoffeeCol] Error al guardar pedido:', err);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Confirmar pedido';
        mostrarToast('Error al procesar el pedido: ' + sanitizar(err.message), 'fa-exclamation-circle');
    }
}

function mostrarExitoCheckout(btn, pedidoId, data = null) {
    document.getElementById('modalFormContent')?.classList.add('oculto');
    const modalSuccess = document.getElementById('modalSuccess');
    if (modalSuccess) {
        modalSuccess.classList.add('visible');
        const msgEl = modalSuccess.querySelector('.success-msg');
        const numPed = data?.numero_pedido || `#TC-${pedidoId}`;
        const ptsHTML = (data && data.puntos_ganados > 0)
            ? `<div style="margin:14px 0 6px;padding:10px 14px;background:#fdf5ee;border:1px solid rgba(196,149,106,0.4);border-radius:10px;display:flex;align-items:center;justify-content:center;gap:8px;color:#28040A;font-weight:600;font-size:13px;">
                <i class="fas fa-star" style="color:#c4956a"></i> ¡Has acumulado <strong>+${data.puntos_ganados} puntos Tantico</strong> con este pedido!
               </div>`
            : '';

        if (msgEl && pedidoId) {
            msgEl.innerHTML = `
                Hemos registrado tu pedido con el código <strong>${sanitizar(numPed)}</strong>. En breve te contactaremos a tu WhatsApp o correo registrado para coordinar los detalles del despacho.
                ${ptsHTML}
                <br><strong>¡Gracias por apoyar el café especial del Huila!</strong>
            `;
        }
    }
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-check-circle"></i> Confirmar pedido';

    // Notificar en tiempo real a las pestañas abiertas de Admin y Mi Perfil
    try {
        const payload = {
            type: 'nuevo_pedido',
            pedido_id: pedidoId,
            numero_pedido: data?.numero_pedido || (`#TC-${pedidoId}`),
            puntos_ganados: data?.puntos_ganados || 0,
            puntos_totales: data?.puntos_totales || null,
            timestamp: Date.now()
        };
        localStorage.setItem('tantico_nuevo_pedido', JSON.stringify(payload));
        if (data?.puntos_totales !== undefined && data?.puntos_totales !== null) {
            localStorage.setItem('tantico_puntos_actualizados', JSON.stringify({ puntos: data.puntos_totales, timestamp: Date.now() }));
        }
        const bc = new BroadcastChannel('tantico_channel');
        bc.postMessage(payload);
    } catch (_) {}
}

/* ============================================================
   FILTROS DE CATEGORÍA — Transición Ultra Fluida
============================================================ */
function inicializarFiltros() {
    const botones  = document.querySelectorAll('.filtro-btn');
    const contador = document.getElementById('contadorProductos');
    const grid     = document.getElementById('productosGrid');
    if (!botones.length) return;

    botones.forEach(btn => {
        btn.addEventListener('click', function () {
            if (this.classList.contains('active')) return;

            botones.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const catSeleccionada = this.dataset.categoria;
            const todasCards = Array.from(document.querySelectorAll('.producto-card'));

            // 1. Desvanecer suavemente todas las tarjetas que están visibles
            todasCards.forEach(card => card.classList.add('anim-salida'));

            setTimeout(() => {
                let visibles = 0;
                let staggerIndex = 0;

                todasCards.forEach(card => {
                    const cardCat = card.dataset.categoria;
                    const coincide = catSeleccionada === 'todos'
                        || (catSeleccionada === 'cafes-todos' && CATEGORIAS_CAFES.includes(cardCat))
                        || cardCat === catSeleccionada;

                    card.classList.remove('anim-salida', 'anim-entrada');

                    if (!coincide) {
                        card.classList.add('filtrado-oculto');
                    } else {
                        card.classList.remove('filtrado-oculto');
                        card.style.animationDelay = `${staggerIndex * 35}ms`;
                        card.classList.add('anim-entrada');
                        staggerIndex++;
                        visibles++;
                    }
                });

                // Manejo de estado vacío en la categoría
                const emptyExistente = document.getElementById('catalogoEmptyState');
                if (visibles === 0) {
                    if (!emptyExistente && grid) {
                        const emptyDiv = document.createElement('div');
                        emptyDiv.id = 'catalogoEmptyState';
                        emptyDiv.className = 'catalogo-vacio anim-entrada';
                        emptyDiv.innerHTML = `
                            <i class="fas fa-mug-hot"></i>
                            <strong>No hay productos en esta categoría actualmente</strong>
                            <p style="margin:0;font-size:13.5px;color:#888;">Te invitamos a explorar nuestras otras variedades y especialidades.</p>
                        `;
                        grid.appendChild(emptyDiv);
                    }
                } else if (emptyExistente) {
                    emptyExistente.remove();
                }

                // Actualizar contador con texto editorial elegante
                if (contador) {
                    const nombreCat = CATEGORIAS_NOMBRE[catSeleccionada] || 'especialidad';
                    if (catSeleccionada === 'todos') {
                        contador.textContent = `Mostrando ${visibles} productos & especialidades`;
                    } else if (catSeleccionada === 'cafes-todos') {
                        contador.textContent = `Mostrando ${visibles} cafés de especialidad`;
                    } else {
                        contador.textContent = `Mostrando ${visibles} producto${visibles !== 1 ? 's' : ''} en ${nombreCat}`;
                    }
                }
            }, 160);
        });
    });
}

/* ============================================================
   FAVORITOS — INICIALIZAR BOTONES EN LAS TARJETAS
============================================================ */
function inicializarFavoritos() {
    document.querySelectorAll('.btn-favorito').forEach(btn => {
        const card       = btn.closest('.producto-card');
        const productoId = card ? parseInt(card.dataset.id, 10) : null;

        // Marcar los que ya son favoritos del usuario logueado
        if (productoId && favoritosActivos.has(productoId)) {
            btn.classList.add('activo');
            const icono = btn.querySelector('i');
            if (icono) {
                icono.classList.remove('far');
                icono.classList.add('fas');
            }
        }

        btn.addEventListener('click', async function (e) {
            e.stopPropagation();
            if (!productoId) return;

            const icono    = this.querySelector('i');
            const eraActivo = this.classList.contains('activo');

            // Optimistic UI: toggle visual inmediato
            this.classList.toggle('activo');
            if (icono) {
                icono.classList.toggle('far', eraActivo);
                icono.classList.toggle('fas', !eraActivo);
            }

            const resultado = await toggleFavoritoServidor(productoId);

            if (resultado === null) {
                // Error o redirigido a login: revertir visual
                this.classList.toggle('activo');
                if (icono) {
                    icono.classList.toggle('far', !eraActivo);
                    icono.classList.toggle('fas', eraActivo);
                }
                return;
            }

            if (resultado.favorito) {
                favoritosActivos.add(productoId);
                mostrarToast('Guardado en favoritos ❤️', 'fa-heart');
            } else {
                favoritosActivos.delete(productoId);
                mostrarToast('Eliminado de favoritos', 'fa-heart');
            }
        });
    });
}

/* ============================================================
   ANIMACIÓN DE ENTRADA INICIAL
============================================================ */
function inicializarAnimaciones() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, idx) => {
            if (entry.isIntersecting) {
                setTimeout(() => entry.target.classList.add('anim-entrada'), idx * 50);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08 });

    document.querySelectorAll('.producto-card').forEach(card => observer.observe(card));
}

/* ============================================================
   INICIALIZACIÓN
============================================================ */
document.addEventListener('DOMContentLoaded', function () {

    cargarCarritoLocal();
    cargarProductos();

    // Event listener para checkout form
    const formCheckout = document.getElementById('formCheckout');
    if (formCheckout) {
        formCheckout.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (!validarFormulario()) return;

            const btn = this.querySelector('.btn-confirmar-modal');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando pedido…';
            }

            const datosCliente = {
                nombre:    document.getElementById('co-nombre')?.value.trim(),
                apellido:  document.getElementById('co-apellido')?.value.trim(),
                email:     document.getElementById('co-email')?.value.trim(),
                telefono:  document.getElementById('co-telefono')?.value.trim(),
                ciudad:    document.getElementById('co-ciudad')?.value,
                direccion: document.getElementById('co-direccion')?.value.trim(),
            };

            console.log('[CoffeeCol] Datos del cliente:', datosCliente);
            await enviarPedidoAlServidor(datosCliente, btn);
        });
    }

    // Cierre al hacer click en overlays
    document.getElementById('modalPreviewOverlay')?.addEventListener('click', function (e) {
        if (e.target === this) cerrarVistaPrevia();
    });

    document.getElementById('modalCheckoutOverlay')?.addEventListener('click', function (e) {
        if (e.target === this) cerrarCheckout();
    });

    document.getElementById('carritoOverlay')?.addEventListener('click', function (e) {
        if (e.target === this) cerrarCarrito();
    });

    // Tecla Escape para cerrar modales/drawer
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            cerrarVistaPrevia();
            cerrarCheckout();
            cerrarCarrito();
        }
    });

    // Abrir carrito si se vino redirigido desde otra página con sessionStorage
    if (sessionStorage.getItem('abrir_carrito') === '1') {
        sessionStorage.removeItem('abrir_carrito');
        setTimeout(() => abrirCarrito(), 500);
    }
});