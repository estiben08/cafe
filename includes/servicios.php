<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Productos &amp; Cafés de Especialidad — Tantico</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Goudy+Bookletter+1911&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="icon" href="../assets/imagenes/banner1.png" type="image/x-icon">

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../csss/servicioss.css">
</head>

<body>

    <?php include 'encabezado.php'; ?>

    <!-- ======================================================= -->
    <!-- 1. HERO EDITORIAL DE CATÁLOGO                           -->
    <!-- ======================================================= -->
    <section class="tienda-hero">
        <img src="../assets/imagenes/baner4a.jpg" alt="Café de Especialidad Tantico" class="tienda-hero-bg">
        <div class="tienda-hero-overlay"></div>
        <div class="tienda-hero-content">
            <span class="hero-pill-badge">
                <i class="fa-solid fa-gem"></i> TIENDA &amp; CATÁLOGO EXCLUSIVO · COSECHAS DEL HUILA
            </span>
            <h1 class="tienda-hero-title">
                Cafés de Especialidad<br><em>&amp; Presentaciones Únicas</em>
            </h1>
            <p class="tienda-hero-lead">
                Granos cultivados a más de 1.700 msnm, seleccionados a mano en micro-lotes huilenses y tostados semanalmente en Neiva. Elige tu perfil de sabor preferido.
            </p>
            <div class="hero-specs-row">
                <div class="hero-spec-item">
                    <i class="fa-solid fa-seedling"></i> 100% Arábica de Altura
                </div>
                <div class="hero-spec-item">
                    <i class="fa-solid fa-fire-burner"></i> Tostión Fresca Semanal
                </div>
                <div class="hero-spec-item">
                    <i class="fa-solid fa-truck-fast"></i> Envíos a Todo el País
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================================= -->
    <!-- 2. BANDA DE BENEFICIOS LUXURY                           -->
    <!-- ======================================================= -->
    <div class="tienda-info-band">
        <div class="info-band-grid">
            <div class="info-band-item">
                <div class="info-band-icon"><i class="fas fa-truck"></i></div>
                <div class="info-band-text">
                    <strong>Envío Gratis VIP</strong>
                    <span>En pedidos mayores a $150.000</span>
                </div>
            </div>
            <div class="info-band-item">
                <div class="info-band-icon"><i class="fas fa-leaf"></i></div>
                <div class="info-band-text">
                    <strong>100% Sostenible</strong>
                    <span>Empaque con válvula desgasificadora</span>
                </div>
            </div>
            <div class="info-band-item">
                <div class="info-band-icon"><i class="fas fa-award"></i></div>
                <div class="info-band-text">
                    <strong>Calidad SCA 85+</strong>
                    <span>Tostión artesanal de precisión</span>
                </div>
            </div>
            <div class="info-band-item">
                <div class="info-band-icon"><i class="fas fa-headset"></i></div>
                <div class="info-band-text">
                    <strong>Asesoría de Barista</strong>
                    <span>Recomendaciones para tu método</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- 3. CABECERA DE CATÁLOGO & FILTROS                       -->
    <!-- ======================================================= -->
    <div class="tienda-catalogo-header">
        <div class="catalogo-titulo-wrap">
            <h2>Nuestra Carta &amp; Catálogo Gourmet</h2>
            <span class="catalogo-contador" id="contadorProductos">Cargando delicias de especialidad...</span>
        </div>
    </div>

    <div class="tienda-filtros">
        <div class="filtros-wrap">
            <button class="filtro-btn active" data-categoria="todos"><i class="fa-solid fa-border-all" style="font-size:11px;margin-right:4px;"></i> Todos</button>
            <button class="filtro-btn" data-categoria="cafes-todos"><i class="fa-solid fa-mug-hot" style="font-size:11px;margin-right:4px;"></i> Cafés</button>
            <button class="filtro-btn" data-categoria="tueste-claro">Tueste Claro</button>
            <button class="filtro-btn" data-categoria="tueste-medio">Tueste Medio</button>
            <button class="filtro-btn" data-categoria="tueste-oscuro">Tueste Oscuro</button>
            <button class="filtro-btn" data-categoria="capsulas">Cápsulas</button>
            <button class="filtro-btn" data-categoria="reposteria"><i class="fa-solid fa-cake-candles" style="font-size:11px;margin-right:4px;"></i> Tortas &amp; Repostería</button>
            <button class="filtro-btn" data-categoria="panaderia"><i class="fa-solid fa-bread-slice" style="font-size:11px;margin-right:4px;"></i> Panadería</button>
            <button class="filtro-btn" data-categoria="desayunos"><i class="fa-solid fa-utensils" style="font-size:11px;margin-right:4px;"></i> Desayunos &amp; Brunch</button>
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- 4. GRID DE PRODUCTOS DINÁMICO                           -->
    <!-- ======================================================= -->
    <div class="productos-grid" id="productosGrid">
        <!-- Renderizado dinámicamente por js/service.js -->
    </div>

    <!-- ======================================================= -->
    <!-- 5. CARRITO — OVERLAY + DRAWER LATERAL                   -->
    <!-- ======================================================= -->
    <div class="carrito-overlay" id="carritoOverlay" onclick="cerrarCarrito()"></div>

    <div class="carrito-drawer" id="carritoDrawer" role="dialog" aria-label="Carrito de compras">

        <div class="carrito-header">
            <div class="carrito-titulo">
                Tu Carrito
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
                <p>Agrega cafés de especialidad para comenzar tu pedido</p>
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

    <!-- ======================================================= -->
    <!-- 6. BOTÓN FLOTANTE DEL CARRITO                           -->
    <!-- ======================================================= -->
    <button class="btn-carrito-flotante" onclick="toggleCarrito()" aria-label="Abrir carrito">
        <i class="fas fa-bag-shopping"></i>
        <span class="carrito-badge oculto" id="carritoBadge">0</span>
    </button>

    <!-- ======================================================= -->
    <!-- 7. CONTENEDOR DE TOASTS                                 -->
    <!-- ======================================================= -->
    <div class="toast-container-custom" id="toastContainer"></div>

    <!-- ======================================================= -->
    <!-- 8. MODAL CHECKOUT                                       -->
    <!-- ======================================================= -->
    <div class="modal-checkout-overlay" id="modalCheckoutOverlay">
        <div class="modal-checkout" role="dialog" aria-label="Formulario de pedido">

            <!-- Formulario -->
            <div class="modal-form-content" id="modalFormContent">
                <h2 class="modal-checkout-title">Finalizar pedido</h2>
                <p class="modal-checkout-sub">Completa tus datos para procesar el envío de tu café</p>

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
                            <div class="form-error-msg" id="err-apellido">Ingresa tu apellido (solo letras)</div>
                        </div>
                    </div>

                    <div class="form-grupo">
                        <label for="co-email">Correo electrónico</label>
                        <input type="email" id="co-email" placeholder="correo@ejemplo.com" autocomplete="email">
                        <div class="form-error-msg" id="err-email">Ingresa un correo electrónico válido</div>
                    </div>

                    <div class="form-grupo">
                        <label for="co-telefono">Teléfono / WhatsApp</label>
                        <input type="tel" id="co-telefono" placeholder="+57 300 000 0000" autocomplete="tel" maxlength="20">
                        <div class="form-error-msg" id="err-telefono">Ingresa un número de teléfono válido</div>
                    </div>

                    <div class="form-grupo">
                        <label for="co-ciudad">Ciudad de destino</label>
                        <select id="co-ciudad" autocomplete="address-level2">
                            <option value="">Selecciona tu ciudad</option>
                            <option>Neiva</option>
                            <option>Bogotá</option>
                            <option>Medellín</option>
                            <option>Cali</option>
                            <option>Barranquilla</option>
                            <option>Bucaramanga</option>
                            <option>Cartagena</option>
                            <option>Pereira</option>
                            <option>Manizales</option>
                            <option>Armenia</option>
                            <option>Pitalito</option>
                            <option>Garzón</option>
                            <option>Otra ciudad</option>
                        </select>
                        <div class="form-error-msg" id="err-ciudad">Selecciona tu ciudad</div>
                    </div>

                    <div class="form-grupo">
                        <label for="co-direccion">Dirección de entrega</label>
                        <input type="text" id="co-direccion" placeholder="Calle 00 # 00-00, Apto / Casa" autocomplete="street-address">
                        <div class="form-error-msg" id="err-direccion">Ingresa una dirección válida (mínimo 5 caracteres)</div>
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
            </div>

            <!-- Estado de éxito -->
            <div class="modal-success" id="modalSuccess">
                <div class="success-icon"><i class="fas fa-check"></i></div>
                <h3 class="success-titulo">¡Pedido confirmado!</h3>
                <p class="success-msg">
                    Hemos recibido tu pedido con éxito. En breve te contactaremos a tu WhatsApp o correo registrado para coordinar los detalles del despacho.
                    <br><br><strong>¡Gracias por apoyar el café especial del Huila!</strong>
                </p>
                <button class="btn-cerrar-success" onclick="cerrarTodo()">Continuar comprando</button>
            </div>

        </div>
    </div>

    <!-- ======================================================= -->
    <!-- 8.1. MODAL VISTA PREVIA RÁPIDA (QUICK VIEW)             -->
    <!-- ======================================================= -->
    <div class="modal-preview-overlay" id="modalPreviewOverlay" role="dialog" aria-modal="true" aria-labelledby="previewNombre">
        <div class="modal-preview-container">
            <button class="btn-cerrar-preview" onclick="cerrarVistaPrevia()" aria-label="Cerrar vista previa">
                <i class="fas fa-times"></i>
            </button>

            <div class="preview-layout">
                <!-- Columna Izquierda: Imagen y Badges -->
                <div class="preview-media-col">
                    <div class="preview-badge-wrap" id="previewBadgeWrap"></div>
                    <div class="preview-img-box" id="previewImgBox">
                        <img src="" alt="" id="previewImg" class="preview-img-element">
                        <div id="previewImgFallback" class="preview-img-fallback" style="display:none;">
                            <i class="fas fa-mug-hot"></i>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Información & Acciones -->
                <div class="preview-info-col">
                    <div class="preview-categoria-pill" id="previewCategoria">Café de Especialidad</div>
                    <h2 class="preview-nombre" id="previewNombre">Nombre del Producto</h2>

                    <div class="preview-rating-row">
                        <span class="preview-estrellas" id="previewEstrellas">★★★★★</span>
                        <span class="preview-rating-text" id="previewRatingText">4.9 (120 valoraciones)</span>
                    </div>

                    <div class="preview-precio-row">
                        <span class="preview-precio-antes" id="previewPrecioAntes"></span>
                        <span class="preview-precio-actual" id="previewPrecioActual">$0</span>
                        <span class="preview-unidad" id="previewUnidad">/ 250 g</span>
                    </div>

                    <p class="preview-descripcion" id="previewDescripcion">
                        Descripción detallada del producto.
                    </p>

                    <div class="preview-tags-section">
                        <div class="preview-tags-label"><i class="fa-solid fa-wand-magic-sparkles"></i> Perfil &amp; Descriptores:</div>
                        <div class="preview-tags-list" id="previewTagsList"></div>
                    </div>

                    <div class="preview-divider"></div>

                    <!-- Controles de Compra -->
                    <div class="preview-acciones-row">
                        <div class="preview-cantidad-box">
                            <button class="btn-preview-cant" onclick="cambiarCantidadPreview(-1)" aria-label="Disminuir cantidad">−</button>
                            <span class="preview-cant-num" id="previewCantidadNum">1</span>
                            <button class="btn-preview-cant" onclick="cambiarCantidadPreview(1)" aria-label="Aumentar cantidad">+</button>
                        </div>
                        <button class="btn-preview-agregar" id="btnPreviewAgregar" onclick="agregarDesdePreview()">
                            <i class="fas fa-bag-shopping"></i> <span>Agregar al carrito</span>
                        </button>
                        <button class="btn-preview-fav" id="btnPreviewFav" aria-label="Guardar en favoritos" title="Favoritos">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>

                    <div class="preview-garantias">
                        <span><i class="fa-solid fa-truck-fast"></i> Envíos a todo el país</span>
                        <span><i class="fa-solid fa-certificate"></i> Elaboración fresca y artesanal</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- 9. SECCIÓN SUSCRIPCIÓN & EMPRESAS                       -->
    <!-- ======================================================= -->
    <section class="seccion-suscripcion-empresas">
        <div class="suscripcion-card">
            <div class="suscripcion-text">
                <span class="suscripcion-tag">Soluciones para Empresas &amp; Hogares</span>
                <h2 class="suscripcion-title">Suscripción de Café Fresco &amp; Pedidos Mayoristas</h2>
                <p class="suscripcion-desc">
                    ¿Quieres recibir café recién tostado periódicamente o necesitas café de especialidad para tu oficina, hotel o restaurante? Ofrecemos perfiles a medida y precios preferenciales.
                </p>
            </div>
            <div class="suscripcion-actions">
                <a href="contacto.php" class="btn-suscripcion-gold">
                    <i class="fa-solid fa-envelope"></i> Cotizar para Empresas
                </a>
                <a href="https://wa.me/573000000000?text=Hola%20Tantico,%20quisiera%20información%20sobre%20pedidos%20al%20por%20mayor" target="_blank" rel="noopener" class="btn-suscripcion-outline">
                    <i class="fa-brands fa-whatsapp"></i> Hablar con un Asesor
                </a>
            </div>
        </div>
    </section>

    <!-- ======================================================= -->
    <!-- 10. FOOTER GLOBAL                                       -->
    <!-- ======================================================= -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS con Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/service.js"></script>
    <script type="module" src="../js/auth-check.js"></script>

</body>

</html>