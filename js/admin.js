'use strict';

/* ============================================================
   CONFIGURACIÓN
   Ajusta la ruta relativa según tu estructura.
   Si admin.php está en /admin/ y la API en /api/:
============================================================ */
const API_PRODUCTS = '../includes/products.php';
const API_AUTH     = '../includes/auth.php';
const TOKEN_KEY    = 'cc_admin_token';

/* ── Estado global ── */
let todosLosProductos = [];   // copia local para filtrado/stats sin re-fetch
let tokenAuth         = '';
let idEliminarPendiente = null;

/* ============================================================
   HELPERS — UTILIDADES
============================================================ */
const $ = id => document.getElementById(id);

function formatCOP(n) {
    return '$' + Math.round(n).toLocaleString('es-CO');
}

function sanitizar(str) {
    if (typeof str !== 'string') return '';
    const m = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#x27;' };
    return str.replace(/[&<>"']/g, c => m[c]).trim();
}

function toast(msg, tipo = 'default') {
    const cont  = $('admin-toasts');
    const el    = document.createElement('div');
    el.className = `admin-toast ${tipo}`;
    const icon  = tipo === 'success' ? 'fa-check-circle' : tipo === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
    el.innerHTML = `<i class="fas ${icon}"></i> ${sanitizar(msg)}`;
    cont.appendChild(el);
    setTimeout(() => {
        el.classList.add('out');
        setTimeout(() => el.remove(), 300);
    }, 3000);
}

/* ============================================================
   SESIÓN — TOKEN
============================================================ */
function guardarToken(token) {
    tokenAuth = token;
    try { sessionStorage.setItem(TOKEN_KEY, token); } catch (_) {}
}

function recuperarToken() {
    try { return sessionStorage.getItem(TOKEN_KEY) || ''; } catch (_) { return ''; }
}

function borrarToken() {
    tokenAuth = '';
    try { sessionStorage.removeItem(TOKEN_KEY); } catch (_) {}
}

function authHeader() {
    return { 'Authorization': 'Bearer ' + tokenAuth, 'Content-Type': 'application/json' };
}

/* ============================================================
   AUTENTICACIÓN — LOGIN
============================================================ */
$('login-form').addEventListener('submit', async e => {
    e.preventDefault();
    const btn  = $('btn-login');
    const err  = $('l-error');
    const user = $('l-usuario').value.trim();
    const pass = $('l-password').value;

    err.classList.remove('visible');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ingresando…';

    try {
        const res  = await fetch(API_AUTH, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ usuario: user, password: pass }),
        });
        const data = await res.json();

        if (data.success) {
            guardarToken(data.token);
            mostrarApp(data.usuario);
        } else {
            err.classList.add('visible');
        }
    } catch (_) {
        err.textContent = 'Error de conexión. Verifica el servidor.';
        err.classList.add('visible');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Ingresar';
    }
});

function mostrarApp(usuario = 'admin') {
    $('login-screen').style.display = 'none';
    $('app').classList.add('visible');
    $('topbar-user-name').textContent = usuario;
    cargarProductos();
}

$('btn-logout').addEventListener('click', () => {
    borrarToken();
    $('login-screen').style.display = '';
    $('app').classList.remove('visible');
    $('l-password').value = '';
    $('l-error').classList.remove('visible');
});

/* ============================================================
   PRODUCTOS — CARGAR Y RENDERIZAR TABLA
============================================================ */
async function cargarProductos() {
    $('tbody-productos').innerHTML =
        '<tr><td colspan="8" class="tabla-vacía"><i class="fas fa-spinner fa-spin" style="font-size:1.4rem;opacity:.4;"></i></td></tr>';

    try {
        const res  = await fetch(API_PRODUCTS + '?all=1', {
            headers: authHeader()
        });
        const data = await res.json();

        if (!data.success) throw new Error(data.error);

        todosLosProductos = data.productos || [];
        renderizarTabla(todosLosProductos);
        actualizarStats(todosLosProductos);

    } catch (err) {
        $('tbody-productos').innerHTML =
            '<tr><td colspan="8" class="tabla-vacía" style="color:#DC2626;">' +
            '<i class="fas fa-exclamation-circle"></i> ' + sanitizar(err.message) + '</td></tr>';
        toast('No se pudieron cargar los productos.', 'error');
    }
}

function renderizarTabla(productos) {
    const categorias = {
        'tueste-claro': 'Tueste Claro', 'tueste-medio': 'Tueste Medio',
        'tueste-oscuro': 'Tueste Oscuro', 'capsulas': 'Cápsulas',
        'origen-especial': 'Origen Especial',
    };

    if (!productos.length) {
        $('tbody-productos').innerHTML =
            '<tr><td colspan="8" class="tabla-vacía">No se encontraron productos.</td></tr>';
        $('tabla-info').textContent = '0 productos';
        return;
    }

    $('tbody-productos').innerHTML = productos.map(p => {
        const badgePill = p.badge
            ? `<span class="badge-pill ${p.badge_tipo?.replace('badge-', '') || 'none'}">${sanitizar(p.badge)}</span>`
            : `<span class="badge-pill none">—</span>`;

        const estado = p.activo
            ? `<span class="chip-activo"><i class="fas fa-circle" style="font-size:7px;"></i> Activo</span>`
            : `<span class="chip-inactivo"><i class="fas fa-circle" style="font-size:7px;"></i> Inactivo</span>`;

        const precioStr = p.precio_antes
            ? `<span style="text-decoration:line-through;color:#9E9E9E;font-size:11px;">${formatCOP(p.precio_antes)}</span><br>${formatCOP(p.precio)}`
            : formatCOP(p.precio);

        return `<tr>
            <td style="color:#9E9E9E;font-size:12px;">${p.id}</td>
            <td><div class="td-icon"><i class="fas ${sanitizar(p.icono)}"></i></div></td>
            <td>
                <div class="td-nombre">${sanitizar(p.nombre)}</div>
                <div class="td-cat">${sanitizar(p.unidad)}</div>
            </td>
            <td>${sanitizar(categorias[p.categoria] || p.categoria)}</td>
            <td>${precioStr}</td>
            <td>${badgePill}</td>
            <td>${estado}</td>
            <td>
                <div class="acciones">
                    <button class="btn-edit" onclick="abrirEditar(${p.id})" title="Editar">
                        <i class="fas fa-pencil"></i>
                    </button>
                    <button class="btn-del" onclick="confirmarEliminar(${p.id}, '${sanitizar(p.nombre)}')" title="Eliminar">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');

    $('tabla-info').textContent = `${productos.length} producto${productos.length !== 1 ? 's' : ''}`;
}

function actualizarStats(productos) {
    const total   = productos.length;
    const activos = productos.filter(p => p.activo).length;
    const oferta  = productos.filter(p => p.precio_antes).length;
    const rating  = total
        ? (productos.reduce((s, p) => s + p.rating_valor, 0) / total).toFixed(1)
        : '—';

    $('stat-total').textContent   = total;
    $('stat-activos').textContent = activos;
    $('stat-oferta').textContent  = oferta;
    $('stat-rating').textContent  = total ? '★ ' + rating : '—';
}

/* ============================================================
   BÚSQUEDA EN TIEMPO REAL
============================================================ */
$('input-buscar').addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    const filtrados = q
        ? todosLosProductos.filter(p =>
            p.nombre.toLowerCase().includes(q) ||
            p.categoria.toLowerCase().includes(q) ||
            (p.descripcion || '').toLowerCase().includes(q))
        : todosLosProductos;
    renderizarTabla(filtrados);
});

/* ============================================================
   MODAL PRODUCTO — ABRIR / CERRAR
============================================================ */
function abrirModal(titulo) {
    $('modal-title-text').textContent = titulo;
    $('modal-producto').classList.add('visible');
    document.body.style.overflow = 'hidden';
    limpiarErrores();
}

function cerrarModal() {
    $('modal-producto').classList.remove('visible');
    document.body.style.overflow = '';
    $('form-producto').reset();
    $('f-id').value = '';
    actualizarIconoPreview('fa-mug-hot');
}

$('btn-cancel-modal').addEventListener('click', cerrarModal);
$('modal-producto').addEventListener('click', e => { if (e.target === $('modal-producto')) cerrarModal(); });

/* ── Nuevo ── */
$('btn-nuevo').addEventListener('click', () => {
    $('f-id').value = '';
    $('modal-icon-title').className = 'fas fa-plus';
    $('btn-save-text').textContent  = 'Guardar producto';
    $('f-activo').value = '1';
    $('f-rating').value = '5.0';
    $('f-rating-cant').value = '0';
    $('f-icono').value = 'fa-mug-hot';
    actualizarIconoPreview('fa-mug-hot');
    abrirModal('Nuevo producto');
    setTimeout(() => $('f-nombre').focus(), 100);
});

/* ── Editar ── */
async function abrirEditar(id) {
    try {
        const res  = await fetch(`${API_PRODUCTS}?id=${id}&all=1`, { headers: authHeader() });
        const data = await res.json();
        if (!data.success) throw new Error(data.error);

        const p = data.producto;
        $('f-id').value          = p.id;
        $('f-nombre').value      = p.nombre;
        $('f-precio').value      = p.precio;
        $('f-precio-antes').value= p.precio_antes ?? '';
        $('f-unidad').value      = p.unidad;
        $('f-icono').value       = p.icono;
        $('f-categoria').value   = p.categoria;
        $('f-badge').value       = p.badge ?? '';
        $('f-badge-tipo').value  = p.badge_tipo ?? '';
        $('f-rating').value      = p.rating_valor;
        $('f-rating-cant').value = p.rating_cantidad;
        $('f-activo').value      = p.activo ? '1' : '0';
        $('f-desc').value        = p.descripcion ?? '';

        actualizarIconoPreview(p.icono);

        $('modal-icon-title').className = 'fas fa-pencil';
        $('btn-save-text').textContent  = 'Actualizar producto';
        abrirModal('Editar producto — ' + p.nombre);

    } catch (err) {
        toast('No se pudo cargar el producto: ' + err.message, 'error');
    }
}

/* ── Preview del ícono mientras se escribe ── */
$('f-icono').addEventListener('input', function () {
    actualizarIconoPreview(this.value.trim());
});

function actualizarIconoPreview(clase) {
    const i = $('icono-preview-i');
    // Remover todas las clases fa-* previas y poner la nueva
    i.className = `fas ${clase || 'fa-mug-hot'}`;
}

/* ============================================================
   MODAL PRODUCTO — SUBMIT (crear o editar)
============================================================ */
$('form-producto').addEventListener('submit', async e => {
    e.preventDefault();

    if (!validarFormModal()) return;

    const id = $('f-id').value ? parseInt($('f-id').value, 10) : null;
    const btn = $('btn-save');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando…';

    const payload = {
        nombre:          $('f-nombre').value.trim(),
        precio:          parseFloat($('f-precio').value),
        precio_antes:    $('f-precio-antes').value !== '' ? parseFloat($('f-precio-antes').value) : null,
        unidad:          $('f-unidad').value.trim(),
        icono:           $('f-icono').value.trim() || 'fa-mug-hot',
        categoria:       $('f-categoria').value,
        badge:           $('f-badge').value.trim() || null,
        badge_tipo:      $('f-badge-tipo').value || null,
        descripcion:     $('f-desc').value.trim(),
        rating_valor:    parseFloat($('f-rating').value)      || 5.0,
        rating_cantidad: parseInt($('f-rating-cant').value, 10) || 0,
        activo:          parseInt($('f-activo').value, 10),
    };

    try {
        const url    = id ? `${API_PRODUCTS}?id=${id}` : API_PRODUCTS;
        const method = id ? 'PUT' : 'POST';

        const res  = await fetch(url, { method, headers: authHeader(), body: JSON.stringify(payload) });
        const data = await res.json();

        if (!data.success) throw new Error(data.error);

        toast(id ? 'Producto actualizado correctamente.' : 'Producto creado correctamente.', 'success');
        cerrarModal();
        await cargarProductos();

    } catch (err) {
        toast('Error: ' + err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> <span id="btn-save-text">' +
            ($('f-id').value ? 'Actualizar producto' : 'Guardar producto') + '</span>';
    }
});

/* ============================================================
   VALIDACIÓN DEL FORMULARIO MODAL
============================================================ */
function limpiarErrores() {
    document.querySelectorAll('.f-error').forEach(el => el.classList.remove('visible'));
    document.querySelectorAll('.f-group input, .f-group select').forEach(el => el.classList.remove('error'));
}

function marcarError(id, errId, msg = '') {
    const el  = $(id);
    const err = $(errId);
    if (el)  el.classList.add('error');
    if (err) { if (msg) err.textContent = msg; err.classList.add('visible'); }
}

function validarFormModal() {
    limpiarErrores();
    let ok = true;

    if (!$('f-nombre').value.trim())    { marcarError('f-nombre',    'fe-nombre');    ok = false; }
    if (!$('f-precio').value || isNaN(parseFloat($('f-precio').value))) {
        marcarError('f-precio', 'fe-precio'); ok = false;
    }
    if (!$('f-unidad').value.trim())    { marcarError('f-unidad',    'fe-unidad');    ok = false; }
    if (!$('f-categoria').value)        { marcarError('f-categoria', 'fe-categoria'); ok = false; }

    return ok;
}

/* ============================================================
   ELIMINAR PRODUCTO — CONFIRMACIÓN
============================================================ */
function confirmarEliminar(id, nombre) {
    idEliminarPendiente = id;
    $('confirm-msg').textContent =
        `¿Estás seguro de que deseas eliminar "${nombre}"? Esta acción no se puede deshacer.`;
    $('modal-confirmar').classList.add('visible');
    document.body.style.overflow = 'hidden';
}

$('btn-cancel-del').addEventListener('click', () => {
    idEliminarPendiente = null;
    $('modal-confirmar').classList.remove('visible');
    document.body.style.overflow = '';
});

$('modal-confirmar').addEventListener('click', e => {
    if (e.target === $('modal-confirmar')) {
        idEliminarPendiente = null;
        $('modal-confirmar').classList.remove('visible');
        document.body.style.overflow = '';
    }
});

$('btn-confirm-del').addEventListener('click', async () => {
    if (!idEliminarPendiente) return;

    const btn = $('btn-confirm-del');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando…';

    try {
        const res  = await fetch(`${API_PRODUCTS}?id=${idEliminarPendiente}`, {
            method: 'DELETE',
            headers: authHeader(),
        });
        const data = await res.json();

        if (!data.success) throw new Error(data.error);

        toast('Producto eliminado.', 'success');
        $('modal-confirmar').classList.remove('visible');
        document.body.style.overflow = '';
        idEliminarPendiente = null;
        await cargarProductos();

    } catch (err) {
        toast('Error al eliminar: ' + err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash-alt"></i> Sí, eliminar';
    }
});

/* ============================================================
   ESC — Cerrar modales
============================================================ */
document.addEventListener('keydown', e => {
    if (e.key !== 'Escape') return;
    cerrarModal();
    $('modal-confirmar').classList.remove('visible');
    document.body.style.overflow = '';
});

/* ============================================================
   INIT — Verificar sesión guardada al cargar
============================================================ */
(function init() {
    const saved = recuperarToken();
    if (saved) {
        tokenAuth = saved;
        mostrarApp();
    }
})();