<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Admin — CaféCol</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --cafe-900: #1a0e08;
    --cafe-800: #2d1a0e;
    --cafe-700: #3d2314;
    --cafe-600: #5c3420;
    --cafe-500: #7a4a2e;
    --cafe-400: #a0623c;
    --cafe-300: #c4895f;
    --cafe-200: #ddb896;
    --cafe-100: #f0dece;
    --cafe-50:  #faf4ee;
    --text-dark:#1a0e08;
    --text-mid: #5c3420;
    --text-soft:#9a7460;
    --danger:   #c0392b;
    --success:  #27704a;
    --radius:   12px;
}

body {
    font-family: 'DM Sans', sans-serif;
    background: var(--cafe-50);
    color: var(--text-dark);
    min-height: 100vh;
}

/* ── SIDEBAR ── */
.sidebar {
    position: fixed; left: 0; top: 0;
    width: 240px; height: 100vh;
    background: var(--cafe-900);
    display: flex; flex-direction: column;
    z-index: 100;
}

.sidebar-logo {
    padding: 28px 24px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

.sidebar-logo h1 {
    font-family: 'Playfair Display', serif;
    font-size: 22px; color: var(--cafe-200); letter-spacing: 0.5px;
}

.sidebar-logo span {
    font-size: 11px; color: var(--cafe-500);
    letter-spacing: 2px; text-transform: uppercase; display: block; margin-top: 2px;
}

.sidebar-nav { padding: 20px 12px; flex: 1; }

.nav-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border-radius: 8px;
    color: var(--cafe-300); font-size: 14px;
    cursor: pointer; transition: all 0.2s; margin-bottom: 2px; text-decoration: none;
}

.nav-item:hover, .nav-item.active {
    background: rgba(255,255,255,0.07); color: var(--cafe-100);
}

.nav-item svg { opacity: 0.7; flex-shrink: 0; }
.nav-item.active svg { opacity: 1; }

.sidebar-footer { padding: 16px 12px; border-top: 1px solid rgba(255,255,255,0.08); }

.btn-logout {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border-radius: 8px;
    color: var(--cafe-400); font-size: 13px; cursor: pointer;
    transition: all 0.2s; background: none; border: none; width: 100%;
}

.btn-logout:hover { background: rgba(192,57,43,0.15); color: #e07060; }

/* ── MAIN ── */
.main { margin-left: 240px; min-height: 100vh; }

.topbar {
    background: white; border-bottom: 1px solid rgba(92,52,32,0.1);
    padding: 0 32px; height: 64px;
    display: flex; align-items: center; justify-content: space-between;
}

.topbar h2 {
    font-family: 'Playfair Display', serif;
    font-size: 20px; font-weight: 500; color: var(--cafe-800);
}

.topbar-user { display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--text-soft); }

.avatar {
    width: 34px; height: 34px; border-radius: 50%;
    background: var(--cafe-700); display: flex; align-items: center;
    justify-content: center; color: var(--cafe-200); font-size: 13px; font-weight: 500;
}

.content { padding: 32px; }

/* ── STATS ── */
.stats-grid {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 16px; margin-bottom: 32px;
}

.stat-card {
    background: white; border-radius: var(--radius);
    padding: 20px 24px; border: 1px solid rgba(92,52,32,0.08);
}

.stat-label { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-soft); margin-bottom: 8px; }
.stat-value { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--cafe-800); }
.stat-sub   { font-size: 12px; color: var(--text-soft); margin-top: 4px; }

/* ── LAYOUT ── */
.layout-grid {
    display: grid; grid-template-columns: 1fr 400px;
    gap: 24px; align-items: start;
}

/* ── CARD ── */
.card {
    background: white; border-radius: var(--radius);
    border: 1px solid rgba(92,52,32,0.08); overflow: hidden;
}

.card-header {
    padding: 20px 24px; border-bottom: 1px solid rgba(92,52,32,0.08);
    display: flex; align-items: center; justify-content: space-between;
}

.card-title {
    font-family: 'Playfair Display', serif; font-size: 16px;
    font-weight: 500; color: var(--cafe-800);
}

.search-box { position: relative; }

.search-box input {
    padding: 7px 12px 7px 32px; border: 1px solid rgba(92,52,32,0.15);
    border-radius: 8px; font-size: 13px; font-family: 'DM Sans', sans-serif;
    color: var(--text-dark); background: var(--cafe-50); outline: none;
    width: 200px; transition: border 0.2s;
}

.search-box input:focus { border-color: var(--cafe-400); }

.search-icon {
    position: absolute; left: 10px; top: 50%;
    transform: translateY(-50%); color: var(--text-soft);
}

table { width: 100%; border-collapse: collapse; }

thead tr { background: var(--cafe-50); border-bottom: 1px solid rgba(92,52,32,0.08); }

th {
    padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 500;
    text-transform: uppercase; letter-spacing: 1px; color: var(--text-soft);
}

td {
    padding: 14px 16px; font-size: 13px;
    border-bottom: 1px solid rgba(92,52,32,0.05); vertical-align: middle;
}

tr:last-child td { border-bottom: none; }
tr:hover td { background: var(--cafe-50); }

/* Miniatura */
.prod-thumb {
    width: 44px; height: 44px; border-radius: 8px;
    background: var(--cafe-100); display: flex; align-items: center;
    justify-content: center; color: var(--cafe-400); font-size: 20px;
    flex-shrink: 0; overflow: hidden;
}

.prod-thumb img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; }
.prod-info { display: flex; align-items: center; gap: 12px; }
.prod-name { font-weight: 500; color: var(--cafe-800); font-size: 14px; }
.prod-cat  { font-size: 11px; color: var(--text-soft); margin-top: 2px; }

.badge { display: inline-block; padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 500; }
.badge-nuevo    { background: #e8f5e9; color: #2e7d32; }
.badge-oferta   { background: #fff3e0; color: #e65100; }
.badge-popular  { background: #fce4ec; color: #c62828; }
.badge-especial { background: #ede7f6; color: #4527a0; }
.badge-default  { background: var(--cafe-100); color: var(--cafe-600); }

.precio { font-weight: 500; color: var(--cafe-700); }
.precio-antes { font-size: 11px; color: var(--text-soft); text-decoration: line-through; }

.btn-delete {
    background: none; border: 1px solid rgba(192,57,43,0.2);
    color: var(--danger); padding: 5px 10px; border-radius: 6px;
    font-size: 12px; cursor: pointer; transition: all 0.2s; font-family: 'DM Sans', sans-serif;
}
.btn-delete:hover { background: var(--danger); color: white; }

/* ── FORMULARIO ── */
.form-card { position: sticky; top: 24px; }
.form-body { padding: 20px 24px; }
.form-group { margin-bottom: 14px; }

label {
    display: block; font-size: 11px; font-weight: 500; color: var(--text-mid);
    margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px;
}

.opt-tag {
    font-size: 10px; color: var(--text-soft);
    font-style: italic; margin-left: 4px; text-transform: none; letter-spacing: 0;
}

input[type="text"],
input[type="number"],
select,
textarea {
    width: 100%; padding: 9px 12px; border: 1px solid rgba(92,52,32,0.15);
    border-radius: 8px; font-size: 14px; font-family: 'DM Sans', sans-serif;
    color: var(--text-dark); background: var(--cafe-50); outline: none; transition: border 0.2s, background 0.2s;
}

input:focus, select:focus, textarea:focus { border-color: var(--cafe-400); background: white; }
textarea { resize: vertical; min-height: 68px; }

.row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.row-2 .form-group { margin-bottom: 0; }

/* ── SECCIÓN IMAGEN ── */
.upload-area {
    border: 2px dashed rgba(92,52,32,0.2); border-radius: 10px;
    cursor: pointer; transition: all 0.2s; background: var(--cafe-50);
    position: relative; overflow: hidden;
    min-height: 130px; display: flex; align-items: center; justify-content: center;
}

.upload-area:hover { border-color: var(--cafe-400); background: var(--cafe-100); }

.upload-area input[type="file"] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer;
    width: 100%; height: 100%; z-index: 2;
}

.upload-placeholder { text-align: center; padding: 20px; pointer-events: none; }
.upload-icon { font-size: 30px; margin-bottom: 8px; }
.upload-text { font-size: 13px; color: var(--text-soft); line-height: 1.5; }
.upload-text small { font-size: 11px; opacity: 0.7; }

.upload-preview-img { width: 100%; height: 160px; object-fit: cover; display: none; border-radius: 8px; }

.upload-area.has-image {
    border-style: solid; border-color: var(--cafe-400);
    min-height: 160px; padding: 0; align-items: stretch;
}

.upload-area.has-image .upload-placeholder { display: none; }
.upload-area.has-image .upload-preview-img { display: block; }

.btn-quitar-img {
    position: absolute; top: 8px; right: 8px;
    background: rgba(26,14,8,0.75); color: white; border: none;
    border-radius: 6px; padding: 4px 9px; font-size: 11px; cursor: pointer;
    z-index: 3; display: none; font-family: 'DM Sans', sans-serif; transition: background 0.2s;
}
.btn-quitar-img:hover { background: var(--danger); }
.upload-area.has-image .btn-quitar-img { display: block; }

/* ── SECCIÓN TITLE ── */
.form-section-title {
    font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px;
    color: var(--text-soft); padding: 10px 0 10px;
    border-top: 1px solid rgba(92,52,32,0.08); margin-top: 4px;
}

/* ── BADGE PREVIEW ── */
.badge-preview-wrap {
    display: flex; align-items: center; gap: 8px; margin-top: 6px; min-height: 22px;
}
.badge-preview-label { font-size: 11px; color: var(--text-soft); }

/* ── SUBMIT ── */
.btn-submit {
    width: 100%; padding: 12px; background: var(--cafe-700);
    color: var(--cafe-100); border: none; border-radius: 10px;
    font-size: 14px; font-weight: 500; font-family: 'DM Sans', sans-serif;
    cursor: pointer; transition: all 0.2s; margin-top: 8px;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.btn-submit:hover { background: var(--cafe-600); }
.btn-submit:active { transform: scale(0.98); }
.btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }

/* ── TOAST ── */
.toast {
    position: fixed; bottom: 24px; right: 24px;
    padding: 12px 20px; border-radius: 10px; font-size: 14px; font-weight: 500;
    color: white; opacity: 0; transform: translateY(10px);
    transition: all 0.3s; z-index: 999; pointer-events: none;
}
.toast.show  { opacity: 1; transform: translateY(0); }
.toast.success { background: var(--success); }
.toast.error   { background: var(--danger); }

/* ── EMPTY / LOADING ── */
.empty-state { padding: 48px; text-align: center; color: var(--text-soft); }
.empty-state svg { opacity: 0.3; margin-bottom: 12px; }
.empty-state p { font-size: 14px; }
.loading-row td { padding: 32px; text-align: center; color: var(--text-soft); font-size: 13px; }
</style>
</head>
<body>

<!-- ═══════════════════ SIDEBAR ═══════════════════ -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <h1>CAFE TANTICO</h1>
        <span>Panel Admin</span>
    </div>
    <nav class="sidebar-nav">
        <a class="nav-item active" href="#">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Productos
        </a>
        <a class="nav-item" href="#">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Pedidos
        </a>
    </nav>
    <div class="sidebar-footer">
        <button class="btn-logout" onclick="cerrarSesion()">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Cerrar sesión
        </button>
    </div>
</aside>

<!-- ═══════════════════ MAIN ═══════════════════ -->
<main class="main">
    <div class="topbar">
        <h2>Gestión de Productos</h2>
        <div class="topbar-user">
            <span id="usuario-label">Admin</span>
            <div class="avatar">A</div>
        </div>
    </div>

    <div class="content">

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total productos</div>
                <div class="stat-value" id="stat-total">—</div>
                <div class="stat-sub">en catálogo</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Categorías</div>
                <div class="stat-value" id="stat-cats">—</div>
                <div class="stat-sub">distintas</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">En oferta</div>
                <div class="stat-value" id="stat-oferta">—</div>
                <div class="stat-sub">con precio anterior</div>
            </div>
        </div>

        <div class="layout-grid">

            <!-- ─────── TABLA ─────── -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Productos</span>
                    <div class="search-box">
                        <svg class="search-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                        </svg>
                        <input type="text" id="buscador" placeholder="Buscar producto…" oninput="filtrar()">
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Badge</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tabla">
                        <tr class="loading-row"><td colspan="4">Cargando productos…</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- ─────── FORMULARIO ─────── -->
            <div class="card form-card">
                <div class="card-header">
                    <span class="card-title">Agregar producto</span>
                </div>
                <div class="form-body">

                    <!-- IMAGEN -->
                    <div class="form-group">
                        <label>Foto del producto <span class="opt-tag">opcional</span></label>
                        <div class="upload-area" id="uploadArea">
                            <input type="file" id="imagenInput" accept="image/jpeg,image/png,image/webp"
                                   onchange="previewImagen(event)">
                            <button type="button" class="btn-quitar-img" onclick="quitarImagen(event)">✕ Quitar</button>
                            <img class="upload-preview-img" id="preview" alt="Vista previa">
                            <div class="upload-placeholder" id="uploadPlaceholder">
                                <div class="upload-icon">📷</div>
                                <div class="upload-text">
                                    Clic para subir imagen<br>
                                    <small>JPG, PNG o WebP · Máx. 5 MB</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- INFO BÁSICA -->
                    <div class="form-section-title">Información básica</div>

                    <div class="form-group">
                        <label>Nombre *</label>
                        <input type="text" id="f-nombre" placeholder="Ej: Café Tostado Especial" maxlength="100">
                    </div>

                    <div class="form-group">
                        <label>Descripción <span class="opt-tag">opcional</span></label>
                        <textarea id="f-descripcion" placeholder="Describe el producto brevemente…"></textarea>
                    </div>

                    <div class="row-2">
                        <div class="form-group">
                            <label>Categoría *</label>
                            <select id="f-categoria">
                                <option value="">Seleccionar…</option>
                                <option value="tueste-claro">Tueste Claro</option>
                                <option value="tueste-medio">Tueste Medio</option>
                                <option value="tueste-oscuro">Tueste Oscuro</option>
                                <option value="capsulas">Cápsulas</option>
                                <option value="origen-especial">Origen Especial</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Unidad <span class="opt-tag">opcional</span></label>
                            <input type="text" id="f-unidad" placeholder="Ej: 250 g · 1 kg">
                        </div>
                    </div>

                    <!-- PRECIOS -->
                    <div class="form-section-title">Precios</div>

                    <div class="row-2">
                        <div class="form-group">
                            <label>Precio actual *</label>
                            <input type="number" id="f-precio" placeholder="0" step="1" min="0">
                        </div>
                        <div class="form-group">
                            <label>Precio antes <span class="opt-tag">tachado</span></label>
                            <input type="number" id="f-precio-antes" placeholder="0" step="1" min="0"
                                   title="Precio anterior tachado — solo para ofertas">
                        </div>
                    </div>

                    <!-- BADGE -->
                    <div class="form-section-title">Etiqueta (badge) <span class="opt-tag">opcional</span></div>

                    <div class="row-2">
                        <div class="form-group">
                            <label>Texto</label>
                            <input type="text" id="f-badge" placeholder="Ej: Nuevo · -20%"
                                   maxlength="30" oninput="actualizarBadgePreview()">
                        </div>
                        <div class="form-group">
                            <label>Tipo</label>
                            <select id="f-badge-tipo" onchange="actualizarBadgePreview()">
                                <option value="">Sin estilo</option>
                                <option value="nuevo">🟢 Nuevo</option>
                                <option value="oferta">🟠 Oferta</option>
                                <option value="popular">🔴 Popular</option>
                                <option value="especial">🟣 Especial</option>
                            </select>
                        </div>
                    </div>

                    <div class="badge-preview-wrap" id="badgePreviewWrap" style="display:none">
                        <span class="badge-preview-label">Vista previa:</span>
                        <span class="badge" id="badgePreview"></span>
                    </div>

                    <button class="btn-submit" id="btnAgregar" onclick="agregar()">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        Agregar producto
                    </button>

                </div>
            </div>

        </div><!-- /layout-grid -->
    </div><!-- /content -->
</main>

<div class="toast" id="toast"></div>

<script>
const API   = '../api/products.php';
const token = localStorage.getItem('token');

if (!token) location.href = 'login.php';

// Mostrar usuario
try {
    const payload = JSON.parse(atob(token.split('.')[1]));
    const u = payload.sub || 'Admin';
    document.getElementById('usuario-label').textContent = u;
    document.querySelector('.avatar').textContent = u[0].toUpperCase();
} catch (e) {}

/* ── Mapa de categorías (igual que service.js) ── */
const CATEGORIAS = {
    'tueste-claro':    'Tueste Claro',
    'tueste-medio':    'Tueste Medio',
    'tueste-oscuro':   'Tueste Oscuro',
    'capsulas':        'Cápsulas',
    'origen-especial': 'Origen Especial',
};

let todosLosProductos = [];

/* ── Toast ── */
function mostrarToast(msg, tipo = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast show ' + tipo;
    setTimeout(() => t.className = 'toast', 3000);
}

/* ── Badge helpers ── */
const BADGE_CLASS = { nuevo:'badge-nuevo', oferta:'badge-oferta', popular:'badge-popular', especial:'badge-especial' };

function getBadgeClass(tipo) { return BADGE_CLASS[tipo] || 'badge-default'; }

function actualizarBadgePreview() {
    const texto = document.getElementById('f-badge').value.trim();
    const tipo  = document.getElementById('f-badge-tipo').value;
    const wrap  = document.getElementById('badgePreviewWrap');
    const el    = document.getElementById('badgePreview');

    if (!texto) { wrap.style.display = 'none'; return; }
    wrap.style.display = 'flex';
    el.textContent = texto;
    el.className   = 'badge ' + (tipo ? getBadgeClass(tipo) : 'badge-default');
}

/* ── Formato precio ── */
function formatPrecio(n) { return Math.round(n).toLocaleString('es-CO'); }

/* ── Render tabla ── */
function renderTabla(productos) {
    const tbody = document.getElementById('tabla');
    if (!productos.length) {
        tbody.innerHTML = `<tr><td colspan="4"><div class="empty-state">
            <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM16 3H8L6 7h12l-2-4z"/>
            </svg><p>No hay productos aún</p></div></td></tr>`;
        return;
    }

    tbody.innerHTML = productos.map(p => {
        // Imagen real si existe, emoji si no
        const thumb = p.imagen
            ? `<div class="prod-thumb"><img src="../${p.imagen}" alt="${p.nombre}"></div>`
            : `<div class="prod-thumb">☕</div>`;

        const catNombre   = CATEGORIAS[p.categoria] || p.categoria || '—';
        const precioAntes = p.precio_antes ? `<div class="precio-antes">$${formatPrecio(p.precio_antes)}</div>` : '';
        const badgeCell   = p.badge ? `<span class="badge ${getBadgeClass(p.badge_tipo)}">${p.badge}</span>` : '—';

        return `<tr>
            <td>
                <div class="prod-info">
                    ${thumb}
                    <div>
                        <div class="prod-name">${p.nombre}</div>
                        <div class="prod-cat">${catNombre}${p.unidad ? ' · ' + p.unidad : ''}</div>
                    </div>
                </div>
            </td>
            <td>
                <div class="precio">$${formatPrecio(p.precio)}</div>
                ${precioAntes}
            </td>
            <td>${badgeCell}</td>
            <td><button class="btn-delete" onclick="eliminar(${p.id})">Eliminar</button></td>
        </tr>`;
    }).join('');
}

/* ── Stats ── */
function actualizarStats(productos) {
    document.getElementById('stat-total').textContent  = productos.length;
    document.getElementById('stat-cats').textContent   = new Set(productos.map(p => p.categoria).filter(Boolean)).size;
    document.getElementById('stat-oferta').textContent = productos.filter(p => p.precio_antes).length;
}

/* ── Filtrar ── */
function filtrar() {
    const q = document.getElementById('buscador').value.toLowerCase();
    renderTabla(todosLosProductos.filter(p =>
        p.nombre.toLowerCase().includes(q) ||
        (CATEGORIAS[p.categoria] || p.categoria || '').toLowerCase().includes(q)
    ));
}

/* ── Cargar productos ── */
function cargar() {
    fetch(API)
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                todosLosProductos = d.productos;
                renderTabla(todosLosProductos);
                actualizarStats(todosLosProductos);
            }
        })
        .catch(() => mostrarToast('Error al cargar productos', 'error'));
}

/* ── Preview imagen ── */
function previewImagen(e) {
    const file = e.target.files[0];
    if (!file) return;

    if (file.size > 5 * 1024 * 1024) {
        mostrarToast('La imagen no debe superar 5 MB', 'error');
        e.target.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = ev => {
        document.getElementById('preview').src = ev.target.result;
        document.getElementById('uploadArea').classList.add('has-image');
    };
    reader.readAsDataURL(file);
}

/* ── Quitar imagen ── */
function quitarImagen(e) {
    e.stopPropagation();
    e.preventDefault();
    document.getElementById('imagenInput').value = '';
    document.getElementById('preview').src = '';
    document.getElementById('uploadArea').classList.remove('has-image');
}

/* ── Agregar producto ── */
function agregar() {
    const nombre    = document.getElementById('f-nombre').value.trim();
    const precio    = document.getElementById('f-precio').value;
    const categoria = document.getElementById('f-categoria').value;

    if (!nombre || !precio || !categoria) {
        mostrarToast('Nombre, precio y categoría son obligatorios', 'error');
        return;
    }

    const btn = document.getElementById('btnAgregar');
    btn.disabled  = true;
    btn.innerHTML = '⏳ Guardando…';

    const fd = new FormData();
    fd.append('nombre',       nombre);
    fd.append('descripcion',  document.getElementById('f-descripcion').value.trim());
    fd.append('precio',       precio);
    fd.append('precio_antes', document.getElementById('f-precio-antes').value || '');
    fd.append('categoria',    categoria);
    fd.append('unidad',       document.getElementById('f-unidad').value.trim());
    fd.append('badge',        document.getElementById('f-badge').value.trim());
    fd.append('badge_tipo',   document.getElementById('f-badge-tipo').value);

    const imgFile = document.getElementById('imagenInput').files[0];
    if (imgFile) fd.append('imagen', imgFile);

    fetch(API, { method:'POST', headers:{ 'Authorization':'Bearer '+token }, body:fd })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                mostrarToast('Producto agregado correctamente ✓');
                limpiarFormulario();
                cargar();
            } else {
                mostrarToast(d.error || 'Error al guardar', 'error');
            }
        })
        .catch(() => mostrarToast('Error de conexión', 'error'))
        .finally(() => {
            btn.disabled  = false;
            btn.innerHTML = `<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2"
                viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Agregar producto`;
        });
}

/* ── Eliminar ── */
function eliminar(id) {
    if (!confirm('¿Eliminar este producto?')) return;
    fetch(API, {
        method:  'DELETE',
        headers: { 'Authorization':'Bearer '+token, 'Content-Type':'application/json' },
        body:    JSON.stringify({ id })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) { mostrarToast('Producto eliminado'); cargar(); }
        else mostrarToast(d.error || 'Error al eliminar', 'error');
    });
}

/* ── Limpiar formulario ── */
function limpiarFormulario() {
    ['f-nombre','f-descripcion','f-precio','f-precio-antes','f-unidad','f-badge']
        .forEach(id => { document.getElementById(id).value = ''; });
    document.getElementById('f-categoria').value  = '';
    document.getElementById('f-badge-tipo').value = '';
    document.getElementById('imagenInput').value  = '';
    document.getElementById('preview').src        = '';
    document.getElementById('uploadArea').classList.remove('has-image');
    document.getElementById('badgePreviewWrap').style.display = 'none';
}

/* ── Cerrar sesión ── */
function cerrarSesion() {
    localStorage.removeItem('token');
    location.href = 'login.php';
}

cargar();
</script>
</body>
</html>