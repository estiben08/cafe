<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Coffee Col</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Onest:wght@500&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .header-container {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .video-fondo {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            overflow: hidden;
            z-index: -1;
        }

        .video-fondo video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translate(-50%, -50%);
            object-fit: cover;
            will-change: transform;
        }

        .video-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 18px;
            z-index: 1;
            transition: opacity 0.3s ease;
        }

        .video-fondo .contenido {
            position: relative;
            z-index: 1;
            color: white;
            text-align: center;
            padding-top: 30vh;
        }

        /* HEADER PRINCIPAL - Responsive */
        .header-principal {
            position: fixed;
            top: 28px;
            left: 32px;
            right: 32px;
            height: 71px;
            max-width: 1220px;
            margin: 0 auto;
            z-index: 2000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Responsive para tablets */
        @media (max-width: 1024px) {
            .header-principal {
                left: 20px;
                right: 20px;
                top: 20px;
            }
        }

        /* Responsive para móviles */
        @media (max-width: 768px) {
            .header-principal {
                left: 16px;
                right: 16px;
                top: 16px;
                height: auto;
                min-height: 60px;
            }
        }

        .navbar-custom {
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 11px;
            padding: 8px 0;
            box-shadow:
                0 4px 20px rgba(0, 0, 0, 0.1),
                inset 0 3px 3px rgba(0, 0, 0, 0.5);
            height: 71px;
            display: flex;
            align-items: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @media (max-width: 768px) {
            .navbar-custom {
                height: auto;
                min-height: 60px;
                padding: 12px 0;
            }
        }

        .navbar-custom .container {
            max-width: 1220px;
            padding: 0 32px;
            width: 100%;
            display: flex;
            align-items: center;
        }

        @media (max-width: 768px) {
            .navbar-custom .container {
                padding: 0 20px;
            }
        }

        .navbar-brand img {
            height: 42px;
            width: 115px;
            transition: transform 0.3s ease;
        }

        @media (max-width: 480px) {
            .navbar-brand img {
                height: 35px;
                width: 95px;
            }
        }

        /* Layout para centrado perfecto */
        .navbar-collapse {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex: 1;
        }

        @media (max-width: 991px) {
            .navbar-collapse {
                flex-direction: column;
                align-items: flex-start;
                background: rgba(0, 0, 0, 0.9);
                border-radius: 8px;
                margin-top: 10px;
                padding: 20px;
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
            }
        }

        .navbar-left {
            flex: 1;
        }

        .navbar-center {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        @media (max-width: 991px) {
            .navbar-center {
                width: 100%;
                justify-content: flex-start;
                margin: 15px 0;
            }
        }

        .navbar-right {
            flex: 1;
            display: flex;
            justify-content: flex-end;
        }

        @media (max-width: 991px) {
            .navbar-right {
                width: 100%;
                justify-content: flex-start;
            }
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        @media (max-width: 991px) {
            .navbar-nav {
                flex-direction: column;
                align-items: flex-start;
                width: 100%;
            }

            .navbar-nav .nav-item {
                width: 100%;
                margin: 5px 0;
            }
        }

        .navbar-nav .nav-link {
            color: #ffffff !important;
            font-weight: 500;
            font-size: 13px;
            margin: 0 20px;
            line-height: 100%;
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 8px 12px;
            border-radius: 6px;
        }

        @media (max-width: 991px) {
            .navbar-nav .nav-link {
                margin: 0;
                padding: 12px 0;
                width: 100%;
                font-size: 14px;
            }
        }

        .navbar-nav .nav-link:hover {
            color: #f0f0f0 !important;
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        @media (max-width: 991px) {
            .navbar-nav .nav-link:hover {
                transform: none;
                background-color: rgba(255, 255, 255, 0.2);
            }
        }

        .navbar-nav .nav-link:active {
            transform: translateY(0);
        }

        /* Íconos sociales */
        .social-icons {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 12px;
            padding: 6px 14px;
            display: flex;
            align-items: center;
            gap: 0px;
            z-index: 10;
        }

        @media (max-width: 991px) {
            .social-icons {
                margin-top: 10px;
                padding: 8px 0;
                background: none;
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
            }
        }

        .social-icons a {
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            font-size: 17.5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        @media (max-width: 991px) {
            .social-icons a {
                width: 32px;
                height: 32px;
                font-size: 20px;
                margin-right: 15px;
            }
        }

        .social-icons a:hover {
            transform: scale(1.15);
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
        }

        .social-icons a:active {
            transform: scale(1);
        }

        /* Botón del menú móvil */
        .navbar-toggler {
            border: none;
            padding: 8px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            transition: all 0.3s ease;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .navbar-toggler:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            width: 24px;
            height: 24px;
        }

        .hero-section {
            background-size: cover;
            background-position: center;
            padding: 120px 20px 60px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 80vh;
            color: #fff;
            text-align: center;
            position: relative;
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 100px 15px 40px 15px;
                min-height: 70vh;
            }
        }

        .hero-content {
            position: absolute;
            top: 293px;
            left: 50%;
            transform: translateX(-50%);
            width: 670px;
            max-width: 90%;
            height: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            animation: fadeInUp 1s ease-out;
        }

        @media (max-width: 1024px) {
            .hero-content {
                top: 250px;
                width: 80%;
            }
        }

        @media (max-width: 768px) {
            .hero-content {
                top: 200px;
                width: 95%;
                padding: 0 10px;
            }
        }

        @media (max-width: 480px) {
            .hero-content {
                top: 180px;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        .hero-content h1 {
            font-size: 39.17px;
            line-height: 100%;
        }

        @media (max-width: 1024px) {
            .hero-content h1 {
                font-size: 32px;
            }
        }

        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 28px;
                line-height: 110%;
            }
        }

        @media (max-width: 480px) {
            .hero-content h1 {
                font-size: 24px;
                line-height: 115%;
            }
        }

        .hero-content .bold {
            font-weight: 800;
        }

        .hero-content .medium {
            font-weight: 500;
        }

        .hero-content p {
            margin-top: 20px;
            font-size: 20px;
            line-height: 100%;
            font-weight: 500;
        }

        @media (max-width: 1024px) {
            .hero-content p {
                font-size: 18px;
                line-height: 110%;
            }
        }

        @media (max-width: 768px) {
            .hero-content p {
                font-size: 16px;
                line-height: 120%;
                margin-top: 15px;
            }
        }

        @media (max-width: 480px) {
            .hero-content p {
                font-size: 14px;
                line-height: 125%;
            }
        }

        .btn-asesor {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin-top: 30px;
            width: 165px;
            height: 29px;
            font-family: 'Onest', sans-serif;
            font-size: 12.83px;
            font-weight: 500;
            line-height: 22.96px;
            letter-spacing: 0.14px;
            text-align: center;
            color: #FFFFFF;
            text-decoration: none;
            border: 1px solid #FFFFFF;
            border-radius: 9.58px;
            background-color: transparent;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .btn-asesor {
                width: 180px;
                height: 35px;
                font-size: 14px;
                margin-top: 25px;
            }
        }

        @media (max-width: 480px) {
            .btn-asesor {
                width: 160px;
                height: 32px;
                font-size: 13px;
                margin-top: 20px;
            }
        }

        .btn-asesor:hover {
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
        }

        .btn-asesor:active {
            transform: translateY(0);
        }

        .partners-section {
            position: relative;
            z-index: 1000;
            max-width: 1220px;
            margin: 0 auto;
            padding: 0 32px;
        }

        @media (max-width: 1024px) {
            .partners-section {
                padding: 0 20px;
            }
        }

        @media (max-width: 768px) {
            .partners-section {
                padding: 0 16px;
            }
        }

        .partners-content {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        @media (max-width: 992px) {
            .partners-content {
                flex-direction: column;
                gap: 20px;
                align-items: stretch;
            }
        }

        .partners-carousel {
            width: 792px;
            max-width: 100%;
            height: 95px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            position: relative;
            box-sizing: border-box;
            overflow: hidden;
        }

        @media (max-width: 992px) {
            .partners-carousel {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .partners-carousel {
                height: 80px;
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            .partners-carousel {
                height: 70px;
                padding: 10px;
            }
        }

        .partners-text {
            color: #ffffff;
            font-size: 21.67px;
            font-weight: 700;
            min-width: 200px;
            text-align: left;
        }

        @media (max-width: 1024px) {
            .partners-text {
                font-size: 18px;
            }
        }

        @media (max-width: 992px) {
            .partners-text {
                text-align: center;
                min-width: auto;
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .partners-text {
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .partners-text {
                font-size: 14px;
            }
        }

        .arriba {
            position: absolute;
            top: 13px;
            left: 25px;
            width: 220px;
            height: 15px;
            color: #FFFFFF;
            font-family: Inter;
            font-weight: 500;
            font-style: Medium;
            font-size: 12px;
            line-height: 100%;
            letter-spacing: 0%;
            text-align: center;
        }

        @media (max-width: 768px) {
            .arriba {
                font-size: 10px;
                top: 10px;
                left: 15px;
                width: 180px;
            }
        }

        @media (max-width: 480px) {
            .arriba {
                font-size: 9px;
                top: 8px;
                left: 10px;
                width: 150px;
            }
        }

        .carousel-controls {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100%;
            margin-top: 10px;
            margin-left: 20px;
            gap: 40px;
        }

        @media (max-width: 768px) {
            .carousel-controls {
                margin-left: 15px;
                gap: 20px;
                margin-top: 8px;
            }
        }

        @media (max-width: 480px) {
            .carousel-controls {
                margin-left: 10px;
                gap: 15px;
                margin-top: 5px;
            }
        }

        /* Contenedor para recortar los logos */
        .carousel-viewport {
            overflow: hidden;
            flex: 1;
            display: flex;
        }

        .carousel-btn {
            border: none;
            color: black;
            width: 44px;
            height: 44px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            background-color: rgba(252, 246, 219, 1);
            z-index: 10;
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .carousel-btn {
                width: 35px;
                height: 35px;
            }
        }

        @media (max-width: 480px) {
            .carousel-btn {
                width: 30px;
                height: 30px;
            }
        }

        .carousel-btn:active {
            transform: scale(1);
        }

        .logo-container {
            display: flex;
            gap: 30px;
            transform: translateX(0);
            will-change: transform;
            width: max-content;
            transition: transform 0.2s linear;
        }

        @media (max-width: 768px) {
            .logo-container {
                gap: 20px;
            }
        }

        @media (max-width: 480px) {
            .logo-container {
                gap: 15px;
            }
        }

        .izquierdo,
        .derecho {
            font-size: 20px;
            transition: all 0.3s ease;
            width: 51px;
        }

        @media (max-width: 768px) {

            .izquierdo,
            .derecho {
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {

            .izquierdo,
            .derecho {
                font-size: 14px;
            }
        }

        .logo-image {
            height: 24px;
            width: auto;
            flex-shrink: 0;
            object-fit: contain;
        }

        @media (max-width: 768px) {
            .logo-image {
                height: 20px;
            }
        }

        @media (max-width: 480px) {
            .logo-image {
                height: 18px;
            }
        }

        .logo-image:hover {
            transform: scale(1.05);
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Evitar scroll horizontal */
        html,
        body {
            overflow-x: hidden;
            width: 100%;
        }

        /* Mejoras de performance para dispositivos móviles */
        @media (max-width: 768px) {
            .video-fondo video {
                transform: translate(-50%, -50%) scale(1.1);
            }
        }

        /* Fix para navbar collapse en móviles */
        @media (max-width: 991px) {
            .navbar-collapse.show {
                border-top: 1px solid rgba(255, 255, 255, 0.2);
                margin-top: 15px;
                padding-top: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="header-container">
        <!-- Video de fondo -->
        <div class="video-fondo">
            <div class="video-loading">Cargando...</div>
            <video autoplay muted loop playsinline preload="metadata">
                <source src="assets/imagenes/banner3.mp4" type="video/mp4">
                Tu navegador no soporta el video.
            </video>
        </div>

        <!-- Header Navigation -->
        <header class="header-principal">
            <nav class="navbar navbar-expand-lg navbar-custom">
                <div class="container">
                    <a class="navbar-brand" href="#">
                        <div class="coffee-logo">
                            <img src="assets/imagenes/banner20.png" alt="Coffee Col Logo">
                        </div>
                    </a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="#inicio">Inicio</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="includes/nosotros.php">Nosotros</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="includes/servicios.php">Servicios</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="includes/contacto.php">Contáctanos</a>
                            </li>
                        </ul>

                        <div class="social-icons">
                            <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
                            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="hero-content">
                    <h1 class="bold">Máquila de Cápsulas de Café<br><span class="medium">con Sello Colombiano</span>
                    </h1>
                    <p>En CoffeeCol convertimos tu café en cápsulas listas para conquistar<br>el mercado. Una marca
                        sobria, profesional y 100% colombiana.</p>
                    <a href="#" class="btn-asesor">Solicitar asesoría</a>
                </div>
            </div>
        </section>

        <!-- Partners Section -->
        <section class="partners-section">
            <div class="partners-content">
                <div class="partners-text">
                    <strong>Alianzas que dan<br>origen al mejor café</strong>
                </div>
                <div class="partners-carousel">
                    <div class="arriba">Marcas que confían en nosotros</div>
                    <div class="carousel-controls">
                        <button class="carousel-btn" aria-label="Anterior">
                            <span class="fas fa-angle-double-left izquierdo"></span>
                        </button>

                        <!-- ✅ NUEVO CONTENEDOR PARA RECORTAR LOS LOGOS -->
                        <div class="carousel-viewport">
                            <div class="logo-container">
                                <img src="assets/imagenes/banner18.png" alt="Logo 1" class="logo-image">
                                <img src="assets/imagenes/banner18.png" alt="Logo 2" class="logo-image">
                                <img src="assets/imagenes/banner18.png" alt="Logo 3" class="logo-image">
                                <img src="assets/imagenes/banner18.png" alt="Logo 4" class="logo-image">
                                <img src="assets/imagenes/banner18.png" alt="Logo 5" class="logo-image">
                                <img src="assets/imagenes/banner18.png" alt="Logo 1" class="logo-image">
                                <img src="assets/imagenes/banner18.png" alt="Logo 2" class="logo-image">
                                <img src="assets/imagenes/banner18.png" alt="Logo 3" class="logo-image">
                                <img src="assets/imagenes/banner18.png" alt="Logo 4" class="logo-image">
                                <img src="assets/imagenes/banner18.png" alt="Logo 5" class="logo-image">
                            </div>
                        </div>

                        <button class="carousel-btn" aria-label="Siguiente">
                            <span class="fas fa-angle-double-right derecho"></span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

    </div>

</body>

</html>