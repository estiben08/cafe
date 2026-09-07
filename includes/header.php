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
            background-size: cover; background-position: center;
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
        .video-fondo .contenido { position: relative; z-index: 1; color: white; text-align: center; padding-top: 30vh; }

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
        .navbar-brand img { height: 70px; width: 140px; transition: transform 0.3s ease; }
        .navbar-collapse { display: flex; align-items: center; justify-content: space-between; flex: 1; }
        .navbar-left  { flex: 1; }
        .navbar-center { flex: 1; display: flex; justify-content: center; }
        .navbar-right  { flex: 1; display: flex; justify-content: flex-end; }
        .navbar-nav { display: flex; align-items: center; margin: 0; padding: 0; list-style: none; }
        .navbar-nav .nav-link {
            color: #ffffff !important; font-weight: 500; font-size: 13px;
            margin: 0 20px; line-height: 100%; position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); padding: 8px 12px; border-radius: 6px;
        }
        .navbar-nav .nav-link:hover { color: #f0f0f0 !important; background-color: rgba(255,255,255,0.1); transform: translateY(-2px); }
        .navbar-nav .nav-link:active { transform: translateY(0); }

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
            transition: all 0.2s; opacity: 1; transform: scale(1);
        }
        .nav-carrito-badge.oculto { opacity: 0; transform: scale(0); pointer-events: none; }

        #btn-usuario {
            color: #ffffff; display: flex; align-items: center; justify-content: center;
            width: 24px; height: 24px; font-size: 17.5px; text-decoration: none; transition: all 0.3s ease;
        }
        #btn-usuario:hover { transform: scale(1.15); background-color: rgba(255,255,255,0.15); border-radius: 50%; }
        #btn-usuario img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }

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
        .menu-usuario-nombre { margin: 0; line-height: 1.2; font-size: 13px; font-weight: 500; color: #e8d8b4; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .menu-usuario-item {
            display: flex; align-items: center; gap: 10px; width: 100%; padding: 9px 16px;
            background: none; border: none; color: rgba(240,228,200,0.65); font-size: 13px; font-weight: 400;
            cursor: pointer; text-decoration: none; transition: background .15s, color .15s, padding-left .15s;
        }
        .menu-usuario-item:hover { background: rgba(210,170,100,0.08); color: #f0e8d2; padding-left: 20px; }
        .menu-usuario-logout { color: rgba(220,100,80,0.65); }
        .menu-usuario-logout:hover { background: rgba(200,60,40,0.08); color: #e07a5f; padding-left: 20px; }
        .menu-usuario-tag { font-size: 10.5px; color: rgba(210,170,100,0.5); letter-spacing: 0.4px; display: block; margin-top: 1px; }

        .navbar-toggler { border: none; padding: 8px; background-color: rgba(255,255,255,0.1); border-radius: 6px; transition: all 0.3s ease; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; }
        .navbar-toggler:hover { background-color: rgba(255,255,255,0.2); }
        .navbar-toggler:focus { box-shadow: none; }
        .navbar-toggler-icon { background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e"); width: 24px; height: 24px; }

        .hero-section { background-size: cover; background-position: center; padding: 120px 20px 60px; display: flex; align-items: center; justify-content: center; min-height: 80vh; color: #fff; text-align: center; position: relative; }
        .hero-content { position: absolute; top: 293px; left: 50%; transform: translateX(-50%); width: 670px; max-width: 90%; height: auto; display: flex; flex-direction: column; align-items: center; justify-content: center; animation: fadeInUp 1s ease-out; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateX(-50%) translateY(30px); } to { opacity: 1; transform: translateX(-50%) translateY(0); } }
        .hero-content h1 { font-size: 39.17px; line-height: 100%; }
        .hero-content .bold { font-weight: 800; }
        .hero-content .medium { font-weight: 500; }
        .hero-content p { margin-top: 20px; font-size: 20px; line-height: 100%; font-weight: 500; }
        .btn-asesor { display: inline-flex; justify-content: center; align-items: center; margin-top: 30px; width: 165px; height: 29px; font-family: 'Onest', sans-serif; font-size: 12.83px; font-weight: 500; line-height: 22.96px; letter-spacing: 0.14px; text-align: center; color: #FFFFFF; text-decoration: none; border: 1px solid #FFFFFF; border-radius: 9.58px; background-color: transparent; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; }
        .btn-asesor:hover { background-color: rgba(255,255,255,0.15); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(255,255,255,0.2); }
        .btn-asesor:active { transform: translateY(0); }

        .partners-section { position: relative; z-index: 1000; max-width: 1220px; margin: 0 auto; padding: 0 32px; }
        .partners-content { display: flex; align-items: center; gap: 30px; }
        .partners-carousel { width: 792px; max-width: 100%; height: 95px; background: rgba(255,255,255,0.1); border-radius: 10px; padding: 20px; backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s ease; position: relative; box-sizing: border-box; overflow: hidden; }
        .partners-text { color: #ffffff; font-size: 21.67px; font-weight: 700; min-width: 200px; text-align: left; }
        .arriba { position: absolute; top: 13px; left: 25px; width: 220px; height: 15px; color: #FFFFFF; font-family: Inter; font-weight: 500; font-size: 12px; line-height: 100%; text-align: center; }
        .carousel-controls { position: relative; display: flex; justify-content: space-between; align-items: center; height: 100%; margin-top: 10px; margin-left: 20px; gap: 40px; }
        .carousel-viewport { overflow: hidden; flex: 1; display: flex; min-width: 0; }
        .carousel-btn { border: none; color: black; width: 44px; height: 44px; border-radius: 6px; display: flex; align-items: center; justify-content: center; transition: all 0.3s cubic-bezier(0.4,0,0.2,1); cursor: pointer; background-color: rgba(252,246,219,1); z-index: 10; flex-shrink: 0; }
        .carousel-btn:active { transform: scale(1); }
        .logo-container { display: flex; gap: 30px; transform: translateX(0); will-change: transform; width: max-content; transition: transform 0.2s linear; }
        .izquierdo, .derecho { font-size: 20px; transition: all 0.3s ease; width: 51px; }
        .logo-image { height: 80px; width: auto; flex-shrink: 0; object-fit: contain; }
        .logo-image:hover { transform: scale(1.05); background-color: rgba(255,255,255,0.2); }

        .mobile-bar { display: none; }
        .btn-hamburguesa { background: none; border: none; cursor: pointer; display: flex; flex-direction: column; justify-content: center; gap: 5px; width: 38px; height: 38px; padding: 6px; border-radius: 7px; transition: background 0.2s; flex-shrink: 0; }
        .btn-hamburguesa:hover { background: rgba(255,255,255,0.1); }
        .btn-hamburguesa span { display: block; width: 20px; height: 2px; background: #fff; border-radius: 2px; transition: all 0.3s ease; }
        .mobile-logo img { height: 34px; width: auto; display: block; }
        .mobile-icons-right { display: flex; align-items: center; gap: 2px; flex-shrink: 0; }

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

        .drawer-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 2998; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); }
        .drawer-overlay.active { opacity: 1; pointer-events: all; }
        .drawer { position: fixed; top: 0; left: 0; width: 270px; height: 100vh; background: #1c0b09; z-index: 2999; transform: translateX(-100%); transition: transform 0.35s cubic-bezier(0.4,0,0.2,1); display: flex; flex-direction: column; border-right: 1px solid rgba(210,170,100,0.12); box-shadow: 8px 0 40px rgba(0,0,0,0.7); }
        .drawer.open { transform: translateX(0); }
        .drawer-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 18px 16px; border-bottom: 1px solid rgba(210,170,100,0.1); }
        .drawer-logo img { height: 34px; width: auto; }
        .drawer-logo-text { color: #e8d8b4; font-size: 18px; font-weight: 700; letter-spacing: 1px; }
        .btn-cerrar { background: none; border: none; color: rgba(240,228,200,0.55); font-size: 20px; cursor: pointer; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.2s; }
        .btn-cerrar:hover { color: #e8d8b4; background: rgba(210,170,100,0.1); }
        .drawer-nav { flex: 1; padding: 8px 0; }
        .drawer-nav a { display: flex; align-items: center; gap: 12px; padding: 13px 22px; color: rgba(240,228,200,0.72); text-decoration: none; font-size: 15px; font-weight: 500; transition: all 0.2s ease; border-left: 3px solid transparent; }
        .drawer-nav a i { width: 17px; text-align: center; font-size: 13px; color: rgba(210,170,100,0.55); transition: color 0.2s; }
        .drawer-nav a:hover { color: #f0e8d2; background: rgba(210,170,100,0.07); border-left-color: rgba(210,170,100,0.45); padding-left: 26px; }
        .drawer-nav a:hover i { color: rgba(210,170,100,0.9); }
        .drawer-divider { height: 1px; background: rgba(210,170,100,0.1); margin: 4px 18px; }
        .drawer-footer { padding: 14px 18px 26px; border-top: 1px solid rgba(210,170,100,0.1); }
        .btn-iniciar-sesion { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px; background: linear-gradient(135deg,#8B3A10,#c4762a); color: #f0e8d2; font-size: 14px; font-weight: 600; letter-spacing: 0.3px; border: none; border-radius: 10px; cursor: pointer; text-decoration: none; transition: all 0.25s ease; box-shadow: 0 4px 14px rgba(139,58,16,0.4); }
        .btn-iniciar-sesion:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(139,58,16,0.55); filter: brightness(1.08); color: #f0e8d2; }
        .drawer-footer-user { padding: 14px 18px 26px !important; }
        .drawer-user-info { display: flex; align-items: center; gap: 10px; padding-bottom: 12px; border-bottom: 1px solid rgba(210,170,100,0.1); margin-bottom: 10px; }
        .drawer-user-avatar { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg,#8B3A10,#c4762a); border: 1.5px solid rgba(210,170,100,0.35); display: flex; align-items: center; justify-content: center; font-size: 15px; color: #f0e8d2; flex-shrink: 0; overflow: hidden; }
        .drawer-user-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        .drawer-user-nombre { margin: 0; font-size: 13px; font-weight: 600; color: #e8d8b4; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; }
        .drawer-user-tag { font-size: 10.5px; color: rgba(210,170,100,0.5); letter-spacing: 0.4px; display: block; margin-top: 1px; }
        .drawer-user-actions { display: flex; gap: 8px; margin-bottom: 10px; }
        .drawer-user-action-btn { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px; padding: 10px 8px; background: rgba(210,170,100,0.07); border: 1px solid rgba(210,170,100,0.12); border-radius: 10px; color: rgba(240,228,200,0.75); font-size: 12px; font-weight: 500; text-decoration: none; transition: all 0.2s ease; }
        .drawer-user-action-btn i { font-size: 17px; color: rgba(210,170,100,0.7); }
        .drawer-user-action-btn:hover { background: rgba(210,170,100,0.14); color: #f0e8d2; border-color: rgba(210,170,100,0.3); }
        .drawer-user-action-btn:hover i { color: rgba(210,170,100,1); }
        .btn-cerrar-sesion { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 11px; background: rgba(200,60,40,0.12); border: 1px solid rgba(200,60,40,0.2); color: rgba(220,100,80,0.85); font-size: 13px; font-weight: 600; border-radius: 10px; cursor: pointer; transition: all 0.25s ease; }
        .btn-cerrar-sesion:hover { background: rgba(200,60,40,0.2); color: #e07a5f; border-color: rgba(200,60,40,0.4); }

        @media (max-width: 1024px) {
            .header-principal { top: 14px; left: 12px; right: 12px; height: auto; }
            .navbar-custom { height: 56px; padding: 0 14px; border-radius: 12px; justify-content: space-between; position: relative; }
            .navbar-custom .container { display: none; }
            .mobile-bar { display: flex !important; align-items: center; justify-content: space-between; width: 100%; padding: 0 4px; }
            .hero-section { min-height: auto; padding: 100px 16px 20px; }
            .hero-content { position: relative; top: auto; left: auto; transform: none; width: 100%; max-width: 100%; padding: 0 16px; }
            .hero-content h1 { font-size: 24px; line-height: 115%; }
            .hero-content p { font-size: 14px; line-height: 140%; margin-top: 12px; }
            .btn-asesor { width: 160px; height: 32px; font-size: 13px; margin-top: 18px; }
            .partners-section { padding: 16px 12px 0; max-width: 100%; width: 100%; }
            .partners-content { flex-direction: column; gap: 10px; align-items: stretch; }
            .partners-text { font-size: 15px; text-align: left; min-width: auto; }
            .partners-carousel { width: 100%; max-width: 100%; height: 80px; padding: 8px 10px; }
            .arriba { font-size: 10px; top: 7px; left: 10px; }
            .carousel-controls { margin-left: 0; margin-top: 18px; gap: 6px; }
            .carousel-btn { width: 26px; height: 26px; }
            .logo-image { height: 16px; }
            .logo-container { gap: 12px; }
        }
    </style>
</head>

<body>
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
                            <li class="nav-item"><a class="nav-link" href="#inicio">Inicio</a></li>
                            <li class="nav-item"><a class="nav-link" href="includes/nosotros.php">Nosotros</a></li>
                            <li class="nav-item"><a class="nav-link" href="includes/servicios.php">Productos</a></li>
                            <li class="nav-item"><a class="nav-link" href="includes/contacto.php">Contáctanos</a></li>
                        </ul>
                        <div class="social-icons" style="position:relative;">
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
                        // Sin sesión: limpiar todo para que el botón vaya al login
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
                <a href="#inicio"><i class="fa-solid fa-house"></i> Inicio</a>
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
                    <h1 class="bold">Cafetería &amp; Café de Especialidad<br><span class="medium">El auténtico sabor del Huila</span></h1>
                    <p>En Tantico seleccionamos y preparamos los mejores cafés de origen.<br>Disfruta de diferentes variedades, métodos de filtrado artesanal y momentos únicos.</p>
                    <a href="includes/servicios.php" class="btn-asesor">Explorar nuestra carta</a>
                </div>
            </div>
        </section>

        <!-- Partners Section -->
        <section class="partners-section">
            <div class="partners-content">
                <div class="partners-text"><strong>Orígenes y Variedades<br>de Café Huilense</strong></div>
                <div class="partners-carousel">
                    <div class="arriba">Perfiles de taza y variedades que servimos</div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const drawer = document.getElementById('drawer');
        const overlay = document.getElementById('drawerOverlay');
        const btnHamburguesa = document.getElementById('btnHamburguesa');
        const btnCerrar = document.getElementById('btnCerrar');
        function abrirDrawer()  { drawer.classList.add('open'); overlay.classList.add('active'); document.body.style.overflow = 'hidden'; }
        function cerrarDrawer() { drawer.classList.remove('open'); overlay.classList.remove('active'); document.body.style.overflow = ''; }
        btnHamburguesa.addEventListener('click', abrirDrawer);
        btnCerrar.addEventListener('click', cerrarDrawer);
        overlay.addEventListener('click', cerrarDrawer);
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

</body>
</html>