<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CoffeeCol — Panel de Administración</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../csss/admin.css">
</head>
<body>

<!-- ══════════════════════════════════════════════════════════
     PANTALLA DE LOGIN
══════════════════════════════════════════════════════════ -->
<div id="login-screen">
    <div class="login-card">
        <div class="login-logo">☕</div>
        <h1>CoffeeCol Admin</h1>
        <p>Panel de administración de productos</p>

        <form id="login-form" novalidate>
            <div class="login-group">
                <label for="l-usuario">Usuario</label>
                <input type="text" id="l-usuario" placeholder="admin" autocomplete="username">
            </div>
            <div class="login-group">
                <label for="l-password">Contraseña</label>
                <input type="password" id="l-password" placeholder="••••••••" autocomplete="current-password">
                <div class="login-error" id="l-error">Usuario o contraseña incorrectos.</div>
            </div>
            <button type="submit" class="btn-login" id="btn-login">
                <i class="fas fa-sign-in-alt"></i> Ingresar
            </button>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     APLICACIÓN PRINCIPAL (oculta hasta login)
══════════════════════════════════════════════════════════ -->
<div id="app">

    <!-- Topbar -->
    <header class="topbar">
        <span class="topbar-logo">Coffee<span>Col</span> Admin</span>
        <div class="topbar-user">
            <i class="fas fa-user-circle"></i>
            <span id="topbar-user-name">admin</span>
        </div>
        <button class="btn-logout" id="btn-logout">
            <i class="fas fa-sign-out-alt"></i> Salir
        </button>
    </header>

    <!-- Contenido -->
    <main class="admin-main">

        <!-- Stats -->
        <div class="stats-grid" id="stats-grid">
            <div class="stat-card coffee">
                <div class="stat-icon"><i class="fas fa-box"></i></div>
                <div>
                    <div class="stat-num" id="stat-total">—</div>
                    <div class="stat-label">Total productos</div>
                </div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div class="stat-num" id="stat-activos">—</div>
                    <div class="stat-label">Activos</div>
                </div>
            </div>
            <div class="stat-card gold">
                <div class="stat-icon"><i class="fas fa-tag"></i></div>
                <div>
                    <div class="stat-num" id="stat-oferta">—</div>
                    <div class="stat-label">En oferta</div>
                </div>
            </div>
            <div class="stat-card yellow">
                <div class="stat-icon"><i class="fas fa-star"></i></div>
                <div>
                    <div class="stat-num" id="stat-rating">—</div>
                    <div class="stat-label">Rating promedio</div>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="table-card">
            <div class="table-header">
                <h2><i class="fas fa-mug-hot" style="color:var(--coffee);margin-right:8px;"></i>Productos</h2>
                <input type="search" class="input-buscar" id="input-buscar" placeholder="Buscar producto…">
                <button class="btn-nuevo" id="btn-nuevo">
                    <i class="fas fa-plus"></i> Nuevo producto
                </button>
            </div>

            <div class="table-wrap">
                <table id="tabla-productos">
                    <thead>
                        <tr>
                            <th style="width:46px">#</th>
                            <th style="width:46px">Ícono</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Badge</th>
                            <th>Estado</th>
                            <th style="width:110px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-productos">
                        <tr><td colspan="8" class="tabla-vacía">
                            <i class="fas fa-spinner fa-spin" style="font-size:1.4rem;opacity:.4;"></i>
                        </td></tr>
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <span id="tabla-info">Cargando…</span>
            </div>
        </div>

    </main>
</div>

<!-- ══════════════════════════════════════════════════════════
     MODAL — CREAR / EDITAR PRODUCTO
══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modal-producto">
    <div class="modal-box">

        <div class="modal-title">
            <i class="fas fa-cube" id="modal-icon-title"></i>
            <span id="modal-title-text">Nuevo producto</span>
        </div>

        <form id="form-producto" novalidate>
            <input type="hidden" id="f-id">

            <div class="form-grid">

                <!-- Nombre -->
                <div class="f-group full">
                    <label for="f-nombre">Nombre del producto *</label>
                    <input type="text" id="f-nombre" placeholder="Ej. Supremo Huila Reserve" maxlength="255">
                    <span class="f-error" id="fe-nombre">El nombre es obligatorio.</span>
                </div>

                <!-- Precio actual -->
                <div class="f-group">
                    <label for="f-precio">Precio (COP) *</label>
                    <input type="number" id="f-precio" placeholder="49900" min="0" step="100">
                    <span class="f-error" id="fe-precio">Ingresa un precio válido.</span>
                </div>

                <!-- Precio anterior (tachado) -->
                <div class="f-group">
                    <label for="f-precio-antes">Precio anterior (opcional)</label>
                    <input type="number" id="f-precio-antes" placeholder="58000" min="0" step="100">
                </div>

                <!-- Unidad -->
                <div class="f-group full">
                    <label for="f-unidad">Descripción de presentación *</label>
                    <input type="text" id="f-unidad" placeholder="Ej. 500g · molido o en grano" maxlength="120">
                    <span class="f-error" id="fe-unidad">La presentación es obligatoria.</span>
                </div>

                <!-- Categoría -->
                <div class="f-group">
                    <label for="f-categoria">Categoría *</label>
                    <select id="f-categoria">
                        <option value="">— Selecciona —</option>
                        <option value="tueste-claro">Tueste Claro</option>
                        <option value="tueste-medio">Tueste Medio</option>
                        <option value="tueste-oscuro">Tueste Oscuro</option>
                        <option value="capsulas">Cápsulas</option>
                        <option value="origen-especial">Origen Especial</option>
                    </select>
                    <span class="f-error" id="fe-categoria">Selecciona una categoría.</span>
                </div>

                <!-- Ícono Font Awesome -->
                <div class="f-group">
                    <label for="f-icono">Ícono (clase FA)</label>
                    <div class="icon-preview">
                        <input type="text" id="f-icono" placeholder="fa-mug-hot" style="flex:1;">
                        <div class="icon-preview-box" id="icono-preview">
                            <i class="fas fa-mug-hot" id="icono-preview-i"></i>
                        </div>
                    </div>
                </div>

                <!-- Badge texto -->
                <div class="f-group">
                    <label for="f-badge">Etiqueta (badge)</label>
                    <input type="text" id="f-badge" placeholder="Ej. ⭐ Popular · Nuevo · Limitado" maxlength="60">
                </div>

                <!-- Badge tipo -->
                <div class="f-group">
                    <label for="f-badge-tipo">Tipo de etiqueta</label>
                    <select id="f-badge-tipo">
                        <option value="">Sin etiqueta</option>
                        <option value="badge-popular">Popular (dorado)</option>
                        <option value="badge-nuevo">Nuevo (vino)</option>
                        <option value="badge-limitado">Limitado (oscuro)</option>
                    </select>
                </div>

                <!-- Rating -->
                <div class="f-group">
                    <label for="f-rating">Rating (0–5)</label>
                    <input type="number" id="f-rating" min="0" max="5" step="0.1" placeholder="4.9">
                </div>

                <!-- Cantidad reseñas -->
                <div class="f-group">
                    <label for="f-rating-cant">N.° reseñas</label>
                    <input type="number" id="f-rating-cant" min="0" placeholder="128">
                </div>

                <!-- Activo -->
                <div class="f-group">
                    <label for="f-activo">Estado</label>
                    <select id="f-activo">
                        <option value="1">Activo (visible en tienda)</option>
                        <option value="0">Inactivo (oculto)</option>
                    </select>
                </div>

                <!-- Descripción -->
                <div class="f-group full">
                    <label for="f-desc">Descripción</label>
                    <textarea id="f-desc" placeholder="Describe el sabor, proceso y características del producto…" maxlength="600"></textarea>
                </div>

            </div><!-- /form-grid -->

            <div class="modal-actions">
                <button type="button" class="btn-cancel" id="btn-cancel-modal">Cancelar</button>
                <button type="submit" class="btn-save" id="btn-save">
                    <i class="fas fa-save"></i> <span id="btn-save-text">Guardar producto</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     MODAL — CONFIRMAR ELIMINACIÓN
══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modal-confirmar">
    <div class="confirm-box">
        <div class="confirm-icon"><i class="fas fa-trash-alt"></i></div>
        <h3>Eliminar producto</h3>
        <p id="confirm-msg">¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.</p>
        <div class="confirm-actions">
            <button class="btn-cancel" id="btn-cancel-del">Cancelar</button>
            <button class="btn-confirm-del" id="btn-confirm-del">
                <i class="fas fa-trash-alt"></i> Sí, eliminar
            </button>
        </div>
    </div>
</div>

<!-- Toasts -->
<div class="admin-toasts" id="admin-toasts"></div>

<script src="../js/admin.js"></script>
</body>
</html>