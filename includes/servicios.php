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
    <link rel="stylesheet" href="../csss/servicioss.css">
</head>

<body>

    <!-- Header Navigation -->
    <header class="header-principal">
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <div class="coffee-logo">
                        <img src="../assets/imagenes/banner20.png" alt="Coffee Col Logo">
                    </div>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="../index.php">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="nosotros.php">Nosotros</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#servicios">Productos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contacto.php">Contáctanos</a>
                        </li>
                    </ul>

                    <div class="social-icons">
                        <a href="#" title="Facebook" aria-label="Facebook">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </a>
                        <a href="#" title="Instagram" aria-label="Instagram">
                            <i class="fa-solid fa-user"></i>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Sección Servicios -->
    <section id="servicios" class="seccion-servicios">
        <!-- Imagen de fondo -->
        <img src="../assets/imagenes/banner21.png" alt="Banner Servicios" class="bg-servicios">

        <!-- Capa oscura encima de la imagen -->
        <div class="overlay-servicios"></div>

        <!-- Contenido centrado -->
        <div class="contenedor-servicios">
            <div class="titulo-box">
                <h2 class="titulo-servicios">Servicios</h2>
            </div>
        </div>
    </section>

    <section class="capsulas-servicio">


        <div class="capsulas-header">
            <div class="capsulas-titulos">
                <h2>Maquila de cápsulas</h2>
                <h3>Maquilamos tu café en cápsulas listas para competir.</h3>
                <p>
                    Procesamos tu café en cápsulas compatibles con los sistemas más reconocidos. Nuestro control preciso
                    de
                    temperatura y humedad asegura que cada cápsula preserve los matices únicos del origen.
                </p>
            </div>
        </div>

        <br><br>

        <!-- Contenedor Asesoría -->
        <div class="asesoria-box">
            <div class="asesoria-info">
                <h3>Asesoría personalizada</h3>
                <h4 class="asesoria-titulo">
                    Cada café tiene su historia.<br>
                    <strong class="texto-fijo">Nosotros te ayudamos a contarla</strong>
                </h4>
                <p>
                    Desde definir el tueste perfecto, hasta elegir el diseño del empaque y el canal de venta ideal. Te
                    acompañamos en cada paso para que tu marca llegue lista y sólida al mercado.
                </p>
            </div>
            <div class="asesoria-img-wrapper">
                <img src="../assets/imagenes/cam.png" alt="caficultor">
            </div>
        </div>
    </section>

    <section class="faq-section" id="faq">
        <h2 class="faq-title">Lo que más nos preguntan</h2>


        <div class="faq-container">

            <!-- Columna izquierda: Preguntas -->
            <div class="faq-left">
                <div class="faq-items">

                    <div class="faq-item active">
                        <button class="faq-question">
                            ¿Puedo traer mi propio café?
                            <span class="icon">−</span>
                        </button>
                        <div class="faq-answer">
                            <p>Sí, trabajamos con el café que cultivas o con opciones seleccionadas si aún no tienes tu
                                propio grano.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">
                            ¿Cuál es la producción mínima?
                            <span class="icon">＋</span>
                        </button>
                        <div class="faq-answer">
                            <p>Nuestra producción mínima es de 50 kg de café verde. Esto nos permite garantizar la
                                calidad del proceso de tostado y ofrecer precios competitivos para nuestros clientes.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">
                            ¿Puedo exportar con ustedes?
                            <span class="icon">＋</span>
                        </button>
                        <div class="faq-answer">
                            <p>Sí, ofrecemos servicios de exportación. Contamos con todos los permisos y certificaciones
                                necesarias para exportar café de alta calidad a diferentes países.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">
                            ¿Ofrecen envases compostables?
                            <span class="icon">＋</span>
                        </button>
                        <div class="faq-answer">
                            <p>Absolutamente. Tenemos una línea completa de envases biodegradables y compostables,
                                comprometidos con la sostenibilidad ambiental.</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Columna derecha: Buscador + ayuda -->
            <div class="faq-right">
                <div class="faq-search">
                    <input type="text" id="faqSearch" placeholder="Buscar..." />
                    <i class="fas fa-search search-icon"></i>
                </div>
                <div class="faq-help">
                    <h3>¡Necesitas ayuda!</h3>
                    <p>Si tienes alguna pregunta o algo que decirnos no dudes en escribirnos, ¡estamos aquí para
                        ayudarte!</p>
                    <button class="contact-button">Contáctanos</button>
                </div>
            </div>

        </div>

    </section>

            <!-- Granos de café -->
    <div class="image-container coffee-ms">
        <img src="../assets/imagenes/pepacafe.png" alt="Granos de café">
    </div>

    <!-- Objeto 0 --> 
    <div class="image-container object-0">
        <img src="../assets/imagenes/OBJECTS55.png" alt="Objeto 0">
    </div>

    <!-- Objeto 1 -->
    <div class="image-container object-1">
        <img src="../assets/imagenes/OBJECTS.png" alt="Objeto 1">
    </div>

    <!-- Objeto 2 -->
    <div class="image-container object-2">
        <img src="../assets/imagenes/OBJECTS53.png" alt="Objeto 2">
    </div>

    <!-- Objeto 3 -->
    <div class="image-container object-3">
        <img src="../assets/imagenes/OBJECTS52.png" alt="Objeto 3">
    </div>

    <!-- Granos de café (segundo) -->
    <div class="image-container coffeee-xr">
        <img src="../assets/imagenes/pepacafe.png" alt="Granos de café">
    </div>
        <br>

    <div class="imagen-full">
        <img src="../assets/imagenes/banner23.png" alt="Banner">
    </div>

    <div class="footer-wrapper">
        <footer class="footer-coffeecol">
            <div class="footer-content">
                <img src="../assets/imagenes/banner1.png" alt="Logo de CoffeeCol" class="footer-logo" />

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
        // === FUNCIONALIDAD FAQ ===
        document.addEventListener('DOMContentLoaded', function () {
            initializeFAQ();
            initializeContactButton();
            initializeSearch();
        });

        function initializeFAQ() {
            const faqItems = document.querySelectorAll('.faq-item');

            faqItems.forEach(item => {
                const button = item.querySelector('.faq-question');

                if (button) {
                    button.addEventListener('click', () => toggleFAQItem(item, faqItems));

                    button.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            toggleFAQItem(item, faqItems);
                        }
                    });
                }
            });
        }

        function toggleFAQItem(currentItem, allItems) {
            const isCurrentlyActive = currentItem.classList.contains('active');

            // Cerrar todos los otros items
            allItems.forEach(item => {
                if (item !== currentItem) {
                    closeFAQItem(item);
                }
            });

            // Toggle del item actual
            if (isCurrentlyActive) {
                closeFAQItem(currentItem);
            } else {
                openFAQItem(currentItem);
            }
        }

        function openFAQItem(item) {
            const icon = item.querySelector('.icon');
            const answer = item.querySelector('.faq-answer');

            item.classList.add('active');

            if (icon) {
                icon.textContent = '−';
                icon.setAttribute('aria-expanded', 'true');
            }

            if (answer) {
                answer.setAttribute('aria-hidden', 'false');
            }
        }

        function closeFAQItem(item) {
            const icon = item.querySelector('.icon');
            const answer = item.querySelector('.faq-answer');

            item.classList.remove('active');

            if (icon) {
                icon.textContent = '＋';
                icon.setAttribute('aria-expanded', 'false');
            }

            if (answer) {
                answer.setAttribute('aria-hidden', 'true');
            }
        }

        function initializeContactButton() {
            const contactButton = document.querySelector('.contact-button');

            if (contactButton) {
                contactButton.addEventListener('click', handleContactClick);

                contactButton.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        handleContactClick();
                    }
                });
            }
        }

        function handleContactClick() {
            alert('¡Contáctanos!\n\nTeléfono: +57 300 123 4567\nEmail: info@cafeteria.com\nHorario: Lunes a Viernes 8:00 AM - 6:00 PM');
        }

        function initializeSearch() {
            const searchInput = document.getElementById('faqSearch');
            const faqItems = document.querySelectorAll('.faq-item');

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const searchTerm = this.value.toLowerCase();

                    faqItems.forEach(item => {
                        const question = item.querySelector('.faq-question').textContent.toLowerCase();
                        const answer = item.querySelector('.faq-answer p').textContent.toLowerCase();

                        if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = searchTerm === '' ? 'block' : 'none';
                        }
                    });
                });
            }
        }

        // Funciones adicionales disponibles globalmente
        window.FAQUtils = {
            closeAll: function () {
                const faqItems = document.querySelectorAll('.faq-item');
                faqItems.forEach(item => closeFAQItem(item));
            },
            openByIndex: function (index) {
                const faqItems = document.querySelectorAll('.faq-item');
                if (faqItems[index]) {
                    window.FAQUtils.closeAll();
                    openFAQItem(faqItems[index]);
                }
            }
        };

        // ✅ JavaScript completo - mantiene bordes originales de 11px

        let ticking = false;

        function updateScrollPosition() {
            const scrolled = window.pageYOffset;
            const header = document.querySelector('.header-principal');

            if (scrolled > 50) {
                // ✅ HEADER NEGRO al hacer scroll (mantiene bordes originales)
                header.style.backdropFilter = 'blur(25px)';
                header.style.background = 'rgba(0, 0, 0, 0.3)';
                header.style.borderRadius = '11px'; //
            } else {
                // Estado original - transparente
                header.style.backdropFilter = 'blur(20px)';
                header.style.background = 'transparent';
                header.style.borderRadius = '0';
            }


            ticking = false;
        }

        function requestTick() {
            if (!ticking) {
                requestAnimationFrame(updateScrollPosition);
                ticking = true;
            }
        }

        // ✅ Event listener para el scroll
        window.addEventListener('scroll', requestTick);

        // ✅ Cerrar menú móvil al hacer clic en un enlace
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                    const collapse = bootstrap.Collapse.getInstance(navbarCollapse);
                    if (collapse) {
                        collapse.hide();
                    }
                }
            });
        });
    </script>

 <!-- Bootstrap JS con Popper (necesario para navbar en móviles) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>