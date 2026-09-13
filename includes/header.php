<link rel="stylesheet" href="css/header.css">

<style>
    /* Estilos específicos de la sección Hero y Fondo en Homepage — Tipografía Homogénea Goudy Bookletter 1911 */
    .header-container {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 100%;
        min-height: 100vh;
        position: relative;
        overflow: hidden;
        box-sizing: border-box;
    }

    .video-fondo {
        position: absolute; top: 0; left: 0;
        width: 100%; height: 100%; min-height: 100%; overflow: hidden; z-index: 0;
    }
    .video-fondo::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at center, rgba(22, 10, 7, 0.42) 0%, rgba(22, 10, 7, 0.88) 85%);
        z-index: 1;
    }
    .video-fondo video {
        position: absolute; top: 50%; left: 50%;
        min-width: 100%; min-height: 100%; width: auto; height: auto;
        transform: translate(-50%, -50%); object-fit: cover; will-change: transform;
    }
    .video-loading {
        position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -50%); color: white; font-size: 18px; z-index: 2; transition: opacity 0.3s ease;
    }

    .hero-section {
        background-size: cover; background-position: center;
        padding: 130px 20px 35px; display: flex; flex: 1; align-items: center; justify-content: center;
        min-height: auto; color: #fff; text-align: center; position: relative; z-index: 2;
    }
    .hero-content {
        position: relative; top: auto; left: auto; transform: none;
        width: 840px; max-width: 92%; height: auto; display: flex; flex-direction: column;
        align-items: center; justify-content: center; animation: fadeInUp 1s cubic-bezier(0.16, 1, 0.3, 1);
        z-index: 2; margin: 0 auto;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(24px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .hero-pill-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 18px;
        background: rgba(198, 167, 107, 0.15);
        border: 1px solid rgba(198, 167, 107, 0.35);
        border-radius: 30px;
        color: #E8D8B4;
        font-family: 'Inter', -apple-system, sans-serif;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 1.8px;
        text-transform: uppercase;
        margin-bottom: 20px;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    .hero-pill-badge i {
        color: #C6A76B;
        font-size: 11px;
    }

    .hero-content h1,
    .hero-title {
        font-family: 'Goudy Bookletter 1911', 'Playfair Display', Georgia, serif;
        font-size: 46px;
        font-weight: 400;
        line-height: 1.15;
        color: #FFFFFF;
        margin-bottom: 18px;
        text-shadow: 0 4px 24px rgba(0, 0, 0, 0.55);
    }
    .hero-content h1 em,
    .hero-title em {
        font-style: italic;
        background: linear-gradient(135deg, #F0E4CE 0%, #C6A76B 60%, #E8D8B4 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-content p,
    .hero-lead {
        font-family: 'Inter', -apple-system, sans-serif;
        margin-top: 0;
        margin-bottom: 28px;
        font-size: 16.5px;
        line-height: 1.65;
        font-weight: 400;
        color: rgba(251, 248, 242, 0.88);
        max-width: 660px;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
    }

    .hero-actions-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .btn-asesor {
        display: inline-flex; justify-content: center; align-items: center; gap: 8px;
        padding: 13px 28px; font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 600;
        text-align: center; color: #160A07; text-decoration: none;
        border: 1px solid rgba(198, 167, 107, 0.4);
        border-radius: 8px; background: linear-gradient(135deg, #C6A76B, #A88748);
        box-shadow: 0 6px 20px rgba(198, 167, 107, 0.35);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer;
    }
    .btn-asesor:hover {
        background: linear-gradient(135deg, #E8D8B4, #C6A76B);
        color: #160A07; transform: translateY(-2px); box-shadow: 0 10px 26px rgba(198, 167, 107, 0.5);
    }
    .btn-asesor:active { transform: translateY(0); }

    .btn-hero-outline {
        display: inline-flex; justify-content: center; align-items: center; gap: 8px;
        padding: 12px 24px; font-family: 'Inter', sans-serif; font-size: 13.5px; font-weight: 500;
        text-align: center; color: #FBF8F2; text-decoration: none;
        border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 8px;
        background-color: rgba(22, 10, 7, 0.5); backdrop-filter: blur(8px);
        transition: all 0.3s ease; cursor: pointer;
    }
    .btn-hero-outline:hover {
        background-color: rgba(255, 255, 255, 0.14); border-color: rgba(255, 255, 255, 0.5);
        color: #FFFFFF; transform: translateY(-2px);
    }

    .partners-section {
        position: relative;
        z-index: 10;
        width: 100%;
        max-width: 1220px;
        margin: 0 auto;
        padding: 10px 32px 48px; /* Espacio elegante antes del final del video */
        box-sizing: border-box;
    }
    .partners-content { display: flex; align-items: center; justify-content: space-between; gap: 30px; }
    .partners-carousel {
        width: 792px; max-width: 100%; height: 95px; background: rgba(22, 10, 7, 0.65);
        border-radius: 12px; padding: 20px; backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
        border: 1px solid rgba(210, 170, 100, 0.2); transition: all 0.3s ease; position: relative;
        box-sizing: border-box; overflow: hidden;
    }
    .partners-text {
        color: #ffffff;
        font-family: 'Goudy Bookletter 1911', 'Playfair Display', Georgia, serif;
        font-size: 22px;
        font-weight: 400;
        line-height: 1.25;
        min-width: 200px;
        text-align: left;
    }
    .partners-text strong {
        font-weight: 400;
        font-family: inherit;
    }
    .arriba {
        position: absolute; top: 13px; left: 25px; width: 220px; height: 15px;
        color: #e8d8b4; font-family: 'Inter', sans-serif; font-weight: 500; font-size: 12px;
        line-height: 100%; text-align: center;
    }
    .carousel-controls {
        position: relative; display: flex; justify-content: space-between; align-items: center;
        height: 100%; margin-top: 10px; margin-left: 20px; gap: 40px;
    }
    .carousel-viewport { overflow: hidden; flex: 1; display: flex; min-width: 0; }
    .carousel-btn {
        border: 1px solid rgba(210, 170, 100, 0.3); color: #1a0e08; width: 40px; height: 40px;
        border-radius: 8px; display: flex; align-items: center; justify-content: center;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1); cursor: pointer;
        background-color: rgba(252,246,219,0.9); z-index: 10; flex-shrink: 0;
    }
    .carousel-btn:hover { background-color: #ffffff; transform: scale(1.05); }
    .logo-container { display: flex; gap: 30px; transform: translateX(0); will-change: transform; width: max-content; transition: transform 0.2s linear; }
    .izquierdo, .derecho { font-size: 18px; transition: all 0.3s ease; }
    .logo-image { height: 75px; width: auto; flex-shrink: 0; object-fit: contain; }
    .logo-image:hover { transform: scale(1.05); }

    @media (max-width: 1199px) and (min-width: 992px) {
        .partners-section { padding: 10px 24px 36px; }
        .partners-text { font-size: 19px; min-width: 170px; }
        .partners-carousel { max-width: calc(100% - 200px); }
    }

    @media (max-width: 991px) {
        .header-container {
            min-height: 85vh;
        }
        .hero-section {
            min-height: 85vh;
            padding: 105px 20px 45px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-content {
            position: relative;
            top: auto;
            left: auto;
            transform: none;
            width: 100%;
            max-width: 580px;
            padding: 0 12px;
            margin: auto;
        }
        .hero-pill-badge {
            font-size: 10px;
            letter-spacing: 1.4px;
            padding: 5px 15px;
            margin-bottom: 14px;
        }
        .hero-content h1, .hero-title {
            font-size: clamp(28px, 6.5vw, 36px);
            line-height: 1.16;
            margin-bottom: 14px;
        }
        .hero-content p, .hero-lead {
            font-size: 14px;
            line-height: 1.5;
            margin-top: 0;
            margin-bottom: 22px;
            max-width: 460px;
        }
        .hero-actions-row {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 10px;
            width: 100%;
            flex-wrap: wrap;
        }
        .btn-asesor {
            width: auto;
            min-width: 165px;
            padding: 11px 22px;
            font-size: 13.5px;
        }
        .btn-hero-outline {
            width: auto;
            min-width: 135px;
            padding: 11px 18px;
            font-size: 13px;
        }
        /* Ocultar carrusel pesado de aliados en móvil para dar aire y ligereza */
        .partners-section {
            display: none !important;
        }
    }

    @media (max-width: 480px) {
        .hero-section {
            min-height: 78vh;
            padding: 95px 12px 35px;
        }
        .hero-content {
            padding: 0 6px;
        }
        .hero-pill-badge {
            font-size: 8.5px;
            letter-spacing: 1.1px;
            padding: 4px 12px;
            margin-bottom: 10px;
        }
        .hero-content h1, .hero-title {
            font-size: clamp(23px, 6.8vw, 27px);
            line-height: 1.2;
            margin-bottom: 10px;
        }
        .hero-content p, .hero-lead {
            font-size: 13px;
            line-height: 1.42;
            margin-bottom: 18px;
        }
        .hero-actions-row {
            flex-direction: row;
            gap: 8px;
            width: 100%;
            justify-content: center;
        }
        .btn-asesor {
            flex: 1;
            min-width: unset;
            max-width: 160px;
            padding: 10px 12px;
            font-size: 12.5px;
        }
        .btn-hero-outline {
            flex: 1;
            min-width: unset;
            max-width: 140px;
            padding: 10px 10px;
            font-size: 12px;
        }
    }
</style>

<div class="header-container">

    <div class="video-fondo">
        <div class="video-loading"></div>
        <video autoplay muted loop playsinline preload="metadata">
            <source src="assets/imagenes/fvideo.mp4" type="video/mp4">
            Tu navegador no soporta el video.
        </video>
    </div>

    <header class="header-principal">
        <nav class="navbar navbar-expand-lg navbar-custom">

            <!-- ── DESKTOP ── -->
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="assets/imagenes/logos.png" alt="Tantico Logo">
                </a>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link active" href="index.php">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link" href="includes/nosotros.php">Nosotros</a></li>
                        <li class="nav-item"><a class="nav-link" href="includes/servicios.php">Productos</a></li>
                        <li class="nav-item"><a class="nav-link" href="includes/contacto.php">Contáctanos</a></li>
                    </ul>
                    <div class="social-icons">
                        <button class="btn-carrito-nav" id="btn-carrito-desktop" aria-label="Carrito">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span class="nav-carrito-badge oculto" id="nav-badge-desktop">0</span>
                        </button>
                        <a href="includes/loginu.php" id="btn-usuario" aria-label="Usuario">
                            <i class="fa-solid fa-user"></i>
                        </a>
                        <div id="menu-usuario" class="menu-usuario-dropdown">
                            <div class="menu-usuario-info">
                                <div class="menu-usuario-avatar" id="menu-avatar"><i class="fa-solid fa-user"></i></div>
                                <div>
                                    <p id="nombre-usuario" class="menu-usuario-nombre"></p>
                                    <span class="menu-usuario-tag">Tantico</span>
                                </div>
                            </div>
                            <button onclick="window.location.href='includes/perfilu.php'" class="menu-usuario-item">
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
                <a class="mobile-logo" href="index.php">
                    <img src="assets/imagenes/logos.png" alt="Tantico Logo">
                </a>
                <div class="mobile-icons-right">
                    <div class="btn-carrito-mobile-wrap">
                        <a href="#" id="btn-carrito-mobile" aria-label="Carrito">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </a>
                        <span class="nav-carrito-badge-mobile oculto" id="nav-badge-mobile">0</span>
                    </div>
                    <a href="includes/loginu.php" id="btn-usuario-mobile" aria-label="Usuario">
                        <i class="fa-solid fa-user"></i>
                    </a>
                </div>
            </div>

        </nav>

        <script type="module">
            import { observarUsuario, cerrarSesion } from './js/auth.js';
            const btnUsuario   = document.getElementById('btn-usuario');
            const menuUsuario  = document.getElementById('menu-usuario');
            const nombreEl     = document.getElementById('nombre-usuario');
            const avatarEl     = document.getElementById('menu-avatar');
            const drawerGuest  = document.getElementById('drawer-footer-guest');
            const drawerUser   = document.getElementById('drawer-footer-user');
            const drawerNombreEl = document.getElementById('drawer-nombre');
            const drawerAvatarEl = document.getElementById('drawer-avatar');
            const btnMobile    = document.getElementById('btn-usuario-mobile');

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
                        btnMobile.style.cursor   = 'pointer';
                        btnMobile.style.position = 'relative';
                        const existingMobileMenu = document.getElementById('menu-usuario-mobile');
                        if (!existingMobileMenu) {
                            const mobileMenu = menuUsuario.cloneNode(true);
                            mobileMenu.id = 'menu-usuario-mobile';
                            mobileMenu.style.right    = '0';
                            mobileMenu.style.top      = 'calc(100% + 10px)';
                            mobileMenu.style.position = 'absolute';
                            mobileMenu.style.zIndex   = '9999';
                            const mNombre = mobileMenu.querySelector('#nombre-usuario');
                            const mAvatar = mobileMenu.querySelector('#menu-avatar');
                            if (mNombre) { mNombre.id = 'nombre-usuario-m'; mNombre.textContent = user.displayName || user.email; }
                            if (mAvatar) { mAvatar.id = 'menu-avatar-m'; if (user.photoURL) mAvatar.innerHTML = `<img src="${user.photoURL}" alt="foto">`; }
                            const clonLogout = mobileMenu.querySelector('.menu-usuario-logout');
                            if (clonLogout) {
                                clonLogout.id = 'btn-logout-mobile-clone';
                                clonLogout.addEventListener('click', () => cerrarSesion());
                            }
                            btnMobile.parentElement.style.position = 'relative';
                            btnMobile.parentElement.appendChild(mobileMenu);
                        }
                        btnMobile.onclick = (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            const mm = document.getElementById('menu-usuario-mobile');
                            if (mm) mm.classList.toggle('visible');
                        };
                    }
                },
                () => {
                    sessionStorage.removeItem('cc_usuario');
                    btnUsuario.setAttribute('href', 'includes/loginu.php');
                    btnUsuario.onclick = null;
                    menuUsuario.classList.remove('visible');
                    drawerGuest.style.display = 'block';
                    drawerUser.style.display  = 'none';
                    if (btnMobile) {
                        btnMobile.setAttribute('href', 'includes/loginu.php');
                        btnMobile.onclick = null;
                    }
                    const mm = document.getElementById('menu-usuario-mobile');
                    if (mm) mm.remove();
                }
            );

            document.getElementById('btn-logout').addEventListener('click', () => cerrarSesion());
            document.getElementById('btn-logout-drawer').addEventListener('click', () => cerrarSesion());

            document.addEventListener('click', (e) => {
                if (!btnUsuario?.contains(e.target) && !menuUsuario?.contains(e.target))
                    menuUsuario.classList.remove('visible');
                const mm = document.getElementById('menu-usuario-mobile');
                if (mm && !btnMobile?.contains(e.target) && !mm?.contains(e.target))
                    mm.classList.remove('visible');
            });
        </script>
    </header>

    <!-- Overlay + Drawer -->
    <div class="drawer-overlay" id="drawerOverlay"></div>
    <div class="drawer" id="drawer">
        <div class="drawer-header">
            <a href="index.php" class="drawer-logo">
                <img src="assets/imagenes/logos.png" alt="Tantico Logo"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                <span class="drawer-logo-text" style="display:none">Tantico</span>
            </a>
            <button class="btn-cerrar" id="btnCerrar" aria-label="Cerrar menú">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <nav class="drawer-nav">
            <a href="index.php" class="active"><i class="fa-solid fa-house"></i> Inicio</a>
            <a href="includes/nosotros.php"><i class="fa-solid fa-users"></i> Nosotros</a>
            <a href="includes/servicios.php"><i class="fa-solid fa-box-open"></i> Productos</a>
            <div class="drawer-divider"></div>
            <a href="includes/contacto.php"><i class="fa-solid fa-envelope"></i> Contáctanos</a>
        </nav>
        <div class="drawer-footer" id="drawer-footer-guest">
            <a href="includes/loginu.php" class="btn-iniciar-sesion">
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
                <a href="includes/perfilu.php" class="drawer-user-action-btn">
                    <i class="fa-solid fa-circle-user"></i> Ver perfil
                </a>
                <a href="includes/servicios.php" class="drawer-user-action-btn">
                    <i class="fa-solid fa-cart-shopping"></i> Carrito
                </a>
            </div>
            <button id="btn-logout-drawer" class="btn-cerrar-sesion">
                <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
            </button>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <span class="hero-pill-badge">
                    <i class="fa-solid fa-leaf"></i> CAFÉ DE ESPECIALIDAD · ORIGEN HUILA
                </span>
                <h1 class="hero-title">
                    Café de Especialidad<br><em>El auténtico sabor del Huila</em>
                </h1>
                <p class="hero-lead">
                    Granos selectos cultivados en las montañas del Huila, preparados con maestría artesanal para crear momentos únicos.
                </p>
                <div class="hero-actions-row">
                    <a href="includes/servicios.php" class="btn-asesor">
                        <i class="fa-solid fa-mug-hot"></i> Explorar carta
                    </a>
                    <a href="includes/nosotros.php" class="btn-hero-outline">
                        <i class="fa-solid fa-circle-info"></i> Conócenos
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    <section class="partners-section">
        <div class="partners-content">
            <div class="partners-text"><strong>Orígenes y Variedades<br>de Café Huilense</strong></div>
            <div class="partners-carousel">
                <div class="arriba">Marcas Aliadas</div>
                <div class="carousel-controls">
                    <button class="carousel-btn" aria-label="Anterior"><span class="fas fa-angle-double-left izquierdo"></span></button>
                    <div class="carousel-viewport">
                        <div class="logo-container">
                            <img src="assets/imagenes/logos.png" alt="Logo 1" class="logo-image">
                            <img src="assets/imagenes/logos.png" alt="Logo 2" class="logo-image">
                            <img src="assets/imagenes/logos.png" alt="Logo 3" class="logo-image">
                            <img src="assets/imagenes/logos.png" alt="Logo 4" class="logo-image">
                            <img src="assets/imagenes/logos.png" alt="Logo 5" class="logo-image">
                            <img src="assets/imagenes/logos.png" alt="Logo 1" class="logo-image">
                            <img src="assets/imagenes/logos.png" alt="Logo 2" class="logo-image">
                            <img src="assets/imagenes/logos.png" alt="Logo 3" class="logo-image">
                            <img src="assets/imagenes/logos.png" alt="Logo 4" class="logo-image">
                            <img src="assets/imagenes/logos.png" alt="Logo 5" class="logo-image">
                        </div>
                    </div>
                    <button class="carousel-btn" aria-label="Siguiente"><span class="fas fa-angle-double-right derecho"></span></button>
                </div>
            </div>
        </div>
    </section>

</div>

<script>
    const drawer = document.getElementById('drawer');
    const overlay = document.getElementById('drawerOverlay');
    const btnHamburguesa = document.getElementById('btnHamburguesa');
    const btnCerrar = document.getElementById('btnCerrar');
    function abrirDrawer()  { drawer?.classList.add('open'); overlay?.classList.add('active'); document.body.style.overflow = 'hidden'; }
    function cerrarDrawer() { drawer?.classList.remove('open'); overlay?.classList.remove('active'); document.body.style.overflow = ''; }
    btnHamburguesa?.addEventListener('click', abrirDrawer);
    btnCerrar?.addEventListener('click', cerrarDrawer);
    overlay?.addEventListener('click', cerrarDrawer);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarDrawer(); });

    const INDEX_LS_KEY = 'coffeecol_carrito_v2';
    function leerTotalCarrito() {
        try {
            const data = JSON.parse(localStorage.getItem(INDEX_LS_KEY) || '{}');
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
        sessionStorage.setItem('abrir_carrito', '1');
        window.location.href = 'includes/servicios.php';
    }
    document.getElementById('btn-carrito-desktop')?.addEventListener('click', manejarClickCarrito);
    document.getElementById('btn-carrito-mobile')?.addEventListener('click', manejarClickCarrito);
    actualizarBadgesNav();
    window.addEventListener('storage', actualizarBadgesNav);
    setInterval(actualizarBadgesNav, 2000);
</script>