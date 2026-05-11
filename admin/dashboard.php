<?php
// Verificar sesión admin
session_start();
// Si usas JWT token (como en el original), la verificación la hace el JS
// Aquí solo servimos el HTML
?>
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

body { font-family: 'DM Sans', sans-serif; background: var(--cafe-50); color: var(--text-dark); min-height: 100vh; }

/* ── SIDEBAR ── */
.sidebar {
    position: fixed; left: 0; top: 0;
    width: 240px; height: 100vh;
    background: var(--cafe-900);
    display: flex; flex-direction: column;
    z-index: 100;
}

.sidebar-logo {
    padding: 20px 24px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    display: flex; flex-direction: column; align-items: flex-start; gap: 6px;
}

.sidebar-logo img {
    height: 40px; width: auto; object-fit: contain;
}

.sidebar-logo span {
    font-size: 11px; color: var(--cafe-500);
    letter-spacing: 2px; text-transform: uppercase; display: block;
}

.sidebar-nav { padding: 20px 12px; flex: 1; }

.nav-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border-radius: 8px;
    color: var(--cafe-300); font-size: 14px;
    cursor: pointer; transition: all 0.2s; margin-bottom: 2px; text-decoration: none;
}

.nav-item:hover, .nav-item.active { background: rgba(255,255,255,0.07); color: var(--cafe-100); }
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
    position: sticky; top: 0; z-index: 50;
}

.topbar h2 { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 500; color: var(--cafe-800); }
.topbar-user { display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--text-soft); }
.avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--cafe-700); display: flex; align-items: center; justify-content: center; color: var(--cafe-200); font-size: 13px; font-weight: 500; }

/* ── SECCIONES ── */
.seccion { display: none; }
.seccion.activa { display: block; }

.content { padding: 32px; }

/* ── STATS ── */
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 32px; }
.stat-card { background: white; border-radius: var(--radius); padding: 20px 24px; border: 1px solid rgba(92,52,32,0.08); }
.stat-label { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-soft); margin-bottom: 8px; }
.stat-value { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--cafe-800); }
.stat-sub   { font-size: 12px; color: var(--text-soft); margin-top: 4px; }

/* ── LAYOUT ── */
.layout-grid { display: grid; grid-template-columns: 1fr 400px; gap: 24px; align-items: start; }

/* ── CARD ── */
.card { background: white; border-radius: var(--radius); border: 1px solid rgba(92,52,32,0.08); overflow: hidden; }
.card-header { padding: 20px 24px; border-bottom: 1px solid rgba(92,52,32,0.08); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
.card-title { font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 500; color: var(--cafe-800); }

.search-box { position: relative; }
.search-box input { padding: 7px 12px 7px 32px; border: 1px solid rgba(92,52,32,0.15); border-radius: 8px; font-size: 13px; font-family: 'DM Sans', sans-serif; color: var(--text-dark); background: var(--cafe-50); outline: none; width: 200px; transition: border 0.2s; }
.search-box input:focus { border-color: var(--cafe-400); }
.search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-soft); }

table { width: 100%; border-collapse: collapse; }
thead tr { background: var(--cafe-50); border-bottom: 1px solid rgba(92,52,32,0.08); }
th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; color: var(--text-soft); }
td { padding: 14px 16px; font-size: 13px; border-bottom: 1px solid rgba(92,52,32,0.05); vertical-align: middle; }
tr:last-child td { border-bottom: none; }
tr:hover td { background: var(--cafe-50); }

.prod-thumb { width: 44px; height: 44px; border-radius: 8px; background: var(--cafe-100); display: flex; align-items: center; justify-content: center; color: var(--cafe-400); font-size: 20px; flex-shrink: 0; overflow: hidden; }
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

/* Badges de estado cliente */
.badge-activo   { background: #e8f5e9; color: #2e7d32; }
.badge-inactivo { background: #fce4ec; color: #c62828; }
.badge-cumple   { background: #fff8e1; color: #f57f17; }

.precio { font-weight: 500; color: var(--cafe-700); }
.precio-antes { font-size: 11px; color: var(--text-soft); text-decoration: line-through; }

.btn-delete { background: none; border: 1px solid rgba(192,57,43,0.2); color: var(--danger); padding: 5px 10px; border-radius: 6px; font-size: 12px; cursor: pointer; transition: all 0.2s; font-family: 'DM Sans', sans-serif; }
.btn-delete:hover { background: var(--danger); color: white; }

.btn-ver { background: none; border: 1px solid rgba(92,52,32,0.2); color: var(--cafe-600); padding: 5px 10px; border-radius: 6px; font-size: 12px; cursor: pointer; transition: all 0.2s; font-family: 'DM Sans', sans-serif; }
.btn-ver:hover { background: var(--cafe-700); color: white; }

/* ── FORMULARIO ── */
.form-card { position: sticky; top: 88px; }
.form-body { padding: 20px 24px; }
.form-group { margin-bottom: 14px; }

label { display: block; font-size: 11px; font-weight: 500; color: var(--text-mid); margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px; }
.opt-tag { font-size: 10px; color: var(--text-soft); font-style: italic; margin-left: 4px; text-transform: none; letter-spacing: 0; }

input[type="text"], input[type="number"], select, textarea {
    width: 100%; padding: 9px 12px; border: 1px solid rgba(92,52,32,0.15);
    border-radius: 8px; font-size: 14px; font-family: 'DM Sans', sans-serif;
    color: var(--text-dark); background: var(--cafe-50); outline: none; transition: border 0.2s, background 0.2s;
}
input:focus, select:focus, textarea:focus { border-color: var(--cafe-400); background: white; }
textarea { resize: vertical; min-height: 68px; }
.row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.row-2 .form-group { margin-bottom: 0; }

.upload-area { border: 2px dashed rgba(92,52,32,0.2); border-radius: 10px; cursor: pointer; transition: all 0.2s; background: var(--cafe-50); position: relative; overflow: hidden; min-height: 130px; display: flex; align-items: center; justify-content: center; }
.upload-area:hover { border-color: var(--cafe-400); background: var(--cafe-100); }
.upload-area input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; z-index: 2; }
.upload-placeholder { text-align: center; padding: 20px; pointer-events: none; }
.upload-icon { font-size: 30px; margin-bottom: 8px; }
.upload-text { font-size: 13px; color: var(--text-soft); line-height: 1.5; }
.upload-text small { font-size: 11px; opacity: 0.7; }
.upload-preview-img { width: 100%; height: 160px; object-fit: cover; display: none; border-radius: 8px; }
.upload-area.has-image { border-style: solid; border-color: var(--cafe-400); min-height: 160px; padding: 0; align-items: stretch; }
.upload-area.has-image .upload-placeholder { display: none; }
.upload-area.has-image .upload-preview-img { display: block; }
.btn-quitar-img { position: absolute; top: 8px; right: 8px; background: rgba(26,14,8,0.75); color: white; border: none; border-radius: 6px; padding: 4px 9px; font-size: 11px; cursor: pointer; z-index: 3; display: none; font-family: 'DM Sans', sans-serif; transition: background 0.2s; }
.btn-quitar-img:hover { background: var(--danger); }
.upload-area.has-image .btn-quitar-img { display: block; }

.form-section-title { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-soft); padding: 10px 0 10px; border-top: 1px solid rgba(92,52,32,0.08); margin-top: 4px; }
.badge-preview-wrap { display: flex; align-items: center; gap: 8px; margin-top: 6px; min-height: 22px; }
.badge-preview-label { font-size: 11px; color: var(--text-soft); }

.btn-submit { width: 100%; padding: 12px; background: var(--cafe-700); color: var(--cafe-100); border: none; border-radius: 10px; font-size: 14px; font-weight: 500; font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.2s; margin-top: 8px; display: flex; align-items: center; justify-content: center; gap: 8px; }
.btn-submit:hover { background: var(--cafe-600); }
.btn-submit:active { transform: scale(0.98); }
.btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }

/* ── CLIENTES ── */
.cliente-avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--cafe-700); display: flex; align-items: center; justify-content: center; color: var(--cafe-200); font-size: 13px; font-weight: 600; flex-shrink: 0; overflow: hidden; }
.cliente-avatar img { width: 100%; height: 100%; object-fit: cover; }
.cliente-info { display: flex; align-items: center; gap: 10px; }
.cliente-nombre { font-weight: 500; color: var(--cafe-800); font-size: 13px; }
.cliente-email  { font-size: 11px; color: var(--text-soft); margin-top: 1px; }
.pts-chip { display: inline-flex; align-items: center; gap: 4px; background: var(--cafe-100); color: var(--cafe-700); padding: 3px 9px; border-radius: 20px; font-size: 12px; font-weight: 500; }

/* Modal detalle cliente */
.modal-overlay { position: fixed; inset: 0; background: rgba(26,14,8,0.55); z-index: 200; display: flex; align-items: center; justify-content: center; padding: 20px; opacity: 0; pointer-events: none; transition: opacity 0.2s; }
.modal-overlay.open { opacity: 1; pointer-events: all; }
.modal-box { background: white; border-radius: 16px; width: 100%; max-width: 520px; max-height: 85vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.2); transform: translateY(16px); transition: transform 0.2s; }
.modal-overlay.open .modal-box { transform: translateY(0); }
.modal-header { padding: 20px 24px; border-bottom: 1px solid rgba(92,52,32,0.08); display: flex; align-items: center; justify-content: space-between; }
.modal-title { font-family: 'Playfair Display', serif; font-size: 17px; color: var(--cafe-800); }
.modal-close { background: none; border: none; font-size: 20px; color: var(--text-soft); cursor: pointer; line-height: 1; padding: 2px 6px; border-radius: 4px; }
.modal-close:hover { background: var(--cafe-100); }
.modal-body { padding: 20px 24px; }
.modal-user-top { display: flex; align-items: center; gap: 14px; margin-bottom: 20px; }
.modal-avatar { width: 56px; height: 56px; border-radius: 50%; background: var(--cafe-700); display: flex; align-items: center; justify-content: center; color: var(--cafe-200); font-size: 22px; font-weight: 600; overflow: hidden; flex-shrink: 0; }
.modal-avatar img { width: 100%; height: 100%; object-fit: cover; }
.modal-name  { font-size: 17px; font-weight: 500; color: var(--cafe-800); }
.modal-email { font-size: 12px; color: var(--text-soft); margin-top: 2px; }
.modal-grid  { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 18px; }
.modal-stat  { background: var(--cafe-50); border-radius: 10px; padding: 12px 14px; }
.modal-stat-label { font-size: 11px; color: var(--text-soft); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.modal-stat-val   { font-size: 18px; font-weight: 500; color: var(--cafe-800); }
.modal-section-title { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-soft); margin-bottom: 8px; padding-top: 14px; border-top: 1px solid rgba(92,52,32,0.08); }
.historial-item { display: flex; justify-content: space-between; align-items: center; padding: 7px 0; border-bottom: 1px solid rgba(92,52,32,0.05); font-size: 12px; }
.historial-item:last-child { border-bottom: none; }
.pts-pos { color: #2e7d32; font-weight: 600; }
.pts-neg { color: #c62828; font-weight: 600; }
.empty-state { padding: 40px; text-align: center; color: var(--text-soft); font-size: 13px; }

/* ── TOAST ── */
.toast { position: fixed; bottom: 24px; right: 24px; padding: 12px 20px; border-radius: 10px; font-size: 14px; font-weight: 500; color: white; opacity: 0; transform: translateY(10px); transition: all 0.3s; z-index: 999; pointer-events: none; }
.toast.show { opacity: 1; transform: translateY(0); }
.toast.success { background: var(--success); }
.toast.error   { background: var(--danger); }

.loading-row td { padding: 32px; text-align: center; color: var(--text-soft); font-size: 13px; }

/* ── FILTROS CLIENTES ── */
.filtros-bar { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.filtro-btn { background: var(--cafe-50); border: 1px solid rgba(92,52,32,0.15); color: var(--text-soft); padding: 5px 12px; border-radius: 20px; font-size: 12px; cursor: pointer; transition: all 0.2s; font-family: 'DM Sans', sans-serif; }
.filtro-btn:hover, .filtro-btn.activo { background: var(--cafe-700); color: var(--cafe-100); border-color: var(--cafe-700); }
</style>
</head>
<body>

<!-- ═══════════════════ SIDEBAR ═══════════════════ -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <img src="/cafe/assets/imagenes/banner20.png" alt="CoffeeCol"
             onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='block'">
        <span id="logo-fallback" style="display:none;font-family:'Playfair Display',serif;font-size:18px;color:#ddb896;letter-spacing:0.5px;">CAFE TANTICO</span>
        <span>Panel Admin</span>
    </div>
    <nav class="sidebar-nav">
        <a class="nav-item active" href="#" onclick="mostrarSeccion('productos', this)">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Productos
        </a>
        <a class="nav-item" href="#" onclick="mostrarSeccion('pedidos', this)">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Pedidos
        </a>
        <a class="nav-item" href="#" onclick="mostrarSeccion('clientes', this)">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            Clientes
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
        <h2 id="topbar-titulo">Gestión de Productos</h2>
        <div class="topbar-user">
            <span id="usuario-label">Admin</span>
            <div class="avatar" id="usuario-avatar">A</div>
        </div>
    </div>

    <!-- ══════════ SECCIÓN PRODUCTOS ══════════ -->
    <div id="sec-productos" class="seccion activa">
        <div class="content">
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
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Productos</span>
                        <div class="search-box">
                            <svg class="search-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                            <input type="text" id="buscador" placeholder="Buscar producto…" oninput="filtrar()">
                        </div>
                    </div>
                    <table>
                        <thead><tr><th>Producto</th><th>Precio</th><th>Badge</th><th></th></tr></thead>
                        <tbody id="tabla"><tr class="loading-row"><td colspan="4">Cargando productos…</td></tr></tbody>
                    </table>
                </div>
                <div class="card form-card">
                    <div class="card-header"><span class="card-title">Agregar producto</span></div>
                    <div class="form-body">
                        <div class="form-group">
                            <label>Foto del producto <span class="opt-tag">opcional</span></label>
                            <div class="upload-area" id="uploadArea">
                                <input type="file" id="imagenInput" accept="image/jpeg,image/png,image/webp" onchange="previewImagen(event)">
                                <button type="button" class="btn-quitar-img" onclick="quitarImagen(event)">✕ Quitar</button>
                                <img class="upload-preview-img" id="preview" alt="Vista previa">
                                <div class="upload-placeholder" id="uploadPlaceholder">
                                    <div class="upload-icon">📷</div>
                                    <div class="upload-text">Clic para subir imagen<br><small>JPG, PNG o WebP · Máx. 5 MB</small></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-section-title">Información básica</div>
                        <div class="form-group"><label>Nombre *</label><input type="text" id="f-nombre" placeholder="Ej: Café Tostado Especial" maxlength="100"></div>
                        <div class="form-group"><label>Descripción <span class="opt-tag">opcional</span></label><textarea id="f-descripcion" placeholder="Describe el producto brevemente…"></textarea></div>
                        <div class="row-2">
                            <div class="form-group"><label>Categoría *</label>
                                <select id="f-categoria">
                                    <option value="">Seleccionar…</option>
                                    <option value="tueste-claro">Tueste Claro</option>
                                    <option value="tueste-medio">Tueste Medio</option>
                                    <option value="tueste-oscuro">Tueste Oscuro</option>
                                    <option value="capsulas">Cápsulas</option>
                                    <option value="origen-especial">Origen Especial</option>
                                </select>
                            </div>
                            <div class="form-group"><label>Unidad <span class="opt-tag">opcional</span></label><input type="text" id="f-unidad" placeholder="Ej: 250 g · 1 kg"></div>
                        </div>
                        <div class="form-section-title">Precios</div>
                        <div class="row-2">
                            <div class="form-group"><label>Precio actual *</label><input type="number" id="f-precio" placeholder="0" step="1" min="0"></div>
                            <div class="form-group"><label>Precio antes <span class="opt-tag">tachado</span></label><input type="number" id="f-precio-antes" placeholder="0" step="1" min="0"></div>
                        </div>
                        <div class="form-section-title">Etiqueta (badge) <span class="opt-tag">opcional</span></div>
                        <div class="row-2">
                            <div class="form-group"><label>Texto</label><input type="text" id="f-badge" placeholder="Ej: Nuevo · -20%" maxlength="30" oninput="actualizarBadgePreview()"></div>
                            <div class="form-group"><label>Tipo</label>
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
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                            Agregar producto
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════ SECCIÓN PEDIDOS ══════════ -->
    <div id="sec-pedidos" class="seccion">
        <div class="content">
            <div class="empty-state" style="padding:80px;">
                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 16px;display:block;opacity:0.3;">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p>Sección de pedidos — integra aquí tu módulo existente</p>
            </div>
        </div>
    </div>

    <!-- ══════════ SECCIÓN CLIENTES ══════════ -->
    <div id="sec-clientes" class="seccion">
        <div class="content">

            <!-- Stats clientes -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total clientes</div>
                    <div class="stat-value" id="stat-cli-total">—</div>
                    <div class="stat-sub">registrados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Nuevos este mes</div>
                    <div class="stat-value" id="stat-cli-nuevos">—</div>
                    <div class="stat-sub">en los últimos 30 días</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Puntos otorgados</div>
                    <div class="stat-value" id="stat-cli-puntos">—</div>
                    <div class="stat-sub">en total</div>
                </div>
            </div>

            <!-- Tabla clientes -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Clientes</span>
                    <div class="filtros-bar">
                        <div class="search-box">
                            <svg class="search-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                            <input type="text" id="buscador-cli" placeholder="Buscar cliente…" oninput="filtrarClientes()">
                        </div>
                        <button class="filtro-btn activo" onclick="setFiltroCliente('todos', this)">Todos</button>
                        <button class="filtro-btn" onclick="setFiltroCliente('con-puntos', this)">Con puntos</button>
                        <button class="filtro-btn" onclick="setFiltroCliente('nuevos', this)">Nuevos</button>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Registro</th>
                            <th>Último acceso</th>
                            <th>Puntos</th>
                            <th>Pedidos</th>
                            <th>Cumpleaños</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tabla-clientes">
                        <tr class="loading-row"><td colspan="8">Cargando clientes…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</main>

<!-- ══════════ MODAL DETALLE CLIENTE ══════════ -->
<div class="modal-overlay" id="modal-cliente">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Detalle del cliente</span>
            <button class="modal-close" onclick="cerrarModal()">✕</button>
        </div>
        <div class="modal-body" id="modal-body">
            <!-- Se rellena por JS -->
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>

<script>
const API    = '../api/products.php';
const token  = localStorage.getItem('token');
const API_CLI = '../api/clientes.php';

if (!token) location.href = 'login.php';

// Usuario en topbar
try {
    const payload = JSON.parse(atob(token.split('.')[1]));
    const u = payload.sub || 'Admin';
    document.getElementById('usuario-label').textContent = u;
    document.getElementById('usuario-avatar').textContent = u[0].toUpperCase();
} catch (e) {}

// ── Navegación secciones ──
const titulos = { productos: 'Gestión de Productos', pedidos: 'Gestión de Pedidos', clientes: 'Base de Clientes' };

function mostrarSeccion(nombre, el) {
    document.querySelectorAll('.seccion').forEach(s => s.classList.remove('activa'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById('sec-' + nombre).classList.add('activa');
    el.classList.add('active');
    document.getElementById('topbar-titulo').textContent = titulos[nombre];
    if (nombre === 'clientes') cargarClientes();
    if (nombre === 'productos') cargar();
    return false;
}

// ── Toast ──
function mostrarToast(msg, tipo = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast show ' + tipo;
    setTimeout(() => t.className = 'toast', 3000);
}

// ── Badge helpers ──
const BADGE_CLASS = { nuevo:'badge-nuevo', oferta:'badge-oferta', popular:'badge-popular', especial:'badge-especial' };
function getBadgeClass(tipo) { return BADGE_CLASS[tipo] || 'badge-default'; }
function formatPrecio(n) { return Math.round(n).toLocaleString('es-CO'); }
function formatFecha(f) { if (!f) return '—'; const d = new Date(f); return d.toLocaleDateString('es-CO', { day:'2-digit', month:'short', year:'numeric' }); }

// ── Badge preview ──
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

// ── CATEGORIAS ──
const CATEGORIAS = { 'tueste-claro':'Tueste Claro','tueste-medio':'Tueste Medio','tueste-oscuro':'Tueste Oscuro','capsulas':'Cápsulas','origen-especial':'Origen Especial' };

let todosLosProductos = [];

function renderTabla(productos) {
    const tbody = document.getElementById('tabla');
    if (!productos.length) {
        tbody.innerHTML = `<tr><td colspan="4"><div class="empty-state"><p>No hay productos aún</p></div></td></tr>`;
        return;
    }
    tbody.innerHTML = productos.map(p => {
        const thumb = p.imagen ? `<div class="prod-thumb"><img src="../${p.imagen}" alt="${p.nombre}"></div>` : `<div class="prod-thumb">☕</div>`;
        const catNombre   = CATEGORIAS[p.categoria] || p.categoria || '—';
        const precioAntes = p.precio_antes ? `<div class="precio-antes">$${formatPrecio(p.precio_antes)}</div>` : '';
        const badgeCell   = p.badge ? `<span class="badge ${getBadgeClass(p.badge_tipo)}">${p.badge}</span>` : '—';
        return `<tr>
            <td><div class="prod-info">${thumb}<div><div class="prod-name">${p.nombre}</div><div class="prod-cat">${catNombre}${p.unidad ? ' · ' + p.unidad : ''}</div></div></div></td>
            <td><div class="precio">$${formatPrecio(p.precio)}</div>${precioAntes}</td>
            <td>${badgeCell}</td>
            <td><button class="btn-delete" onclick="eliminar(${p.id})">Eliminar</button></td>
        </tr>`;
    }).join('');
}

function actualizarStats(productos) {
    document.getElementById('stat-total').textContent  = productos.length;
    document.getElementById('stat-cats').textContent   = new Set(productos.map(p => p.categoria).filter(Boolean)).size;
    document.getElementById('stat-oferta').textContent = productos.filter(p => p.precio_antes).length;
}

function filtrar() {
    const q = document.getElementById('buscador').value.toLowerCase();
    renderTabla(todosLosProductos.filter(p => p.nombre.toLowerCase().includes(q) || (CATEGORIAS[p.categoria]||p.categoria||'').toLowerCase().includes(q)));
}

function cargar() {
    fetch(API)
        .then(r => r.json())
        .then(d => { if (d.success) { todosLosProductos = d.productos; renderTabla(todosLosProductos); actualizarStats(todosLosProductos); } })
        .catch(() => mostrarToast('Error al cargar productos', 'error'));
}

function previewImagen(e) {
    const file = e.target.files[0];
    if (!file) return;
    if (file.size > 5 * 1024 * 1024) { mostrarToast('La imagen no debe superar 5 MB', 'error'); e.target.value = ''; return; }
    const reader = new FileReader();
    reader.onload = ev => { document.getElementById('preview').src = ev.target.result; document.getElementById('uploadArea').classList.add('has-image'); };
    reader.readAsDataURL(file);
}

function quitarImagen(e) {
    e.stopPropagation(); e.preventDefault();
    document.getElementById('imagenInput').value = '';
    document.getElementById('preview').src = '';
    document.getElementById('uploadArea').classList.remove('has-image');
}

function agregar() {
    const nombre    = document.getElementById('f-nombre').value.trim();
    const precio    = document.getElementById('f-precio').value;
    const categoria = document.getElementById('f-categoria').value;
    if (!nombre || !precio || !categoria) { mostrarToast('Nombre, precio y categoría son obligatorios', 'error'); return; }
    const btn = document.getElementById('btnAgregar');
    btn.disabled = true; btn.innerHTML = '⏳ Guardando…';
    const fd = new FormData();
    fd.append('nombre', nombre); fd.append('descripcion', document.getElementById('f-descripcion').value.trim());
    fd.append('precio', precio); fd.append('precio_antes', document.getElementById('f-precio-antes').value || '');
    fd.append('categoria', categoria); fd.append('unidad', document.getElementById('f-unidad').value.trim());
    fd.append('badge', document.getElementById('f-badge').value.trim()); fd.append('badge_tipo', document.getElementById('f-badge-tipo').value);
    const imgFile = document.getElementById('imagenInput').files[0];
    if (imgFile) fd.append('imagen', imgFile);
    fetch(API, { method:'POST', headers:{'Authorization':'Bearer '+token}, body:fd })
        .then(r => r.json())
        .then(d => { if (d.success) { mostrarToast('Producto agregado ✓'); limpiarFormulario(); cargar(); } else mostrarToast(d.error||'Error al guardar','error'); })
        .catch(() => mostrarToast('Error de conexión','error'))
        .finally(() => { btn.disabled=false; btn.innerHTML=`<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Agregar producto`; });
}

function eliminar(id) {
    if (!confirm('¿Eliminar este producto?')) return;
    fetch(API, { method:'DELETE', headers:{'Authorization':'Bearer '+token,'Content-Type':'application/json'}, body:JSON.stringify({id}) })
        .then(r => r.json())
        .then(d => { if (d.success) { mostrarToast('Producto eliminado'); cargar(); } else mostrarToast(d.error||'Error','error'); });
}

function limpiarFormulario() {
    ['f-nombre','f-descripcion','f-precio','f-precio-antes','f-unidad','f-badge'].forEach(id => { document.getElementById(id).value=''; });
    document.getElementById('f-categoria').value=''; document.getElementById('f-badge-tipo').value='';
    document.getElementById('imagenInput').value=''; document.getElementById('preview').src='';
    document.getElementById('uploadArea').classList.remove('has-image');
    document.getElementById('badgePreviewWrap').style.display='none';
}

// ══════════════════════════════════
// ── CLIENTES ──
// ══════════════════════════════════
let todosLosClientes = [];
let filtroActivo = 'todos';

function cargarClientes() {
    document.getElementById('tabla-clientes').innerHTML = '<tr class="loading-row"><td colspan="8">Cargando clientes…</td></tr>';
    fetch(API_CLI, { headers:{'Authorization':'Bearer '+token} })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                todosLosClientes = d.clientes;
                actualizarStatsClientes(d.clientes);
                renderClientes(d.clientes);
            } else {
                mostrarToast('Error al cargar clientes', 'error');
            }
        })
        .catch(() => mostrarToast('Error de conexión', 'error'));
}

function actualizarStatsClientes(clientes) {
    const ahora = new Date();
    const hace30 = new Date(ahora - 30*24*60*60*1000);
    const nuevos = clientes.filter(c => new Date(c.fecha_registro) >= hace30).length;
    const totalPts = clientes.reduce((s, c) => s + (parseInt(c.puntos)||0), 0);
    document.getElementById('stat-cli-total').textContent  = clientes.length;
    document.getElementById('stat-cli-nuevos').textContent = nuevos;
    document.getElementById('stat-cli-puntos').textContent = totalPts.toLocaleString('es-CO');
}

function renderClientes(clientes) {
    const tbody = document.getElementById('tabla-clientes');
    if (!clientes.length) {
        tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state"><p>No hay clientes aún</p></div></td></tr>`;
        return;
    }

    const hoyMD = new Date().toLocaleDateString('es-CO', {month:'2-digit', day:'2-digit'});

    tbody.innerHTML = clientes.map(c => {
        const iniciales = (c.nombre||c.email||'?')[0].toUpperCase();
        const avatar = c.foto
            ? `<div class="cliente-avatar"><img src="${c.foto}" alt="foto"></div>`
            : `<div class="cliente-avatar">${iniciales}</div>`;

        // Cumpleaños hoy
        let cumpleCell = '—';
        if (c.fecha_nacimiento) {
            const fn = new Date(c.fecha_nacimiento + 'T00:00:00');
            const fnMD = fn.toLocaleDateString('es-CO', {month:'2-digit', day:'2-digit'});
            cumpleCell = fnMD === hoyMD
                ? `<span class="badge badge-cumple">🎂 Hoy</span>`
                : fn.toLocaleDateString('es-CO', {day:'2-digit', month:'short'});
        }

        // Último acceso Firebase (viene del API)
        const ultimoAcceso = formatFecha(c.ultimo_acceso);

        // Estado: activo si accedió en los últimos 30 días
        const hace30 = new Date(Date.now() - 30*24*60*60*1000);
        const activo = c.ultimo_acceso && new Date(c.ultimo_acceso) >= hace30;
        const estadoBadge = activo
            ? `<span class="badge badge-activo">Activo</span>`
            : `<span class="badge badge-inactivo">Inactivo</span>`;

        return `<tr>
            <td>
                <div class="cliente-info">
                    ${avatar}
                    <div>
                        <div class="cliente-nombre">${c.nombre || '—'}</div>
                        <div class="cliente-email">${c.email}</div>
                    </div>
                </div>
            </td>
            <td>${formatFecha(c.fecha_registro)}</td>
            <td>${ultimoAcceso}</td>
            <td><span class="pts-chip">⭐ ${parseInt(c.puntos)||0}</span></td>
            <td>${parseInt(c.total_pedidos)||0}</td>
            <td>${cumpleCell}</td>
            <td>${estadoBadge}</td>
            <td><button class="btn-ver" onclick='verCliente(${JSON.stringify(c)})'>Ver</button></td>
        </tr>`;
    }).join('');
}

function filtrarClientes() {
    const q = document.getElementById('buscador-cli').value.toLowerCase();
    const ahora = new Date();
    const hace30 = new Date(ahora - 30*24*60*60*1000);

    let lista = todosLosClientes.filter(c =>
        (c.nombre||'').toLowerCase().includes(q) ||
        (c.email||'').toLowerCase().includes(q)
    );

    if (filtroActivo === 'con-puntos') lista = lista.filter(c => (parseInt(c.puntos)||0) > 0);
    if (filtroActivo === 'nuevos')     lista = lista.filter(c => new Date(c.fecha_registro) >= hace30);

    renderClientes(lista);
}

function setFiltroCliente(filtro, el) {
    filtroActivo = filtro;
    document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('activo'));
    el.classList.add('activo');
    filtrarClientes();
}

function verCliente(c) {
    const modal = document.getElementById('modal-cliente');
    const body  = document.getElementById('modal-body');
    const iniciales = (c.nombre||c.email||'?')[0].toUpperCase();
    const avatar = c.foto
        ? `<div class="modal-avatar"><img src="${c.foto}" alt="foto"></div>`
        : `<div class="modal-avatar">${iniciales}</div>`;

    const fn = c.fecha_nacimiento ? (() => { const d = new Date(c.fecha_nacimiento+'T00:00:00'); return d.toLocaleDateString('es-CO',{day:'2-digit',month:'long'}); })() : '—';

    let historialHTML = '<p style="font-size:12px;color:var(--text-soft)">Sin movimientos</p>';
    if (c.historial && c.historial.length) {
        historialHTML = c.historial.map(h => `
            <div class="historial-item">
                <div>
                    <div>${h.descripcion}</div>
                    <div style="color:var(--text-soft);font-size:11px;">${formatFecha(h.fecha)}</div>
                </div>
                <div class="${h.puntos > 0 ? 'pts-pos' : 'pts-neg'}">${h.puntos > 0 ? '+' : ''}${h.puntos} pts</div>
            </div>
        `).join('');
    }

    body.innerHTML = `
        <div class="modal-user-top">
            ${avatar}
            <div>
                <div class="modal-name">${c.nombre || '—'}</div>
                <div class="modal-email">${c.email}</div>
            </div>
        </div>
        <div class="modal-grid">
            <div class="modal-stat"><div class="modal-stat-label">Puntos</div><div class="modal-stat-val">⭐ ${parseInt(c.puntos)||0}</div></div>
            <div class="modal-stat"><div class="modal-stat-label">Pedidos</div><div class="modal-stat-val">${parseInt(c.total_pedidos)||0}</div></div>
            <div class="modal-stat"><div class="modal-stat-label">Miembro desde</div><div class="modal-stat-val" style="font-size:13px">${formatFecha(c.fecha_registro)}</div></div>
            <div class="modal-stat"><div class="modal-stat-label">Cumpleaños</div><div class="modal-stat-val" style="font-size:13px">${fn}</div></div>
        </div>
        <div class="modal-section-title">Historial de puntos</div>
        ${historialHTML}
    `;
    modal.classList.add('open');
}

function cerrarModal() {
    document.getElementById('modal-cliente').classList.remove('open');
}

document.getElementById('modal-cliente').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});

// ── Cerrar sesión ──
function cerrarSesion() { localStorage.removeItem('token'); location.href = 'login.php'; }

cargar();
</script>
</body>
</html>