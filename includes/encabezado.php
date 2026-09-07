<!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <title>Tantico - Café de Especialidad &amp; Cafetería</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Onest:wght@500&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            html, body { overflow-x: hidden; width: 100%; }
            body {
                font-family: 'Inter', sans-serif;
                min-height: 100vh;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
            .header-container { display: flex; flex-direction: column; width: 100%; }

            .video-fondo {
                position: absolute; top: 0; left: 0;
                width: 100%; height: 100vh; overflow: hidden; z-index: -1;
            }
            .video-fondo video {
                position: absolute; top: 50%; left: 50%;
                min-width: 100%; min-height: 100%; width: auto; height: auto;
                transform: translate(-50%, -50%); object-fit: cover; will-change: transform;
            }
            .video-loading {
                position: absolute; top: 50%; left: 50%;
                transform: translate(-50%, -50%); color: white; font-size: 18px; z-index: 1; transition: opacity 0.3s ease;
            }

            .header-principal {
                position: fixed; top: 28px; left: 32px; right: 32px;
                height: 71px; max-width: 1220px; margin: 0 auto;
                z-index: 2000; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .navbar-custom {
                backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
                border: 2px solid rgba(255,255,255,0.2); border-radius: 11px; padding: 8px 0;
                box-shadow: 0 4px 20px rgba(0,0,0,0.1), inset 0 3px 3px rgba(0,0,0,0.5);
                height: 71px; display: flex; align-items: center;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .navbar-custom .container {
                max-width: 1220px; padding: 0 32px; width: 100%; display: flex; align-items: center;
            }
            .navbar-brand img { height: 80px; width: 200px transition: transform 0.3s ease; }
            .navbar-collapse { display: flex; align-items: center; justify-content: space-between; flex: 1; }
            .navbar-nav { display: flex; align-items: center; margin: 0; padding: 0; list-style: none; }
            .navbar-nav .nav-link {
                color: #ffffff !important; font-weight: 500; font-size: 13px;
                margin: 0 20px; line-height: 100%; position: relative;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); padding: 8px 12px; border-radius: 6px;
            }
            .navbar-nav .nav-link:hover {
                color: #f0f0f0 !important; background-color: rgba(255,255,255,0.1); transform: translateY(-2px);
            }
            .navbar-nav .nav-link:active { transform: translateY(0); }

            /* ── Íconos sociales desktop ── */
            .social-icons {
                backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
                border-radius: 12px; padding: 6px 14px;
                display: flex; align-items: center; gap: 0; z-index: 10;
            }
            .social-icons a {
                color: #ffffff; display: flex; align-items: center; justify-content: center;
                width: 24px; height: 24px; font-size: 17.5px; text-decoration: none; transition: all 0.3s ease;
            }
            .social-icons a:hover { transform: scale(1.15); background-color: rgba(255,255,255,0.15); border-radius: 50%; }
            .social-icons a:active { transform: scale(1); }

            /* ── Botón carrito con badge ── */
            .btn-carrito-nav {
                position: relative; color: #ffffff; display: flex; align-items: center;
                justify-content: center; width: 24px; height: 24px; font-size: 17.5px;
                text-decoration: none; transition: all 0.3s ease; cursor: pointer;
                background: none; border: none; padding: 0;
            }
            .btn-carrito-nav:hover { transform: scale(1.15); background-color: rgba(255,255,255,0.15); border-radius: 50%; }

            .nav-carrito-badge {
                position: absolute; top: -6px; right: -7px;
                background: #c0392b; color: #fff;
                font-size: 9px; font-weight: 700; line-height: 1;
                width: 16px; height: 16px; border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                border: 1.5px solid rgba(255,255,255,0.3);
                transition: all 0.2s; transform: scale(1);
                opacity: 1;
            }
            .nav-carrito-badge.oculto { opacity: 0; transform: scale(0); pointer-events: none; }

            /* ── Dropdown usuario ── */
            #btn-usuario {
                color: #ffffff; display: flex; align-items: center; justify-content: center;
                width: 24px; height: 24px; font-size: 17.5px; text-decoration: none; transition: all 0.3s ease;
            }
            #btn-usuario:hover { transform: scale(1.15); background-color: rgba(255,255,255,0.15); border-radius: 50%; }

            .menu-usuario-dropdown {
                position: absolute; top: calc(100% + 14px); right: 0; width: 230px;
                background: #1c0b09; border: 1px solid rgba(210,170,100,0.15); border-radius: 14px;
                overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.6), inset 0 1px 0 rgba(210,170,100,0.08);
                opacity: 0; transform: translateY(-6px) scale(0.98); pointer-events: none;
                transition: opacity .2s ease, transform .2s ease; z-index: 1000;
            }
            .menu-usuario-dropdown.visible { opacity: 1; transform: translateY(0) scale(1); pointer-events: all; }
            .menu-usuario-dropdown::before {
                content: ''; position: absolute; top: -5px; right: 12px;
                width: 10px; height: 10px; background: #1c0b09;
                border-top: 1px solid rgba(210,170,100,0.15); border-left: 1px solid rgba(210,170,100,0.15);
                transform: rotate(45deg);
            }
            .menu-usuario-info {
                padding: 16px 16px 13px; display: flex; align-items: center; gap: 11px;
                border-bottom: 1px solid rgba(210,170,100,0.1);
            }
            .menu-usuario-avatar {
                width: 36px; height: 36px; border-radius: 50%;
                background: linear-gradient(135deg,#8B3A10,#c4762a); border: 1.5px solid rgba(210,170,100,0.3);
                display: flex; align-items: center; justify-content: center;
                font-size: 15px; color: #f0e8d2; flex-shrink: 0; overflow: hidden;
            }
            .menu-usuario-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
            .menu-usuario-nombre {
                margin: 0; line-height: 1.2; font-size: 13px; font-weight: 500;
                color: #e8d8b4; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            }
            .menu-usuario-item {
                display: flex; align-items: center; gap: 10px; width: 100%; padding: 9px 16px;
                background: none; border: none; color: rgba(240,228,200,0.65); font-size: 13px;
                font-weight: 400; cursor: pointer; text-decoration: none;
                transition: background .15s, color .15s, padding-left .15s;
            }
            .menu-usuario-item:hover { background: rgba(210,170,100,0.08); color: #f0e8d2; padding-left: 20px; }
            .menu-usuario-logout { color: rgba(220,100,80,0.65); }
            .menu-usuario-logout:hover { background: rgba(200,60,40,0.08); color: #e07a5f; padding-left: 20px; }
            .menu-usuario-tag { font-size: 10.5px; color: rgba(210,170,100,0.5); letter-spacing: 0.4px; display: block; margin-top: 1px; }

            /* ── Barra móvil ── */
            .mobile-bar { display: none; }
            .btn-hamburguesa {
                background: none; border: none; cursor: pointer; display: flex;
                flex-direction: column; justify-content: center; gap: 5px;
                width: 38px; height: 38px; padding: 6px; border-radius: 7px; transition: background 0.2s; flex-shrink: 0;
            }
            .btn-hamburguesa:hover { background: rgba(255,255,255,0.1); }
            .btn-hamburguesa span { display: block; width: 20px; height: 2px; background: #fff; border-radius: 2px; transition: all 0.3s ease; }
            .mobile-logo img { height: 34px; width: auto; display: block; }
            .mobile-icons-right { display: flex; align-items: center; gap: 2px; flex-shrink: 0; }

            /* Botón carrito móvil con badge */
            .btn-carrito-mobile-wrap { position: relative; display: flex; }
            #btn-carrito-mobile, #btn-usuario-mobile {
                color: #ffffff; display: flex; align-items: center; justify-content: center;
                width: 38px; height: 38px; font-size: 17px; text-decoration: none;
                border-radius: 50%; transition: all 0.3s ease; flex-shrink: 0;
            }
            #btn-carrito-mobile:hover, #btn-usuario-mobile:hover { transform: scale(1.1); background: rgba(255,255,255,0.15); }
            .nav-carrito-badge-mobile {
                position: absolute; top: 2px; right: 2px;
                background: #c0392b; color: #fff; font-size: 9px; font-weight: 700; line-height: 1;
                width: 15px; height: 15px; border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                border: 1.5px solid rgba(255,255,255,0.3); transition: all 0.2s;
            }
            .nav-carrito-badge-mobile.oculto { opacity: 0; transform: scale(0); pointer-events: none; }

            /* ── Drawer lateral ── */
            .drawer-overlay {
                position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 2998;
                opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
                backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px);
            }
            .drawer-overlay.active { opacity: 1; pointer-events: all; }
            .drawer {
                position: fixed; top: 0; left: 0; width: 270px; height: 100vh;
                background: #1c0b09; z-index: 2999; transform: translateX(-100%);
                transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
                display: flex; flex-direction: column;
                border-right: 1px solid rgba(210,170,100,0.12); box-shadow: 8px 0 40px rgba(0,0,0,0.7);
            }
            .drawer.open { transform: translateX(0); }
            .drawer-header {
                display: flex; align-items: center; justify-content: space-between;
                padding: 20px 18px 16px; border-bottom: 1px solid rgba(210,170,100,0.1);
            }
            .drawer-logo img { height: 34px; width: auto; }
            .drawer-logo-text { color: #e8d8b4; font-size: 18px; font-weight: 700; letter-spacing: 1px; }
            .btn-cerrar {
                background: none; border: none; color: rgba(240,228,200,0.55); font-size: 20px;
                cursor: pointer; width: 32px; height: 32px; display: flex; align-items: center;
                justify-content: center; border-radius: 50%; transition: all 0.2s;
            }
            .btn-cerrar:hover { color: #e8d8b4; background: rgba(210,170,100,0.1); }
            .drawer-nav { flex: 1; padding: 8px 0; }
            .drawer-nav a {
                display: flex; align-items: center; gap: 12px; padding: 13px 22px;
                color: rgba(240,228,200,0.72); text-decoration: none; font-size: 15px; font-weight: 500;
                transition: all 0.2s ease; border-left: 3px solid transparent;
            }
            .drawer-nav a i { width: 17px; text-align: center; font-size: 13px; color: rgba(210,170,100,0.55); transition: color 0.2s; }
            .drawer-nav a:hover { color: #f0e8d2; background: rgba(210,170,100,0.07); border-left-color: rgba(210,170,100,0.45); padding-left: 26px; }
            .drawer-nav a:hover i { color: rgba(210,170,100,0.9); }
            .drawer-divider { height: 1px; background: rgba(210,170,100,0.1); margin: 4px 18px; }
            .drawer-footer { padding: 14px 18px 26px; border-top: 1px solid rgba(210,170,100,0.1); }
            .btn-iniciar-sesion {
                display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;
                padding: 12px; background: linear-gradient(135deg,#8B3A10,#c4762a); color: #f0e8d2;
                font-size: 14px; font-weight: 600; letter-spacing: 0.3px; border: none; border-radius: 10px;
                cursor: pointer; text-decoration: none; transition: all 0.25s ease; box-shadow: 0 4px 14px rgba(139,58,16,0.4);
            }
            .btn-iniciar-sesion:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(139,58,16,0.55); filter: brightness(1.08); color: #f0e8d2; }
            .drawer-footer-user { padding: 14px 18px 26px !important; }
            .drawer-user-info { display: flex; align-items: center; gap: 10px; padding-bottom: 12px; border-bottom: 1px solid rgba(210,170,100,0.1); margin-bottom: 10px; }
            .drawer-user-avatar {
                width: 38px; height: 38px; border-radius: 50%;
                background: linear-gradient(135deg,#8B3A10,#c4762a); border: 1.5px solid rgba(210,170,100,0.35);
                display: flex; align-items: center; justify-content: center;
                font-size: 15px; color: #f0e8d2; flex-shrink: 0; overflow: hidden;
            }
            .drawer-user-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
            .drawer-user-nombre { margin: 0; font-size: 13px; font-weight: 600; color: #e8d8b4; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; }
            .drawer-user-tag { font-size: 10.5px; color: rgba(210,170,100,0.5); letter-spacing: 0.4px; display: block; margin-top: 1px; }
            .drawer-user-actions { display: flex; gap: 8px; margin-bottom: 10px; }
            .drawer-user-action-btn {
                flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;
                gap: 5px; padding: 10px 8px; background: rgba(210,170,100,0.07);
                border: 1px solid rgba(210,170,100,0.12); border-radius: 10px;
                color: rgba(240,228,200,0.75); font-size: 12px; font-weight: 500; text-decoration: none; transition: all 0.2s ease;
            }
            .drawer-user-action-btn i { font-size: 17px; color: rgba(210,170,100,0.7); }
            .drawer-user-action-btn:hover { background: rgba(210,170,100,0.14); color: #f0e8d2; border-color: rgba(210,170,100,0.3); }
            .drawer-user-action-btn:hover i { color: rgba(210,170,100,1); }
            .btn-cerrar-sesion {
                display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;
                padding: 11px; background: rgba(200,60,40,0.12); border: 1px solid rgba(200,60,40,0.2);
                color: rgba(220,100,80,0.85); font-size: 13px; font-weight: 600; border-radius: 10px;
                cursor: pointer; transition: all 0.25s ease;
            }
            .btn-cerrar-sesion:hover { background: rgba(200,60,40,0.2); color: #e07a5f; border-color: rgba(200,60,40,0.4); }

            @media (max-width: 1024px) {
                .header-principal { top: 14px; left: 12px; right: 12px; height: auto; }
                .navbar-custom { height: 56px; padding: 0 14px; border-radius: 12px; justify-content: space-between; position: relative; }
                .navbar-custom .container { display: none; }
                .mobile-bar { display: flex !important; align-items: center; justify-content: space-between; width: 100%; padding: 0 4px; }
            }
        </style>
    </head>

    <body>
        <div class="header-container">
            <header class="header-principal">
                <nav class="navbar navbar-expand-lg navbar-custom">

                    <!-- ── BARRA DESKTOP ── -->
                    <div class="container">
                        <a class="navbar-brand" href="../index.php">
                            <div class="coffee-logo">
                                <img src="../assets/imagenes/logos.png" alt="Tantico Logo">
                            </div>
                        </a>

                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav mx-auto">
                                <li class="nav-item"><a class="nav-link" href="../index.php">Inicio</a></li>
                                <li class="nav-item"><a class="nav-link" href="nosotros.php">Nosotros</a></li>
                                <li class="nav-item"><a class="nav-link" href="servicios.php">Productos</a></li>
                                <li class="nav-item"><a class="nav-link" href="contacto.php">Contáctanos</a></li>
                            </ul>

                            <div class="social-icons" style="position:relative;">

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

                    <!-- ── BARRA MÓVIL ── -->
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
                    import { observarUsuario, cerrarSesion } from '<?= str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/") - 2) ?>js/auth.js';

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
                            btnUsuario.href = 'loginu.php';
                            drawerGuest.style.display = 'block';
                            drawerUser.style.display  = 'none';
                            if (btnMobile) btnMobile.href = 'loginu.php';
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
                    <a href="../index.php"><i class="fa-solid fa-house"></i> Inicio</a>
                    <a href="nosotros.php"><i class="fa-solid fa-users"></i> Nosotros</a>
                    <a href="servicios.php"><i class="fa-solid fa-box-open"></i> Productos</a>
                    <div class="drawer-divider"></div>
                    <a href="contacto.php"><i class="fa-solid fa-envelope"></i> Contáctanos</a>
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

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            /* ── Drawer hamburguesa ── */
            const drawer         = document.getElementById('drawer');
            const overlay        = document.getElementById('drawerOverlay');
            const btnHamburguesa = document.getElementById('btnHamburguesa');
            const btnCerrar      = document.getElementById('btnCerrar');

            function abrirDrawer()  { drawer.classList.add('open'); overlay.classList.add('active'); document.body.style.overflow = 'hidden'; }
            function cerrarDrawer() { drawer.classList.remove('open'); overlay.classList.remove('active'); document.body.style.overflow = ''; }

            btnHamburguesa.addEventListener('click', abrirDrawer);
            btnCerrar.addEventListener('click', cerrarDrawer);
            overlay.addEventListener('click', cerrarDrawer);
            document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarDrawer(); });

            /* ══════════════════════════════════════════
               CARRITO — badge sincronizado en todas las páginas
               Lee localStorage y actualiza el contador del nav.
               Si estamos en servicios.php, abre el drawer del carrito.
               Si no, redirige a servicios.php.
            ══════════════════════════════════════════ */
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
                    // Abrir el drawer del carrito que vive en servicios.php
                    if (typeof toggleCarrito === 'function') {
                        toggleCarrito();
                    } else if (typeof abrirCarrito === 'function') {
                        abrirCarrito();
                    }
                } else {
                    // Ir a servicios y abrir carrito al llegar
                    sessionStorage.setItem('abrir_carrito', '1');
                    window.location.href = 'servicios.php';
                }
            }

            // Asignar click a ambos botones del carrito
            document.getElementById('btn-carrito-desktop')?.addEventListener('click', manejarClickCarrito);
            document.getElementById('btn-carrito-mobile')?.addEventListener('click', manejarClickCarrito);

            // Actualizar badge al cargar
            actualizarBadgesNav();

            // Escuchar cambios en localStorage (cuando se agrega desde servicios.php)
            window.addEventListener('storage', actualizarBadgesNav);

            // Polling ligero cada 2 segundos para actualizar badge en la misma pestaña
            setInterval(actualizarBadgesNav, 2000);
        </script>

<!-- Agregar esto al final del DOMContentLoaded en service.js -->
<!--
    // Al final de document.addEventListener('DOMContentLoaded', ...) agrega:
    if (sessionStorage.getItem('abrir_carrito') === '1') {
        sessionStorage.removeItem('abrir_carrito');
        setTimeout(() => abrirCarrito(), 600);
    }
-->