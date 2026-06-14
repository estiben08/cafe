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
  <link rel="stylesheet" href="../csss/contactoo.css">
</head>

<body>

   <?php include 'encabezado.php'; ?>

  <!-- Sección Contacto -->
  <section id="contacto" class="seccion-contacto">
    <!-- Imagen de fondo -->
    <img src="../assets/imagenes/banner24.jpeg" alt="Banner contacto" class="bg-contacto">

    <!-- Capa oscura encima de la imagen -->
    <div class="overlay-servicios"></div>

    <!-- Contenido centrado -->
    <div class="contenedor-contacto">
      <div class="titulo-box">
        <h2 class="titulo-contacto">Contacto</h2>
      </div>
    </div>
  </section>

  <section class="contacto-cafe-section container my-5">
    <div class="text-center mb-4">
        <h2 class="contact-header">¿Tienes un café que merece ser descubierto?</h2>
        <p class="contact-subtext">
            Queremos conocer productores, fincas y marcas locales que compartan nuestra pasión por la calidad.
            Escríbenos y descubre cómo podemos llevar tu café a más personas a través de Tantico.
        </p>
        <button class="custom-btn-top">Hablemos de tu café</button>
    </div>

    <!-- HTML -->

    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="form-section">
                <h5>Comparte tus datos y cuéntanos sobre tu café</h5>

                <form action="contactos.php" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="nombre" class="form-label">Nombre completo</label>
                            <input type="text" id="nombre" name="nombre" class="form-control"
                                placeholder="Ingresa tu nombre completo" required />
                        </div>
                        <div class="col-md-6">
                            <label for="empresa" class="form-label">Empresa</label>
                            <input type="text" id="empresa" name="empresa" class="form-control"
                                placeholder="Nombre de tu finca, marca o emprendimiento" />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" class="form-control"
                                placeholder="Ej: 3101234567" required />
                        </div>
                        <div class="col-md-6">
                            <label for="correo" class="form-label">Correo electrónico</label>
                            <input type="email" id="correo" name="correo" class="form-control"
                                placeholder="tucorreo@dominio.com" required />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="comentarios" class="form-label">Comentarios</label>
                        <textarea id="comentarios" name="comentarios" class="form-control" rows="4"
                            placeholder="Cuéntanos sobre tu café, finca o marca"></textarea>
                    </div>

                    <p class="nota-campos">
                        * Campos obligatorios. Nos pondremos en contacto contigo lo antes posible.
                    </p>

                    <button type="submit" class="btn-send">ENVIAR INFORMACIÓN</button>
                </form>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row align-items-center">
            <div class="col-md-6 visit-section">
                <h5>Conoce <span>Tantico</span></h5>
                <p><i class="fas fa-map-marker-alt personalizar-1"></i> Neiva, Huila, Colombia</p>
                <p><i class="fas fa-envelope personalizar-2"></i> contacto@tantico.com</p>
                <button class="btn btn-project">Quiero presentar mi café</button>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-12 mb-3">
                        <img src="../assets/imagenes/banner25.png" alt="Imagen planta"
                            class="img-fluid rounded ubicacion" />
                    </div>
                    <div class="col-12">
                        <img src="../assets/imagenes/banner26.png" alt="Mapa ubicación"
                            class="img-fluid rounded ubicacion" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

  <br><br><br><br>

  <div class="imagen-full">
    <img src="../assets/imagenes/banner23.png" alt="Banner">
  </div>

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>