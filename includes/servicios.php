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
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Inter:wght@300;400;500;600;700;800&family=Dancing+Script:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="icon" href="../assets/imagenes/banner1.png" type="image/x-icon">

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../csss/servicioss.css">
</head>



<body>
    <?php include 'encabezado.php'; ?>



    <!-- Sección Servicios -->
    <section id="servicios" class="seccion-servicios">
        <!-- Imagen de fondo -->
        <img src="../assets/imagenes/banner21.png" alt="Banner Servicios" class="bg-servicios">

        <!-- Capa oscura encima de la imagen -->
        <div class="overlay-servicios"></div>

        <!-- Contenido centrado -->
        <div class="contenedor-servicios">
            <div class="titulo-box">
                <h2 class="titulo-servicios">Conoce cada una de nuestras presentaciones</h2>
            </div>
        </div>
    </section>

    <div class="tienda-info-band">
        <div class="info-band-grid">
            <div class="info-band-item">
                <div class="info-band-icon"><i class="fas fa-truck"></i></div>
                <div class="info-band-text">
                    <strong>Envío gratis</strong>
                    <span>En pedidos mayores a $150.000</span>
                </div>
            </div>
            <div class="info-band-item">
                <div class="info-band-icon"><i class="fas fa-leaf"></i></div>
                <div class="info-band-text">
                    <strong>100% Sostenible</strong>
                    <span>Empaque biodegradable</span>
                </div>
            </div>
            <div class="info-band-item">
                <div class="info-band-icon"><i class="fas fa-award"></i></div>
                <div class="info-band-text">
                    <strong>Calidad garantizada</strong>
                    <span>Tostión artesanal certificada</span>
                </div>
            </div>
            <div class="info-band-item">
                <div class="info-band-icon"><i class="fas fa-headset"></i></div>
                <div class="info-band-text">
                    <strong>Asesoría personalizada</strong>
                    <span>Te acompañamos en cada paso</span>
                </div>
            </div>
        </div>
    </div>


    <!-- ══════════════════════════════════════════════════════════
         FILTROS DE CATEGORÍA
    ══════════════════════════════════════════════════════════ -->
    <div class="tienda-filtros">
        <div class="filtros-wrap">
            <button class="filtro-btn active" data-categoria="todos">Todos</button>
            <button class="filtro-btn" data-categoria="tueste-claro">Tueste Claro</button>
            <button class="filtro-btn" data-categoria="tueste-medio">Tueste Medio</button>
            <button class="filtro-btn" data-categoria="tueste-oscuro">Tueste Oscuro</button>
            <button class="filtro-btn" data-categoria="capsulas">Cápsulas</button>
            <button class="filtro-btn" data-categoria="origen-especial">Origen Especial</button>
        </div>
    </div>
    

    <!-- ══════════════════════════════════════════════════════════
         GRID DE PRODUCTOS
    ══════════════════════════════════════════════════════════ -->
    <div class="productos-grid" id="productosGrid">
        <!-- Se llenará con JS -->
    </div>


    <!-- ══════════════════════════════════════════════════════════
         CARRITO — OVERLAY + DRAWER LATERAL
    ══════════════════════════════════════════════════════════ -->
    <div class="carrito-overlay" id="carritoOverlay" onclick="cerrarCarrito()"></div>

    <div class="carrito-drawer" id="carritoDrawer" role="dialog" aria-label="Carrito de compras">

        <div class="carrito-header">
            <div class="carrito-titulo">
                Carrito
                <span id="carritoContadorHeader">0 artículos</span>
            </div>
            <button class="btn-cerrar-carrito" onclick="cerrarCarrito()" aria-label="Cerrar carrito">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Barra de Progreso Envío Gratis VIP -->
        <div class="carrito-envio-progreso" id="carritoEnvioProgreso">
            <div class="envio-progreso-texto" id="envioProgresoTexto">
                <i class="fas fa-truck-fast"></i> <span>Agrega productos para envío gratis</span>
            </div>
            <div class="envio-progreso-track">
                <div class="envio-progreso-bar" id="envioProgresoBar" style="width: 0%;"></div>
            </div>
        </div>

        <!-- Estado vacío -->
        <div class="carrito-vacio" id="carritoVacio">
            <i class="fas fa-bag-shopping"></i>
            <div>
                <strong>Tu carrito está vacío</strong>
                <p>Agrega productos para comenzar tu pedido</p>
            </div>
        </div>

        <!-- Lista de ítems (renderizada por JS) -->
        <div class="carrito-items" id="carritoItemsList" style="display:none"></div>

        <!-- Totales + acciones -->
        <div class="carrito-footer" id="carritoFooter" style="display:none">
            <div class="carrito-resumen">
                <div class="resumen-fila">
                    <span>Subtotal</span>
                    <span id="subtotalCarrito">$0</span>
                </div>
                <div class="resumen-fila">
                    <span>Envío</span>
                    <span id="envioCarrito">Por calcular</span>
                </div>
                <div class="resumen-fila total">
                    <span>Total</span>
                    <span class="precio-total" id="totalCarrito">$0</span>
                </div>
            </div>
            <button class="btn-checkout" onclick="abrirCheckout()">
                <i class="fas fa-lock"></i> Finalizar pedido
            </button>
            <button class="btn-vaciar" onclick="vaciarCarrito()">Vaciar carrito</button>
        </div>

    </div>


    <!-- ══════════════════════════════════════════════════════════
         BOTÓN FLOTANTE DEL CARRITO
    ══════════════════════════════════════════════════════════ -->
    <button class="btn-carrito-flotante" onclick="toggleCarrito()" aria-label="Abrir carrito">
        <i class="fas fa-bag-shopping"></i>
        <span class="carrito-badge oculto" id="carritoBadge">0</span>
    </button>


    <!-- ══════════════════════════════════════════════════════════
         CONTENEDOR DE TOASTS
    ══════════════════════════════════════════════════════════ -->
    <div class="toast-container-custom" id="toastContainer"></div>


    <!-- ══════════════════════════════════════════════════════════
         MODAL CHECKOUT
    ══════════════════════════════════════════════════════════ -->
    <div class="modal-checkout-overlay" id="modalCheckoutOverlay">
        <div class="modal-checkout" role="dialog" aria-label="Formulario de pedido">

            <!-- Formulario -->
            <div class="modal-form-content" id="modalFormContent">
                <h2 class="modal-checkout-title">Finalizar pedido</h2>
                <p class="modal-checkout-sub">Completa tus datos para procesar el envío</p>

                <form id="formCheckout" novalidate>

                    <div class="form-fila">
                        <div class="form-grupo">
                            <label for="co-nombre">Nombre</label>
                            <input type="text" id="co-nombre" placeholder="Tu nombre" autocomplete="given-name">
                            <div class="form-error-msg" id="err-nombre">Ingresa tu nombre (solo letras)</div>
                        </div>
                        <div class="form-grupo">
                            <label for="co-apellido">Apellido</label>
                            <input type="text" id="co-apellido" placeholder="Tu apellido" autocomplete="family-name">
                            <div class="form-error-msg" id="err-apellido">Ingresa tu apellido (solo letras)
                            </div>
                        </div>
                    </div>

                    <div class="form-grupo">
                        <label for="co-email">Correo electrónico</label>
                        <input type="email" id="co-email" placeholder="correo@ejemplo.com" autocomplete="email">
                        <div class="form-error-msg" id="err-email">Ingresa un correo electrónico válido</div>
                    </div>

                    <div class="form-grupo">
                        <label for="co-telefono">Teléfono</label>
                        <input type="tel" id="co-telefono" placeholder="+57 300 000 0000" autocomplete="tel"
                            maxlength="20">
                        <div class="form-error-msg" id="err-telefono">Ingresa un número de teléfono válido</div>
                    </div>

                    <div class="form-grupo">
                        <label for="co-ciudad">Ciudad</label>
                        <select id="co-ciudad" autocomplete="address-level2">
                            <option value="">Selecciona tu ciudad</option>
                            <option>Bogotá</option>
                            <option>Medellín</option>
                            <option>Cali</option>
                            <option>Barranquilla</option>
                            <option>Bucaramanga</option>
                            <option>Cartagena</option>
                            <option>Pereira</option>
                            <option>Manizales</option>
                            <option>Armenia</option>
                            <option>Otra ciudad</option>
                        </select>
                        <div class="form-error-msg" id="err-ciudad">Selecciona tu ciudad</div>
                    </div>

                    <div class="form-grupo">
                        <label for="co-direccion">Dirección de entrega</label>
                        <input type="text" id="co-direccion" placeholder="Calle 00 # 00-00, Apto/Casa"
                            autocomplete="street-address">
                        <div class="form-error-msg" id="err-direccion">Ingresa una dirección válida (mínimo 5
                            caracteres)</div>
                    </div>

                    <!-- Resumen del pedido (llenado por JS) -->
                    <div class="modal-checkout-resumen" id="modalResumen"></div>

                    <div class="modal-acciones">
                        <button type="button" class="btn-cancelar-modal" onclick="cerrarCheckout()">Volver</button>
                        <button type="submit" class="btn-confirmar-modal">
                            <i class="fas fa-check-circle"></i> Confirmar pedido
                        </button>
                    </div>

                </form>
            </div><!-- /modal-form-content -->

            <!-- Estado de éxito -->
            <div class="modal-success" id="modalSuccess">
                <div class="success-icon"><i class="fas fa-check"></i></div>
                <h3 class="success-titulo">¡Pedido confirmado!</h3>
                <p class="success-msg">
                    Hemos recibido tu pedido con éxito. Pronto te contactaremos al correo
                    registrado para confirmar los detalles del envío.
                    <br><br><strong>¡Gracias por elegir CoffeeCol!</strong>
                </p>
                <button class="btn-cerrar-success" onclick="cerrarTodo()">Continuar comprando</button>
            </div>

        </div>
    </div><!-- /modal-checkout-overlay -->


    <br>

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

    <!-- Bootstrap JS con Popper (necesario para navbar en móviles) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/service.js"></script>
    <script type="module" src="../js/auth-check.js"></script>

</body>

</html>