<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tantico - Contáctanos</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Enlace para usar Font Awesome desde CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Goudy+Bookletter+1911&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="icon" href="../assets/imagenes/banner1.png" type="image/x-icon">

  <!-- CSS personalizado -->
  <link rel="stylesheet" href="../css/header.css">
  <link rel="stylesheet" href="../csss/contactoo.css">
</head>

<body>

   <?php include 'encabezado.php'; ?>

  <!-- Sección Contacto -->
  <section id="contacto" class="seccion-contacto">
    <!-- Imagen de fondo -->
    <img src="../assets/imagenes/baner7a.jpg" alt="Banner contacto" class="bg-contacto">

    <!-- Capa oscura encima de la imagen -->
    <div class="overlay-servicios"></div>

    <!-- Contenido centrado -->
    <div class="contenedor-contacto">
      <div class="titulo-box">
        <span class="hero-pill-badge" style="display:inline-flex;align-items:center;gap:8px;padding:6px 18px;background:rgba(198,167,107,0.15);border:1px solid rgba(198,167,107,0.35);border-radius:30px;color:#E8D8B4;font-family:'Inter',sans-serif;font-size:11px;font-weight:600;letter-spacing:1.8px;text-transform:uppercase;margin-bottom:14px;backdrop-filter:blur(10px);">
            <i class="fa-solid fa-envelope" style="color:#C6A76B;font-size:11px;"></i> CANALES DE ATENCIÓN · TANTICO
        </span>
        <h1 class="titulo-contacto">Contacto &amp; <em>Alianzas</em></h1>
        <p style="color:rgba(251,248,242,0.85);font-size:16px;max-width:560px;margin:12px auto 0;line-height:1.6;font-family:'Inter',sans-serif;">Estamos listos para atenderte, resolver tus dudas o colaborar con tu proyecto cafetero.</p>
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

  <?php include 'footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>