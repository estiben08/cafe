<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nosotros — Tantico Café de Especialidad</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Goudy+Bookletter+1911&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" href="../assets/imagenes/banner1.png" type="image/x-icon">

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../csss/nosotross.css">
</head>

<body>

    <?php include 'encabezado.php'; ?>

    <!-- ======================================================= -->
    <!-- 1. HERO EDITORIAL                                       -->
    <!-- ======================================================= -->
    <section class="nosotros-hero">
        <img src="../assets/imagenes/baner1a.jpg" alt="Cafetales del Huila" class="nosotros-hero-bg">
        <div class="nosotros-hero-overlay"></div>
        <div class="nosotros-hero-content">
            <span class="hero-pill-badge">
                <i class="fa-solid fa-gem"></i> HISTORIA &amp; FILOSOFÍA · NEIVA, HUILA
            </span>
            <h1 class="nosotros-hero-title">
                El Alma del Huila<br><em>en Cada Taza</em>
            </h1>
            <p class="nosotros-hero-lead">
                Rendimos tributo a la tierra cafetera más premiada de Colombia. Seleccionamos cosechas de altura, tostamos con devoción artesanal y creamos momentos memorables en torno al café de especialidad.
            </p>
            <div class="hero-specs-row">
                <div class="hero-spec-item">
                    <i class="fa-solid fa-mountain"></i> 1.750m Altura Promedio
                </div>
                <div class="hero-spec-item">
                    <i class="fa-solid fa-award"></i> 86.5+ SCA Score
                </div>
                <div class="hero-spec-item">
                    <i class="fa-solid fa-seedling"></i> 100% Origen Huila
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================================= -->
    <!-- 2. MANIFIESTO & MÉTRICAS SPLIT                          -->
    <!-- ======================================================= -->
    <section class="seccion-manifiesto">
        <div class="manifiesto-grid">
            <div class="manifiesto-left">
                <span class="editorial-tag">Nuestra Esencia</span>
                <h2 class="manifiesto-quote">
                    "Creemos que cada taza debe ser una celebración de origen, esfuerzo y maestría."
                </h2>
                <p class="manifiesto-body">
                    En el corazón de Neiva, <strong>Tantico</strong> nace con una misión clara: conectar el trabajo silencioso y apasionado de las familias caficultoras del Huila con quienes buscan una experiencia sensorial irrepetible.
                </p>
                <p class="manifiesto-body">
                    No comercializamos solo café; compartimos el fruto de <strong>suelos volcánicos</strong>, microclimas privilegiados del Macizo Colombiano y procesos de fermentación cuidados grano a grano.
                </p>
                <div class="firma-wrapper">
                    <div class="firma-icono">
                        <i class="fa-solid fa-feather-pointed"></i>
                    </div>
                    <div class="firma-info">
                        <h5>Tantico Café de Especialidad</h5>
                        <span>Pasión Huilense desde Neiva</span>
                    </div>
                </div>
            </div>

            <!-- Bento Card de Métricas -->
            <div class="metrics-card">
                <div class="metrics-header">
                    <span class="metrics-header-title">Compromiso en Cifras</span>
                    <i class="fa-solid fa-certificate metrics-header-icon"></i>
                </div>
                <div class="metrics-grid">
                    <div class="metric-item">
                        <span class="metric-num">1.750m+</span>
                        <span class="metric-label">Altitud Promedio</span>
                        <span class="metric-desc">Pitalito, San Agustín, Garzón y La Plata</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-num">86.5+</span>
                        <span class="metric-label">Puntaje SCA</span>
                        <span class="metric-desc">Taza de excelencia y notas complejas</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-num">100%</span>
                        <span class="metric-label">Trato Directo</span>
                        <span class="metric-desc">Comercio justo con familias caficultoras</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-num">0</span>
                        <span class="metric-label">Intermediarios</span>
                        <span class="metric-desc">De la finca a nuestra tostaduría en Neiva</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================================= -->
    <!-- 3. LOS 4 PILARES DE TANTICO (BENTO GRID)                -->
    <!-- ======================================================= -->
    <section class="seccion-pilares">
        <div class="pilares-container">
            <div class="seccion-header-center">
                <span class="editorial-tag" style="justify-content: center;">Filosofía de Origen</span>
                <h2 class="seccion-title">Los Cuatro Pilares que Definen Nuestra Taza</h2>
                <p class="seccion-subtext">
                    Cada paso en nuestra cadena está diseñado para honrar la riqueza botánica del Huila y ofrecer una taza inolvidable.
                </p>
            </div>

            <div class="pilares-bento-grid">
                <!-- Pilar 1 -->
                <div class="pilar-card">
                    <div>
                        <div class="pilar-top">
                            <div class="pilar-icon-wrap">
                                <i class="fa-solid fa-mountain-sun"></i>
                            </div>
                            <span class="pilar-step-num">01 · TERROIR</span>
                        </div>
                        <h3 class="pilar-title">Suelos Volcánicos &amp; Altura</h3>
                        <p class="pilar-desc">
                            Nuestros lotes provienen de microclimas únicos en el Macizo Colombiano entre 1.500 y 1.950 msnm, confiriendo una acidez brillante, cuerpo sedoso y notas florales distintivas.
                        </p>
                    </div>
                    <div class="pilar-badge-list">
                        <span class="pilar-badge">Pitalito</span>
                        <span class="pilar-badge">San Agustín</span>
                        <span class="pilar-badge">Garzón</span>
                        <span class="pilar-badge">La Plata</span>
                    </div>
                </div>

                <!-- Pilar 2 -->
                <div class="pilar-card">
                    <div>
                        <div class="pilar-top">
                            <div class="pilar-icon-wrap">
                                <i class="fa-solid fa-handshake-angle"></i>
                            </div>
                            <span class="pilar-step-num">02 · COMERCIO DIRECTO</span>
                        </div>
                        <h3 class="pilar-title">Comercio Ético &amp; Justo</h3>
                        <p class="pilar-desc">
                            Establecemos relaciones de largo plazo con los caficultores locales, pagando precios por encima del mercado internacional para recompensar cosechas selectas y sostenibles.
                        </p>
                    </div>
                    <div class="pilar-badge-list">
                        <span class="pilar-badge">Trato Directo</span>
                        <span class="pilar-badge">Trazabilidad Total</span>
                        <span class="pilar-badge">Impacto Social</span>
                    </div>
                </div>

                <!-- Pilar 3 -->
                <div class="pilar-card">
                    <div>
                        <div class="pilar-top">
                            <div class="pilar-icon-wrap">
                                <i class="fa-solid fa-fire-burner"></i>
                            </div>
                            <span class="pilar-step-num">03 · TOSTIÓN ARTESANAL</span>
                        </div>
                        <h3 class="pilar-title">Curvas de Tueste en Neiva</h3>
                        <p class="pilar-desc">
                            Tostamos semanalmente en pequeños baches con curvas térmicas personalizadas para resaltar la dulzura de la panela, la caña de azúcar y los matices cítricos y achocolatados.
                        </p>
                    </div>
                    <div class="pilar-badge-list">
                        <span class="pilar-badge">Tueste Medio</span>
                        <span class="pilar-badge">Lotes Pequeños</span>
                        <span class="pilar-badge">Frescura Garantizada</span>
                    </div>
                </div>

                <!-- Pilar 4 -->
                <div class="pilar-card">
                    <div>
                        <div class="pilar-top">
                            <div class="pilar-icon-wrap">
                                <i class="fa-solid fa-mug-hot"></i>
                            </div>
                            <span class="pilar-step-num">04 · BARISMO</span>
                        </div>
                        <h3 class="pilar-title">Experiencia Sensorial en Barra</h3>
                        <p class="pilar-desc">
                            Nuestros baristas dominan métodos de extracción artesanal (V60, Chemex, Aeropress, Prensa Francesa y Espresso) calibrando molienda, agua y ratio para una taza perfecta.
                        </p>
                    </div>
                    <div class="pilar-badge-list">
                        <span class="pilar-badge">Métodos Filtrados</span>
                        <span class="pilar-badge">Espresso Calibrado</span>
                        <span class="pilar-badge">Catación Guiada</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================================= -->
    <!-- 4. EL VIAJE DEL CAFÉ (TIMELINE 3 PASOS)                  -->
    <!-- ======================================================= -->
    <section class="seccion-viaje">
        <div class="seccion-header-center">
            <span class="editorial-tag" style="justify-content: center;">Trazabilidad</span>
            <h2 class="seccion-title">El Viaje del Grano a tu Taza</h2>
            <p class="seccion-subtext">
                El rigor y la paciencia detrás de cada presentación de Tantico.
            </p>
        </div>

        <div class="viaje-steps-grid">
            <div class="viaje-step-card">
                <span class="viaje-step-number">01</span>
                <h3 class="viaje-step-title">Cosecha Selectiva en Altura</h3>
                <p class="viaje-step-text">
                    Recolección manual únicamente de frutos en estado óptimo de maduración en fincas tradicionales del departamento del Huila.
                </p>
            </div>
            <div class="viaje-step-card">
                <span class="viaje-step-number">02</span>
                <h3 class="viaje-step-title">Beneficio &amp; Fermentación</h3>
                <p class="viaje-step-text">
                    Procesos Lavado clásico, Honey sedoso y Natural intenso con monitoreo de temperatura y horas de fermentación controlada.
                </p>
            </div>
            <div class="viaje-step-card">
                <span class="viaje-step-number">03</span>
                <h3 class="viaje-step-title">Catación SCA &amp; Servicio</h3>
                <p class="viaje-step-text">
                    Validación sensorial en mesa de catación y preparación experta en nuestra barra para que vivas el verdadero sabor huilense.
                </p>
            </div>
        </div>
    </section>

    <!-- ======================================================= -->
    <!-- 5. LUXURY CTA BANNER                                    -->
    <!-- ======================================================= -->
    <section class="seccion-cta-nosotros">
        <div class="cta-nosotros-card">
            <span class="cta-tag">Vive la Experiencia Tantico</span>
            <h2 class="cta-title">El Huila se saborea sorbo a sorbo</h2>
            <p class="cta-desc">
                Te invitamos a descubrir nuestras presentaciones de café en grano y molido, o a visitarnos en Neiva para disfrutar de una preparación artesanal única.
            </p>
            <div class="cta-actions">
                <a href="servicios.php" class="btn-cta-gold">
                    <i class="fa-solid fa-bag-shopping"></i> Explorar Catálogo de Cafés
                </a>
                <a href="contacto.php" class="btn-cta-outline">
                    <i class="fa-solid fa-envelope"></i> Escríbenos
                </a>
            </div>
        </div>
    </section>

    <!-- ======================================================= -->
    <!-- ======================================================= -->
    <!-- 6. FOOTER LUXURY                                        -->
    <!-- ======================================================= -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS con Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>