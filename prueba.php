
#servicios.seccion-tienda {
    position: relative;
    width: 100%;
    height: 480px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.tienda-hero-bg {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse at 20% 50%, rgba(71, 6, 14, 0.85) 0%, transparent 55%),
        radial-gradient(ellipse at 80% 20%, rgba(200, 169, 110, 0.12) 0%, transparent 50%),
        linear-gradient(160deg, #1C0A0D 0%, #47060E 40%, #28040A 100%);
}

.tienda-hero-bg::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 15% 85%, rgba(200, 169, 110, 0.08) 0%, transparent 40%),
        radial-gradient(circle at 85% 15%, rgba(200, 169, 110, 0.06) 0%, transparent 35%);
}

/* Anillos decorativos */
.hero-ring {
    position: absolute;
    width: 340px;
    height: 340px;
    border-radius: 50%;
    border: 1px solid rgba(200, 169, 110, 0.12);
    right: 10%;
    top: 50%;
    transform: translateY(-50%);
}

.hero-ring::before {
    content: '';
    position: absolute;
    inset: 36px;
    border-radius: 50%;
    border: 1px solid rgba(200, 169, 110, 0.07);
}

.hero-ring::after {
    content: '';
    position: absolute;
    inset: 72px;
    border-radius: 50%;
    border: 1px solid rgba(200, 169, 110, 0.04);
}

/* Contenido del hero */
.tienda-hero-content {
    position: relative;
    z-index: 5;
    text-align: center;
    padding: 0 32px;
    max-width: 760px;
}

.tienda-hero-tag {
    display: inline-block;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 4px;
    text-transform: uppercase;
    color: var(--gold);
    border: 1px solid rgba(200, 169, 110, 0.35);
    padding: 6px 20px;
    border-radius: 100px;
    margin-bottom: 22px;
}

.tienda-hero-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: clamp(36px, 6vw, 68px);
    font-weight: 300;
    color: var(--cream);
    line-height: 1.1;
    margin-bottom: 14px;
    letter-spacing: -1px;
}

.tienda-hero-title em {
    font-style: italic;
    color: var(--gold);
    font-weight: 400;
}

.tienda-hero-sub {
    font-size: 15px;
    color: rgba(252, 246, 219, 0.55);
    font-weight: 300;
    letter-spacing: 0.5px;
}

/* ============================================================
   BANDA INFORMATIVA
============================================================ */
.tienda-info-band {
    background: var(--coffee-dark);
    padding: 24px 20px;
}

.info-band-grid {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 24px;
}

.info-band-item {
    display: flex;
    align-items: center;
    gap: 14px;
}

.info-band-icon {
    width: 42px;
    height: 42px;
    background: rgba(200, 169, 110, 0.12);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gold);
    font-size: 17px;
    flex-shrink: 0;
}

.info-band-text strong {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--cream);
    margin-bottom: 2px;
}

.info-band-text span {
    font-size: 11px;
    color: rgba(252, 246, 219, 0.45);
}

/* ============================================================
   FILTROS DE CATEGORÍA
============================================================ */
.tienda-filtros {
    background: var(--cream);
    padding: 32px 20px 0;
    text-align: center;
    position: sticky;
    top: 99px;
    z-index: 100;
    border-bottom: 1px solid rgba(71, 6, 14, 0.08);
}

.filtros-wrap {
    display: flex;
    gap: 8px;
    justify-content: center;
    flex-wrap: wrap;
    padding-bottom: 20px;
    max-width: 900px;
    margin: 0 auto;
}

.filtro-btn {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    padding: 9px 22px;
    border-radius: 100px;
    border: 1.5px solid rgba(71, 6, 14, 0.2);
    background: transparent;
    color: var(--coffee-rich);
    cursor: pointer;
    transition: all 0.25s ease;
}

.filtro-btn:hover,
.filtro-btn.active {
    background: var(--coffee-rich);
    color: var(--cream);
    border-color: var(--coffee-rich);
}

/* ============================================================
   SECCIÓN / GRID DE PRODUCTOS
============================================================ */
.tienda-section {
    padding: 56px 20px 100px;
    max-width: 1300px;
    margin: 0 auto;
}

.tienda-section-header {
    display: flex;
    align-items: baseline;
    gap: 16px;
    margin-bottom: 44px;
}

.tienda-section-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 38px;
    font-weight: 600;
    color: var(--coffee-dark);
    letter-spacing: -0.5px;
    white-space: nowrap;
}

.tienda-section-line {
    flex: 1;
    height: 1px;
    background: linear-gradient(to right, rgba(71, 6, 14, 0.2), transparent);
}

.tienda-section-count {
    font-size: 11px;
    color: var(--coffee-mid);
    letter-spacing: 1px;
    text-transform: uppercase;
    white-space: nowrap;
}

.productos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 26px;
    position: relative;
}

/* ============================================================
   TARJETA DE PRODUCTO
============================================================ */
.producto-card {
    background: var(--white);
    border-radius: 20px;
    overflow: hidden;
    position: relative;
    border: 1px solid rgba(71, 6, 14, 0.08);
    display: flex;
    flex-direction: column;
    /* animación de entrada */
    opacity: 0;
    transform: translateY(22px);
    transition:
        opacity 0.5s ease,
        transform 0.5s ease,
        box-shadow 0.35s ease,
        border-color 0.35s ease;
}

.producto-card.visible {
    opacity: 1;
    transform: translateY(0);
}

.producto-card.visible:hover {
    transform: translateY(-8px);
    box-shadow: 0 22px 48px var(--shadow-warm), 0 6px 14px rgba(0, 0, 0, 0.05);
    border-color: rgba(71, 6, 14, 0.14);
}

/* Oculto por filtro */
.producto-card.filtrado {
    opacity: 0 !important;
    transform: scale(0.9) !important;
    pointer-events: none;
    position: absolute;
    visibility: hidden;
}

/* ── Badge ── */
.producto-badge {
    position: absolute;
    top: 14px;
    left: 14px;
    z-index: 3;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    padding: 5px 12px;
    border-radius: 100px;
}

.badge-nuevo    { background: var(--coffee-rich); color: var(--cream); }
.badge-popular  { background: var(--gold);         color: var(--coffee-deep); }
.badge-limitado { background: var(--coffee-dark);  color: var(--gold); }

/* ── Imagen ── */
.producto-img-wrap {
    position: relative;
    height: 218px;
    background: linear-gradient(135deg, var(--cream-soft) 0%, var(--cream-mid) 100%);
    overflow: hidden;
    flex-shrink: 0;
}

.producto-img-wrap::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 70% 30%, rgba(200, 169, 110, 0.1) 0%, transparent 55%);
    z-index: 1;
}

.producto-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 2;
    position: relative;
    transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.producto-card:hover .producto-img { transform: scale(1.06); }

.producto-img-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    position: relative;
}

.producto-img-placeholder i {
    font-size: 60px;
    color: var(--coffee-warm);
    opacity: 0.3;
}

/* ── Favorito ── */
.btn-favorito {
    position: absolute;
    top: 12px;
    right: 12px;
    z-index: 4;
    width: 34px;
    height: 34px;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--coffee-mid);
    font-size: 13px;
    transition: all 0.25s ease;
    backdrop-filter: blur(4px);
}

.btn-favorito:hover,
.btn-favorito.activo {
    background: var(--coffee-rich);
    color: var(--cream);
    transform: scale(1.1);
}

/* ── Info ── */
.producto-info {
    padding: 20px 20px 18px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.producto-categoria {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--gold);
    margin-bottom: 7px;
}

.producto-nombre {
    font-family: 'Cormorant Garamond', serif;
    font-size: 21px;
    font-weight: 600;
    color: var(--coffee-dark);
    line-height: 1.25;
    margin-bottom: 8px;
}

.producto-descripcion {
    font-size: 13px;
    color: var(--text-mid);
    line-height: 1.6;
    margin-bottom: 14px;
    flex: 1;
    font-weight: 300;
}

/* ── Rating ── */
.producto-rating {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 14px;
}

.estrellas {
    color: var(--gold);
    font-size: 11px;
    letter-spacing: 1px;
}

.rating-num {
    font-size: 11px;
    color: var(--text-mid);
    font-weight: 500;
}

/* ── Footer tarjeta ── */
.producto-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 14px;
    border-top: 1px solid rgba(71, 6, 14, 0.07);
    margin-top: auto;
}

.producto-precio { display: flex; flex-direction: column; }

.precio-antes {
    font-size: 11px;
    color: rgba(92, 58, 46, 0.45);
    text-decoration: line-through;
    letter-spacing: 0.5px;
}

.precio-actual {
    font-family: 'Cormorant Garamond', serif;
    font-size: 25px;
    font-weight: 700;
    color: var(--coffee-rich);
    line-height: 1;
    letter-spacing: -0.5px;
}

.precio-unidad {
    font-size: 10px;
    color: var(--text-mid);
    margin-top: 2px;
}

/* ── Botón agregar ── */
.btn-agregar {
    display: flex;
    align-items: center;
    gap: 7px;
    background: var(--coffee-rich);
    color: var(--cream);
    border: none;
    padding: 10px 16px;
    border-radius: 12px;
    font-family: 'DM Sans', sans-serif;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.5px;
    cursor: pointer;
    overflow: hidden;
    position: relative;
    transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.btn-agregar::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(200, 169, 110, 0.2);
    transform: translateX(-100%);
    transition: transform 0.3s ease;
}

.btn-agregar:hover            { transform: scale(1.05); background: var(--coffee-dark); }
.btn-agregar:hover::before    { transform: translateX(0); }
.btn-agregar:active           { transform: scale(0.97); }
.btn-agregar.agregado         { background: #2D7D46; pointer-events: none; }

/* ============================================================
   CARRITO — OVERLAY
============================================================ */
.carrito-overlay {
    position: fixed;
    inset: 0;
    background: rgba(28, 10, 13, 0.6);
    z-index: 2000;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.35s ease;
    backdrop-filter: blur(3px);
}

.carrito-overlay.visible {
    opacity: 1;
    pointer-events: all;
}

/* ============================================================
   CARRITO — DRAWER LATERAL
============================================================ */
.carrito-drawer {
    position: fixed;
    top: 0;
    right: 0;
    width: min(420px, 100vw);
    height: 100vh;
    background: var(--cream);
    z-index: 2001;
    transform: translateX(100%);
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
    box-shadow: -18px 0 56px rgba(28, 10, 13, 0.18);
}

.carrito-drawer.abierto { transform: translateX(0); }

/* Header del drawer */
.carrito-header {
    padding: 26px 26px 20px;
    border-bottom: 1px solid rgba(71, 6, 14, 0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    background: var(--coffee-dark);
}

.carrito-titulo {
    font-family: 'Cormorant Garamond', serif;
    font-size: 26px;
    font-weight: 600;
    color: var(--cream);
}

.carrito-titulo span {
    font-size: 12px;
    font-family: 'DM Sans', sans-serif;
    font-weight: 400;
    color: var(--gold);
    letter-spacing: 1px;
    margin-left: 8px;
}

.btn-cerrar-carrito {
    background: rgba(252, 246, 219, 0.1);
    border: 1px solid rgba(252, 246, 219, 0.15);
    color: var(--cream);
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 15px;
    transition: all 0.2s ease;
}

.btn-cerrar-carrito:hover { background: rgba(252, 246, 219, 0.18); }

/* Estado vacío */
.carrito-vacio {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 14px;
    padding: 40px;
    text-align: center;
}

.carrito-vacio i { font-size: 52px; color: rgba(71, 6, 14, 0.12); }

.carrito-vacio strong {
    font-family: 'Cormorant Garamond', serif;
    font-size: 20px;
    color: var(--coffee-dark);
    display: block;
    margin-bottom: 4px;
}

.carrito-vacio p { color: var(--text-mid); font-size: 14px; }

/* Lista de items */
.carrito-items {
    flex: 1;
    overflow-y: auto;
    padding: 18px 26px;
    scrollbar-width: thin;
    scrollbar-color: rgba(71, 6, 14, 0.15) transparent;
}

.carrito-item {
    display: flex;
    gap: 13px;
    padding: 14px 0;
    border-bottom: 1px solid rgba(71, 6, 14, 0.07);
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from { opacity: 0; transform: translateX(16px); }
    to   { opacity: 1; transform: translateX(0); }
}

.carrito-item-img {
    width: 62px;
    height: 62px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--cream-soft), var(--cream-mid));
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    overflow: hidden;
}

.carrito-item-img i { font-size: 22px; color: var(--coffee-warm); opacity: 0.45; }

.carrito-item-info { flex: 1; min-width: 0; }

.carrito-item-nombre {
    font-family: 'Cormorant Garamond', serif;
    font-size: 15px;
    font-weight: 600;
    color: var(--coffee-dark);
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.carrito-item-precio {
    font-size: 13px;
    color: var(--coffee-rich);
    font-weight: 600;
    margin-bottom: 7px;
}

.carrito-item-controles {
    display: flex;
    align-items: center;
    gap: 7px;
}

.btn-cantidad {
    width: 25px;
    height: 25px;
    border-radius: 6px;
    border: 1.5px solid rgba(71, 6, 14, 0.18);
    background: transparent;
    color: var(--coffee-rich);
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    line-height: 1;
}

.btn-cantidad:hover {
    background: var(--coffee-rich);
    color: var(--cream);
    border-color: var(--coffee-rich);
}

.cantidad-num {
    font-size: 13px;
    font-weight: 600;
    color: var(--coffee-dark);
    min-width: 18px;
    text-align: center;
}

.btn-eliminar-item {
    margin-left: auto;
    background: none;
    border: none;
    color: rgba(71, 6, 14, 0.28);
    cursor: pointer;
    font-size: 13px;
    transition: color 0.2s ease;
    padding: 4px;
}

.btn-eliminar-item:hover { color: var(--coffee-rich); }

/* Footer del drawer */
.carrito-footer {
    padding: 18px 26px 30px;
    border-top: 1px solid rgba(71, 6, 14, 0.1);
    background: var(--cream);
    flex-shrink: 0;
}

.carrito-resumen {
    display: flex;
    flex-direction: column;
    gap: 7px;
    margin-bottom: 18px;
}

.resumen-fila {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    color: var(--text-mid);
}

.resumen-fila.total {
    padding-top: 11px;
    border-top: 1px solid rgba(71, 6, 14, 0.1);
    font-size: 15px;
    font-weight: 600;
    color: var(--coffee-dark);
}

.resumen-fila.total .precio-total {
    font-family: 'Cormorant Garamond', serif;
    font-size: 22px;
    color: var(--coffee-rich);
}

.btn-checkout {
    width: 100%;
    background: var(--coffee-rich);
    color: var(--cream);
    border: none;
    padding: 15px;
    border-radius: 14px;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
}

.btn-checkout:hover {
    background: var(--coffee-dark);
    transform: translateY(-1px);
    box-shadow: 0 8px 22px var(--shadow-warm);
}

.btn-vaciar {
    width: 100%;
    background: transparent;
    color: var(--text-mid);
    border: 1.5px solid rgba(71, 6, 14, 0.14);
    padding: 10px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    margin-top: 9px;
    transition: all 0.2s ease;
}

.btn-vaciar:hover {
    border-color: var(--coffee-rich);
    color: var(--coffee-rich);
}

/* ============================================================
   BOTÓN FLOTANTE DEL CARRITO
============================================================ */
.btn-carrito-flotante {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 1500;
    background: var(--coffee-rich);
    color: var(--cream);
    width: 58px;
    height: 58px;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 21px;
    cursor: pointer;
    box-shadow: 0 8px 28px var(--shadow-warm);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.btn-carrito-flotante:hover {
    transform: scale(1.1);
    background: var(--coffee-dark);
}

.carrito-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: var(--gold);
    color: var(--coffee-deep);
    width: 21px;
    height: 21px;
    border-radius: 50%;
    font-size: 11px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--cream);
    animation: pop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes pop {
    from { transform: scale(0); }
    to   { transform: scale(1); }
}

.carrito-badge.oculto { display: none; }

/* ============================================================
   TOAST / NOTIFICACIONES
============================================================ */
.toast-container-custom {
    position: fixed;
    bottom: 106px;
    right: 30px;
    z-index: 3000;
    display: flex;
    flex-direction: column;
    gap: 7px;
    pointer-events: none;
}

.toast-notif {
    background: var(--coffee-dark);
    color: var(--cream);
    padding: 11px 18px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 9px;
    box-shadow: 0 8px 22px rgba(28, 10, 13, 0.28);
    animation: toastIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    border-left: 3px solid var(--gold);
    max-width: 270px;
}

.toast-notif.saliendo { animation: toastOut 0.25s ease forwards; }

@keyframes toastIn {
    from { opacity: 0; transform: translateX(16px) scale(0.95); }
    to   { opacity: 1; transform: translateX(0) scale(1); }
}

@keyframes toastOut {
    from { opacity: 1; transform: translateX(0) scale(1); }
    to   { opacity: 0; transform: translateX(16px) scale(0.95); }
}

/* ============================================================
   MODAL CHECKOUT
============================================================ */
.modal-checkout-overlay {
    position: fixed;
    inset: 0;
    background: rgba(28, 10, 13, 0.72);
    z-index: 3500;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
    backdrop-filter: blur(6px);
}

.modal-checkout-overlay.visible {
    opacity: 1;
    pointer-events: all;
}

.modal-checkout {
    background: var(--cream);
    border-radius: 22px;
    width: min(520px, 100%);
    max-height: 90vh;
    overflow-y: auto;
    padding: 38px;
    transform: scale(0.92) translateY(18px);
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    scrollbar-width: thin;
}

.modal-checkout-overlay.visible .modal-checkout {
    transform: scale(1) translateY(0);
}

.modal-checkout-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 30px;
    font-weight: 600;
    color: var(--coffee-dark);
    margin-bottom: 6px;
}

.modal-checkout-sub {
    font-size: 13px;
    color: var(--text-mid);
    margin-bottom: 28px;
}

/* ── Formulario ── */
.form-grupo { margin-bottom: 18px; }

.form-grupo label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: var(--coffee-rich);
    margin-bottom: 7px;
}

.form-grupo input,
.form-grupo select {
    width: 100%;
    padding: 12px 15px;
    border: 1.5px solid rgba(71, 6, 14, 0.14);
    border-radius: 11px;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    color: var(--coffee-dark);
    background: var(--white);
    transition: border-color 0.2s ease;
    outline: none;
}

.form-grupo input:focus,
.form-grupo select:focus {
    border-color: var(--coffee-rich);
    box-shadow: 0 0 0 3px rgba(71, 6, 14, 0.05);
}

.form-grupo input.error,
.form-grupo select.error { border-color: #DC2626; }

.form-error-msg {
    font-size: 11px;
    color: #DC2626;
    margin-top: 4px;
    display: none;
}

.form-error-msg.visible { display: block; }

.form-fila {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

/* ── Resumen dentro del modal ── */
.modal-checkout-resumen {
    background: var(--coffee-dark);
    border-radius: 14px;
    padding: 18px;
    margin: 22px 0;
    color: var(--cream);
}

.resumen-titulo-modal {
    font-family: 'Cormorant Garamond', serif;
    font-size: 17px;
    font-weight: 600;
    margin-bottom: 10px;
    color: var(--gold);
}

.resumen-item-modal {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    padding: 3px 0;
    border-bottom: 1px solid rgba(252, 246, 219, 0.07);
    color: rgba(252, 246, 219, 0.65);
}

.resumen-total-modal {
    display: flex;
    justify-content: space-between;
    padding-top: 10px;
    font-weight: 600;
    color: var(--cream);
    font-size: 14px;
}

.resumen-total-modal span:last-child {
    font-family: 'Cormorant Garamond', serif;
    font-size: 20px;
    color: var(--gold);
}

/* ── Acciones del modal ── */
.modal-acciones {
    display: flex;
    gap: 11px;
    margin-top: 6px;
}

.btn-cancelar-modal {
    flex: 1;
    padding: 13px;
    border: 1.5px solid rgba(71, 6, 14, 0.14);
    border-radius: 11px;
    background: transparent;
    color: var(--text-mid);
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-cancelar-modal:hover {
    border-color: var(--coffee-rich);
    color: var(--coffee-rich);
}

.btn-confirmar-modal {
    flex: 2;
    padding: 13px;
    border: none;
    border-radius: 11px;
    background: var(--coffee-rich);
    color: var(--cream);
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-confirmar-modal:hover {
    background: var(--coffee-dark);
    transform: translateY(-1px);
}

/* ── Estado de éxito ── */
.modal-success {
    text-align: center;
    padding: 20px 0;
    display: none;
}

.modal-success.visible   { display: block; }
.modal-form-content.oculto { display: none; }

.success-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #2D7D46, #22C55E);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 22px;
    font-size: 30px;
    color: white;
    animation: successPop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes successPop {
    from { transform: scale(0); }
    to   { transform: scale(1); }
}

.success-titulo {
    font-family: 'Cormorant Garamond', serif;
    font-size: 28px;
    font-weight: 600;
    color: var(--coffee-dark);
    margin-bottom: 10px;
}

.success-msg {
    font-size: 14px;
    color: var(--text-mid);
    line-height: 1.6;
    margin-bottom: 26px;
}

.btn-cerrar-success {
    background: var(--coffee-rich);
    color: var(--cream);
    border: none;
    padding: 13px 34px;
    border-radius: 11px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
}

.btn-cerrar-success:hover { background: var(--coffee-dark); }

/* ============================================================
   RESPONSIVE
============================================================ */
@media (max-width: 767px) {
    #servicios.seccion-tienda  { height: 340px; }
    .tienda-section            { padding: 36px 14px 80px; }
    .productos-grid            { grid-template-columns: repeat(2, 1fr); gap: 13px; }
    .producto-img-wrap         { height: 155px; }
    .producto-nombre           { font-size: 17px; }
    .precio-actual             { font-size: 21px; }
    .btn-agregar span          { display: none; }
    .btn-agregar               { padding: 9px 11px; }
    .tienda-filtros            { top: 83px; padding: 14px 10px 0; }
    .modal-checkout            { padding: 26px 18px; }
    .form-fila                 { grid-template-columns: 1fr; }
    .btn-carrito-flotante      { bottom: 18px; right: 18px; width: 52px; height: 52px; }
    .tienda-section-title      { font-size: 28px; }
}

@media (max-width: 430px) {
    .productos-grid {
        grid-template-columns: 1fr;
        max-width: 320px;
        margin: 0 auto;
    }
    .producto-img-wrap { height: 190px; }
}