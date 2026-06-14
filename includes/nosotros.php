
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Coffe</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Enlace para usar Font Awesome desde CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Goudy+Bookletter+1911&family=Dancing+Script:wght@400;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" href="../assets/imagenes/banner1.png" type="image/x-icon">

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../csss/nosotross.css">
</head>

<body>

    <?php include 'encabezado.php'; ?>

    <!-- Sección Servicios -->
    <section id="servicios" class="seccion-servicios">
        <!-- Imagen de fondo -->
        <img src="../assets/imagenes/banner14.jpg" alt="Banner Servicios" class="bg-servicios">

        <!-- Capa oscura encima de la imagen -->
        <div class="overlay-servicios"></div>

        <!-- Contenido centrado -->
        <div class="contenedor-servicios">
            <div class="titulo-box">
                <h2 class="titulo-servicios">Nosotros</h2>
            </div>
        </div>
    </section>

    <!-- Sección Quiénes Somos -->
    <section class="quienes-somos">
        <div class="contenido">
            <h1>Quiénes somos</h1>
            <h2>Más que una cafetería, una experiencia que nace en el corazón del Huila.</h2>
            <h3>Orgullosamente huilenses, apasionadamente cafeteros.</h3>
            <p>En Tantico rendimos homenaje a la tierra que produce algunos de los cafés más reconocidos de Colombia y del mundo.</p>
            <p>Desde Neiva, trabajamos con café de origen huilense cuidadosamente seleccionado para ofrecer sabores auténticos, aromas inolvidables y una experiencia que refleja la riqueza de nuestra región.</p>
            <p>Creemos que cada taza debe contar una historia. Por eso unimos tradición, calidad y hospitalidad para compartir con nuestros visitantes la esencia del Huila en cada sorbo.</p>
        </div>
        <div class="decoracion-cafe"></div>
    </section>

    <section class="coffee-section">
    <h1 class="main-title">Nuestro propósito</h1>

    <!-- Sección 1: imagen a la derecha -->
    <div class="content-section">
        <div class="text-content">
            <h2 class="section-subtitle">
                Celebramos el café huilense:<br>
                <span class="highlight-text">auténtico, memorable y lleno de tradición.</span>
            </h2>
            <p class="section-text">
                Queremos que cada taza se disfrute con la misma pasión y dedicación con la que es cultivada.
                Que nuestros caficultores sean protagonistas, compartiendo la historia, el esfuerzo y la calidad
                excepcional que hacen del café del Huila un referente mundial.
            </p>
        </div>
        <div class="image-container-1">
            <img src="../assets/imagenes/hui1.png" alt="Taza de café roja">
        </div>
    </div>

    <!-- Sección 2: imagen a la izquierda -->
    <div class="content-section reverse">
        <div class="image-container-2">
            <img src="../assets/imagenes/hui2.png" alt="Café huilense">
        </div>
        <div class="text-content">
            <h2 class="tech-title">Compartimos el orgullo del Huila</h2>
            <span class="tech-highlight">tradición, calidad y<br><strong>origen.</strong></span>
            <p class="section-text anchos">
                Seleccionamos cuidadosamente cafés provenientes de distintas zonas del departamento para ofrecer
                una experiencia auténtica en cada preparación. Nuestro equipo trabaja con pasión y compromiso para
                resaltar los aromas, sabores y características únicas que distinguen al café huilense y lo convierten
                en un símbolo de nuestra región.
            </p>
        </div>
    </div>
</section>

    <div class="footer-wrapper">
        <footer class="footer-coffeecol">
            <div class="footer-content">
                <img src="../assets/imagenes/tantico.png" alt="Logo de CoffeeCol" class="footer-logo" />

                <nav class="footer-nav">
                    <a href="../index.php">Inicio</a>
                    <a href="nosotros.php">Nosotros</a>
                    <a href="servicios.php">Productos</a>
                    <a href="contacto.php">Contáctanos</a>
                </nav>

                <div class="footer-social">
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </footer>
    </div>

<script>
    let ticking = false;
    const tieneVideo = document.querySelector('.video-fondo') !== null;

    function updateScrollPosition() {
        const scrolled = document.body.scrollTop || window.pageYOffset;
        const header = document.querySelector('.navbar-custom');
        if (!header) return;

        if (scrolled > 50) {
            header.style.backdropFilter = 'blur(25px)';
            header.style.background = 'rgba(0, 0, 0, 0.55)';
            header.style.borderRadius = '11px'; // ✅ siempre 11px
        } else {
            header.style.backdropFilter = 'blur(20px)';
            header.style.background = tieneVideo ? 'transparent' : 'rgba(0, 0, 0, 0.0)';
            header.style.borderRadius = '11px'; // ✅ siempre 11px
        }

        // ✅ Quitar fondo del contenedor de íconos para que no se duplique
        const socialIcons = document.querySelector('.social-icons');
        if (socialIcons) {
            socialIcons.style.background = 'transparent';
            socialIcons.style.backdropFilter = 'none';
        }

        ticking = false;
    }

    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(updateScrollPosition);
            ticking = true;
        }
    }

    document.body.addEventListener('scroll', requestTick);
    window.addEventListener('scroll', requestTick);

    // Aplicar estado inicial
    updateScrollPosition();
</script>




    <!-- Bootstrap JS con Popper (necesario para navbar en móviles) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>