
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
            <h2>Somos más que una maquina.</h2>
            <h3>Somos tu socio para llevar el café colombiano al mundo.</h3>
            <p>En CoffeeCol creemos que el buen café no solo se produce, se honra.</p>
            <p>Somos una empresa colombiana nacida con el objetivo de transformar el café de origen en cápsulas premium
                que respetan su esencia y elevan su presentación al más alto nivel.</p>
            <p>Más que maquilar, acompañamos a marcas, fincas y emprendedores en el proceso de profesionalizar su
                propuesta y conquistar nuevos mercados. Nuestro compromiso es con el detalle, con la elegancia sobria, y
                con una experiencia de consumo moderna, sin perder la conexión con el origen.</p>
        </div>
        <div class="decoracion-cafe"></div>
    </section>

    <section class="coffee-section">
        <h1 class="main-title">Nuestro propósito</h1>

        <!-- Sección 1: imagen a la derecha -->
        <div class="content-section">
            <div class="text-content">
                <h2 class="section-subtitle">
                    Redefinimos el café premium:<br>
                    <span class="highlight-text">auténtico, sofisticado, sin excesos.</span>
                </h2>
                <p class="section-text">
                    Queremos que el café colombiano se disfrute con la misma pasión con que se cultiva.
                    Que el productor sea protagonista, con una cápsula que hable de su historia y de la calidad
                    incomparable del origen.
                </p>
            </div>
            <div class="image-container-1">
                <img src="../assets/imagenes/banner29.png" alt="Taza de café roja">
            </div>
        </div>

        <!-- Sección 2: imagen a la izquierda -->
        <div class="content-section reverse">
            <div class="image-container-2">
                <img src="../assets/imagenes/banner28.jpg" alt="Cápsulas de café coloridas">
            </div>
            <div class="text-content">
                <h2 class="tech-title ">Equipo & Tecnología</h2>
                <span class="tech-highlight">Cada café de origen del país <br><strong>tenga la oportunidad de
                        brillar.</strong></span>
                <p class="section-text anchos">
                    Trabajamos durante meses buscando la maquinaria adecuada para brindar la posibilidad y la
                    oportunidad a cada buen café de origen del país. Nuestro equipo combina baristas, ingenieros de
                    procesos y diseñadores industriales, operando con tecnología europea de última generación que
                    garantiza sellos herméticos y perfiles consistentes, manteniendo la personalidad única de cada lote.
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