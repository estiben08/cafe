<?php
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
?>
<link rel="stylesheet" href="../css/header.css">

<div class="header-container">
    <header class="header-principal">
        <nav class="navbar navbar-expand-lg navbar-custom">

            <!-- ── DESKTOP ── -->
            <div class="container">
                <a class="navbar-brand" href="../index.php">
                    <img src="../assets/imagenes/logos.png" alt="Tantico Logo">
                </a>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link <?= ($currentPage === 'index.php') ? 'active' : '' ?>" href="../index.php">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link <?= ($currentPage === 'nosotros.php') ? 'active' : '' ?>" href="nosotros.php">Nosotros</a></li>
                        <li class="nav-item"><a class="nav-link <?= ($currentPage === 'servicios.php') ? 'active' : '' ?>" href="servicios.php">Productos</a></li>
                        <li class="nav-item"><a class="nav-link <?= ($currentPage === 'contacto.php') ? 'active' : '' ?>" href="contacto.php">Contáctanos</a></li>
                    </ul>

                    <div class="social-icons">
                        <!-- Carrito desktop con badge -->
                        <button class="btn-carrito-nav" id="btn-carrito-desktop" aria-label="Carrito">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span class="nav-carrito-badge oculto" id="nav-badge-desktop">0</span>
                        </button>

                        <a href="loginu.php" id="btn-usuario" aria-label="Usuario">
                            <i class="fa-solid fa-user"></i>
                        </a>

                        <div id="menu-usuario" class="menu-usuario-dropdown">
                            <div class="menu-usuario-info">
                                <div class="menu-usuario-avatar" id="menu-avatar">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div>
                                    <p id="nombre-usuario" class="menu-usuario-nombre"></p>
                                    <span class="menu-usuario-tag">Tantico</span>
                                </div>
                            </div>
                            <button onclick="window.location.href='perfilu.php'" class="menu-usuario-item">
                                <i class="fa-solid fa-circle-user"></i> Ver perfil
                            </button>
                            <button id="btn-logout" class="menu-usuario-item menu-usuario-logout">
                                <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── MÓVIL ── -->
            <div class="mobile-bar">
                <button class="btn-hamburguesa" id="btnHamburguesa" aria-label="Abrir menú">
                    <span></span><span></span><span></span>
                </button>

                <a class="mobile-logo" href="../index.php">
                    <img src="../assets/imagenes/logos.png" alt="Tantico Logo">
                </a>

                <div class="mobile-icons-right">
                    <!-- Carrito móvil con badge -->
                    <div class="btn-carrito-mobile-wrap">
                        <a href="#" id="btn-carrito-mobile" aria-label="Carrito">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </a>
                        <span class="nav-carrito-badge-mobile oculto" id="nav-badge-mobile">0</span>
                    </div>
                    <a href="loginu.php" id="btn-usuario-mobile" aria-label="Usuario">
                        <i class="fa-solid fa-user"></i>
                    </a>
                </div>
            </div>

        </nav>

        <script type="module">
            import { observarUsuario, cerrarSesion } from '../js/auth.js';

            const btnUsuario     = document.getElementById('btn-usuario');
            const menuUsuario    = document.getElementById('menu-usuario');
            const nombreEl       = document.getElementById('nombre-usuario');
            const avatarEl       = document.getElementById('menu-avatar');
            const drawerGuest    = document.getElementById('drawer-footer-guest');
            const drawerUser     = document.getElementById('drawer-footer-user');
            const drawerNombreEl = document.getElementById('drawer-nombre');
            const drawerAvatarEl = document.getElementById('drawer-avatar');
            const btnMobile      = document.getElementById('btn-usuario-mobile');

            observarUsuario(
                (user) => {
                    nombreEl.textContent = user.displayName || user.email;
                    if (user.photoURL) avatarEl.innerHTML = `<img src="${user.photoURL}" alt="foto">`;
                    btnUsuario.removeAttribute('href');
                    btnUsuario.style.cursor = 'pointer';
                    btnUsuario.onclick = (e) => { e.preventDefault(); menuUsuario.classList.toggle('visible'); };

                    drawerGuest.style.display = 'none';
                    drawerUser.style.display  = 'block';
                    drawerNombreEl.textContent = user.displayName || user.email;
                    if (user.photoURL) drawerAvatarEl.innerHTML = `<img src="${user.photoURL}" alt="foto">`;

                    if (btnMobile) {
                        btnMobile.removeAttribute('href');
                        btnMobile.style.cursor = 'pointer';
                        btnMobile.style.position = 'relative';

                        const existingMobileMenu = document.getElementById('menu-usuario-mobile');
                        if (!existingMobileMenu) {
                            const mobileMenu = menuUsuario.cloneNode(true);
                            mobileMenu.id = 'menu-usuario-mobile';
                            mobileMenu.style.right = '0'; mobileMenu.style.top = 'calc(100% + 10px)';
                            mobileMenu.style.position = 'absolute'; mobileMenu.style.zIndex = '9999';
                            const mNombre = mobileMenu.querySelector('#nombre-usuario');
                            const mAvatar = mobileMenu.querySelector('#menu-avatar');
                            if (mNombre) { mNombre.id = 'nombre-usuario-m'; mNombre.textContent = user.displayName || user.email; }
                            if (mAvatar) { mAvatar.id = 'menu-avatar-m'; if (user.photoURL) mAvatar.innerHTML = `<img src="${user.photoURL}" alt="foto">`; }
                            const clonLogout = mobileMenu.querySelector('.menu-usuario-logout');
                            if (clonLogout) { clonLogout.id = 'btn-logout-mobile-clone'; clonLogout.addEventListener('click', () => cerrarSesion()); }
                            btnMobile.parentElement.style.position = 'relative';
                            btnMobile.parentElement.appendChild(mobileMenu);
                        }
                        btnMobile.onclick = (e) => {
                            e.preventDefault(); e.stopPropagation();
                            const mm = document.getElementById('menu-usuario-mobile');
                            if (mm) mm.classList.toggle('visible');
                        };
                    }
                },
                () => {
                    sessionStorage.removeItem('cc_usuario');
                    btnUsuario.setAttribute('href', 'loginu.php');
                    btnUsuario.onclick = null;
                    menuUsuario.classList.remove('visible');
                    drawerGuest.style.display = 'block';
                    drawerUser.style.display  = 'none';
                    if (btnMobile) {
                        btnMobile.setAttribute('href', 'loginu.php');
                        btnMobile.onclick = null;
                    }
                    const mm = document.getElementById('menu-usuario-mobile');
                    if (mm) mm.remove();
                }
            );

            document.getElementById('btn-logout').addEventListener('click', () => cerrarSesion());
            document.getElementById('btn-logout-drawer').addEventListener('click', () => cerrarSesion());

            document.addEventListener('click', (e) => {
                if (!btnUsuario?.contains(e.target) && !menuUsuario?.contains(e.target)) menuUsuario.classList.remove('visible');
                const mm = document.getElementById('menu-usuario-mobile');
                if (mm && !btnMobile?.contains(e.target) && !mm?.contains(e.target)) mm.classList.remove('visible');
            });
        </script>
    </header>

    <!-- ── Overlay + Drawer lateral (móvil) ── -->
    <div class="drawer-overlay" id="drawerOverlay"></div>
    <div class="drawer" id="drawer">
        <div class="drawer-header">
            <a href="../index.php" class="drawer-logo">
                <img src="../assets/imagenes/logos.png" alt="Tantico Logo"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                <span class="drawer-logo-text" style="display:none">Tantico</span>
            </a>
            <button class="btn-cerrar" id="btnCerrar" aria-label="Cerrar menú">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <nav class="drawer-nav">
            <a href="../index.php" class="<?= ($currentPage === 'index.php') ? 'active' : '' ?>"><i class="fa-solid fa-house"></i> Inicio</a>
            <a href="nosotros.php" class="<?= ($currentPage === 'nosotros.php') ? 'active' : '' ?>"><i class="fa-solid fa-users"></i> Nosotros</a>
            <a href="servicios.php" class="<?= ($currentPage === 'servicios.php') ? 'active' : '' ?>"><i class="fa-solid fa-box-open"></i> Productos</a>
            <div class="drawer-divider"></div>
            <a href="contacto.php" class="<?= ($currentPage === 'contacto.php') ? 'active' : '' ?>"><i class="fa-solid fa-envelope"></i> Contáctanos</a>
        </nav>
        <div class="drawer-footer" id="drawer-footer-guest">
            <a href="loginu.php" class="btn-iniciar-sesion">
                <i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión
            </a>
        </div>
        <div class="drawer-footer drawer-footer-user" id="drawer-footer-user" style="display:none;">
            <div class="drawer-user-info">
                <div class="drawer-user-avatar" id="drawer-avatar"><i class="fa-solid fa-user"></i></div>
                <div class="drawer-user-text">
                    <p id="drawer-nombre" class="drawer-user-nombre"></p>
                    <span class="drawer-user-tag">Tantico</span>
                </div>
            </div>
            <div class="drawer-user-actions">
                <a href="perfilu.php" class="drawer-user-action-btn">
                    <i class="fa-solid fa-circle-user"></i> Ver perfil
                </a>
                <a href="servicios.php" class="drawer-user-action-btn">
                    <i class="fa-solid fa-cart-shopping"></i> Carrito
                </a>
            </div>
            <button id="btn-logout-drawer" class="btn-cerrar-sesion">
                <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
            </button>
        </div>
    </div>
</div>

<script>
    /* ── Drawer hamburguesa ── */
    const drawer         = document.getElementById('drawer');
    const overlay        = document.getElementById('drawerOverlay');
    const btnHamburguesa = document.getElementById('btnHamburguesa');
    const btnCerrar      = document.getElementById('btnCerrar');

    function abrirDrawer()  { drawer?.classList.add('open'); overlay?.classList.add('active'); document.body.style.overflow = 'hidden'; }
    function cerrarDrawer() { drawer?.classList.remove('open'); overlay?.classList.remove('active'); document.body.style.overflow = ''; }

    btnHamburguesa?.addEventListener('click', abrirDrawer);
    btnCerrar?.addEventListener('click', cerrarDrawer);
    overlay?.addEventListener('click', cerrarDrawer);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarDrawer(); });

    /* ── Carrito Badge Sincronizado ── */
    const NAV_LS_KEY = 'coffeecol_carrito_v2';

    function leerTotalCarrito() {
        try {
            const data = JSON.parse(localStorage.getItem(NAV_LS_KEY) || '{}');
            return Object.values(data).reduce((s, item) => s + (item.cantidad || 0), 0);
        } catch (_) { return 0; }
    }

    function actualizarBadgesNav() {
        const total = leerTotalCarrito();
        const desktop = document.getElementById('nav-badge-desktop');
        const mobile  = document.getElementById('nav-badge-mobile');
        [desktop, mobile].forEach(el => {
            if (!el) return;
            el.textContent = total;
            el.classList.toggle('oculto', total === 0);
        });
    }

    function manejarClickCarrito(e) {
        e.preventDefault();
        const enServicios = window.location.pathname.includes('servicios.php');
        if (enServicios) {
            if (typeof toggleCarrito === 'function') {
                toggleCarrito();
            } else if (typeof abrirCarrito === 'function') {
                abrirCarrito();
            }
        } else {
            sessionStorage.setItem('abrir_carrito', '1');
            window.location.href = 'servicios.php';
        }
    }

    document.getElementById('btn-carrito-desktop')?.addEventListener('click', manejarClickCarrito);
    document.getElementById('btn-carrito-mobile')?.addEventListener('click', manejarClickCarrito);

    actualizarBadgesNav();
    window.addEventListener('storage', actualizarBadgesNav);
    setInterval(actualizarBadgesNav, 2000);
</script>