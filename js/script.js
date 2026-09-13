// Optimización de carga del video
document.addEventListener('DOMContentLoaded', function () {
    const video = document.querySelector('.video-fondo video');
    const loadingText = document.querySelector('.video-loading');

    if (video) {
        video.addEventListener('loadeddata', function () {
            if (loadingText) {
                loadingText.style.opacity = '0';
                setTimeout(() => {
                    loadingText.style.display = 'none';
                }, 300);
            }
        });
    }
});

// Smooth scrolling optimizado
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

const container = document.querySelector('.logo-container');
const logos = Array.from(document.querySelectorAll('.logo-image'));
const btnPrev = document.querySelector('.carousel-btn[aria-label="Anterior"]');
const btnNext = document.querySelector('.carousel-btn[aria-label="Siguiente"]');

if (container && logos.length > 0) {
    const logoWidth = (logos[0].offsetWidth || 80) + 30; // ancho + gap
    const scrollStep = logoWidth * 2.5; // cuanto avanza o retrocede por clic
    let scrollX = 0;
    let isHovering = false;

    // Clonar logos para infinito
    logos.forEach(logo => {
        const clone = logo.cloneNode(true);
        container.appendChild(clone);
    });

    // Movimiento automático
    function autoScroll() {
        if (!isHovering) {
            scrollX += 0.5;
            if (scrollX >= container.scrollWidth / 2) {
                scrollX = 0;
            }
            container.style.transform = `translateX(-${scrollX}px)`;
        }
        requestAnimationFrame(autoScroll);
    }

    // Botón siguiente
    if (btnNext) {
        btnNext.addEventListener('click', () => {
            scrollX += scrollStep;
            if (scrollX >= container.scrollWidth / 2) {
                scrollX = 0;
            }
            container.style.transform = `translateX(-${scrollX}px)`;
        });
    }

    // Botón anterior
    if (btnPrev) {
        btnPrev.addEventListener('click', () => {
            scrollX -= scrollStep;
            if (scrollX < 0) {
                scrollX = container.scrollWidth / 2;
            }
            container.style.transform = `translateX(-${scrollX}px)`;
        });
    }

    // Pausar al pasar mouse
    container.addEventListener('mouseenter', () => isHovering = true);
    container.addEventListener('mouseleave', () => isHovering = false);

    autoScroll();
}

// Scroll handling para el header
function updateHeaderScroll() {
    const scrolled = window.pageYOffset || document.documentElement.scrollTop;
    const header = document.querySelector('.header-principal');
    if (header) {
        if (scrolled > 40) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }
}
window.addEventListener('scroll', updateHeaderScroll, { passive: true });
updateHeaderScroll();

// Optimización de touch events
document.addEventListener('touchstart', function () { }, { passive: true });
document.addEventListener('touchmove', function () { }, { passive: true });

document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
    link.addEventListener('click', () => {
        const navbarCollapse = document.querySelector('.navbar-collapse');
        if (navbarCollapse) {
            const collapse = bootstrap.Collapse.getOrCreateInstance(navbarCollapse);
            collapse.hide();
        }
    });
});

// Preload de imágenes críticas
const criticalImages = ['assets/imagenes/banner1.png'];
criticalImages.forEach(src => {
    const img = new Image();
    img.src = src;
});



// Configuración enriquecida de las galerías Tantico
const gallerySets = {
    1: [ // Métodos de Filtrado
        { src: "assets/imagenes/banner14.jpg", class: "item1", title: "V60 Dripper", tag: "Filtrado Manual", desc: "Extracción limpia con acidez y notas florales brillantes" },
        { src: "assets/imagenes/banner15.png", class: "item2", title: "Chemex & Selección", tag: "Cuerpo Balanceado", desc: "Claridad aromática excepcional y dulzor prolongado" },
        { src: "assets/imagenes/banner12.jpg", class: "item3", title: "Aeropress", tag: "Inmersión & Presión", desc: "Perfil concentrado con gran complejidad sensorial" },
        { src: "assets/imagenes/banner11.jpg", class: "item4", title: "Prensa Francesa", tag: "Cuerpo Completo", desc: "Aceites naturales y textura densa envolvente" },
        { src: "assets/imagenes/banner10.jpg", class: "item5", title: "Sifón Japonés", tag: "Vacío Térmico", desc: "Espectáculo visual y taza de máxima pureza" }
    ],
    2: [ // Barra de Espresso
        { src: "assets/imagenes/banner12.jpg", class: "item1", title: "Espresso Doble", tag: "9 Bares de Presión", desc: "Crema dorada densa y dulzor achocolatado concentrado" },
        { src: "assets/imagenes/banner14.jpg", class: "item2", title: "Latte Art Signature", tag: "Cremado Perfecto", desc: "Microespuma sedosa y armonía de sabores lácteos" },
        { src: "assets/imagenes/banner10.jpg", class: "item3", title: "Capuchino Clásico", tag: "Proporción 1:1:1", desc: "Textura cremosa aterciopelada y notas a cacao" },
        { src: "assets/imagenes/banner13.jpg", class: "item4", title: "Flat White", tag: "Doble Ristretto", desc: "Intensidad y suavidad en perfecto equilibrio" },
        { src: "assets/imagenes/banner11.jpg", class: "item5", title: "Mocaccino de Origen", tag: "Cacao Huilense", desc: "Fusión de café de especialidad y chocolate artesanal" }
    ],
    3: [ // Nuestra Cafetería
        { src: "assets/imagenes/tantiii.png", class: "item1", title: "Espacio & Calidez", tag: "Ambiente Acogedor", desc: "Diseñado para disfrutar el café con calma y buena charla" },
        { src: "assets/imagenes/banner15.png", class: "item2", title: "Mesa de Catación", tag: "Origen Huila", desc: "Evaluando perfiles de taza de microlotes seleccionados" },
        { src: "assets/imagenes/banner10.jpg", class: "item3", title: "Nuestros Baristas", tag: "Pasión & Técnica", desc: "Calibración milimétrica en cada receta de extracción" },
        { src: "assets/imagenes/banner11.jpg", class: "item4", title: "Tueste Artesanal", tag: "Granos Frescos", desc: "Resaltando el potencial genético de cada variedad" },
        { src: "assets/imagenes/banner14.jpg", class: "item5", title: "Comunidad Tantico", tag: "Experiencias", desc: "Donde el orgullo por el Huila se comparte taza a taza" }
    ]
};

// Elementos del DOM de la Galería
const gridContainer = document.getElementById("gallery-grid");
const buttons = document.querySelectorAll(".tab-btn");

// Función para cargar una galería específica con animación suave
function loadGallery(set) {
    if (!gridContainer || !gallerySets[set]) return;
    
    gridContainer.style.opacity = "0";
    gridContainer.style.transform = "translateY(12px)";
    
    setTimeout(() => {
        gridContainer.innerHTML = "";
        gallerySets[set].forEach((item, index) => {
            const div = document.createElement("div");
            div.classList.add("grid-item", item.class);
            div.style.animationDelay = `${index * 0.07}s`;
            div.innerHTML = `
                <div class="gallery-card-inner">
                    <img src="${item.src}" alt="${item.title}" class="gallery-img">
                    <div class="gallery-overlay">
                        <span class="gallery-card-tag">${item.tag}</span>
                        <h4 class="gallery-card-title">${item.title}</h4>
                        <p class="gallery-card-desc">${item.desc}</p>
                    </div>
                </div>
            `;
            gridContainer.appendChild(div);
        });
        
        gridContainer.style.opacity = "1";
        gridContainer.style.transform = "translateY(0)";
    }, 180);
}

// Event listeners para los botones de pestañas
if (buttons.length > 0) {
    buttons.forEach(btn => {
        btn.addEventListener("click", () => {
            buttons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
            loadGallery(btn.getAttribute("data-set"));
        });
    });
}

// Cargar la primera galería al inicio si el contenedor existe
if (gridContainer) {
    loadGallery(1);
}

/* ================================================ */
/* mapa-huila.js                                    */
/* Ficha de Catación & Origen Especialidad Huila   */
/* ================================================ */

(function () {
    'use strict';

    /* ---------- Datos de Especialidad Cafetera del Huila ---------- */
    const datos = {
        "neiva": {
            nombre: "Neiva",
            region: "Norte del Huila",
            score: "86.5 SCA",
            altitud: "1.450 - 1.700 msnm",
            variedad: "Castillo & Caturra",
            proceso: "Lavado Clásico",
            tueste: "Medio Intenso",
            precio: "$42.000",
            desc: "Cafés cultivados en las estribaciones de la cordillera oriental de Neiva, caracterizados por notas dulces a caramelo y chocolate con cuerpo denso.",
            tags: [
                { texto: "Caramelo Tostado", tipo: "dulce" },
                { texto: "Chocolate Negro", tipo: "chocolate" },
                { texto: "Cítricos Suaves", tipo: "citrico" }
            ],
            dulzura: 82, acidez: 72, cuerpo: 85
        },
        "palermo": {
            nombre: "Palermo",
            region: "Noroccidente del Huila",
            score: "87.0 SCA",
            altitud: "1.550 - 1.850 msnm",
            variedad: "Castillo & Colombia",
            proceso: "Lavado 30h",
            tueste: "Medio",
            precio: "$44.000",
            desc: "Microclimas cordilleranos que generan granos densos con notas a frutos secos, panela y sutil acidez de manzana roja.",
            tags: [
                { texto: "Panela Orgánica", tipo: "dulce" },
                { texto: "Manzana Roja", tipo: "frutal" },
                { texto: "Cacao Suave", tipo: "chocolate" }
            ],
            dulzura: 85, acidez: 76, cuerpo: 81
        },
        "isnos": {
            nombre: "Isnos",
            region: "Macizo Colombiano",
            score: "88.8 SCA",
            altitud: "1.700 - 2.050 msnm",
            variedad: "Bourbon Rosado & Tabi",
            proceso: "Honey Rojo",
            tueste: "Medio Claro",
            precio: "$50.000",
            desc: "Tierra de cascadas y cañones profundos en el Macizo. Destaca por su acidez málica brillante, notas a frutos rojos y dulzura de caña panelera.",
            tags: [
                { texto: "Frutos Rojos", tipo: "frutal" },
                { texto: "Panela de Caña", tipo: "dulce" },
                { texto: "Cereza Madura", tipo: "frutal" }
            ],
            dulzura: 90, acidez: 86, cuerpo: 82
        },
        "villa-vieja": {
            nombre: "Villa Vieja",
            region: "Norte del Huila",
            score: "86.5 SCA",
            altitud: "1.500 - 1.800 msnm",
            variedad: "Castillo & Caturra",
            proceso: "Lavado Clásico",
            tueste: "Medio Intenso",
            precio: "$44.000",
            desc: "Zona norte del departamento con gran riqueza térmica. Cafés con perfil dulce y achocolatado, ideales para espressos intensos y métodos de filtro.",
            tags: [
                { texto: "Chocolate Amargo", tipo: "chocolate" },
                { texto: "Caramelo Tostado", tipo: "dulce" },
                { texto: "Vainilla", tipo: "dulce" }
            ],
            dulzura: 84, acidez: 72, cuerpo: 86
        },
        "tello": {
            nombre: "Tello",
            region: "Norte del Huila",
            score: "86.8 SCA",
            altitud: "1.500 - 1.800 msnm",
            variedad: "Castillo & Colombia",
            proceso: "Lavado 28h",
            tueste: "Medio",
            precio: "$43.000",
            desc: "Granos cultivados entre la cordillera y el valle, con notas dulces y acidez balanceada de naranja dulce.",
            tags: [
                { texto: "Naranja Dulce", tipo: "citrico" },
                { texto: "Panela", tipo: "dulce" },
                { texto: "Chocolate", tipo: "chocolate" }
            ],
            dulzura: 83, acidez: 74, cuerpo: 82
        },
        "colombia": {
            nombre: "Colombia",
            region: "Nororiente del Huila",
            score: "87.2 SCA",
            altitud: "1.600 - 1.950 msnm",
            variedad: "Caturra & Typica",
            proceso: "Lavado Tradicional",
            tueste: "Medio Claro",
            precio: "$45.000",
            desc: "Tierras altas con agradable clima de montaña que propician una lenta maduración y sabores frutales aromáticos.",
            tags: [
                { texto: "Durazno", tipo: "frutal" },
                { texto: "Miel", tipo: "dulce" },
                { texto: "Cítricos", tipo: "citrico" }
            ],
            dulzura: 86, acidez: 80, cuerpo: 79
        },
        "gigante": {
            nombre: "Gigante",
            region: "Centro del Huila",
            score: "87.0 SCA",
            altitud: "1.500 - 1.850 msnm",
            variedad: "Caturra Chiroso & Castillo",
            proceso: "Lavado 36h",
            tueste: "Medio",
            precio: "$45.000",
            desc: "Cafetales cultivados bajo sombra con vista al valle del Magdalena. Perfil dulce, sedoso y notas a frutas tropicales maduras.",
            tags: [
                { texto: "Frutas Tropicales", tipo: "frutal" },
                { texto: "Caramelo", tipo: "dulce" },
                { texto: "Cacao Suave", tipo: "chocolate" }
            ],
            dulzura: 85, acidez: 78, cuerpo: 80
        },
        "hobo": {
            nombre: "Hobo",
            region: "Centro del Huila",
            score: "86.5 SCA",
            altitud: "1.480 - 1.750 msnm",
            variedad: "Castillo & Colombia",
            proceso: "Lavado Clásico",
            tueste: "Medio Intenso",
            precio: "$42.000",
            desc: "Cafés con cuerpo balanceado y notas dulces a panela y chocolate con leche.",
            tags: [
                { texto: "Chocolate con Leche", tipo: "chocolate" },
                { texto: "Panela", tipo: "dulce" },
                { texto: "Avellana", tipo: "dulce" }
            ],
            dulzura: 82, acidez: 71, cuerpo: 84
        },
        "san-agustin": {
            nombre: "San Agustín",
            region: "Macizo Colombiano",
            score: "89.5 SCA",
            altitud: "1.750 - 2.100 msnm",
            variedad: "Pink Bourbon & Geisha",
            proceso: "Lavado 48h Fermentación",
            tueste: "Medio Claro Especial",
            precio: "$52.000",
            desc: "Origen legendario del Macizo Colombiano. Suelo volcánico y gran altura dan vida a tazas florales excepcionales con notas a jazmín y frutas exóticas.",
            tags: [
                { texto: "Jazmín & Azahar", tipo: "floral" },
                { texto: "Frutos Rojos", tipo: "frutal" },
                { texto: "Miel de Abejas", tipo: "dulce" },
                { texto: "Mandarina", tipo: "citrico" }
            ],
            dulzura: 92, acidez: 88, cuerpo: 82
        },
        "algeciras": {
            nombre: "Algeciras",
            region: "Oriente del Huila",
            score: "87.8 SCA",
            altitud: "1.550 - 1.900 msnm",
            variedad: "Caturra & Castillo",
            proceso: "Lavado 36h",
            tueste: "Medio",
            precio: "$46.000",
            desc: "La Perla del Oriente huilense. Cafés con fragancia intensa, cuerpo sedoso y notas a frutas amarillas y chocolate amargo.",
            tags: [
                { texto: "Frutas Amarillas", tipo: "frutal" },
                { texto: "Chocolate Amargo", tipo: "chocolate" },
                { texto: "Caramelo", tipo: "dulce" }
            ],
            dulzura: 86, acidez: 80, cuerpo: 83
        },
        "iquira": {
            nombre: "Íquira",
            region: "Occidente del Huila",
            score: "88.0 SCA",
            altitud: "1.600 - 1.980 msnm",
            variedad: "Castillo & Caturra",
            proceso: "Lavado Tradicional",
            tueste: "Medio Claro",
            precio: "$46.000",
            desc: "Zona montañosa de la cordillera Central con gran pureza hídrica. Tazas limpias con notas cítricas a mandarina y miel.",
            tags: [
                { texto: "Mandarina", tipo: "citrico" },
                { texto: "Miel Silvestre", tipo: "dulce" },
                { texto: "Cacao", tipo: "chocolate" }
            ],
            dulzura: 87, acidez: 83, cuerpo: 80
        },
        "garzon": {
            nombre: "Garzón",
            region: "Centro del Huila",
            score: "87.5 SCA",
            altitud: "1.500 - 1.800 msnm",
            variedad: "Castillo & Colombia",
            proceso: "Lavado Tradicional",
            tueste: "Medio Balanceado",
            precio: "$45.000",
            desc: "Tierra de gran tradición cafetera y cacaotera. Perfil de taza armónico y redondo, con notas a panela, nueces tostadas y chocolate con leche.",
            tags: [
                { texto: "Panela", tipo: "dulce" },
                { texto: "Chocolate con Leche", tipo: "chocolate" },
                { texto: "Nueces Tostadas", tipo: "dulce" }
            ],
            dulzura: 86, acidez: 76, cuerpo: 82
        },
        "rivera": {
            nombre: "Rivera",
            region: "Centro-Norte del Huila",
            score: "86.8 SCA",
            altitud: "1.450 - 1.750 msnm",
            variedad: "Castillo & Caturra",
            proceso: "Lavado 24h",
            tueste: "Medio",
            precio: "$43.000",
            desc: "Cafés aromáticos con dulzura marcada de caramelo y final limpio.",
            tags: [
                { texto: "Caramelo", tipo: "dulce" },
                { texto: "Chocolate", tipo: "chocolate" },
                { texto: "Cítrico Suave", tipo: "citrico" }
            ],
            dulzura: 84, acidez: 73, cuerpo: 83
        },
        "campoalegre": {
            nombre: "Campoalegre",
            region: "Centro del Huila",
            score: "86.5 SCA",
            altitud: "1.450 - 1.700 msnm",
            variedad: "Castillo",
            proceso: "Lavado Clásico",
            tueste: "Medio Intenso",
            precio: "$42.000",
            desc: "Notas a cacao y panela tostada con cuerpo consistente.",
            tags: [
                { texto: "Cacao", tipo: "chocolate" },
                { texto: "Panela Tostada", tipo: "dulce" }
            ],
            dulzura: 82, acidez: 70, cuerpo: 85
        },
        "aipe": {
            nombre: "Aipe",
            region: "Norte del Huila",
            score: "86.2 SCA",
            altitud: "1.400 - 1.650 msnm",
            variedad: "Castillo & Colombia",
            proceso: "Lavado",
            tueste: "Medio Intenso",
            precio: "$42.000",
            desc: "Cafés de buena textura y notas a chocolate amargo y frutos secos.",
            tags: [
                { texto: "Chocolate Amargo", tipo: "chocolate" },
                { texto: "Nuez", tipo: "dulce" }
            ],
            dulzura: 80, acidez: 68, cuerpo: 86
        },
        "tarqui": {
            nombre: "Tarqui",
            region: "Centro-Sur del Huila",
            score: "87.8 SCA",
            altitud: "1.550 - 1.900 msnm",
            variedad: "Castillo & Bourbon",
            proceso: "Lavado",
            tueste: "Medio",
            precio: "$45.000",
            desc: "Fincas familiares de gran tradición. Cafés con excelente dulzura, notas a chocolate con leche y suaves notas a frutos rojos.",
            tags: [
                { texto: "Chocolate con Leche", tipo: "chocolate" },
                { texto: "Panela", tipo: "dulce" },
                { texto: "Frutos Rojos", tipo: "frutal" }
            ],
            dulzura: 86, acidez: 78, cuerpo: 84
        },
        "yaguara": {
            nombre: "Yaguará",
            region: "Centro del Huila",
            score: "86.6 SCA",
            altitud: "1.450 - 1.750 msnm",
            variedad: "Castillo",
            proceso: "Lavado Clásico",
            tueste: "Medio",
            precio: "$43.000",
            desc: "Café con notas acarameladas y balance suave, ideal para disfrutar a cualquier hora.",
            tags: [
                { texto: "Caramelo", tipo: "dulce" },
                { texto: "Cacao", tipo: "chocolate" }
            ],
            dulzura: 83, acidez: 72, cuerpo: 81
        },
        "tesalia": {
            nombre: "Tesalia",
            region: "Occidente del Huila",
            score: "87.2 SCA",
            altitud: "1.500 - 1.820 msnm",
            variedad: "Caturra & Castillo",
            proceso: "Lavado 32h",
            tueste: "Medio",
            precio: "$44.000",
            desc: "Granos con agradable perfil aromático a frutos amarillos y caña dulce.",
            tags: [
                { texto: "Frutas Amarillas", tipo: "frutal" },
                { texto: "Caña Dulce", tipo: "dulce" }
            ],
            dulzura: 85, acidez: 77, cuerpo: 80
        },
        "nataga": {
            nombre: "Nátaga",
            region: "Occidente del Huila",
            score: "87.4 SCA",
            altitud: "1.550 - 1.880 msnm",
            variedad: "Castillo & Caturra",
            proceso: "Lavado 36h",
            tueste: "Medio Claro",
            precio: "$45.000",
            desc: "Relieve montañoso de la cordillera Central. Produce tazas con notas florales y acidez cítrica brillante.",
            tags: [
                { texto: "Flores Blancas", tipo: "floral" },
                { texto: "Cítricos", tipo: "citrico" },
                { texto: "Miel", tipo: "dulce" }
            ],
            dulzura: 86, acidez: 81, cuerpo: 80
        },
        "elias": {
            nombre: "Elías",
            region: "Sur del Huila",
            score: "88.2 SCA",
            altitud: "1.650 - 1.950 msnm",
            variedad: "Pink Bourbon & Castillo",
            proceso: "Lavado Fermentación 36h",
            tueste: "Medio Claro",
            precio: "$48.000",
            desc: "Zona de alta montaña con microclimas privilegiados. Perfil exquisito a frutos rojos, caña de azúcar y flor de café.",
            tags: [
                { texto: "Frutos Rojos", tipo: "frutal" },
                { texto: "Caña de Azúcar", tipo: "dulce" },
                { texto: "Flor de Café", tipo: "floral" }
            ],
            dulzura: 89, acidez: 84, cuerpo: 81
        },
        "pitalito": {
            nombre: "Pitalito",
            region: "Sur del Huila",
            score: "89.0 SCA",
            altitud: "1.650 - 1.950 msnm",
            variedad: "Bourbon Rosado & Caturra",
            proceso: "Honey Amarillo",
            tueste: "Medio Aromático",
            precio: "$49.000",
            desc: "La capital cafetera de Colombia. Reconocido internacionalmente por sus perfiles achocolatados, acidez brillante de frutos amarillos y dulzor prolongado.",
            tags: [
                { texto: "Panela Orgánica", tipo: "dulce" },
                { texto: "Maracuyá", tipo: "frutal" },
                { texto: "Chocolate Amargo", tipo: "chocolate" },
                { texto: "Naranja Valencia", tipo: "citrico" }
            ],
            dulzura: 90, acidez: 85, cuerpo: 86
        },
        "suaza": {
            nombre: "Suaza",
            region: "Suroriente del Huila",
            score: "87.5 SCA",
            altitud: "1.500 - 1.850 msnm",
            variedad: "Castillo & Tabi",
            proceso: "Lavado Tradicional",
            tueste: "Medio",
            precio: "$45.000",
            desc: "Valle fértil con vientos andinos que favorecen un secado homogéneo al sol. Taza balanceada con notas a nueces y panela.",
            tags: [
                { texto: "Panela", tipo: "dulce" },
                { texto: "Nuez", tipo: "dulce" },
                { texto: "Cítricos Suaves", tipo: "citrico" }
            ],
            dulzura: 84, acidez: 75, cuerpo: 83
        },
        "guadalupe": {
            nombre: "Guadalupe",
            region: "Centro-Sur del Huila",
            score: "87.3 SCA",
            altitud: "1.500 - 1.850 msnm",
            variedad: "Castillo & Caturra",
            proceso: "Lavado 30h",
            tueste: "Medio",
            precio: "$44.000",
            desc: "Cafés con cuerpo balanceado, notas a cacao y manzana verde.",
            tags: [
                { texto: "Manzana Verde", tipo: "frutal" },
                { texto: "Cacao", tipo: "chocolate" },
                { texto: "Caramelo", tipo: "dulce" }
            ],
            dulzura: 84, acidez: 78, cuerpo: 81
        },
        "la-plata": {
            nombre: "La Plata",
            region: "Occidente del Huila",
            score: "88.0 SCA",
            altitud: "1.600 - 1.950 msnm",
            variedad: "Caturra & Typica",
            proceso: "Lavado Tradicional",
            tueste: "Medio Claro",
            precio: "$46.000",
            desc: "Puerta de entrada al Macizo. Sus suelos de alta montaña otorgan una acidez cítrica limpia, dulzura a miel y final sedoso persistente.",
            tags: [
                { texto: "Miel Silvestre", tipo: "dulce" },
                { texto: "Limón Mandarino", tipo: "citrico" },
                { texto: "Almendras", tipo: "dulce" }
            ],
            dulzura: 87, acidez: 84, cuerpo: 79
        },
        "agrado": {
            nombre: "Agrado",
            region: "Centro del Huila",
            score: "86.8 SCA",
            altitud: "1.480 - 1.780 msnm",
            variedad: "Castillo & Colombia",
            proceso: "Lavado",
            tueste: "Medio",
            precio: "$43.000",
            desc: "Taza dulce con notas a chocolate y frutos secos.",
            tags: [
                { texto: "Chocolate", tipo: "chocolate" },
                { texto: "Nueces", tipo: "dulce" }
            ],
            dulzura: 83, acidez: 73, cuerpo: 82
        },
        "pital": {
            nombre: "Pital",
            region: "Centro del Huila",
            score: "87.4 SCA",
            altitud: "1.520 - 1.860 msnm",
            variedad: "Caturra & Castillo",
            proceso: "Lavado 32h",
            tueste: "Medio",
            precio: "$44.000",
            desc: "Microclimas cordilleranos que generan cafés con notas a cacao fino y caña de azúcar.",
            tags: [
                { texto: "Cacao Fino", tipo: "chocolate" },
                { texto: "Caña de Azúcar", tipo: "dulce" }
            ],
            dulzura: 85, acidez: 76, cuerpo: 83
        },
        "saladoblanco": {
            nombre: "Saladoblanco",
            region: "Sur del Huila",
            score: "88.5 SCA",
            altitud: "1.650 - 1.950 msnm",
            variedad: "Castillo & Caturra",
            proceso: "Lavado con Fermentación Extendida",
            tueste: "Medio Aromático",
            precio: "$47.000",
            desc: "Altas colinas del sur con excelente régimen de lluvias. Granos de taza balanceada, floral y notas persistentes a durazno y miel.",
            tags: [
                { texto: "Durazno", tipo: "frutal" },
                { texto: "Miel", tipo: "dulce" },
                { texto: "Flores Blancas", tipo: "floral" }
            ],
            dulzura: 88, acidez: 81, cuerpo: 82
        },
        "baraya": {
            nombre: "Baraya",
            region: "Nororiente del Huila",
            score: "86.6 SCA",
            altitud: "1.450 - 1.750 msnm",
            variedad: "Castillo & Colombia",
            proceso: "Lavado",
            tueste: "Medio Intenso",
            precio: "$42.000",
            desc: "Taza con cuerpo marcado, notas a caramelo y chocolate oscuro.",
            tags: [
                { texto: "Chocolate Oscuro", tipo: "chocolate" },
                { texto: "Caramelo", tipo: "dulce" }
            ],
            dulzura: 82, acidez: 71, cuerpo: 85
        },
        "teruel": {
            nombre: "Teruel",
            region: "Occidente del Huila",
            score: "87.0 SCA",
            altitud: "1.500 - 1.820 msnm",
            variedad: "Castillo & Caturra",
            proceso: "Lavado 30h",
            tueste: "Medio",
            precio: "$44.000",
            desc: "Café de montaña con notas achocolatadas y acidez suave de frutos amarillos.",
            tags: [
                { texto: "Cacao", tipo: "chocolate" },
                { texto: "Frutos Amarillos", tipo: "frutal" }
            ],
            dulzura: 84, acidez: 75, cuerpo: 81
        },
        "oporapa": {
            nombre: "Oporapa",
            region: "Sur del Huila",
            score: "88.3 SCA",
            altitud: "1.650 - 1.950 msnm",
            variedad: "Pink Bourbon & Castillo",
            proceso: "Lavado 36h",
            tueste: "Medio Claro",
            precio: "$48.000",
            desc: "Municipio montañoso del sur. Cafés de alta calidad con notas a frutos rojos, panela y sutil toque especiado.",
            tags: [
                { texto: "Frutos Rojos", tipo: "frutal" },
                { texto: "Panela", tipo: "dulce" },
                { texto: "Toque Especiado", tipo: "dulce" }
            ],
            dulzura: 88, acidez: 83, cuerpo: 82
        },
        "altamira": {
            nombre: "Altamira",
            region: "Centro-Sur del Huila",
            score: "87.2 SCA",
            altitud: "1.500 - 1.800 msnm",
            variedad: "Castillo & Caturra",
            proceso: "Lavado",
            tueste: "Medio",
            precio: "$44.000",
            desc: "Taza suave y dulce con notas a chocolate con leche y vainilla.",
            tags: [
                { texto: "Chocolate con Leche", tipo: "chocolate" },
                { texto: "Vainilla", tipo: "dulce" }
            ],
            dulzura: 85, acidez: 74, cuerpo: 82
        },
        "timana": {
            nombre: "Timaná",
            region: "Sur del Huila",
            score: "88.2 SCA",
            altitud: "1.550 - 1.900 msnm",
            variedad: "Caturra & Colombia",
            proceso: "Lavado 36h",
            tueste: "Medio",
            precio: "$46.000",
            desc: "Municipio histórico con una de las cooperativas cafeteras más premiadas. Notas a caramelo cremoso, ciruela y chocolate semiamargo.",
            tags: [
                { texto: "Caramelo Cremoso", tipo: "dulce" },
                { texto: "Ciruela", tipo: "frutal" },
                { texto: "Chocolate Semiamargo", tipo: "chocolate" }
            ],
            dulzura: 87, acidez: 79, cuerpo: 85
        },
        "santa-maria": {
            nombre: "Santa María",
            region: "Noroccidente del Huila",
            score: "87.8 SCA",
            altitud: "1.580 - 1.950 msnm",
            variedad: "Castillo & Typica",
            proceso: "Lavado 36h",
            tueste: "Medio Claro",
            precio: "$46.000",
            desc: "Estribaciones cordilleranas con alta biodiversidad. Cafés aromáticos con notas florales y manzana verde.",
            tags: [
                { texto: "Manzana Verde", tipo: "frutal" },
                { texto: "Miel", tipo: "dulce" },
                { texto: "Flores", tipo: "floral" }
            ],
            dulzura: 86, acidez: 82, cuerpo: 80
        },
        "paicol": {
            nombre: "Paicol",
            region: "Occidente del Huila",
            score: "87.0 SCA",
            altitud: "1.500 - 1.820 msnm",
            variedad: "Castillo & Caturra",
            proceso: "Lavado",
            tueste: "Medio",
            precio: "$44.000",
            desc: "A orillas de la cuenca del río Páez. Café aromático con notas a chocolate y frutas tropicales.",
            tags: [
                { texto: "Chocolate", tipo: "chocolate" },
                { texto: "Frutas Tropicales", tipo: "frutal" }
            ],
            dulzura: 84, acidez: 76, cuerpo: 81
        },
        "acevedo": {
            nombre: "Acevedo",
            region: "Suroriente del Huila",
            score: "88.5 SCA",
            altitud: "1.600 - 1.900 msnm",
            variedad: "Tabi & Castillo",
            proceso: "Lavado Doble Fermentación",
            tueste: "Medio Artesanal",
            precio: "$47.000",
            desc: "Cercano al Parque Cueva de los Guácharos, sus microclimas de bosque de niebla producen cafés con intensa fragancia frutal y notas a caña de azúcar.",
            tags: [
                { texto: "Caña de Azúcar", tipo: "dulce" },
                { texto: "Frutas del Bosque", tipo: "frutal" },
                { texto: "Cacao Fino", tipo: "chocolate" }
            ],
            dulzura: 88, acidez: 82, cuerpo: 84
        },
        "la-argentina": {
            nombre: "La Argentina",
            region: "Occidente del Huila",
            score: "88.4 SCA",
            altitud: "1.650 - 2.000 msnm",
            variedad: "Geisha & Caturra",
            proceso: "Lavado 40h",
            tueste: "Medio Claro",
            precio: "$49.000",
            desc: "Tierras de alta cordillera productoras de microlotes especiales con notas florales y acidez jugosa de moras y arándanos.",
            tags: [
                { texto: "Mora Silvestre", tipo: "frutal" },
                { texto: "Flores de Azahar", tipo: "floral" },
                { texto: "Caña Dulce", tipo: "dulce" }
            ],
            dulzura: 89, acidez: 87, cuerpo: 79
        },
        "palestina": {
            nombre: "Palestina",
            region: "Suroriente del Huila",
            score: "88.6 SCA",
            altitud: "1.600 - 1.980 msnm",
            variedad: "Tabi & Bourbon Rosado",
            proceso: "Lavado Fermentación Controlada",
            tueste: "Medio Claro",
            precio: "$48.000",
            desc: "Colindante con la Serranía de los Churumbelos. Cafés con perfil sensorial exótico, notas a maracuyá, cacao fino y jazmín.",
            tags: [
                { texto: "Maracuyá", tipo: "frutal" },
                { texto: "Cacao Fino", tipo: "chocolate" },
                { texto: "Jazmín", tipo: "floral" }
            ],
            dulzura: 89, acidez: 85, cuerpo: 81
        }
    };

    /* ---------- Referencias al DOM ---------- */
    const tooltip = document.getElementById('mh-tooltip');
    const overlay = document.getElementById('mh-modal-overlay');
    const btnClose = document.getElementById('mh-btn-cerrar');
    let muniActivo = null;

    /* ---------- Abrir Modal Split Panorámico ---------- */
    function abrirModal(info) {
        document.getElementById('mh-muni-nombre').textContent = info.nombre;
        document.getElementById('mh-muni-tipo').textContent = 'ORIGEN · DEPARTAMENTO DEL HUILA';
        document.getElementById('mh-muni-region').textContent = info.region || 'Huila, Colombia';
        document.getElementById('mh-muni-score').textContent = info.score || '87.5 SCA';
        document.getElementById('mh-muni-altitud').textContent = info.altitud || '1.600 - 1.900 msnm';
        document.getElementById('mh-muni-variedad').textContent = info.variedad || 'Castillo & Caturra';
        document.getElementById('mh-muni-proceso').textContent = info.proceso || 'Lavado 36h';
        document.getElementById('mh-muni-tueste').textContent = info.tueste || 'Medio Artesanal';
        document.getElementById('mh-muni-desc').textContent = info.desc;
        
        // Tarjeta de café recomendada
        document.getElementById('mh-muni-nombre-prod').textContent = info.nombre;
        const precioEl = document.getElementById('mh-muni-precio');
        if (precioEl) {
            precioEl.textContent = (info.precio || '$45.000') + ' COP';
        }

        // Píldoras de notas sensoriales
        const tagsContainer = document.getElementById('mh-muni-tags');
        if (tagsContainer && info.tags) {
            tagsContainer.innerHTML = info.tags.map(t => {
                const text = typeof t === 'object' ? t.texto : t;
                const type = typeof t === 'object' ? t.tipo : 'dulce';
                return `<span class="mh-sensory-tag tag-${type}"><i class="fa-solid fa-circle-dot"></i> ${text}</span>`;
            }).join('');
        }

        // Barras de balance sensorial
        const dulzura = info.dulzura || 85;
        const acidez = info.acidez || 75;
        const cuerpo = info.cuerpo || 80;

        const barDulzura = document.getElementById('mh-bar-dulzura');
        const barAcidez = document.getElementById('mh-bar-acidez');
        const barCuerpo = document.getElementById('mh-bar-cuerpo');

        if (barDulzura) barDulzura.style.width = dulzura + '%';
        if (barAcidez) barAcidez.style.width = acidez + '%';
        if (barCuerpo) barCuerpo.style.width = cuerpo + '%';

        const valDulzura = document.getElementById('mh-muni-val-dulzura');
        const valAcidez = document.getElementById('mh-muni-val-acidez');
        const valCuerpo = document.getElementById('mh-muni-val-cuerpo');

        if (valDulzura) valDulzura.textContent = (dulzura / 10).toFixed(1) + ' / 10';
        if (valAcidez) valAcidez.textContent = (acidez / 10).toFixed(1) + ' / 10';
        if (valCuerpo) valCuerpo.textContent = (cuerpo / 10).toFixed(1) + ' / 10';

        overlay.classList.add('mh-visible');
        document.body.style.overflow = 'hidden';
    }

    /* ---------- Cerrar modal ---------- */
    function cerrarModal() {
        overlay.classList.remove('mh-visible');
        document.body.style.overflow = '';
        if (muniActivo) {
            muniActivo.classList.remove('mh-activo');
            muniActivo = null;
        }
    }

    /* ---------- Mover tooltip ---------- */
    function moverTooltip(e) {
        tooltip.style.left = (e.clientX - tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = (e.clientY - tooltip.offsetHeight - 14) + 'px';
    }

    /* ---------- Eventos del mapa ---------- */
    document.querySelectorAll('#mapa-huila path').forEach(function (path) {

        path.addEventListener('mouseenter', function (e) {
            const info = datos[path.id];
            if (!info) return;
            tooltip.textContent = info.nombre;
            tooltip.classList.add('mh-visible');
            moverTooltip(e);
        });

        path.addEventListener('mousemove', moverTooltip);

        path.addEventListener('mouseleave', function () {
            tooltip.classList.remove('mh-visible');
        });

        path.addEventListener('click', function () {
            const info = datos[path.id];
            if (!info) return;

            if (muniActivo) muniActivo.classList.remove('mh-activo');
            path.classList.add('mh-activo');
            muniActivo = path;

            tooltip.classList.remove('mh-visible');
            abrirModal(info);
        });
    });

    /* ---------- Cerrar modal: botón, overlay, Escape ---------- */
    if (btnClose) btnClose.addEventListener('click', cerrarModal);

    if (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) cerrarModal();
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') cerrarModal();
    });

})();