'use strict';

/* ============================================================
   CONFIGURACIÓN
   Ajusta API_URL según tu estructura de carpetas.
   Si service.js está en /js/ y la API en /api/, la ruta es '../api/products.php'
============================================================ */
const API_URL = '../includes/products.php';

/* ============================================================
   CATÁLOGO EN MEMORIA
   Se puebla dinámicamente desde la API al cargar la página.
   Estructura: { [id]: { nombre, precio, icono } }
============================================================ */
let PRODUCTOS = {};

/* ============================================================
   MAPA DE CATEGORÍAS — slug → nombre visible
============================================================ */
const CATEGORIAS_NOMBRE = {
    'tueste-claro':    'Tueste Claro',
    'tueste-medio':    'Tueste Medio',
    'tueste-oscuro':   'Tueste Oscuro',
    'capsulas':        'Cápsulas',
    'origen-especial': 'Origen Especial',
};

/* ============================================================
   ESTADO DEL CARRITO
   Se persiste en localStorage automáticamente.
============================================================ */
let carrito = {};

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
   ESTRELLAS — genera el string de estrellas según valor
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
    try { localStorage.setItem(LS_KEY, JSON.stringify(carrito)); } catch (_) {}
}

function cargarCarritoLocal() {
    try {
        const saved = localStorage.getItem(LS_KEY);
        if (saved) carrito = JSON.parse(saved) || {};
    } catch (_) {
        carrito = {};
    }
}

/* ============================================================
   CARGA DINÁMICA DE PRODUCTOS DESDE LA API
============================================================ */
async function cargarProductos() {
    const grid = document.getElementById('productosGrid');
    if (!grid) return;

    // Esqueleto de carga
    grid.innerHTML = generarEsqueletos(4);

    try {
        const res  = await fetch(API_URL);
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const data = await res.json();

        if (!data.success) throw new Error(data.error || 'Error de API');

        const productos = data.productos || [];

        // Poblar catálogo en memoria
        PRODUCTOS = {};
        productos.forEach(p => {
            PRODUCTOS[p.id] = { nombre: p.nombre, precio: p.precio, icono: p.icono };
        });

        // Renderizar tarjetas
        renderizarProductos(productos);

        // Actualizar contador
        const contador = document.getElementById('contadorProductos');
        if (contador) {
            contador.textContent = `${productos.length} producto${productos.length !== 1 ? 's' : ''}`;
        }

        // Re-inicializar features que dependen del DOM
        inicializarFiltros();
        inicializarFavoritos();
        inicializarAnimaciones();

        // Sincronizar carrito (por si hay ítems guardados de sesiones previas)
        reconstruirCarritoDesdeLocal();

    } catch (err) {
        console.error('[CoffeeCol] Error cargando productos:', err);
        grid.innerHTML = `
            <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:#47060E;">
                <i class="fas fa-exclamation-circle" style="font-size:2rem;opacity:.4;display:block;margin-bottom:12px;"></i>
                <strong>No se pudieron cargar los productos.</strong><br>
                <small style="color:#888;">Verifica la conexión con la base de datos.</small>
            </div>`;
    }
}

/**
 * Tras restaurar el carrito del localStorage, eliminamos ítems
 * cuyo ID ya no existe en PRODUCTOS (fueron borrados del admin).
 */
function reconstruirCarritoDesdeLocal() {
    const idsValidos = Object.keys(PRODUCTOS).map(Number);
    Object.keys(carrito).forEach(id => {
        if (!idsValidos.includes(parseInt(id, 10))) {
            delete carrito[id];
        }
    });
    guardarCarritoLocal();
    actualizarUI();
}

/* ============================================================
   RENDER DE TARJETAS DE PRODUCTO
   Reproduce fielmente la estructura HTML de servicios.php,
   usando las mismas clases CSS sin modificarlas.
============================================================ */
function renderizarProductos(productos) {
    const grid = document.getElementById('productosGrid');
    if (!grid) return;

    if (!productos.length) {
        grid.innerHTML = `
            <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:#888;">
                No hay productos disponibles en este momento.
            </div>`;
        return;
    }

    grid.innerHTML = productos.map(p => {
        const badgeHTML = p.badge && p.badge_tipo
            ? `<div class="producto-badge ${sanitizar(p.badge_tipo)}">${sanitizar(p.badge)}</div>`
            : '';

        const precioAntesHTML = p.precio_antes
            ? `<span class="precio-antes">${formatearPrecio(p.precio_antes)}</span>`
            : '';

        const categoriaNombre = CATEGORIAS_NOMBRE[p.categoria] || p.categoria;
        const estrellas       = generarEstrellas(p.rating_valor);

        return `
        <div class="producto-card" data-categoria="${sanitizar(p.categoria)}" data-id="${p.id}">
            ${badgeHTML}
            <button class="btn-favorito" aria-label="Agregar a favoritos">
                <i class="far fa-heart"></i>
            </button>
            <div class="producto-img-wrap">
                <div class="producto-img-placeholder">
                    <i class="fas ${sanitizar(p.icono)}"></i>
                </div>
            </div>
            <div class="producto-info">
                <div class="producto-categoria">${sanitizar(categoriaNombre)}</div>
                <h3 class="producto-nombre">${sanitizar(p.nombre)}</h3>
                <p class="producto-descripcion">${sanitizar(p.descripcion)}</p>
                <div class="producto-rating">
                    <span class="estrellas">${estrellas}</span>
                    <span class="rating-num">${p.rating_valor} (${p.rating_cantidad})</span>
                </div>
                <div class="producto-footer">
                    <div class="producto-precio">
                        ${precioAntesHTML}
                        <span class="precio-actual">${formatearPrecio(p.precio)}</span>
                        <span class="precio-unidad">${sanitizar(p.unidad)}</span>
                    </div>
                    <button class="btn-agregar" onclick="agregarAlCarrito(${p.id})">
                        <i class="fas fa-bag-shopping"></i><span>Agregar</span>
                    </button>
                </div>
            </div>
        </div>`;
    }).join('');
}

/**
 * Genera tarjetas de carga "esqueleto" mientras llega la API.
 */
function generarEsqueletos(n) {
    const card = `
        <div class="producto-card visible" style="pointer-events:none;">
            <div class="producto-img-wrap" style="background:linear-gradient(90deg,#f0ebe0 25%,#e8e3d8 50%,#f0ebe0 75%);background-size:200% 100%;animation:shimmer 1.4s infinite;">
            </div>
            <div class="producto-info">
                <div style="height:10px;border-radius:4px;background:#e8e3d8;margin-bottom:10px;width:50%;"></div>
                <div style="height:18px;border-radius:4px;background:#e8e3d8;margin-bottom:8px;width:80%;"></div>
                <div style="height:12px;border-radius:4px;background:#f0ebe0;margin-bottom:6px;"></div>
                <div style="height:12px;border-radius:4px;background:#f0ebe0;width:70%;"></div>
            </div>
        </div>`;

    // Añadir animación shimmer si no existe
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

    // Feedback visual en el botón
    const btn = document.querySelector(`[data-id="${id}"] .btn-agregar`);
    if (btn) {
        btn.classList.add('agregado');
        const textoOriginal = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i><span>¡Listo!</span>';
        setTimeout(() => {
            btn.classList.remove('agregado');
            btn.innerHTML = textoOriginal;
        }, 1400);
    }
}

function cambiarCantidad(id, delta) {
    id = parseInt(id, 10);
    if (!carrito[id]) return;

    carrito[id].cantidad += delta;

    if (carrito[id].cantidad <= 0) {
        delete carrito[id];
    }

    guardarCarritoLocal();
    actualizarUI();
}

function eliminarItem(id) {
    id = parseInt(id, 10);
    delete carrito[id];
    guardarCarritoLocal();
    actualizarUI();
}

function vaciarCarrito() {
    if (!Object.keys(carrito).length) return;
    if (!confirm('¿Estás seguro de que quieres vaciar el carrito?')) return;
    carrito = {};
    guardarCarritoLocal();
    actualizarUI();
    mostrarToast('Carrito vaciado', 'fa-trash');
}

/* ============================================================
   CARRITO — ACTUALIZACIÓN DE LA INTERFAZ
============================================================ */
function actualizarUI() {
    const ids        = Object.keys(carrito);
    const totalItems = ids.reduce((s, id) => s + carrito[id].cantidad, 0);
    const subtotal   = ids.reduce((s, id) => s + carrito[id].precio * carrito[id].cantidad, 0);
    const envioGratis = subtotal >= 150000;
    const costoEnvio  = subtotal > 0 ? (envioGratis ? 0 : 12000) : 0;
    const total       = subtotal + costoEnvio;

    // Badge flotante
    const badge = document.getElementById('carritoBadge');
    if (badge) {
        badge.textContent = totalItems;
        badge.classList.toggle('oculto', totalItems === 0);
    }

    // Contador en header del drawer
    const headerCount = document.getElementById('carritoContadorHeader');
    if (headerCount) {
        headerCount.textContent = totalItems === 1 ? '1 artículo' : `${totalItems} artículos`;
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
    if (elLista)  elLista.style.display  = 'block';
    if (elFooter) elFooter.style.display = 'block';

    // Render ítems del carrito
    if (elLista) {
        elLista.innerHTML = ids.map(id => {
            const item = carrito[id];
            return `
            <div class="carrito-item" id="ci${id}">
                <div class="carrito-item-img">
                    <i class="fas ${sanitizar(item.icono)}"></i>
                </div>
                <div class="carrito-item-info">
                    <div class="carrito-item-nombre">${sanitizar(item.nombre)}</div>
                    <div class="carrito-item-precio">${formatearPrecio(item.precio)}</div>
                    <div class="carrito-item-controles">
                        <button class="btn-cantidad"
                            onclick="cambiarCantidad(${id}, -1)"
                            aria-label="Reducir cantidad">−</button>
                        <span class="cantidad-num">${item.cantidad}</span>
                        <button class="btn-cantidad"
                            onclick="cambiarCantidad(${id}, 1)"
                            aria-label="Aumentar cantidad">+</button>
                        <button class="btn-eliminar-item"
                            onclick="eliminarItem(${id})"
                            aria-label="Eliminar producto">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    // Totales
    const elSubtotal = document.getElementById('subtotalCarrito');
    const elEnvio    = document.getElementById('envioCarrito');
    const elTotal    = document.getElementById('totalCarrito');

    if (elSubtotal) elSubtotal.textContent = formatearPrecio(subtotal);
    if (elEnvio)    elEnvio.textContent    = envioGratis ? 'Gratis 🎉' : formatearPrecio(costoEnvio);
    if (elTotal)    elTotal.textContent    = formatearPrecio(total);
}

/* ============================================================
   CARRITO — ABRIR / CERRAR DRAWER
============================================================ */
function toggleCarrito() {
    const drawer = document.getElementById('carritoDrawer');
    if (!drawer) return;
    drawer.classList.contains('abierto') ? cerrarCarrito() : abrirCarrito();
}

function abrirCarrito() {
    document.getElementById('carritoDrawer')?.classList.add('abierto');
    document.getElementById('carritoOverlay')?.classList.add('visible');
    document.body.style.overflow = 'hidden';
}

function cerrarCarrito() {
    document.getElementById('carritoDrawer')?.classList.remove('abierto');
    document.getElementById('carritoOverlay')?.classList.remove('visible');
    document.body.style.overflow = '';
}

/* ============================================================
   TOAST / NOTIFICACIONES
============================================================ */
function mostrarToast(mensaje, icono = 'fa-check') {
    const contenedor = document.getElementById('toastContainer');
    if (!contenedor) return;

    const toast = document.createElement('div');
    toast.className = 'toast-notif';
    toast.innerHTML = `<i class="fas ${sanitizar(icono)}"></i> ${sanitizar(mensaje)}`;
    contenedor.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('saliendo');
        setTimeout(() => toast.remove(), 250);
    }, 2500);
}

/* ============================================================
   CHECKOUT — ABRIR / CERRAR MODAL
============================================================ */
function abrirCheckout() {
    if (!Object.keys(carrito).length) {
        mostrarToast('El carrito está vacío', 'fa-exclamation-circle');
        return;
    }

    const ids       = Object.keys(carrito);
    const subtotal  = ids.reduce((s, id) => s + carrito[id].precio * carrito[id].cantidad, 0);
    const envioGratis = subtotal >= 150000;
    const costoEnvio  = envioGratis ? 0 : 12000;
    const total       = subtotal + costoEnvio;

    const modalResumen = document.getElementById('modalResumen');
    if (modalResumen) {
        modalResumen.innerHTML = `
            <div class="resumen-titulo-modal">Resumen del pedido</div>
            ${ids.map(id => `
            <div class="resumen-item-modal">
                <span>${sanitizar(carrito[id].nombre)} × ${carrito[id].cantidad}</span>
                <span>${formatearPrecio(carrito[id].precio * carrito[id].cantidad)}</span>
            </div>`).join('')}
            <div class="resumen-item-modal">
                <span>Envío</span>
                <span>${envioGratis ? 'Gratis' : formatearPrecio(costoEnvio)}</span>
            </div>
            <div class="resumen-total-modal">
                <span>Total</span>
                <span>${formatearPrecio(total)}</span>
            </div>`;
    }

    document.getElementById('modalFormContent')?.classList.remove('oculto');
    document.getElementById('modalSuccess')?.classList.remove('visible');
    limpiarErrores();

    document.getElementById('modalCheckoutOverlay')?.classList.add('visible');
    cerrarCarrito();
    document.body.style.overflow = 'hidden';
}

function cerrarCheckout() {
    document.getElementById('modalCheckoutOverlay')?.classList.remove('visible');
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
    document.querySelectorAll('.form-grupo input, .form-grupo select').forEach(el => el.classList.remove('error'));
}

function marcarError(campoId, errorId) {
    document.getElementById(campoId)?.classList.add('error');
    document.getElementById(errorId)?.classList.add('visible');
}

function validarFormulario() {
    limpiarErrores();
    let valido = true;

    const nombre    = document.getElementById('co-nombre')?.value.trim()   || '';
    const apellido  = document.getElementById('co-apellido')?.value.trim() || '';
    const email     = document.getElementById('co-email')?.value.trim()    || '';
    const telefono  = document.getElementById('co-telefono')?.value.trim() || '';
    const ciudad    = document.getElementById('co-ciudad')?.value          || '';
    const direccion = document.getElementById('co-direccion')?.value.trim()|| '';

    const soloLetras = /^[a-zA-ZÀ-ÿ\s'\-]{2,50}$/;
    const emailRx    = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~\-]+@[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?)*\.[a-zA-Z]{2,}$/;

    if (!soloLetras.test(nombre))                  { marcarError('co-nombre',    'err-nombre');    valido = false; }
    if (!soloLetras.test(apellido))                { marcarError('co-apellido',  'err-apellido');  valido = false; }
    if (!emailRx.test(email) || email.length > 254){ marcarError('co-email',     'err-email');     valido = false; }
    if (!/^[0-9]{7,15}$/.test(telefono.replace(/[\s\-\(\)\+]/g, ''))) {
        marcarError('co-telefono', 'err-telefono'); valido = false;
    }
    if (!ciudad)                                   { marcarError('co-ciudad',    'err-ciudad');    valido = false; }
    if (direccion.length < 5 || direccion.length > 200) { marcarError('co-direccion', 'err-direccion'); valido = false; }

    return valido;
}

/* ============================================================
   CHECKOUT — ESTADO ÉXITO
============================================================ */
function mostrarExitoCheckout(btn) {
    document.getElementById('modalFormContent')?.classList.add('oculto');
    document.getElementById('modalSuccess')?.classList.add('visible');
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-check-circle"></i> Confirmar pedido';
}

/* ============================================================
   FILTROS DE CATEGORÍA
============================================================ */
function inicializarFiltros() {
    const botones = document.querySelectorAll('.filtro-btn');
    const contador = document.getElementById('contadorProductos');

    botones.forEach(btn => {
        // Evitar doble listener
        btn.replaceWith(btn.cloneNode(true));
    });

    document.querySelectorAll('.filtro-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const cat = this.dataset.categoria;
            let visibles = 0;

            document.querySelectorAll('.producto-card').forEach(card => {
                const coincide = cat === 'todos' || card.dataset.categoria === cat;
                card.classList.toggle('filtrado', !coincide);
                if (!coincide) {
                    card.style.position   = 'absolute';
                    card.style.visibility = 'hidden';
                } else {
                    card.style.position   = '';
                    card.style.visibility = '';
                    visibles++;
                }
            });

            if (contador) {
                contador.textContent = `${visibles} producto${visibles !== 1 ? 's' : ''}`;
            }
        });
    });
}

/* ============================================================
   FAVORITOS
============================================================ */
function inicializarFavoritos() {
    document.querySelectorAll('.btn-favorito').forEach(btn => {
        btn.replaceWith(btn.cloneNode(true));
    });

    document.querySelectorAll('.btn-favorito').forEach(btn => {
        btn.addEventListener('click', function () {
            this.classList.toggle('activo');
            const icono = this.querySelector('i');
            if (this.classList.contains('activo')) {
                icono.classList.replace('far', 'fas');
                mostrarToast('Guardado en favoritos', 'fa-heart');
            } else {
                icono.classList.replace('fas', 'far');
            }
        });
    });
}

/* ============================================================
   ANIMACIÓN DE ENTRADA
============================================================ */
function inicializarAnimaciones() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, idx) => {
            if (entry.isIntersecting) {
                setTimeout(() => entry.target.classList.add('visible'), idx * 75);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08 });

    document.querySelectorAll('.producto-card').forEach(card => observer.observe(card));
}

/* ============================================================
   INICIALIZACIÓN AL CARGAR LA PÁGINA
============================================================ */
document.addEventListener('DOMContentLoaded', function () {

    // 1) Restaurar carrito guardado
    cargarCarritoLocal();

    // 2) Cargar productos desde la API y renderizarlos
    cargarProductos();

    // 3) Submit del formulario de checkout
    const formCheckout = document.getElementById('formCheckout');
    if (formCheckout) {
        formCheckout.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!validarFormulario()) return;

            const btn = this.querySelector('.btn-confirmar-modal');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

            /*
             * ── INTEGRACIÓN CON BACKEND PHP ──
             * Descomenta y adapta al conectar con tu endpoint real:
             *
             * const payload = {
             *     nombre:    document.getElementById('co-nombre').value.trim(),
             *     apellido:  document.getElementById('co-apellido').value.trim(),
             *     email:     document.getElementById('co-email').value.trim(),
             *     telefono:  document.getElementById('co-telefono').value.trim(),
             *     ciudad:    document.getElementById('co-ciudad').value,
             *     direccion: document.getElementById('co-direccion').value.trim(),
             *     carrito,
             * };
             *
             * fetch('../api/orders.php', {
             *     method: 'POST',
             *     headers: { 'Content-Type': 'application/json' },
             *     body: JSON.stringify(payload),
             * })
             * .then(r => r.json())
             * .then(data => {
             *     if (data.ok) mostrarExitoCheckout(btn);
             *     else mostrarToast('Error al procesar el pedido', 'fa-exclamation-triangle');
             * })
             * .catch(() => mostrarToast('Error de conexión', 'fa-wifi'))
             * .finally(() => { btn.disabled = false; });
             */

            // Demo: simula respuesta del servidor
            setTimeout(() => mostrarExitoCheckout(btn), 1500);
        });
    }

    // Cerrar modal al click en overlay
    document.getElementById('modalCheckoutOverlay')?.addEventListener('click', function (e) {
        if (e.target === this) cerrarCheckout();
    });

    // Tecla ESC para cerrar
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { cerrarCheckout(); cerrarCarrito(); }
    });
});