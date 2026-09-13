<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Admin — CaféCol</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
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

.btn-edit { background: none; border: 1px solid rgba(92,52,32,0.2); color: var(--cafe-600); padding: 5px 10px; border-radius: 6px; font-size: 12px; cursor: pointer; transition: all 0.2s; font-family: 'DM Sans', sans-serif; margin-right: 4px; }
.btn-edit:hover { background: var(--cafe-700); color: white; border-color: var(--cafe-700); }

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

/* ── FILTROS ── */
.filtros-bar { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.filtro-btn { background: var(--cafe-50); border: 1px solid rgba(92,52,32,0.15); color: var(--text-soft); padding: 5px 12px; border-radius: 20px; font-size: 12px; cursor: pointer; transition: all 0.2s; font-family: 'DM Sans', sans-serif; }
.filtro-btn:hover, .filtro-btn.activo { background: var(--cafe-700); color: var(--cafe-100); border-color: var(--cafe-700); }

/* ══════════════════════════════════════
   PEDIDOS — estilos propios
══════════════════════════════════════ */

/* Stats 4 columnas para pedidos */
.stats-grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 32px;
}

/* Badge de estado del pedido */
.badge-pendiente  { background: #fff8e1; color: #f57f17; }
.badge-procesando { background: #e3f2fd; color: #1565c0; }
.badge-enviado    { background: #e8f5e9; color: #2e7d32; }
.badge-entregado  { background: #ede7f6; color: #4527a0; }
.badge-cancelado  { background: #fce4ec; color: #c62828; }

/* Select de estado dentro de la tabla */
.select-estado {
    padding: 4px 8px;
    border: 1px solid rgba(92,52,32,0.2);
    border-radius: 6px;
    font-size: 12px;
    font-family: 'DM Sans', sans-serif;
    color: var(--text-dark);
    background: var(--cafe-50);
    cursor: pointer;
    outline: none;
    transition: border 0.2s;
    width: auto;
}
.select-estado:focus { border-color: var(--cafe-400); }

/* Número de pedido destacado */
.pedido-id {
    font-family: 'Playfair Display', serif;
    font-size: 15px;
    color: var(--cafe-700);
    font-weight: 500;
}
.pedido-fecha {
    font-size: 11px;
    color: var(--text-soft);
    margin-top: 2px;
}

/* Cliente en tabla pedidos */
.pedido-cliente-nombre { font-weight: 500; font-size: 13px; color: var(--cafe-800); }
.pedido-cliente-email  { font-size: 11px; color: var(--text-soft); margin-top: 1px; }

/* Total */
.pedido-total { font-weight: 600; color: var(--cafe-700); font-size: 14px; }

/* Modal pedido */
.modal-pedido-box {
    max-width: 600px;
}

.pedido-detalle-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 20px;
    gap: 12px;
}
.pedido-detalle-id {
    font-family: 'Playfair Display', serif;
    font-size: 22px;
    color: var(--cafe-800);
}
.pedido-detalle-fecha {
    font-size: 12px;
    color: var(--text-soft);
    margin-top: 2px;
}

.pedido-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 20px;
}
.pedido-info-cell {
    background: var(--cafe-50);
    border-radius: 10px;
    padding: 12px 14px;
}
.pedido-info-cell-label {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--text-soft);
    margin-bottom: 4px;
}
.pedido-info-cell-val {
    font-size: 13px;
    color: var(--cafe-800);
    font-weight: 500;
}

/* Tabla de items dentro del modal */
.items-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 16px;
}
.items-table th {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--text-soft);
    padding: 8px 0;
    border-bottom: 1px solid rgba(92,52,32,0.1);
    text-align: left;
}
.items-table td {
    padding: 10px 0;
    font-size: 13px;
    border-bottom: 1px solid rgba(92,52,32,0.05);
}
.items-table tr:last-child td { border-bottom: none; }

.pedido-total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 16px;
    background: var(--cafe-900);
    border-radius: 10px;
    margin-top: 4px;
}
.pedido-total-label {
    font-size: 13px;
    color: var(--cafe-300);
}
.pedido-total-val {
    font-family: 'Playfair Display', serif;
    font-size: 20px;
    color: var(--cafe-100);
}

/* Estado selector en modal */
.modal-estado-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid rgba(92,52,32,0.08);
}
.modal-estado-label {
    font-size: 12px;
    color: var(--text-soft);
    white-space: nowrap;
}
.modal-estado-select {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid rgba(92,52,32,0.2);
    border-radius: 8px;
    font-size: 13px;
    font-family: 'DM Sans', sans-serif;
    color: var(--text-dark);
    background: var(--cafe-50);
    outline: none;
    transition: border 0.2s;
    width: auto;
}
.modal-estado-select:focus { border-color: var(--cafe-400); }

.btn-guardar-estado {
    padding: 8px 16px;
    background: var(--cafe-700);
    color: var(--cafe-100);
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    transition: background 0.2s;
    white-space: nowrap;
}
.btn-guardar-estado:hover { background: var(--cafe-600); }

/* ══════════════════════════════════════
   REPORTES — estilos
══════════════════════════════════════ */

/* KPIs 5 columnas */
.rpt-kpis {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
.rpt-kpi-card {
    background: white;
    border-radius: var(--radius);
    padding: 20px;
    border: 1px solid rgba(92,52,32,0.08);
    display: flex;
    align-items: flex-start;
    gap: 14px;
    transition: transform 0.2s, box-shadow 0.2s;
}
.rpt-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(26,14,8,0.08);
}
.rpt-kpi-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 20px;
}
.rpt-kpi-icon.ventas   { background: #e8f5e9; color: #2e7d32; }
.rpt-kpi-icon.mes      { background: #e3f2fd; color: #1565c0; }
.rpt-kpi-icon.pedidos  { background: #fff3e0; color: #e65100; }
.rpt-kpi-icon.ticket   { background: #fce4ec; color: #c62828; }
.rpt-kpi-icon.producto { background: var(--cafe-100); color: var(--cafe-600); }
.rpt-kpi-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-soft);
    margin-bottom: 6px;
}
.rpt-kpi-value {
    font-family: 'Playfair Display', serif;
    font-size: 24px;
    color: var(--cafe-800);
    line-height: 1.1;
}
.rpt-kpi-sub {
    font-size: 11px;
    color: var(--text-soft);
    margin-top: 4px;
}

/* Filtros */
.rpt-filtros {
    background: white;
    border-radius: var(--radius);
    border: 1px solid rgba(92,52,32,0.08);
    padding: 16px 20px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.rpt-filtros-label {
    font-size: 12px;
    font-weight: 500;
    color: var(--text-mid);
    margin-right: 4px;
}
.rpt-fecha-input {
    padding: 6px 10px;
    border: 1px solid rgba(92,52,32,0.18);
    border-radius: 8px;
    font-size: 13px;
    font-family: 'DM Sans', sans-serif;
    color: var(--text-dark);
    background: var(--cafe-50);
    outline: none;
    transition: border 0.2s;
    width: 145px;
}
.rpt-fecha-input:focus { border-color: var(--cafe-400); }
.rpt-sep { color: var(--text-soft); font-size: 13px; }
.rpt-btn-aplicar {
    padding: 6px 14px;
    background: var(--cafe-700);
    color: var(--cafe-100);
    border: none;
    border-radius: 8px;
    font-size: 12px;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    transition: background 0.2s;
}
.rpt-btn-aplicar:hover { background: var(--cafe-600); }

/* Gráficos grid */
.rpt-charts-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 24px;
}
.rpt-chart-card {
    background: white;
    border-radius: var(--radius);
    border: 1px solid rgba(92,52,32,0.08);
    padding: 20px;
}
.rpt-chart-title {
    font-family: 'Playfair Display', serif;
    font-size: 15px;
    color: var(--cafe-800);
    margin-bottom: 16px;
    font-weight: 500;
}
.rpt-chart-wrap {
    position: relative;
    height: 260px;
}
.rpt-chart-wrap canvas {
    width: 100% !important;
    height: 100% !important;
}

/* Exportación */
.rpt-export-bar {
    display: flex;
    gap: 10px;
    margin-bottom: 16px;
    justify-content: flex-end;
}
.rpt-btn-export {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border: 1px solid rgba(92,52,32,0.2);
    border-radius: 8px;
    background: white;
    color: var(--cafe-600);
    font-size: 12px;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    transition: all 0.2s;
}
.rpt-btn-export:hover {
    background: var(--cafe-700);
    color: white;
    border-color: var(--cafe-700);
}
.rpt-btn-export svg { width: 14px; height: 14px; }

/* Tabla reportes */
.rpt-table-card {
    background: white;
    border-radius: var(--radius);
    border: 1px solid rgba(92,52,32,0.08);
    overflow: hidden;
}
.rpt-table-header {
    padding: 18px 24px;
    border-bottom: 1px solid rgba(92,52,32,0.08);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.rpt-total-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--cafe-100);
    color: var(--cafe-700);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}
.rpt-estado-mini {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 3px;
}
.rpt-estado-mini.pendiente  { background: #f57f17; }
.rpt-estado-mini.procesando { background: #1565c0; }
.rpt-estado-mini.enviado    { background: #2e7d32; }
.rpt-estado-mini.entregado  { background: #4527a0; }
.rpt-estado-mini.cancelado  { background: #c62828; }

/* Loading skeleton */
.rpt-loading {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-soft);
    font-size: 14px;
}
.rpt-loading-spinner {
    display: inline-block;
    width: 32px;
    height: 32px;
    border: 3px solid var(--cafe-100);
    border-top-color: var(--cafe-500);
    border-radius: 50%;
    animation: rptSpin 0.8s linear infinite;
    margin-bottom: 12px;
}
@keyframes rptSpin { to { transform: rotate(360deg); } }

/* Print styles */
@media print {
    .sidebar, .topbar, .rpt-filtros, .rpt-export-bar, .btn-logout,
    .nav-item, .modal-overlay, .toast { display: none !important; }
    .main { margin-left: 0 !important; }
    .seccion { display: block !important; }
    #sec-productos, #sec-pedidos, #sec-clientes { display: none !important; }
    .rpt-kpis { grid-template-columns: repeat(5, 1fr); }
    .rpt-charts-grid { grid-template-columns: 1fr 1fr; }
    .rpt-chart-card { break-inside: avoid; page-break-inside: avoid; }
    body { background: white; }
}

/* Responsive */
@media (max-width: 1200px) {
    .rpt-kpis { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 900px) {
    .rpt-kpis { grid-template-columns: repeat(2, 1fr); }
    .rpt-charts-grid { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    .rpt-kpis { grid-template-columns: 1fr; }
    .rpt-filtros { flex-direction: column; align-items: stretch; }
    .rpt-fecha-input { width: 100%; }
}

/* ══════════════════════════════════════
   ANUNCIOS & POPUPS — estilos propios
══════════════════════════════════════ */
.switch-wrap {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    user-select: none;
}
.switch-input {
    display: none;
}
.switch-slider {
    width: 38px;
    height: 22px;
    background: #d8c6ba;
    border-radius: 20px;
    position: relative;
    transition: background 0.25s ease;
    flex-shrink: 0;
}
.switch-slider::before {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: white;
    top: 3px;
    left: 3px;
    transition: transform 0.25s ease;
    box-shadow: 0 1px 4px rgba(0,0,0,0.2);
}
.switch-input:checked + .switch-slider {
    background: #27704a;
}
.switch-input:checked + .switch-slider::before {
    transform: translateX(16px);
}
.switch-label {
    font-size: 12px;
    font-weight: 500;
    color: var(--text-dark);
}

.anu-thumb {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    background: var(--cafe-100);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1px solid rgba(92,52,32,0.1);
}
.anu-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.anu-info {
    display: flex;
    align-items: center;
    gap: 12px;
}
.anu-title {
    font-weight: 500;
    font-size: 13.5px;
    color: var(--cafe-900);
    line-height: 1.3;
}
.anu-eyebrow {
    font-size: 10.5px;
    color: var(--cafe-500);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 2px;
}
.anu-badge-tipo {
    display: inline-block;
    font-size: 11px;
    font-weight: 500;
    padding: 3px 8px;
    border-radius: 6px;
    background: var(--cafe-100);
    color: var(--cafe-700);
    text-transform: capitalize;
}
.anu-badge-tipo.partido { background: #e3f2fd; color: #1565c0; }
.anu-badge-tipo.promo   { background: #fff8e1; color: #f57f17; }
.anu-badge-tipo.evento  { background: #f3e5f5; color: #7b1fa2; }
.anu-badge-tipo.general { background: #ede7f6; color: #4527a0; }

.btn-preview-popup {
    background: rgba(196,137,95,0.15);
    border: 1px solid rgba(196,137,95,0.4);
    color: var(--cafe-700);
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-preview-popup:hover {
    background: var(--cafe-700);
    color: white;
}

/* Modal Popup Preview en Admin */
.modal-anu-preview-box {
    max-width: 680px;
    padding: 0;
    background: transparent;
    box-shadow: none;
    overflow: visible;
}
.modal-anu-preview-inner {
    border-radius: 22px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 30px 80px rgba(0,0,0,0.6);
}
</style>
</head>
<body>

<!-- ═══════════════════ SIDEBAR ═══════════════════ -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <img src="/cafe/assets/imagenes/banner20.png" alt="Tantico"
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
        <a class="nav-item" href="#" onclick="mostrarSeccion('anuncios', this)">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 8a3 3 0 00-3-3H7a2 2 0 00-2 2v6a2 2 0 002 2h8a3 3 0 003-3V8z"/>
                <path d="M19 13l2.5 1.5M19 8l3-2M19 10.5h3"/>
            </svg>
            Anuncios &amp; Popups
        </a>
        <a class="nav-item" href="#" onclick="mostrarSeccion('reportes', this)">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M3 3v18h18"/><path d="M7 16l4-8 4 4 4-6"/>
            </svg>
            Reportes
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
                    <div class="card-header">
                        <span class="card-title" id="form-card-title">Agregar producto</span>
                        <button type="button" id="btn-cancel-edit" style="display:none;background:none;border:none;color:var(--text-soft);font-size:12px;cursor:pointer;text-decoration:underline;" onclick="cancelarEdicion()">Cancelar edición</button>
                    </div>
                    <div class="form-body">
                        <input type="hidden" id="f-id" value="">
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
                        <div class="form-group"><label>Nombre *</label><input type="text" id="f-nombre" placeholder="Ej: Tarta Vasca o Geisha Huila" maxlength="100"></div>
                        <div class="form-group"><label>Descripción <span class="opt-tag">opcional</span></label><textarea id="f-descripcion" placeholder="Describe el producto, notas de cata o ingredientes…"></textarea></div>
                        <div class="row-2">
                            <div class="form-group"><label>Categoría *</label>
                                <select id="f-categoria">
                                    <option value="">Seleccionar…</option>
                                    <optgroup label="☕ Cafés de Especialidad">
                                        <option value="tueste-claro">Tueste Claro</option>
                                        <option value="tueste-medio">Tueste Medio</option>
                                        <option value="tueste-oscuro">Tueste Oscuro</option>
                                        <option value="capsulas">Cápsulas</option>
                                        <option value="origen-especial">Origen Especial</option>
                                    </optgroup>
                                    <optgroup label="🥐 Alimentos & Repostería">
                                        <option value="reposteria">Tortas &amp; Repostería</option>
                                        <option value="panaderia">Panadería Artesanal</option>
                                        <option value="desayunos">Desayunos &amp; Brunch</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="form-group"><label>Unidad / Porción <span class="opt-tag">opcional</span></label><input type="text" id="f-unidad" placeholder="Ej: 250 g · Porción · Unidad"></div>
                        </div>
                        <div class="form-section-title">Precios (COP)</div>
                        <div class="row-2">
                            <div class="form-group"><label>Precio actual *</label><input type="number" id="f-precio" placeholder="0" step="1" min="0"></div>
                            <div class="form-group"><label>Precio antes <span class="opt-tag">tachado</span></label><input type="number" id="f-precio-antes" placeholder="0" step="1" min="0"></div>
                        </div>
                        <div class="form-section-title">Etiqueta (badge) <span class="opt-tag">opcional</span></div>
                        <div class="row-2">
                            <div class="form-group"><label>Texto</label><input type="text" id="f-badge" placeholder="Ej: Nuevo · -20% · Especial" maxlength="30" oninput="actualizarBadgePreview()"></div>
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
                            <span id="btnAgregarTexto">Agregar producto</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════ SECCIÓN PEDIDOS ══════════ -->
    <div id="sec-pedidos" class="seccion">
        <div class="content">

            <!-- Stats pedidos -->
            <div class="stats-grid-4">
                <div class="stat-card">
                    <div class="stat-label">Total pedidos</div>
                    <div class="stat-value" id="stat-ped-total">—</div>
                    <div class="stat-sub">registrados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Pendientes</div>
                    <div class="stat-value" id="stat-ped-pendientes" style="color:#f57f17">—</div>
                    <div class="stat-sub">por procesar</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Enviados</div>
                    <div class="stat-value" id="stat-ped-enviados" style="color:var(--success)">—</div>
                    <div class="stat-sub">en camino</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Ingresos totales</div>
                    <div class="stat-value" id="stat-ped-ingresos" style="font-size:22px">—</div>
                    <div class="stat-sub">COP</div>
                </div>
            </div>

            <!-- Tabla pedidos -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Pedidos</span>
                    <div class="filtros-bar">
                        <div class="search-box">
                            <svg class="search-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                            <input type="text" id="buscador-ped" placeholder="Buscar por cliente…" oninput="filtrarPedidos()">
                        </div>
                        <button class="filtro-btn activo" onclick="setFiltroPedido('todos', this)">Todos</button>
                        <button class="filtro-btn" onclick="setFiltroPedido('pendiente', this)">Pendientes</button>
                        <button class="filtro-btn" onclick="setFiltroPedido('procesando', this)">Procesando</button>
                        <button class="filtro-btn" onclick="setFiltroPedido('enviado', this)">Enviados</button>
                        <button class="filtro-btn" onclick="setFiltroPedido('entregado', this)">Entregados</button>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Cliente</th>
                            <th>Ciudad</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tabla-pedidos">
                        <tr class="loading-row"><td colspan="6">Cargando pedidos…</td></tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- ══════════ SECCIÓN CLIENTES ══════════ -->
    <div id="sec-clientes" class="seccion">
        <div class="content">
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

    <!-- ══════════ SECCIÓN REPORTES ══════════ -->
    <div id="sec-reportes" class="seccion">
        <div class="content">

            <!-- KPIs -->
            <div class="rpt-kpis" id="rpt-kpis">
                <div class="rpt-kpi-card">
                    <div class="rpt-kpi-icon ventas">💰</div>
                    <div>
                        <div class="rpt-kpi-label">Ventas hoy</div>
                        <div class="rpt-kpi-value" id="rpt-ventas-hoy">—</div>
                        <div class="rpt-kpi-sub">COP</div>
                    </div>
                </div>
                <div class="rpt-kpi-card">
                    <div class="rpt-kpi-icon mes">📊</div>
                    <div>
                        <div class="rpt-kpi-label">Ventas del mes</div>
                        <div class="rpt-kpi-value" id="rpt-ventas-mes">—</div>
                        <div class="rpt-kpi-sub">COP</div>
                    </div>
                </div>
                <div class="rpt-kpi-card">
                    <div class="rpt-kpi-icon pedidos">📦</div>
                    <div>
                        <div class="rpt-kpi-label">Pedidos hoy</div>
                        <div class="rpt-kpi-value" id="rpt-pedidos-hoy">—</div>
                        <div class="rpt-kpi-sub">registrados</div>
                    </div>
                </div>
                <div class="rpt-kpi-card">
                    <div class="rpt-kpi-icon ticket">🎫</div>
                    <div>
                        <div class="rpt-kpi-label">Ticket promedio</div>
                        <div class="rpt-kpi-value" id="rpt-ticket">—</div>
                        <div class="rpt-kpi-sub">COP</div>
                    </div>
                </div>
                <div class="rpt-kpi-card">
                    <div class="rpt-kpi-icon producto">☕</div>
                    <div>
                        <div class="rpt-kpi-label">Más vendido</div>
                        <div class="rpt-kpi-value" id="rpt-top-producto" style="font-size:16px">—</div>
                        <div class="rpt-kpi-sub" id="rpt-top-qty"></div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="rpt-filtros">
                <span class="rpt-filtros-label">Período:</span>
                <button class="filtro-btn" onclick="rptFiltroRapido('hoy', this)">Hoy</button>
                <button class="filtro-btn" onclick="rptFiltroRapido('7dias', this)">7 días</button>
                <button class="filtro-btn activo" onclick="rptFiltroRapido('30dias', this)">30 días</button>
                <button class="filtro-btn" onclick="rptFiltroRapido('mes', this)">Este mes</button>
                <button class="filtro-btn" onclick="rptFiltroRapido('anio', this)">Este año</button>
                <span class="rpt-sep">|</span>
                <input type="date" class="rpt-fecha-input" id="rpt-desde">
                <span class="rpt-sep">a</span>
                <input type="date" class="rpt-fecha-input" id="rpt-hasta">
                <button class="rpt-btn-aplicar" onclick="rptAplicarFechas()">Aplicar</button>
            </div>

            <!-- Gráficos -->
            <div class="rpt-charts-grid">
                <div class="rpt-chart-card">
                    <div class="rpt-chart-title">Ventas por día</div>
                    <div class="rpt-chart-wrap"><canvas id="chartVentasDiarias"></canvas></div>
                </div>
                <div class="rpt-chart-card">
                    <div class="rpt-chart-title">Productos más vendidos</div>
                    <div class="rpt-chart-wrap"><canvas id="chartProductosTop"></canvas></div>
                </div>
                <div class="rpt-chart-card">
                    <div class="rpt-chart-title">Ventas por categoría</div>
                    <div class="rpt-chart-wrap"><canvas id="chartCategorias"></canvas></div>
                </div>
                <div class="rpt-chart-card">
                    <div class="rpt-chart-title">Tendencia mensual</div>
                    <div class="rpt-chart-wrap"><canvas id="chartTendencia"></canvas></div>
                </div>
            </div>

            <!-- Exportación -->
            <div class="rpt-export-bar">
                <button class="rpt-btn-export" onclick="rptExportarExcel()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h8M8 9h2"/></svg>
                    Exportar Excel
                </button>
                <button class="rpt-btn-export" onclick="rptExportarPDF()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                    Exportar PDF
                </button>
                <button class="rpt-btn-export" onclick="window.print()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Imprimir
                </button>
            </div>

            <!-- Tabla detalle -->
            <div class="rpt-table-card">
                <div class="rpt-table-header">
                    <span class="card-title">Detalle por fecha</span>
                    <span class="rpt-total-badge" id="rpt-total-badge">—</span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Pedidos</th>
                            <th>Ingresos</th>
                            <th>Ticket prom.</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="rpt-tabla-body">
                        <tr class="loading-row"><td colspan="5">Seleccione un período para ver el reporte.</td></tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- ══════════ SECCIÓN ANUNCIOS & POPUPS ══════════ -->
    <div id="sec-anuncios" class="seccion">
        <div class="content">

            <!-- Stats Anuncios -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total anuncios</div>
                    <div class="stat-value" id="stat-anu-total">—</div>
                    <div class="stat-sub">configurados en el sistema</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Anuncio popup activo</div>
                    <div class="stat-value" id="stat-anu-activo" style="font-size:18px;line-height:1.3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">—</div>
                    <div class="stat-sub" id="stat-anu-sub">Se muestra automáticamente al entrar</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Estado del Modal Popup</div>
                    <div class="stat-value" id="stat-anu-estado" style="font-size:22px;">—</div>
                    <div class="stat-sub">Control global de visualización</div>
                </div>
            </div>

            <!-- Layout 2 columnas: Lista a la izquierda, Formulario a la derecha -->
            <div class="layout-grid">

                <!-- Tabla de Anuncios -->
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Anuncios en el sistema</span>
                        <div class="search-box">
                            <input type="text" id="buscador-anu" placeholder="Buscar anuncios…" oninput="filtrarAnuncios()">
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Anuncio</th>
                                <th>Tipo</th>
                                <th>Visible</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-anuncios">
                            <tr class="loading-row"><td colspan="4">Cargando anuncios…</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Formulario Crear / Editar -->
                <div class="card form-card" id="form-anu-card">
                    <div class="card-header">
                        <span class="card-title" id="form-anu-title">Crear Anuncio</span>
                        <button class="btn btn-secondary" id="btn-cancel-anu-edit" style="display:none;padding:4px 10px;font-size:12px;" onclick="cancelarEdicionAnuncio()">Cancelar edición</button>
                    </div>
                    <div class="card-body" style="padding:20px 24px;display:flex;flex-direction:column;gap:14px;">
                        <input type="hidden" id="f-anu-id">
                        <input type="hidden" id="f-anu-imagen-existente">

                        <!-- Selector de Categoría / Tipo -->
                        <div>
                            <label class="form-label" style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--text-soft);margin-bottom:6px;display:block;">1. Tipo de Anuncio</label>
                            <select id="f-anu-tipo" class="form-control" style="width:100%;padding:10px 12px;border:1.5px solid rgba(92,52,32,0.2);border-radius:8px;font-size:13.5px;background:white;font-weight:500;" onchange="cambiarTipoAnuncio(this.value, true)">
                                <option value="partido">⚽ Transmisión de Partido / Deporte en Vivo</option>
                                <option value="promo">🏷️ Promoción / Descuento / Oferta Especial</option>
                                <option value="evento">🎉 Evento Especial / Noche de Música / Cata</option>
                                <option value="general">📢 Comunicado General / Horarios / Novedades</option>
                            </select>
                        </div>

                        <!-- Textos Principales -->
                        <div>
                            <label class="form-label" style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--text-soft);margin-bottom:4px;display:block;">2. Título Principal *</label>
                            <input type="text" id="f-anu-titulo" class="form-control" style="width:100%;padding:9px 12px;border:1px solid rgba(92,52,32,0.18);border-radius:8px;font-size:13.5px;" placeholder="Ej: La Selección Colombia se vive diferente aquí">
                            <small style="color:var(--text-soft);font-size:11px;display:block;margin-top:2px;">El texto se mostrará con la tipografía editorial de Tantico.</small>
                        </div>

                        <div>
                            <label class="form-label" style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--text-soft);margin-bottom:4px;display:block;">3. Subtítulo / Descripción</label>
                            <textarea id="f-anu-subtitulo" class="form-control" rows="2" style="width:100%;padding:8px 12px;border:1px solid rgba(92,52,32,0.18);border-radius:8px;font-size:13px;font-family:sans-serif;" placeholder="Cada gol y cada jugada en pantallas de alto nivel. Café, comida y el mejor ambiente..."></textarea>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                            <div>
                                <label class="form-label" style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--text-soft);margin-bottom:4px;display:block;">Etiqueta Superior (Eyebrow)</label>
                                <input type="text" id="f-anu-eyebrow" class="form-control" style="width:100%;padding:8px 10px;border:1px solid rgba(92,52,32,0.18);border-radius:8px;font-size:13px;" placeholder="Transmisión en vivo · Neiva, Huila">
                            </div>
                            <div>
                                <label class="form-label" style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--text-soft);margin-bottom:4px;display:block;">Insignia Inferior (Badge)</label>
                                <input type="text" id="f-anu-badge" class="form-control" style="width:100%;padding:8px 10px;border:1px solid rgba(92,52,32,0.18);border-radius:8px;font-size:13px;" placeholder="Café Región · Neiva, Huila">
                            </div>
                        </div>

                        <!-- Carga de Imagen -->
                        <div>
                            <label class="form-label" style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--text-soft);margin-bottom:4px;display:block;">4. Fotografía Lateral</label>
                            <div class="upload-area" id="anuUploadArea" onclick="document.getElementById('anuImagenInput').click()" style="border:1.5px dashed rgba(92,52,32,0.25);border-radius:10px;padding:14px;text-align:center;cursor:pointer;position:relative;background:var(--cafe-50);">
                                <input type="file" id="anuImagenInput" accept="image/*" style="display:none;" onchange="previewAnuImagen(event)">
                                <img id="anuPreview" src="" alt="" style="max-height:100px;border-radius:8px;display:none;margin:0 auto 8px;">
                                <div id="anuUploadPrompt">
                                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="color:var(--cafe-500);margin:0 auto 4px;display:block;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                                    <span style="font-size:12px;color:var(--text-soft);">Click para subir foto (JPG, PNG, WebP) o se usará la foto predeterminada</span>
                                </div>
                            </div>
                        </div>

                        <!-- ══ PANEL DINÁMICO SEGÚN LA CATEGORÍA ══ -->
                        <div id="panel-categoria-wrap" style="background:#FAF4EE;border:1.5px solid rgba(196,137,95,0.25);border-radius:12px;padding:14px;display:flex;flex-direction:column;gap:12px;">

                            <!-- Sub-bloque 1: Si es PARTIDO -->
                            <div id="box-partido" style="display:flex;flex-direction:column;gap:10px;">
                                <div style="display:flex;align-items:center;justify-content:space-between;">
                                    <span style="font-size:12px;font-weight:700;color:var(--cafe-800);text-transform:uppercase;letter-spacing:0.5px;">⚽ Datos del Partido &amp; Cuenta Regresiva</span>
                                    <input type="hidden" id="f-anu-mostrar-partido" value="1">
                                </div>

                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                                    <div>
                                        <label style="font-size:10.5px;font-weight:600;color:var(--text-soft);display:block;margin-bottom:2px;">Fecha del Partido (Texto)</label>
                                        <input type="text" id="f-anu-fecha-texto" class="form-control" style="width:100%;padding:7px 8px;font-size:12.5px;border:1px solid rgba(92,52,32,0.18);border-radius:6px;background:white;" placeholder="Lunes 01 de Junio - 6:00 PM">
                                    </div>
                                    <div>
                                        <label style="font-size:10.5px;font-weight:600;color:var(--text-soft);display:block;margin-bottom:2px;">Inicio del Partido (Reloj Cuenta Regresiva)</label>
                                        <input type="datetime-local" id="f-anu-fecha-objetivo" class="form-control" style="width:100%;padding:7px 8px;font-size:12.5px;border:1px solid rgba(92,52,32,0.18);border-radius:6px;background:white;">
                                    </div>
                                </div>

                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                                    <div>
                                        <label style="font-size:10.5px;font-weight:600;color:var(--text-soft);display:block;margin-bottom:2px;">Equipo 1 (Local)</label>
                                        <div style="display:flex;gap:4px;">
                                            <input type="text" id="f-anu-eq1-nom" placeholder="Colombia" style="flex:1.8;padding:7px 8px;font-size:12px;border:1px solid rgba(92,52,32,0.18);border-radius:6px;background:white;">
                                            <select id="f-anu-eq1-ban" style="flex:1.4;padding:7px 4px;font-size:12px;border:1px solid rgba(92,52,32,0.18);border-radius:6px;background:white;">
                                                <option value="co">🇨🇴 Colombia</option>
                                                <option value="cr">🇨🇷 Costa Rica</option>
                                                <option value="ar">🇦🇷 Argentina</option>
                                                <option value="br">🇧🇷 Brasil</option>
                                                <option value="es">🇪🇸 España</option>
                                                <option value="us">🇺🇸 EE.UU.</option>
                                                <option value="mx">🇲🇽 México</option>
                                                <option value="de">🇩🇪 Alemania</option>
                                                <option value="pe">🇵🇪 Perú</option>
                                                <option value="uy">🇺🇾 Uruguay</option>
                                                <option value="cl">🇨🇱 Chile</option>
                                                <option value="ec">🇪🇨 Ecuador</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label style="font-size:10.5px;font-weight:600;color:var(--text-soft);display:block;margin-bottom:2px;">Equipo 2 (Visitante)</label>
                                        <div style="display:flex;gap:4px;">
                                            <input type="text" id="f-anu-eq2-nom" placeholder="Costa Rica" style="flex:1.8;padding:7px 8px;font-size:12px;border:1px solid rgba(92,52,32,0.18);border-radius:6px;background:white;">
                                            <select id="f-anu-eq2-ban" style="flex:1.4;padding:7px 4px;font-size:12px;border:1px solid rgba(92,52,32,0.18);border-radius:6px;background:white;">
                                                <option value="cr">🇨🇷 Costa Rica</option>
                                                <option value="co">🇨🇴 Colombia</option>
                                                <option value="ar">🇦🇷 Argentina</option>
                                                <option value="br">🇧🇷 Brasil</option>
                                                <option value="es">🇪🇸 España</option>
                                                <option value="us">🇺🇸 EE.UU.</option>
                                                <option value="mx">🇲🇽 México</option>
                                                <option value="de">🇩🇪 Alemania</option>
                                                <option value="pe">🇵🇪 Perú</option>
                                                <option value="uy">🇺🇾 Uruguay</option>
                                                <option value="cl">🇨🇱 Chile</option>
                                                <option value="ec">🇪🇨 Ecuador</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sub-bloque 2: Si es PROMO / EVENTO / GENERAL (Campos de Botón de Acción y Vigencia) -->
                            <div id="box-no-partido" style="display:none;flex-direction:column;gap:10px;">
                                <div style="display:flex;align-items:center;justify-content:space-between;">
                                    <span style="font-size:12px;font-weight:700;color:var(--cafe-800);text-transform:uppercase;letter-spacing:0.5px;">🏷️ Botón de Acción &amp; Llamado</span>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                                    <div>
                                        <label style="font-size:10.5px;font-weight:600;color:var(--text-soft);display:block;margin-bottom:2px;">Texto del Botón (Opcional)</label>
                                        <input type="text" id="f-anu-btn-txt" class="form-control" style="width:100%;padding:7px 8px;font-size:12.5px;border:1px solid rgba(92,52,32,0.18);border-radius:6px;background:white;" placeholder="Ej: Ver Catálogo de Productos">
                                    </div>
                                    <div>
                                        <label style="font-size:10.5px;font-weight:600;color:var(--text-soft);display:block;margin-bottom:2px;">Enlace de Destino</label>
                                        <input type="text" id="f-anu-btn-link" class="form-control" style="width:100%;padding:7px 8px;font-size:12.5px;border:1px solid rgba(92,52,32,0.18);border-radius:6px;background:white;" placeholder="Ej: /cafe/includes/servicios.php">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- ══ 3 PUNTOS DESTACADOS CON SELECTORES VISUALES ══ -->
                        <div>
                            <label style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--text-soft);margin-bottom:6px;display:block;">
                                5. Tres Puntos Destacados (Selecciona el Icono y escribe el Texto)
                            </label>
                            
                            <div style="display:flex;flex-direction:column;gap:8px;">
                                
                                <!-- Punto 1 -->
                                <div style="display:flex;gap:8px;align-items:center;background:var(--cafe-50);padding:8px 10px;border-radius:8px;border:1px solid rgba(92,52,32,0.1);">
                                    <span style="font-size:11px;font-weight:700;color:var(--cafe-600);width:16px;">#1</span>
                                    <select id="f-anu-f1-ico" style="flex:1.4;padding:6px 8px;font-size:12px;border:1px solid rgba(92,52,32,0.18);border-radius:6px;background:white;">
                                        <option value="fa-solid fa-tv">📺 Pantallas 4K / TV</option>
                                        <option value="fa-solid fa-users">👥 Ambiente / Grupos</option>
                                        <option value="fa-brands fa-java">☕ Bebidas / Café</option>
                                        <option value="fa-solid fa-mug-hot">☕ Café Caliente / Taza</option>
                                        <option value="fa-solid fa-tag">🏷️ Descuento / Oferta</option>
                                        <option value="fa-solid fa-cake-candles">🎂 Repostería / Tartas</option>
                                        <option value="fa-solid fa-bread-slice">🥐 Panadería Artesanal</option>
                                        <option value="fa-solid fa-gift">🎁 Regalo / Cortesía</option>
                                        <option value="fa-solid fa-music">🎵 Música en Vivo</option>
                                        <option value="fa-solid fa-martini-glass-citrus">🍸 Coctelería de Café</option>
                                        <option value="fa-solid fa-wifi">📶 Wi-Fi &amp; Coworking</option>
                                        <option value="fa-solid fa-paw">🐾 Pet Friendly</option>
                                        <option value="fa-solid fa-snowflake">❄️ Aire Acondicionado</option>
                                        <option value="fa-solid fa-ticket">🎟️ Entrada / Reserva</option>
                                        <option value="fa-solid fa-star">⭐ Calidad SCA / Especial</option>
                                        <option value="fa-solid fa-clock">🕒 Horario / Atención</option>
                                        <option value="fa-solid fa-location-dot">📍 Ubicación / Sede</option>
                                    </select>
                                    <input type="text" id="f-anu-f1-txt" placeholder="Texto (ej: Pantallas 4K)" style="flex:1.6;padding:6px 8px;font-size:12px;border:1px solid rgba(92,52,32,0.18);border-radius:6px;background:white;">
                                </div>

                                <!-- Punto 2 -->
                                <div style="display:flex;gap:8px;align-items:center;background:var(--cafe-50);padding:8px 10px;border-radius:8px;border:1px solid rgba(92,52,32,0.1);">
                                    <span style="font-size:11px;font-weight:700;color:var(--cafe-600);width:16px;">#2</span>
                                    <select id="f-anu-f2-ico" style="flex:1.4;padding:6px 8px;font-size:12px;border:1px solid rgba(92,52,32,0.18);border-radius:6px;background:white;">
                                        <option value="fa-solid fa-users" selected>👥 Ambiente / Grupos</option>
                                        <option value="fa-solid fa-tv">📺 Pantallas 4K / TV</option>
                                        <option value="fa-brands fa-java">☕ Bebidas / Café</option>
                                        <option value="fa-solid fa-mug-hot">☕ Café Caliente / Taza</option>
                                        <option value="fa-solid fa-tag">🏷️ Descuento / Oferta</option>
                                        <option value="fa-solid fa-cake-candles">🎂 Repostería / Tartas</option>
                                        <option value="fa-solid fa-bread-slice">🥐 Panadería Artesanal</option>
                                        <option value="fa-solid fa-gift">🎁 Regalo / Cortesía</option>
                                        <option value="fa-solid fa-music">🎵 Música en Vivo</option>
                                        <option value="fa-solid fa-martini-glass-citrus">🍸 Coctelería de Café</option>
                                        <option value="fa-solid fa-wifi">📶 Wi-Fi &amp; Coworking</option>
                                        <option value="fa-solid fa-paw">🐾 Pet Friendly</option>
                                        <option value="fa-solid fa-snowflake">❄️ Aire Acondicionado</option>
                                        <option value="fa-solid fa-ticket">🎟️ Entrada / Reserva</option>
                                        <option value="fa-solid fa-star">⭐ Calidad SCA / Especial</option>
                                        <option value="fa-solid fa-clock">🕒 Horario / Atención</option>
                                        <option value="fa-solid fa-location-dot">📍 Ubicación / Sede</option>
                                    </select>
                                    <input type="text" id="f-anu-f2-txt" placeholder="Texto (ej: Ambiente futbolero)" style="flex:1.6;padding:6px 8px;font-size:12px;border:1px solid rgba(92,52,32,0.18);border-radius:6px;background:white;">
                                </div>

                                <!-- Punto 3 -->
                                <div style="display:flex;gap:8px;align-items:center;background:var(--cafe-50);padding:8px 10px;border-radius:8px;border:1px solid rgba(92,52,32,0.1);">
                                    <span style="font-size:11px;font-weight:700;color:var(--cafe-600);width:16px;">#3</span>
                                    <select id="f-anu-f3-ico" style="flex:1.4;padding:6px 8px;font-size:12px;border:1px solid rgba(92,52,32,0.18);border-radius:6px;background:white;">
                                        <option value="fa-brands fa-java" selected>☕ Bebidas / Café</option>
                                        <option value="fa-solid fa-tv">📺 Pantallas 4K / TV</option>
                                        <option value="fa-solid fa-users">👥 Ambiente / Grupos</option>
                                        <option value="fa-solid fa-mug-hot">☕ Café Caliente / Taza</option>
                                        <option value="fa-solid fa-tag">🏷️ Descuento / Oferta</option>
                                        <option value="fa-solid fa-cake-candles">🎂 Repostería / Tartas</option>
                                        <option value="fa-solid fa-bread-slice">🥐 Panadería Artesanal</option>
                                        <option value="fa-solid fa-gift">🎁 Regalo / Cortesía</option>
                                        <option value="fa-solid fa-music">🎵 Música en Vivo</option>
                                        <option value="fa-solid fa-martini-glass-citrus">🍸 Coctelería de Café</option>
                                        <option value="fa-solid fa-wifi">📶 Wi-Fi &amp; Coworking</option>
                                        <option value="fa-solid fa-paw">🐾 Pet Friendly</option>
                                        <option value="fa-solid fa-snowflake">❄️ Aire Acondicionado</option>
                                        <option value="fa-solid fa-ticket">🎟️ Entrada / Reserva</option>
                                        <option value="fa-solid fa-star">⭐ Calidad SCA / Especial</option>
                                        <option value="fa-solid fa-clock">🕒 Horario / Atención</option>
                                        <option value="fa-solid fa-location-dot">📍 Ubicación / Sede</option>
                                    </select>
                                    <input type="text" id="f-anu-f3-txt" placeholder="Texto (ej: Bebidas premium)" style="flex:1.6;padding:6px 8px;font-size:12px;border:1px solid rgba(92,52,32,0.18);border-radius:6px;background:white;">
                                </div>

                            </div>
                        </div>

                        <!-- Estado Activo & Preview -->
                        <div style="display:flex;align-items:center;justify-content:space-between;padding-top:8px;border-top:1px solid rgba(92,52,32,0.08);">
                            <label class="switch-wrap">
                                <input type="checkbox" id="f-anu-activo" class="switch-input" checked>
                                <span class="switch-slider"></span>
                                <span class="switch-label">Publicar y activar popup de inmediato</span>
                            </label>
                            <button type="button" class="btn-preview-popup" onclick="probarPreviewFormulario()">
                                👁️ Vista previa
                            </button>
                        </div>

                        <button id="btnGuardarAnuncio" class="btn-submit" onclick="guardarAnuncio()" style="margin-top:6px;">
                            Guardar Anuncio
                        </button>
                    </div>
                </div>

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
        <div class="modal-body" id="modal-body"></div>
    </div>
</div>

<!-- ══════════ MODAL DETALLE PEDIDO ══════════ -->
<div class="modal-overlay" id="modal-pedido">
    <div class="modal-box modal-pedido-box">
        <div class="modal-header">
            <span class="modal-title">Detalle del pedido</span>
            <button class="modal-close" onclick="cerrarModalPedido()">✕</button>
        </div>
        <div class="modal-body" id="modal-pedido-body"></div>
    </div>
</div>

<!-- ══════════ MODAL PREVIEW ANUNCIO ══════════ -->
<div class="modal-overlay" id="modal-preview-anuncio" onclick="if(event.target===this)cerrarModalPreviewAnuncio()">
    <div class="modal-box modal-anu-preview-box">
        <div id="anu-modal-preview-container"></div>
    </div>
</div>

<div class="toast" id="toast"></div>

<script>
const API     = '../api/products.php';
const API_CLI = '../api/clientes.php';
const API_PED = '../api/orders.php';
const API_RPT = '../api/reports.php';
const API_ANU = '../api/announcements.php';
const token   = localStorage.getItem('token');

if (!token) location.href = 'login.php';

// Verificar expiración del token al cargar la página
try {
    const payload = JSON.parse(atob(token.split('.')[1]));
    if (payload.exp && payload.exp < Math.floor(Date.now() / 1000)) {
        localStorage.removeItem('token');
        location.href = 'login.php';
    }
    const u = payload.sub || 'Admin';
    document.getElementById('usuario-label').textContent = u;
    document.getElementById('usuario-avatar').textContent = u[0].toUpperCase();
} catch (e) {
    localStorage.removeItem('token');
    location.href = 'login.php';
}

// ── Navegación ──
const titulos = {
    productos: 'Gestión de Productos',
    pedidos: 'Gestión de Pedidos',
    clientes: 'Base de Clientes',
    anuncios: 'Gestión de Anuncios & Popups',
    reportes: 'Reportes y Analítica'
};

function mostrarSeccion(nombre, el) {
    document.querySelectorAll('.seccion').forEach(s => s.classList.remove('activa'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById('sec-' + nombre).classList.add('activa');
    el.classList.add('active');
    document.getElementById('topbar-titulo').textContent = titulos[nombre];
    if (nombre === 'clientes')  cargarClientes();
    if (nombre === 'productos') cargar();
    if (nombre === 'pedidos')   cargarPedidos();
    if (nombre === 'anuncios')  cargarAnuncios();
    if (nombre === 'reportes')  rptFiltroRapido('30dias');
    return false;
}

// ── Toast ──
function mostrarToast(msg, tipo = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast show ' + tipo;
    setTimeout(() => t.className = 'toast', 3000);
}

// ── Helpers ──
const BADGE_CLASS = { nuevo:'badge-nuevo', oferta:'badge-oferta', popular:'badge-popular', especial:'badge-especial' };
function getBadgeClass(tipo) { return BADGE_CLASS[tipo] || 'badge-default'; }
function formatPrecio(n) { return Math.round(n).toLocaleString('es-CO'); }
function formatFecha(f) {
    if (!f) return '—';
    const d = new Date(f);
    return d.toLocaleDateString('es-CO', { day:'2-digit', month:'short', year:'numeric' });
}

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

const CATEGORIAS = {
    'tueste-claro':    'Tueste Claro',
    'tueste-medio':    'Tueste Medio',
    'tueste-oscuro':   'Tueste Oscuro',
    'capsulas':        'Cápsulas',
    'origen-especial': 'Origen Especial',
    'reposteria':      'Tortas & Repostería',
    'panaderia':       'Panadería Artesanal',
    'desayunos':       'Desayunos & Brunch'
};
let todosLosProductos = [];

function resolverRutaImg(img) {
    if (!img) return '';
    if (img.startsWith('http://') || img.startsWith('https://') || img.startsWith('//')) return img;
    return '../' + img.replace(/^\/+/, '');
}

function renderTabla(productos) {
    const tbody = document.getElementById('tabla');
    if (!productos.length) {
        tbody.innerHTML = `<tr><td colspan="4"><div class="empty-state"><p>No hay productos aún</p></div></td></tr>`;
        return;
    }
    tbody.innerHTML = productos.map(p => {
        const ruta = resolverRutaImg(p.imagen);
        const thumb = ruta ? `<div class="prod-thumb"><img src="${ruta}" alt="${p.nombre}"></div>` : `<div class="prod-thumb">☕</div>`;
        const catNombre   = CATEGORIAS[p.categoria] || p.categoria || '—';
        const precioAntes = p.precio_antes ? `<div class="precio-antes">$${formatPrecio(p.precio_antes)}</div>` : '';
        const badgeCell   = p.badge ? `<span class="badge ${getBadgeClass(p.badge_tipo)}">${p.badge}</span>` : '—';
        return `<tr>
            <td><div class="prod-info">${thumb}<div><div class="prod-name">${p.nombre}</div><div class="prod-cat">${catNombre}${p.unidad ? ' · ' + p.unidad : ''}</div></div></div></td>
            <td><div class="precio">$${formatPrecio(p.precio)}</div>${precioAntes}</td>
            <td>${badgeCell}</td>
            <td style="white-space:nowrap">
                <button class="btn-edit" onclick="editarProducto(${p.id})">Editar</button>
                <button class="btn-delete" onclick="eliminar(${p.id})">Eliminar</button>
            </td>
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

function editarProducto(id) {
    const p = todosLosProductos.find(x => x.id == id);
    if (!p) return;

    document.getElementById('f-id').value           = p.id;
    document.getElementById('f-nombre').value       = p.nombre || '';
    document.getElementById('f-descripcion').value  = p.descripcion || '';
    document.getElementById('f-categoria').value    = p.categoria || '';
    document.getElementById('f-unidad').value       = p.unidad || '';
    document.getElementById('f-precio').value       = p.precio || '';
    document.getElementById('f-precio-antes').value = p.precio_antes || '';
    document.getElementById('f-badge').value        = p.badge || '';
    document.getElementById('f-badge-tipo').value   = p.badge_tipo || '';

    actualizarBadgePreview();

    const ruta = resolverRutaImg(p.imagen);
    if (ruta) {
        document.getElementById('preview').src = ruta;
        document.getElementById('uploadArea').classList.add('has-image');
    } else {
        quitarImagen({ stopPropagation: () => {}, preventDefault: () => {} });
    }

    document.getElementById('form-card-title').textContent = 'Editar producto #' + p.id;
    document.getElementById('btnAgregarTexto').textContent  = 'Actualizar producto';
    document.getElementById('btn-cancel-edit').style.display = 'inline-block';

    document.querySelector('.form-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function cancelarEdicion() {
    limpiarFormulario();
    document.getElementById('form-card-title').textContent = 'Agregar producto';
    document.getElementById('btnAgregarTexto').textContent  = 'Agregar producto';
    document.getElementById('btn-cancel-edit').style.display = 'none';
}

function agregar() {
    const id        = document.getElementById('f-id').value;
    const nombre    = document.getElementById('f-nombre').value.trim();
    const precio    = document.getElementById('f-precio').value;
    const categoria = document.getElementById('f-categoria').value;
    if (!nombre || !precio || !categoria) { mostrarToast('Nombre, precio y categoría son obligatorios', 'error'); return; }
    
    const btn = document.getElementById('btnAgregar');
    const textoOriginal = document.getElementById('btnAgregarTexto').textContent;
    btn.disabled = true; btn.innerHTML = '⏳ Guardando…';
    
    const fd = new FormData();
    if (id) fd.append('id', id);
    fd.append('nombre', nombre);
    fd.append('descripcion', document.getElementById('f-descripcion').value.trim());
    fd.append('precio', precio);
    fd.append('precio_antes', document.getElementById('f-precio-antes').value || '');
    fd.append('categoria', categoria);
    fd.append('unidad', document.getElementById('f-unidad').value.trim());
    fd.append('badge', document.getElementById('f-badge').value.trim());
    fd.append('badge_tipo', document.getElementById('f-badge-tipo').value);
    
    const imgFile = document.getElementById('imagenInput').files[0];
    if (imgFile) fd.append('imagen', imgFile);

    fetch(API, { method:'POST', headers:{'Authorization':'Bearer '+token}, body:fd })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                mostrarToast(id ? 'Producto actualizado ✓' : 'Producto agregado ✓');
                cancelarEdicion();
                cargar();
            } else {
                mostrarToast(d.error || 'Error al guardar', 'error');
            }
        })
        .catch(() => mostrarToast('Error de conexión', 'error'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = `<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> <span id="btnAgregarTexto">${textoOriginal}</span>`;
        });
}

function eliminar(id) {
    if (!confirm('¿Eliminar este producto?')) return;
    fetch(API, { method:'DELETE', headers:{'Authorization':'Bearer '+token,'Content-Type':'application/json'}, body:JSON.stringify({id}) })
        .then(r => r.json())
        .then(d => { if (d.success) { mostrarToast('Producto eliminado'); cargar(); } else mostrarToast(d.error||'Error','error'); });
}

function limpiarFormulario() {
    ['f-id','f-nombre','f-descripcion','f-precio','f-precio-antes','f-unidad','f-badge'].forEach(id => { document.getElementById(id).value=''; });
    document.getElementById('f-categoria').value=''; document.getElementById('f-badge-tipo').value='';
    document.getElementById('imagenInput').value=''; document.getElementById('preview').src='';
    document.getElementById('uploadArea').classList.remove('has-image');
    document.getElementById('badgePreviewWrap').style.display='none';
}

// ══════════════════════════════════
// ── PEDIDOS ──
// ══════════════════════════════════
let todosLosPedidos = [];
let filtroPedidoActivo = 'todos';

const ESTADO_BADGE = {
    pendiente:  'badge-pendiente',
    procesando: 'badge-procesando',
    enviado:    'badge-enviado',
    entregado:  'badge-entregado',
    cancelado:  'badge-cancelado'
};

const ESTADOS = ['pendiente', 'procesando', 'enviado', 'entregado', 'cancelado'];

function estadoBadge(estado) {
    const cls = ESTADO_BADGE[estado] || 'badge-default';
    const label = estado ? estado.charAt(0).toUpperCase() + estado.slice(1) : '—';
    return `<span class="badge ${cls}">${label}</span>`;
}

function cargarPedidos() {
    document.getElementById('tabla-pedidos').innerHTML = '<tr class="loading-row"><td colspan="6">Cargando pedidos…</td></tr>';
    fetch(API_PED, { headers: { 'Authorization': 'Bearer ' + token } })
        .then(r => {
            if (r.status === 401) {
                localStorage.removeItem('token');
                location.href = 'login.php';
                throw new Error('Token expirado');
            }
            return r.json();
        })
        .then(d => {
            if (d.success) {
                todosLosPedidos = d.pedidos;
                actualizarStatsPedidos(d.pedidos);
                renderPedidos(d.pedidos);
            } else {
                mostrarToast('Error al cargar pedidos', 'error');
                document.getElementById('tabla-pedidos').innerHTML = '<tr class="loading-row"><td colspan="6">No se pudieron cargar los pedidos.</td></tr>';
            }
        })
        .catch(() => {
            mostrarToast('Error de conexión', 'error');
            document.getElementById('tabla-pedidos').innerHTML = '<tr class="loading-row"><td colspan="6">Error de conexión.</td></tr>';
        });
}

function actualizarStatsPedidos(pedidos) {
    const pendientes = pedidos.filter(p => p.estado === 'pendiente').length;
    const enviados   = pedidos.filter(p => p.estado === 'enviado').length;
    const ingresos   = pedidos
        .filter(p => p.estado !== 'cancelado')
        .reduce((s, p) => s + parseFloat(p.total || 0), 0);

    document.getElementById('stat-ped-total').textContent     = pedidos.length;
    document.getElementById('stat-ped-pendientes').textContent = pendientes;
    document.getElementById('stat-ped-enviados').textContent   = enviados;
    document.getElementById('stat-ped-ingresos').textContent   = '$' + formatPrecio(ingresos);
}

function renderPedidos(pedidos) {
    const tbody = document.getElementById('tabla-pedidos');
    if (!pedidos.length) {
        tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state"><p>No hay pedidos aún</p></div></td></tr>`;
        return;
    }
    tbody.innerHTML = pedidos.map(p => {
        const nombre = [p.nombre, p.apellido].filter(Boolean).join(' ') || '—';
        return `<tr>
            <td>
                <div class="pedido-id">#${p.id}</div>
                <div class="pedido-fecha">${formatFecha(p.fecha)}</div>
            </td>
            <td>
                <div class="pedido-cliente-nombre">${nombre}</div>
                <div class="pedido-cliente-email">${p.email || '—'}</div>
            </td>
            <td>${p.ciudad || '—'}</td>
            <td><span class="pedido-total">$${formatPrecio(p.total)}</span></td>
            <td>${estadoBadge(p.estado)}</td>
            <td><button class="btn-ver" onclick="verPedido(${p.id})">Ver</button></td>
        </tr>`;
    }).join('');
}

function filtrarPedidos() {
    const q = document.getElementById('buscador-ped').value.toLowerCase();
    let lista = todosLosPedidos.filter(p => {
        const nombre = [p.nombre, p.apellido].filter(Boolean).join(' ').toLowerCase();
        return nombre.includes(q) || (p.email||'').toLowerCase().includes(q);
    });
    if (filtroPedidoActivo !== 'todos') lista = lista.filter(p => p.estado === filtroPedidoActivo);
    renderPedidos(lista);
}

function setFiltroPedido(filtro, el) {
    filtroPedidoActivo = filtro;
    document.querySelectorAll('#sec-pedidos .filtro-btn').forEach(b => b.classList.remove('activo'));
    el.classList.add('activo');
    filtrarPedidos();
}

function verPedido(id) {
    const p = todosLosPedidos.find(x => x.id == id);
    if (!p) return;
    const modal = document.getElementById('modal-pedido');
    const body  = document.getElementById('modal-pedido-body');

    const nombre = [p.nombre, p.apellido].filter(Boolean).join(' ') || '—';

    // Items del pedido
    let itemsHTML = '<p style="font-size:12px;color:var(--text-soft)">Sin detalle de productos</p>';
    if (p.items && p.items.length) {
        itemsHTML = `
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th style="text-align:center">Cant.</th>
                        <th style="text-align:right">Precio u.</th>
                        <th style="text-align:right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    ${p.items.map(it => `
                        <tr>
                            <td>${it.nombre || 'Producto #' + it.producto_id}</td>
                            <td style="text-align:center">${it.cantidad}</td>
                            <td style="text-align:right">$${formatPrecio(it.precio_unitario)}</td>
                            <td style="text-align:right">$${formatPrecio(it.cantidad * it.precio_unitario)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>`;
    }

    // Opciones del select de estado
    const opcionesEstado = ESTADOS.map(e =>
        `<option value="${e}" ${p.estado === e ? 'selected' : ''}>${e.charAt(0).toUpperCase() + e.slice(1)}</option>`
    ).join('');

    body.innerHTML = `
        <div class="pedido-detalle-header">
            <div>
                <div class="pedido-detalle-id">Pedido #${p.id}</div>
                <div class="pedido-detalle-fecha">${formatFecha(p.fecha)}</div>
            </div>
            ${estadoBadge(p.estado)}
        </div>

        <div class="pedido-info-grid">
            <div class="pedido-info-cell">
                <div class="pedido-info-cell-label">Cliente</div>
                <div class="pedido-info-cell-val">${nombre}</div>
            </div>
            <div class="pedido-info-cell">
                <div class="pedido-info-cell-label">Correo</div>
                <div class="pedido-info-cell-val" style="font-size:12px;word-break:break-all">${p.email || '—'}</div>
            </div>
            <div class="pedido-info-cell">
                <div class="pedido-info-cell-label">Teléfono</div>
                <div class="pedido-info-cell-val">${p.telefono || '—'}</div>
            </div>
            <div class="pedido-info-cell">
                <div class="pedido-info-cell-label">Ciudad</div>
                <div class="pedido-info-cell-val">${p.ciudad || '—'}</div>
            </div>
            <div class="pedido-info-cell" style="grid-column:1/-1">
                <div class="pedido-info-cell-label">Dirección</div>
                <div class="pedido-info-cell-val">${p.direccion || '—'}</div>
            </div>
        </div>

        <div class="modal-section-title" style="padding-top:0;border-top:none;margin-bottom:12px;">Productos</div>
        ${itemsHTML}

        <div class="pedido-total-row">
            <span class="pedido-total-label">Total del pedido</span>
            <span class="pedido-total-val">$${formatPrecio(p.total)}</span>
        </div>

        <div class="modal-estado-row">
            <span class="modal-estado-label">Cambiar estado:</span>
            <select class="modal-estado-select" id="modal-estado-select">${opcionesEstado}</select>
            <button class="btn-guardar-estado" onclick="guardarEstado(${p.id})">Guardar</button>
        </div>
    `;

    modal.classList.add('open');
}

function guardarEstado(pedidoId) {
    const nuevoEstado = document.getElementById('modal-estado-select').value;
    fetch(API_PED, {
        method: 'PATCH',
        headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: pedidoId, estado: nuevoEstado })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            // Actualizar en memoria
            const idx = todosLosPedidos.findIndex(p => p.id == pedidoId);
            if (idx !== -1) todosLosPedidos[idx].estado = nuevoEstado;
            mostrarToast('Estado actualizado ✓');
            cerrarModalPedido();
            filtrarPedidos();
            actualizarStatsPedidos(todosLosPedidos);
        } else {
            mostrarToast(d.error || 'Error al actualizar', 'error');
        }
    })
    .catch(() => mostrarToast('Error de conexión', 'error'));
}

function cerrarModalPedido() {
    document.getElementById('modal-pedido').classList.remove('open');
}

document.getElementById('modal-pedido').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalPedido();
});

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
        let cumpleCell = '—';
        if (c.fecha_nacimiento) {
            const fn = new Date(c.fecha_nacimiento + 'T00:00:00');
            const fnMD = fn.toLocaleDateString('es-CO', {month:'2-digit', day:'2-digit'});
            cumpleCell = fnMD === hoyMD
                ? `<span class="badge badge-cumple">🎂 Hoy</span>`
                : fn.toLocaleDateString('es-CO', {day:'2-digit', month:'short'});
        }
        const ultimoAcceso = formatFecha(c.ultimo_acceso);
        const hace30 = new Date(Date.now() - 30*24*60*60*1000);
        const activo = c.ultimo_acceso && new Date(c.ultimo_acceso) >= hace30;
        const estadoBadgeHTML = activo
            ? `<span class="badge badge-activo">Activo</span>`
            : `<span class="badge badge-inactivo">Inactivo</span>`;
        return `<tr>
            <td><div class="cliente-info">${avatar}<div><div class="cliente-nombre">${c.nombre || '—'}</div><div class="cliente-email">${c.email}</div></div></div></td>
            <td>${formatFecha(c.fecha_registro)}</td>
            <td>${ultimoAcceso}</td>
            <td><span class="pts-chip">⭐ ${parseInt(c.puntos)||0}</span></td>
            <td>${parseInt(c.total_pedidos)||0}</td>
            <td>${cumpleCell}</td>
            <td>${estadoBadgeHTML}</td>
            <td><button class="btn-ver" onclick='verCliente(${JSON.stringify(c)})'>Ver</button></td>
        </tr>`;
    }).join('');
}

function filtrarClientes() {
    const q = document.getElementById('buscador-cli').value.toLowerCase();
    const ahora = new Date();
    const hace30 = new Date(ahora - 30*24*60*60*1000);
    let lista = todosLosClientes.filter(c =>
        (c.nombre||'').toLowerCase().includes(q) || (c.email||'').toLowerCase().includes(q)
    );
    if (filtroActivo === 'con-puntos') lista = lista.filter(c => (parseInt(c.puntos)||0) > 0);
    if (filtroActivo === 'nuevos')     lista = lista.filter(c => new Date(c.fecha_registro) >= hace30);
    renderClientes(lista);
}

function setFiltroCliente(filtro, el) {
    filtroActivo = filtro;
    document.querySelectorAll('#sec-clientes .filtro-btn').forEach(b => b.classList.remove('activo'));
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

function cerrarSesion() { localStorage.removeItem('token'); location.href = 'login.php'; }

cargar();

// ══════════════════════════════════
// ── REPORTES ──
// ══════════════════════════════════

const CAFE_COLORS = ['#7a4a2e','#c4895f','#ddb896','#3d2314','#a0623c','#f0dece','#5c3420','#2d1a0e','#c0392b','#27704a'];
const CAFE_CATEGORIAS = {
    'tueste-claro':'Tueste Claro','tueste-medio':'Tueste Medio','tueste-oscuro':'Tueste Oscuro',
    'capsulas':'Cápsulas','origen-especial':'Origen Especial','sin-categoria':'Sin categoría'
};

let rptCharts = {};
let rptDatosActuales = null;

function formatCOP(n) {
    return '$' + Math.round(n).toLocaleString('es-CO');
}

function rptFechaISO(date) {
    return date.toISOString().split('T')[0];
}

/* ── Filtros rápidos ── */
function rptFiltroRapido(tipo, btn) {
    const hoy = new Date();
    let desde, hasta = rptFechaISO(hoy);
    switch (tipo) {
        case 'hoy':    desde = hasta; break;
        case '7dias':  desde = rptFechaISO(new Date(hoy - 7*86400000)); break;
        case '30dias': desde = rptFechaISO(new Date(hoy - 30*86400000)); break;
        case 'mes':    desde = hoy.getFullYear() + '-' + String(hoy.getMonth()+1).padStart(2,'0') + '-01'; break;
        case 'anio':   desde = hoy.getFullYear() + '-01-01'; break;
        default:       desde = rptFechaISO(new Date(hoy - 30*86400000));
    }
    document.getElementById('rpt-desde').value = desde;
    document.getElementById('rpt-hasta').value = hasta;
    // Actualizar botón activo
    if (btn) {
        document.querySelectorAll('#sec-reportes .rpt-filtros .filtro-btn').forEach(b => b.classList.remove('activo'));
        btn.classList.add('activo');
    }
    cargarReportes(desde, hasta);
}

function rptAplicarFechas() {
    const desde = document.getElementById('rpt-desde').value;
    const hasta = document.getElementById('rpt-hasta').value;
    if (!desde || !hasta) { mostrarToast('Selecciona ambas fechas', 'error'); return; }
    document.querySelectorAll('#sec-reportes .rpt-filtros .filtro-btn').forEach(b => b.classList.remove('activo'));
    cargarReportes(desde, hasta);
}

/* ── Cargar datos del API ── */
function cargarReportes(desde, hasta) {
    document.getElementById('rpt-tabla-body').innerHTML = '<tr class="loading-row"><td colspan="5"><div class="rpt-loading"><div class="rpt-loading-spinner"></div><br>Cargando reportes…</div></td></tr>';

    fetch(API_RPT + '?desde=' + desde + '&hasta=' + hasta, {
        headers: { 'Authorization': 'Bearer ' + token }
    })
    .then(r => {
        if (r.status === 401) { localStorage.removeItem('token'); location.href = 'login.php'; throw new Error('Auth'); }
        return r.json();
    })
    .then(d => {
        if (!d.success) { mostrarToast(d.error || 'Error al cargar reportes', 'error'); return; }
        rptDatosActuales = d;
        rptRenderKPIs(d.kpis);
        rptRenderCharts(d);
        rptRenderTabla(d.tabla_detalle, d.totales_rango);
    })
    .catch(e => {
        if (e.message !== 'Auth') mostrarToast('Error de conexión', 'error');
    });
}

/* ── Render KPIs ── */
function rptRenderKPIs(k) {
    document.getElementById('rpt-ventas-hoy').textContent   = formatCOP(k.ventas_hoy);
    document.getElementById('rpt-ventas-mes').textContent   = formatCOP(k.ventas_mes);
    document.getElementById('rpt-pedidos-hoy').textContent  = k.pedidos_hoy;
    document.getElementById('rpt-ticket').textContent       = formatCOP(k.ticket_promedio);
    document.getElementById('rpt-top-producto').textContent = k.producto_top;
    document.getElementById('rpt-top-qty').textContent      = k.producto_top_qty ? k.producto_top_qty + ' unidades' : '';
}

/* ── Render Charts ── */
function rptRenderCharts(d) {
    // Destruir charts anteriores
    Object.values(rptCharts).forEach(c => c.destroy());
    rptCharts = {};

    const baseOpts = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { labels: { font: { family: 'DM Sans', size: 11 }, color: '#9a7460' } } },
        scales: {
            x: { ticks: { font: { family: 'DM Sans', size: 10 }, color: '#9a7460' }, grid: { color: 'rgba(92,52,32,0.06)' } },
            y: { ticks: { font: { family: 'DM Sans', size: 10 }, color: '#9a7460', callback: v => formatCOP(v) }, grid: { color: 'rgba(92,52,32,0.06)' } }
        }
    };

    // 1. Ventas diarias (línea)
    rptCharts.diarias = new Chart(document.getElementById('chartVentasDiarias'), {
        type: 'line',
        data: {
            labels: d.ventas_diarias.map(v => {
                const dt = new Date(v.dia + 'T00:00:00');
                return dt.toLocaleDateString('es-CO', { day: '2-digit', month: 'short' });
            }),
            datasets: [{
                label: 'Ingresos',
                data: d.ventas_diarias.map(v => v.ingresos),
                borderColor: '#7a4a2e',
                backgroundColor: 'rgba(122,74,46,0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#7a4a2e'
            }]
        },
        options: { ...baseOpts, plugins: { ...baseOpts.plugins, legend: { display: false } } }
    });

    // 2. Productos top (barras)
    rptCharts.productos = new Chart(document.getElementById('chartProductosTop'), {
        type: 'bar',
        data: {
            labels: d.productos_top.map(p => p.nombre.length > 18 ? p.nombre.substring(0,18)+'…' : p.nombre),
            datasets: [{
                label: 'Unidades vendidas',
                data: d.productos_top.map(p => p.vendidos),
                backgroundColor: CAFE_COLORS.slice(0, d.productos_top.length),
                borderRadius: 6
            }]
        },
        options: {
            ...baseOpts,
            indexAxis: 'y',
            plugins: { ...baseOpts.plugins, legend: { display: false } },
            scales: {
                ...baseOpts.scales,
                x: { ...baseOpts.scales.x, ticks: { ...baseOpts.scales.x.ticks, callback: v => v } },
                y: { ...baseOpts.scales.y, ticks: { ...baseOpts.scales.y.ticks, callback: function(v) { return this.getLabelForValue(v); } } }
            }
        }
    });

    // 3. Categorías (circular)
    rptCharts.categorias = new Chart(document.getElementById('chartCategorias'), {
        type: 'doughnut',
        data: {
            labels: d.ventas_categoria.map(c => CAFE_CATEGORIAS[c.categoria] || c.categoria),
            datasets: [{
                data: d.ventas_categoria.map(c => c.total),
                backgroundColor: CAFE_COLORS.slice(0, d.ventas_categoria.length),
                borderWidth: 2,
                borderColor: 'white'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: 'DM Sans', size: 11 }, color: '#9a7460', padding: 12, usePointStyle: true } },
                tooltip: { callbacks: { label: ctx => ctx.label + ': ' + formatCOP(ctx.parsed) } }
            }
        }
    });

    // 4. Tendencia mensual (barras + línea)
    const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    rptCharts.tendencia = new Chart(document.getElementById('chartTendencia'), {
        type: 'bar',
        data: {
            labels: d.tendencia_mensual.map(t => {
                const [y, m] = t.mes.split('-');
                return meses[parseInt(m)-1] + ' ' + y.slice(2);
            }),
            datasets: [
                {
                    label: 'Ingresos',
                    data: d.tendencia_mensual.map(t => t.ingresos),
                    backgroundColor: 'rgba(122,74,46,0.3)',
                    borderColor: '#7a4a2e',
                    borderWidth: 1,
                    borderRadius: 6,
                    order: 2
                },
                {
                    label: 'Pedidos',
                    type: 'line',
                    data: d.tendencia_mensual.map(t => t.pedidos),
                    borderColor: '#c4895f',
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#c4895f',
                    yAxisID: 'y1',
                    order: 1
                }
            ]
        },
        options: {
            ...baseOpts,
            scales: {
                ...baseOpts.scales,
                y1: { position: 'right', ticks: { font: { family: 'DM Sans', size: 10 }, color: '#c4895f' }, grid: { display: false } }
            }
        }
    });
}

/* ── Render Tabla ── */
function rptRenderTabla(rows, totales) {
    const tbody = document.getElementById('rpt-tabla-body');
    document.getElementById('rpt-total-badge').textContent = totales.pedidos + ' pedidos · ' + formatCOP(totales.ingresos);

    if (!rows.length) {
        tbody.innerHTML = '<tr><td colspan="5"><div class="empty-state"><p>No hay datos para este período.</p></div></td></tr>';
        return;
    }

    tbody.innerHTML = rows.map(r => {
        const dt = new Date(r.fecha + 'T00:00:00');
        const fechaStr = dt.toLocaleDateString('es-CO', { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' });
        // Mini badges de estado
        let estados = '';
        if (r.pendientes)  estados += `<span class="rpt-estado-mini pendiente"></span>${r.pendientes} `;
        if (r.procesando)  estados += `<span class="rpt-estado-mini procesando"></span>${r.procesando} `;
        if (r.enviados)    estados += `<span class="rpt-estado-mini enviado"></span>${r.enviados} `;
        if (r.entregados)  estados += `<span class="rpt-estado-mini entregado"></span>${r.entregados} `;
        if (r.cancelados)  estados += `<span class="rpt-estado-mini cancelado"></span>${r.cancelados} `;
        return `<tr>
            <td>${fechaStr}</td>
            <td><strong>${r.pedidos}</strong></td>
            <td class="precio">${formatCOP(r.ingresos)}</td>
            <td>${formatCOP(r.ticket_promedio)}</td>
            <td style="font-size:11px">${estados || '—'}</td>
        </tr>`;
    }).join('');
}

/* ── Exportar Excel (CSV) ── */
function rptExportarExcel() {
    if (!rptDatosActuales || !rptDatosActuales.tabla_detalle.length) {
        mostrarToast('No hay datos para exportar', 'error'); return;
    }
    const BOM = '\uFEFF';
    let csv = BOM + 'Fecha,Pedidos,Ingresos,Ticket Promedio,Pendientes,Procesando,Enviados,Entregados,Cancelados\n';
    rptDatosActuales.tabla_detalle.forEach(r => {
        csv += `${r.fecha},${r.pedidos},${r.ingresos},${r.ticket_promedio},${r.pendientes},${r.procesando},${r.enviados},${r.entregados},${r.cancelados}\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `reporte_tantico_${rptDatosActuales.rango.desde}_${rptDatosActuales.rango.hasta}.csv`;
    a.click();
    URL.revokeObjectURL(url);
    mostrarToast('Reporte exportado ✓');
}

/* ── Exportar PDF (print) ── */
function rptExportarPDF() {
    window.print();
}

// ══════════════════════════════════
// ── ANUNCIOS & POPUPS ──
// ══════════════════════════════════
let todosLosAnuncios = [];
let intervalCountdownPreview = null;

const BANDERAS_MAP = {
    co: 'linear-gradient(to bottom, #FCD116 0%, #FCD116 50%, #003893 50%, #003893 75%, #CE1126 75%, #CE1126 100%)',
    cr: 'linear-gradient(to bottom, #002B7F 0%, #002B7F 20%, #fff 20%, #fff 40%, #CE1126 40%, #CE1126 60%, #fff 60%, #fff 80%, #002B7F 80%, #002B7F 100%)',
    ar: 'linear-gradient(to bottom, #74ACDF 0%, #74ACDF 33%, #fff 33%, #fff 66%, #74ACDF 66%, #74ACDF 100%)',
    br: 'linear-gradient(to bottom, #009C3B 0%, #009C3B 100%)',
    es: 'linear-gradient(to bottom, #AA151B 0%, #AA151B 25%, #F1BF00 25%, #F1BF00 75%, #AA151B 75%, #AA151B 100%)',
    us: 'linear-gradient(to bottom, #B22234 0%, #B22234 50%, #3C3B6E 50%, #3C3B6E 100%)',
    mx: 'linear-gradient(to right, #006847 0%, #006847 33%, #fff 33%, #fff 66%, #CE1126 66%, #CE1126 100%)',
    de: 'linear-gradient(to bottom, #000 0%, #000 33%, #D00 33%, #D00 66%, #FFCE00 66%, #FFCE00 100%)',
    pe: 'linear-gradient(to right, #D91023 0%, #D91023 33%, #fff 33%, #fff 66%, #D91023 66%, #D91023 100%)',
    uy: 'linear-gradient(to bottom, #fff 0%, #fff 20%, #0038A8 20%, #0038A8 40%, #fff 40%, #fff 60%, #0038A8 60%, #0038A8 80%, #fff 80%, #fff 100%)',
    cl: 'linear-gradient(to bottom, #0039A6 0%, #0039A6 50%, #D52B1E 50%, #D52B1E 100%)',
    ec: 'linear-gradient(to bottom, #FFDD00 0%, #FFDD00 50%, #034EA2 50%, #034EA2 75%, #ED1C24 75%, #ED1C24 100%)'
};

function cargarAnuncios() {
    const tbody = document.getElementById('tabla-anuncios');
    tbody.innerHTML = '<tr class="loading-row"><td colspan="4">Cargando anuncios…</td></tr>';
    
    fetch(API_ANU, { headers: { 'Authorization': 'Bearer ' + token } })
        .then(r => {
            if (r.status === 401) {
                localStorage.removeItem('token');
                location.href = 'login.php';
                throw new Error('Token expirado');
            }
            return r.json();
        })
        .then(d => {
            if (d.success) {
                todosLosAnuncios = d.anuncios || [];
                actualizarStatsAnuncios(todosLosAnuncios);
                renderTablaAnuncios(todosLosAnuncios);
            } else {
                mostrarToast('Error al cargar anuncios', 'error');
                tbody.innerHTML = '<tr class="loading-row"><td colspan="4">No se pudieron cargar los anuncios.</td></tr>';
            }
        })
        .catch(err => {
            console.error(err);
            mostrarToast('Error de conexión al cargar anuncios', 'error');
            tbody.innerHTML = '<tr class="loading-row"><td colspan="4">Error de conexión al cargar anuncios.</td></tr>';
        });
}

function actualizarStatsAnuncios(anuncios) {
    document.getElementById('stat-anu-total').textContent = anuncios.length;
    const activo = anuncios.find(a => parseInt(a.activo) === 1);
    if (activo) {
        document.getElementById('stat-anu-activo').innerHTML = activo.titulo.replace(/<[^>]*>?/gm, ' ');
        document.getElementById('stat-anu-estado').innerHTML = '<span style="color:#27704a;font-weight:600;">🟢 Activado</span>';
        document.getElementById('stat-anu-sub').textContent = 'Tipo: ' + (activo.tipo || 'partido');
    } else {
        document.getElementById('stat-anu-activo').textContent = 'Ninguno (Pausado)';
        document.getElementById('stat-anu-estado').innerHTML = '<span style="color:#c0392b;font-weight:600;">🔴 Desactivado</span>';
        document.getElementById('stat-anu-sub').textContent = 'El popup no se mostrará a los clientes';
    }
}

function renderTablaAnuncios(anuncios) {
    const tbody = document.getElementById('tabla-anuncios');
    if (!anuncios.length) {
        tbody.innerHTML = '<tr><td colspan="4"><div class="empty-state"><p>No hay anuncios creados aún. ¡Crea el primero a la derecha!</p></div></td></tr>';
        return;
    }

    tbody.innerHTML = anuncios.map(a => {
        const ruta = resolverRutaImg(a.imagen) || '../assets/imagenes/tantoooo.png';
        const isActivo = parseInt(a.activo) === 1;
        const tipoCls = a.tipo || 'partido';
        const tipoTexto = a.tipo ? a.tipo.charAt(0).toUpperCase() + a.tipo.slice(1) : 'General';
        const tituloLimpio = a.titulo.replace(/<[^>]*>?/gm, ' ');

        return `<tr>
            <td>
                <div class="anu-info">
                    <div class="anu-thumb"><img src="${ruta}" alt="" onerror="this.src='../assets/imagenes/tantoooo.png'"></div>
                    <div>
                        <div class="anu-title">${tituloLimpio}</div>
                        <div class="anu-eyebrow">${a.eyebrow || '—'}</div>
                    </div>
                </div>
            </td>
            <td>
                <span class="anu-badge-tipo ${tipoCls}">${tipoTexto}</span>
                ${parseInt(a.mostrar_partido) === 1 ? `<div style="font-size:11px;color:var(--text-soft);margin-top:2px;">${a.equipo1_nombre||''} vs ${a.equipo2_nombre||''}</div>` : ''}
            </td>
            <td>
                <label class="switch-wrap" title="${isActivo ? 'Click para pausar' : 'Click para activar'}">
                    <input type="checkbox" class="switch-input" ${isActivo ? 'checked' : ''} onchange="toggleActivoAnuncio(${a.id}, ${isActivo ? 1 : 0})">
                    <span class="switch-slider"></span>
                    <span style="font-size:11.5px;color:${isActivo ? '#27704a' : 'var(--text-soft)'};font-weight:${isActivo ? '600' : '400'};">
                        ${isActivo ? 'Visible' : 'Pausado'}
                    </span>
                </label>
            </td>
            <td style="white-space:nowrap;">
                <button class="btn-preview-popup" onclick="abrirModalPreviewAnuncio(${a.id})" title="Ver vista previa de cómo lo verán los clientes">
                    👁️ Ver
                </button>
                <button class="btn-edit" onclick="editarAnuncio(${a.id})">Editar</button>
                <button class="btn-delete" onclick="eliminarAnuncio(${a.id})">Eliminar</button>
            </td>
        </tr>`;
    }).join('');
}

function filtrarAnuncios() {
    const q = document.getElementById('buscador-anu').value.toLowerCase();
    const filtrados = todosLosAnuncios.filter(a => {
        return (a.titulo || '').toLowerCase().includes(q) ||
               (a.subtitulo || '').toLowerCase().includes(q) ||
               (a.eyebrow || '').toLowerCase().includes(q) ||
               (a.tipo || '').toLowerCase().includes(q) ||
               (a.equipo1_nombre || '').toLowerCase().includes(q) ||
               (a.equipo2_nombre || '').toLowerCase().includes(q);
    });
    renderTablaAnuncios(filtrados);
}

function toggleActivoAnuncio(id, estadoActual) {
    const nuevoEstado = estadoActual === 1 ? 0 : 1;
    fetch(API_ANU + '?action=toggle', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: id, activo: nuevoEstado })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            mostrarToast(d.mensaje || (nuevoEstado ? 'Anuncio activado ✓' : 'Anuncio pausado ✓'));
            cargarAnuncios();
        } else {
            mostrarToast(d.error || 'Error al cambiar estado', 'error');
            cargarAnuncios();
        }
    })
    .catch(() => {
        mostrarToast('Error de conexión', 'error');
        cargarAnuncios();
    });
}

function previewAnuImagen(e) {
    const file = e.target.files[0];
    if (!file) return;
    if (file.size > 5 * 1024 * 1024) {
        mostrarToast('La imagen no debe superar 5 MB', 'error');
        e.target.value = '';
        return;
    }
    const reader = new FileReader();
    reader.onload = ev => {
        const preview = document.getElementById('anuPreview');
        preview.src = ev.target.result;
        preview.style.display = 'block';
        document.getElementById('anuUploadPrompt').style.display = 'none';
    };
    reader.readAsDataURL(file);
}

function cambiarTipoAnuncio(tipo, esManual = false) {
    const boxPartido = document.getElementById('box-partido');
    const boxNoPartido = document.getElementById('box-no-partido');
    const hidPartido = document.getElementById('f-anu-mostrar-partido');

    if (tipo === 'partido') {
        if (boxPartido) boxPartido.style.display = 'flex';
        if (boxNoPartido) boxNoPartido.style.display = 'none';
        if (hidPartido) hidPartido.value = '1';

        if (esManual) {
            document.getElementById('f-anu-eyebrow').value = 'Transmisión en vivo · Neiva, Huila';
            document.getElementById('f-anu-badge').value = 'Café Región · Neiva, Huila';
            document.getElementById('f-anu-f1-ico').value = 'fa-solid fa-tv';
            document.getElementById('f-anu-f1-txt').value = 'Pantallas<br>4K';
            document.getElementById('f-anu-f2-ico').value = 'fa-solid fa-users';
            document.getElementById('f-anu-f2-txt').value = 'Ambiente<br>futbolero';
            document.getElementById('f-anu-f3-ico').value = 'fa-brands fa-java';
            document.getElementById('f-anu-f3-txt').value = 'Bebidas<br>premium';
        }
    } else {
        if (boxPartido) boxPartido.style.display = 'none';
        if (boxNoPartido) boxNoPartido.style.display = 'flex';
        if (hidPartido) hidPartido.value = '0';

        if (esManual) {
            if (tipo === 'promo') {
                document.getElementById('f-anu-eyebrow').value = 'Descuento Especial · Tiempo Limitado';
                document.getElementById('f-anu-badge').value = 'Tantico Café · Tienda Física & Online';
                document.getElementById('f-anu-btn-txt').value = 'Ver Catálogo y Pedir';
                document.getElementById('f-anu-btn-link').value = 'includes/servicios.php';
                document.getElementById('f-anu-f1-ico').value = 'fa-solid fa-tag';
                document.getElementById('f-anu-f1-txt').value = 'Descuento<br>Especial';
                document.getElementById('f-anu-f2-ico').value = 'fa-solid fa-cake-candles';
                document.getElementById('f-anu-f2-txt').value = 'Repostería<br>& Café';
                document.getElementById('f-anu-f3-ico').value = 'fa-solid fa-store';
                document.getElementById('f-anu-f3-txt').value = 'Válido en<br>Tienda';
            } else if (tipo === 'evento') {
                document.getElementById('f-anu-eyebrow').value = 'Noche Especial · Experiencia Tantico';
                document.getElementById('f-anu-badge').value = 'Tantico Café · Neiva, Huila';
                document.getElementById('f-anu-btn-txt').value = 'Reservar Mesa Ahora';
                document.getElementById('f-anu-btn-link').value = 'includes/contacto.php';
                document.getElementById('f-anu-f1-ico').value = 'fa-solid fa-music';
                document.getElementById('f-anu-f1-txt').value = 'Música en<br>Vivo';
                document.getElementById('f-anu-f2-ico').value = 'fa-solid fa-martini-glass-citrus';
                document.getElementById('f-anu-f2-txt').value = 'Coctelería<br>de Café';
                document.getElementById('f-anu-f3-ico').value = 'fa-solid fa-ticket';
                document.getElementById('f-anu-f3-txt').value = 'Entrada<br>Libre';
            } else {
                document.getElementById('f-anu-eyebrow').value = 'Comunicado Oficial · Novedades';
                document.getElementById('f-anu-badge').value = 'Tantico Café de Especialidad';
                document.getElementById('f-anu-btn-txt').value = 'Conocer Más';
                document.getElementById('f-anu-btn-link').value = 'index.php';
                document.getElementById('f-anu-f1-ico').value = 'fa-solid fa-mug-hot';
                document.getElementById('f-anu-f1-txt').value = 'Café de<br>Especialidad';
                document.getElementById('f-anu-f2-ico').value = 'fa-solid fa-wifi';
                document.getElementById('f-anu-f2-txt').value = 'Wi-Fi &<br>Coworking';
                document.getElementById('f-anu-f3-ico').value = 'fa-solid fa-paw';
                document.getElementById('f-anu-f3-txt').value = 'Pet<br>Friendly';
            }
        }
    }
}

function editarAnuncio(id) {
    const a = todosLosAnuncios.find(x => x.id == id);
    if (!a) return;

    document.getElementById('f-anu-id').value = a.id;
    document.getElementById('f-anu-titulo').value = a.titulo || '';
    document.getElementById('f-anu-subtitulo').value = a.subtitulo || '';
    document.getElementById('f-anu-eyebrow').value = a.eyebrow || '';
    document.getElementById('f-anu-badge').value = a.badge_texto || '';
    document.getElementById('f-anu-tipo').value = a.tipo || 'partido';
    document.getElementById('f-anu-imagen-existente').value = a.imagen || '';

    cambiarTipoAnuncio(a.tipo || 'partido', false);

    document.getElementById('f-anu-fecha-texto').value = a.fecha_evento_texto || '';
    if (a.fecha_objetivo) {
        const dt = new Date(a.fecha_objetivo);
        const pad = n => String(n).padStart(2, '0');
        const formatted = `${dt.getFullYear()}-${pad(dt.getMonth()+1)}-${pad(dt.getDate())}T${pad(dt.getHours())}:${pad(dt.getMinutes())}`;
        document.getElementById('f-anu-fecha-objetivo').value = formatted;
    }

    document.getElementById('f-anu-eq1-nom').value = a.equipo1_nombre || '';
    document.getElementById('f-anu-eq1-ban').value = a.equipo1_bandera || 'co';
    document.getElementById('f-anu-eq2-nom').value = a.equipo2_nombre || '';
    document.getElementById('f-anu-eq2-ban').value = a.equipo2_bandera || 'cr';

    document.getElementById('f-anu-btn-txt').value = a.boton_texto || '';
    document.getElementById('f-anu-btn-link').value = a.boton_enlace || '';

    document.getElementById('f-anu-f1-ico').value = a.feat1_icono || 'fa-solid fa-tv';
    document.getElementById('f-anu-f1-txt').value = a.feat1_texto || '';
    document.getElementById('f-anu-f2-ico').value = a.feat2_icono || 'fa-solid fa-users';
    document.getElementById('f-anu-f2-txt').value = a.feat2_texto || '';
    document.getElementById('f-anu-f3-ico').value = a.feat3_icono || 'fa-brands fa-java';
    document.getElementById('f-anu-f3-txt').value = a.feat3_texto || '';

    document.getElementById('f-anu-activo').checked = parseInt(a.activo) === 1;

    // Preview de imagen
    const ruta = resolverRutaImg(a.imagen);
    if (ruta) {
        const prev = document.getElementById('anuPreview');
        prev.src = ruta;
        prev.style.display = 'block';
        document.getElementById('anuUploadPrompt').style.display = 'none';
    } else {
        document.getElementById('anuPreview').style.display = 'none';
        document.getElementById('anuUploadPrompt').style.display = 'block';
    }

    document.getElementById('form-anu-title').textContent = 'Editar Anuncio #' + a.id;
    document.getElementById('btnGuardarAnuncio').textContent = 'Actualizar Anuncio';
    document.getElementById('btn-cancel-anu-edit').style.display = 'inline-block';

    document.getElementById('form-anu-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function cancelarEdicionAnuncio() {
    limpiarFormularioAnuncio();
    document.getElementById('form-anu-title').textContent = 'Crear Anuncio';
    document.getElementById('btnGuardarAnuncio').textContent = 'Guardar Anuncio';
    document.getElementById('btn-cancel-anu-edit').style.display = 'none';
}

function limpiarFormularioAnuncio() {
    ['f-anu-id', 'f-anu-titulo', 'f-anu-subtitulo', 'f-anu-eyebrow', 'f-anu-badge',
     'f-anu-fecha-texto', 'f-anu-fecha-objetivo', 'f-anu-eq1-nom', 'f-anu-eq2-nom',
     'f-anu-btn-txt', 'f-anu-btn-link',
     'f-anu-f1-txt', 'f-anu-f2-txt', 'f-anu-f3-txt',
     'f-anu-imagen-existente'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });

    document.getElementById('anuImagenInput').value = '';
    document.getElementById('anuPreview').src = '';
    document.getElementById('anuPreview').style.display = 'none';
    document.getElementById('anuUploadPrompt').style.display = 'block';
    document.getElementById('f-anu-tipo').value = 'partido';
    cambiarTipoAnuncio('partido', true);
    document.getElementById('f-anu-activo').checked = true;
}

function guardarAnuncio() {
    const id = document.getElementById('f-anu-id').value;
    const titulo = document.getElementById('f-anu-titulo').value.trim();

    if (!titulo) {
        mostrarToast('El título del anuncio es obligatorio', 'error');
        return;
    }

    const btn = document.getElementById('btnGuardarAnuncio');
    const textoOriginal = btn.textContent;
    btn.disabled = true;
    btn.textContent = '⏳ Guardando…';

    const tipo = document.getElementById('f-anu-tipo').value;
    const esPartido = tipo === 'partido';

    const fd = new FormData();
    if (id) fd.append('id', id);
    fd.append('titulo', titulo);
    fd.append('subtitulo', document.getElementById('f-anu-subtitulo').value.trim());
    fd.append('eyebrow', document.getElementById('f-anu-eyebrow').value.trim());
    fd.append('badge_texto', document.getElementById('f-anu-badge').value.trim());
    fd.append('tipo', tipo);
    fd.append('mostrar_partido', esPartido ? 1 : 0);
    fd.append('fecha_evento_texto', document.getElementById('f-anu-fecha-texto').value.trim());
    fd.append('fecha_objetivo', document.getElementById('f-anu-fecha-objetivo').value);
    fd.append('equipo1_nombre', document.getElementById('f-anu-eq1-nom').value.trim());
    fd.append('equipo1_bandera', document.getElementById('f-anu-eq1-ban').value);
    fd.append('equipo2_nombre', document.getElementById('f-anu-eq2-nom').value.trim());
    fd.append('equipo2_bandera', document.getElementById('f-anu-eq2-ban').value);
    fd.append('boton_texto', document.getElementById('f-anu-btn-txt').value.trim());
    fd.append('boton_enlace', document.getElementById('f-anu-btn-link').value.trim());
    fd.append('feat1_icono', document.getElementById('f-anu-f1-ico').value.trim());
    fd.append('feat1_texto', document.getElementById('f-anu-f1-txt').value.trim());
    fd.append('feat2_icono', document.getElementById('f-anu-f2-ico').value.trim());
    fd.append('feat2_texto', document.getElementById('f-anu-f2-txt').value.trim());
    fd.append('feat3_icono', document.getElementById('f-anu-f3-ico').value.trim());
    fd.append('feat3_texto', document.getElementById('f-anu-f3-txt').value.trim());
    fd.append('activo', document.getElementById('f-anu-activo').checked ? 1 : 0);

    const imgExistente = document.getElementById('f-anu-imagen-existente').value;
    if (imgExistente) fd.append('imagen_existente', imgExistente);

    const imgFile = document.getElementById('anuImagenInput').files[0];
    if (imgFile) fd.append('imagen', imgFile);

    fetch(API_ANU, {
        method: 'POST',
        headers: { 'Authorization': 'Bearer ' + token },
        body: fd
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            mostrarToast(id ? 'Anuncio actualizado con éxito ✓' : 'Anuncio creado con éxito ✓');
            cancelarEdicionAnuncio();
            cargarAnuncios();
        } else {
            mostrarToast(d.error || 'Error al guardar anuncio', 'error');
        }
    })
    .catch(() => {
        mostrarToast('Error de conexión al guardar anuncio', 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = textoOriginal;
    });
}

function eliminarAnuncio(id) {
    if (!confirm('¿Seguro que deseas eliminar este anuncio?')) return;

    fetch(API_ANU + '?action=delete', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: id })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            mostrarToast('Anuncio eliminado ✓');
            cargarAnuncios();
        } else {
            mostrarToast(d.error || 'Error al eliminar', 'error');
        }
    })
    .catch(() => {
        mostrarToast('Error de conexión', 'error');
    });
}

// ── VISTA PREVIA MODAL POPUP EN ADMIN ──
function probarPreviewFormulario() {
    const tipo = document.getElementById('f-anu-tipo').value;
    const esPartido = tipo === 'partido';
    const anu = {
        titulo: document.getElementById('f-anu-titulo').value || 'Título del anuncio de prueba',
        subtitulo: document.getElementById('f-anu-subtitulo').value || 'Subtítulo y descripción del anuncio...',
        eyebrow: document.getElementById('f-anu-eyebrow').value || 'Transmisión en vivo · Neiva, Huila',
        badge_texto: document.getElementById('f-anu-badge').value || 'Café Región · Neiva, Huila',
        imagen: document.getElementById('anuPreview').src || '../assets/imagenes/tantoooo.png',
        tipo: tipo,
        mostrar_partido: esPartido ? 1 : 0,
        fecha_evento_texto: document.getElementById('f-anu-fecha-texto').value || 'Lunes 01 de Junio',
        fecha_objetivo: document.getElementById('f-anu-fecha-objetivo').value || new Date(Date.now() + 7*86400000).toISOString(),
        equipo1_nombre: document.getElementById('f-anu-eq1-nom').value || 'Colombia',
        equipo1_bandera: document.getElementById('f-anu-eq1-ban').value || 'co',
        equipo2_nombre: document.getElementById('f-anu-eq2-nom').value || 'Costa Rica',
        equipo2_bandera: document.getElementById('f-anu-eq2-ban').value || 'cr',
        boton_texto: document.getElementById('f-anu-btn-txt').value,
        boton_enlace: document.getElementById('f-anu-btn-link').value,
        feat1_icono: document.getElementById('f-anu-f1-ico').value || 'fa-solid fa-tv',
        feat1_texto: document.getElementById('f-anu-f1-txt').value || 'Pantallas<br>4K',
        feat2_icono: document.getElementById('f-anu-f2-ico').value || 'fa-solid fa-users',
        feat2_texto: document.getElementById('f-anu-f2-txt').value || 'Ambiente<br>futbolero',
        feat3_icono: document.getElementById('f-anu-f3-ico').value || 'fa-brands fa-java',
        feat3_texto: document.getElementById('f-anu-f3-txt').value || 'Bebidas<br>premium'
    };
    renderPreviewModalContent(anu);
    document.getElementById('modal-preview-anuncio').classList.add('open');
}

function abrirModalPreviewAnuncio(id) {
    const a = todosLosAnuncios.find(x => x.id == id);
    if (!a) return;
    renderPreviewModalContent(a);
    document.getElementById('modal-preview-anuncio').classList.add('open');
}

function cerrarModalPreviewAnuncio() {
    document.getElementById('modal-preview-anuncio').classList.remove('open');
    if (intervalCountdownPreview) {
        clearInterval(intervalCountdownPreview);
        intervalCountdownPreview = null;
    }
}

function renderPreviewModalContent(anu) {
    const rutaImg = anu.imagen && anu.imagen.startsWith('data:') ? anu.imagen : resolverRutaImg(anu.imagen || 'assets/imagenes/tantoooo.png');
    const flag1Bg = BANDERAS_MAP[anu.equipo1_bandera] || BANDERAS_MAP.co;
    const flag2Bg = BANDERAS_MAP[anu.equipo2_bandera] || BANDERAS_MAP.cr;
    const tienePartido = parseInt(anu.mostrar_partido) === 1;

    const html = `
    <div style="background:#28040A;border-radius:22px;overflow:hidden;border:1px solid rgba(212,168,67,0.22);display:flex;position:relative;box-shadow:0 30px 80px rgba(0,0,0,0.8);max-width:680px;width:100%;font-family:'Inter',sans-serif;">
        <button onclick="cerrarModalPreviewAnuncio()" style="position:absolute;top:12px;right:12px;z-index:20;width:32px;height:32px;border-radius:50%;border:1px solid rgba(252,246,219,0.2);background:rgba(28,4,10,0.7);color:#FAF6EE;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;">✕</button>
        
        <div style="flex:1;padding:26px 24px;color:#FAF6EE;display:flex;flex-direction:column;">
            <div style="height:3px;background:linear-gradient(90deg, #FCD116 0%, #FCD116 50%, #003893 50%, #003893 75%, #CE1126 75%, #CE1126 100%);border-radius:2px;margin-bottom:14px;"></div>
            
            <div style="font-size:10.5px;font-weight:700;letter-spacing:1.4px;text-transform:uppercase;color:#D4A843;display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                <span style="width:7px;height:7px;border-radius:50%;background:#2ECC71;box-shadow:0 0 6px #2ECC71;display:inline-block;"></span>
                ${anu.eyebrow || 'Transmisión en vivo · Neiva, Huila'}
            </div>

            <div style="font-family:'Goudy Bookletter 1911','Playfair Display',serif;font-size:22px;line-height:1.2;color:#FCF6DB;margin-bottom:8px;">
                ${anu.titulo || ''}
            </div>

            <p style="font-size:12px;color:rgba(255,255,255,0.85);line-height:1.5;margin-bottom:14px;">
                ${anu.subtitulo || ''}
            </p>

            <div style="display:flex;border:1px solid rgba(212,168,67,0.2);border-radius:10px;background:rgba(212,168,67,0.04);margin-bottom:14px;">
                <div style="flex:1;padding:10px 4px;text-align:center;border-right:1px solid rgba(212,168,67,0.15);">
                    <i class="${anu.feat1_icono||'fa-solid fa-tv'}" style="color:#D4A843;font-size:16px;"></i>
                    <div style="font-size:9px;font-weight:700;text-transform:uppercase;color:rgba(212,168,67,0.85);margin-top:4px;">${anu.feat1_texto||'Pantallas 4K'}</div>
                </div>
                <div style="flex:1;padding:10px 4px;text-align:center;border-right:1px solid rgba(212,168,67,0.15);">
                    <i class="${anu.feat2_icono||'fa-solid fa-users'}" style="color:#D4A843;font-size:16px;"></i>
                    <div style="font-size:9px;font-weight:700;text-transform:uppercase;color:rgba(212,168,67,0.85);margin-top:4px;">${anu.feat2_texto||'Ambiente'}</div>
                </div>
                <div style="flex:1;padding:10px 4px;text-align:center;">
                    <i class="${anu.feat3_icono||'fa-brands fa-java'}" style="color:#D4A843;font-size:16px;"></i>
                    <div style="font-size:9px;font-weight:700;text-transform:uppercase;color:rgba(212,168,67,0.85);margin-top:4px;">${anu.feat3_texto||'Bebidas'}</div>
                </div>
            </div>

            ${tienePartido ? `
            <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(212,168,67,0.18);border-radius:12px;padding:12px;display:flex;flex-direction:column;gap:8px;">
                <div style="font-size:9.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#D4A843;text-align:center;">
                    ${anu.fecha_evento_texto || 'Próximo Encuentro'}
                </div>
                <div style="display:flex;align-items:center;justify-content:center;gap:12px;">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:3px;min-width:60px;">
                        <div style="width:34px;height:22px;border-radius:4px;box-shadow:0 2px 6px rgba(0,0,0,0.5);background:${flag1Bg};"></div>
                        <span style="font-size:9.5px;font-weight:700;color:#FCF6DB;text-transform:uppercase;">${anu.equipo1_nombre||'Equipo 1'}</span>
                    </div>
                    <span style="font-family:'Playfair Display',serif;font-size:13px;font-weight:900;color:rgba(212,168,67,0.6);">VS</span>
                    <div style="display:flex;flex-direction:column;align-items:center;gap:3px;min-width:60px;">
                        <div style="width:34px;height:22px;border-radius:4px;box-shadow:0 2px 6px rgba(0,0,0,0.5);background:${flag2Bg};"></div>
                        <span style="font-size:9.5px;font-weight:700;color:#FCF6DB;text-transform:uppercase;">${anu.equipo2_nombre||'Equipo 2'}</span>
                    </div>
                </div>
                <div style="display:flex;justify-content:center;gap:6px;margin-top:2px;">
                    <div style="background:rgba(212,168,67,0.1);border:1px solid rgba(212,168,67,0.2);border-radius:6px;padding:3px 6px;text-align:center;min-width:38px;">
                        <div id="prev-dias" style="font-size:16px;font-weight:800;color:#F0C96A;line-height:1;">00</div>
                        <div style="font-size:7.5px;text-transform:uppercase;color:rgba(252,246,219,0.4);">Días</div>
                    </div>
                    <div style="color:rgba(212,168,67,0.4);font-weight:bold;line-height:22px;">:</div>
                    <div style="background:rgba(212,168,67,0.1);border:1px solid rgba(212,168,67,0.2);border-radius:6px;padding:3px 6px;text-align:center;min-width:38px;">
                        <div id="prev-horas" style="font-size:16px;font-weight:800;color:#F0C96A;line-height:1;">00</div>
                        <div style="font-size:7.5px;text-transform:uppercase;color:rgba(252,246,219,0.4);">Horas</div>
                    </div>
                    <div style="color:rgba(212,168,67,0.4);font-weight:bold;line-height:22px;">:</div>
                    <div style="background:rgba(212,168,67,0.1);border:1px solid rgba(212,168,67,0.2);border-radius:6px;padding:3px 6px;text-align:center;min-width:38px;">
                        <div id="prev-mins" style="font-size:16px;font-weight:800;color:#F0C96A;line-height:1;">00</div>
                        <div style="font-size:7.5px;text-transform:uppercase;color:rgba(252,246,219,0.4);">Mins</div>
                    </div>
                    <div style="color:rgba(212,168,67,0.4);font-weight:bold;line-height:22px;">:</div>
                    <div style="background:rgba(212,168,67,0.1);border:1px solid rgba(212,168,67,0.2);border-radius:6px;padding:3px 6px;text-align:center;min-width:38px;">
                        <div id="prev-segs" style="font-size:16px;font-weight:800;color:#F0C96A;line-height:1;">00</div>
                        <div style="font-size:7.5px;text-transform:uppercase;color:rgba(252,246,219,0.4);">Segs</div>
                    </div>
                </div>
            </div>` : ''}

            ${!tienePartido && anu.boton_texto ? `
            <div style="margin-top:10px;text-align:center;">
                <a href="${anu.boton_enlace || '#'}" onclick="event.preventDefault()" style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#D4A843 0%,#F0C96A 100%);color:#28040A;font-weight:700;font-size:11.5px;padding:9px 20px;border-radius:25px;text-decoration:none;text-transform:uppercase;letter-spacing:1px;box-shadow:0 4px 15px rgba(212,168,67,0.35);">
                    <span>${anu.boton_texto}</span>
                    <i class="fa-solid fa-arrow-right" style="font-size:10px;"></i>
                </a>
            </div>` : ''}

            <div style="margin-top:14px;text-align:center;">
                <span style="display:inline-block;background:rgba(40,4,10,0.7);border:1px solid rgba(212,168,67,0.3);border-radius:20px;padding:4px 12px;font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:rgba(212,168,67,0.9);">
                    ☕ ${anu.badge_texto || 'Café Región · Neiva, Huila'}
                </span>
            </div>
        </div>

        <div style="width:260px;position:relative;background:#1a0305;flex-shrink:0;">
            <img src="${rutaImg}" alt="" style="width:100%;height:100%;object-fit:cover;display:block;" onerror="this.src='../assets/imagenes/tantoooo.png'">
            <div style="position:absolute;inset:0;background:linear-gradient(to right, rgba(40,4,10,0.5) 0%, transparent 50%), linear-gradient(to top, rgba(40,4,10,0.7) 0%, transparent 50%);"></div>
        </div>
    </div>`;

    document.getElementById('anu-modal-preview-container').innerHTML = html;

    // Countdown loop
    if (tienePartido && anu.fecha_objetivo) {
        const targetMs = new Date(anu.fecha_objetivo).getTime();
        const pad = n => String(n).padStart(2, '0');
        const updateTick = () => {
            const diff = Math.max(0, targetMs - Date.now());
            const dEl = document.getElementById('prev-dias');
            const hEl = document.getElementById('prev-horas');
            const mEl = document.getElementById('prev-mins');
            const sEl = document.getElementById('prev-segs');
            if (dEl) dEl.textContent = pad(Math.floor(diff / 86400000));
            if (hEl) hEl.textContent = pad(Math.floor((diff % 86400000) / 3600000));
            if (mEl) mEl.textContent = pad(Math.floor((diff % 3600000) / 60000));
            if (sEl) sEl.textContent = pad(Math.floor((diff % 60000) / 1000));
        };
        updateTick();
        if (intervalCountdownPreview) clearInterval(intervalCountdownPreview);
        intervalCountdownPreview = setInterval(updateTick, 1000);
    }
}

</script>
</body>
</html>