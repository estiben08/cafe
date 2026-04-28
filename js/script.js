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

const logoWidth = logos[0].offsetWidth + 30; // ancho + gap
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
btnNext.addEventListener('click', () => {
    scrollX += scrollStep;
    if (scrollX >= container.scrollWidth / 2) {
        scrollX = 0;
    }
    container.style.transform = `translateX(-${scrollX}px)`;
});

// Botón anterior
btnPrev.addEventListener('click', () => {
    scrollX -= scrollStep;
    if (scrollX < 0) {
        scrollX = container.scrollWidth / 2;
    }
    container.style.transform = `translateX(-${scrollX}px)`;
});

// Pausar al pasar mouse
container.addEventListener('mouseenter', () => isHovering = true);
container.addEventListener('mouseleave', () => isHovering = false);

autoScroll();

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

window.addEventListener('scroll', requestTick);

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



// Configuración de las galerías - manteniendo rutas originales
const gallerySets = {
    1: [
        { src: "assets/imagenes/banner14.jpg", class: "item1" },
        { src: "assets/imagenes/banner13.jpg", class: "item2" },
        { src: "assets/imagenes/banner12.jpg", class: "item3" },
        { src: "assets/imagenes/banner11.jpg", class: "item4" },
        { src: "assets/imagenes/banner10.jpg", class: "item5" }
    ],
    2: [
        { src: "assets/imagenes/banner14.jpg", class: "item1" },
        { src: "assets/imagenes/banner13.jpg", class: "item2" },
        { src: "assets/imagenes/banner12.jpg", class: "item3" },
        { src: "assets/imagenes/banner11.jpg", class: "item4" },
        { src: "assets/imagenes/banner10.jpg", class: "item5" }
    ],
    3: [
        { src: "assets/imagenes/banner12.jpg", class: "item1" },
        { src: "assets/imagenes/banner14.jpg", class: "item2" },
        { src: "assets/imagenes/banner10.jpg", class: "item3" },
        { src: "assets/imagenes/banner11.jpg", class: "item4" },
        { src: "assets/imagenes/banner13.jpg", class: "item5" }
    ]
};

// Elementos del DOM
const gridContainer = document.getElementById("gallery-grid");
const buttons = document.querySelectorAll(".tab-btn");

// Función para cargar una galería específica
function loadGallery(set) {
    gridContainer.innerHTML = "";
    gallerySets[set].forEach(item => {
        const div = document.createElement("div");
        div.classList.add("grid-item", item.class);
        div.innerHTML = `<img src="${item.src}" alt="">`;
        gridContainer.appendChild(div);
    });
}

// Event listeners para los botones
buttons.forEach(btn => {
    btn.addEventListener("click", () => {
        buttons.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
        loadGallery(btn.getAttribute("data-set"));
    });
});

// Cargar la primera galería al inicio
loadGallery(1);

/* ================================================ */
/* mapa-huila.js                                    */
/* Lógica interactiva de la sección mapa del Huila  */
/* Sin dependencias externas                        */
/* ================================================ */

(function () {
    'use strict';

    /* ---------- Datos de municipios ---------- */
    const datos = {
        "neiva": {
            nombre: "Neiva",
            tipo: "Capital del departamento",
            poblacion: "~360.000 hab.",
            area: "1.553 km²",
            altitud: "442 m s.n.m.",
            fundacion: "1612",
            desc: "Capital del departamento del Huila, ubicada a orillas del río Magdalena. Centro económico, político y cultural de la región. Famosa mundialmente por el Festival Folclórico del Bambuco.",
            tags: ["Capital departamental", "Festival del Bambuco", "Río Magdalena", "Tatacoa"]
        },
        "palermo": {
            nombre: "Palermo",
            tipo: "Municipio",
            poblacion: "~32.000 hab.",
            area: "961 km²",
            altitud: "520 m s.n.m.",
            fundacion: "1884",
            desc: "Municipio ubicado al norte de Neiva, en el valle del Magdalena. Conocido por su producción agrícola y ganadería. Limita con el Desierto de la Tatacoa.",
            tags: ["Valle del Magdalena", "Agricultura", "Ganadería"]
        },
        "isnos": {
            nombre: "Isnos",
            tipo: "Municipio",
            poblacion: "~32.000 hab.",
            area: "961 km²",
            altitud: "520 m s.n.m.",
            fundacion: "1884",
            desc: "Municipio ubicado al norte de Neiva, en el valle del Magdalena. Conocido por su producción agrícola y ganadería. Limita con el Desierto de la Tatacoa.",
            tags: ["Valle del Magdalena", "Agricultura", "Ganadería"]
        },
        "villa-vieja": {
            nombre: "Villa Vieja",
            tipo: "Municipio",
            poblacion: "~32.000 hab.",
            area: "961 km²",
            altitud: "520 m s.n.m.",
            fundacion: "1884",
            desc: "Municipio ubicado al norte de Neiva, en el valle del Magdalena. Conocido por su producción agrícola y ganadería. Limita con el Desierto de la Tatacoa.",
            tags: ["Valle del Magdalena", "Agricultura", "Ganadería"]
        },
        "tello": {
            nombre: "Tello",
            tipo: "Municipio",
            poblacion: "~32.000 hab.",
            area: "961 km²",
            altitud: "520 m s.n.m.",
            fundacion: "1884",
            desc: "Municipio ubicado al norte de Neiva, en el valle del Magdalena. Conocido por su producción agrícola y ganadería. Limita con el Desierto de la Tatacoa.",
            tags: ["Valle del Magdalena", "Agricultura", "Ganadería"]
        },
        "colombia": {
            nombre: "Colombia",
            tipo: "Municipio",
            poblacion: "~12.000 hab.",
            area: "759 km²",
            altitud: "1.100 m s.n.m.",
            fundacion: "1850",
            desc: "Municipio de la región nororiental del Huila. Productor de café de alta calidad y productos agrícolas variados. Goza de un agradable clima de montaña.",
            tags: ["Café", "Montaña", "Clima templado"]
        },
        "gigante": {
            nombre: "Gigante",
            tipo: "Municipio",
            poblacion: "~33.000 hab.",
            area: "1.028 km²",
            altitud: "757 m s.n.m.",
            fundacion: "1786",
            desc: "Municipio con importante vocación agrícola, especialmente en cacao, café y arroz. Su cercanía al embalse de Betania le da relevancia pesquera y turística.",
            tags: ["Embalse de Betania", "Cacao", "Pesca", "Turismo"]
        },
        "hobo": {
            nombre: "Hobo",
            tipo: "Municipio",
            poblacion: "~7.000 hab.",
            area: "130 km²",
            altitud: "710 m s.n.m.",
            fundacion: "1877",
            desc: "Uno de los municipios más pequeños del Huila, ubicado a orillas del río Magdalena. Su economía se basa en la agricultura y la pesca artesanal.",
            tags: ["Río Magdalena", "Agricultura", "Municipio pequeño"]
        },
        "san-agustin": {
            nombre: "San Agustín",
            tipo: "Municipio",
            poblacion: "~31.000 hab.",
            area: "1.316 km²",
            altitud: "1.695 m s.n.m.",
            fundacion: "1790",
            desc: "Municipio declarado Patrimonio de la Humanidad por la UNESCO por su Parque Arqueológico. Alberga el legado de una cultura precolombina de esculturas en piedra únicas en el mundo.",
            tags: ["Patrimonio UNESCO", "Arqueología", "Cultura precolombina", "Ecoturismo"]
        },
        "algeciras": {
            nombre: "Algeciras",
            tipo: "Municipio",
            poblacion: "~22.000 hab.",
            area: "838 km²",
            altitud: "940 m s.n.m.",
            fundacion: "1870",
            desc: "Municipio productor de café, frutas y ganadería. Conocido como 'La Perla del Oriente'. Cuenta con yacimientos petroleros de importancia para el departamento.",
            tags: ["Petróleo", "Café", "La Perla del Oriente"]
        },
        "iquira": {
            nombre: "Íquira",
            tipo: "Municipio",
            poblacion: "~10.000 hab.",
            area: "596 km²",
            altitud: "1.500 m s.n.m.",
            fundacion: "1908",
            desc: "Municipio montañoso en la cordillera Central. Produce café, caña panelera y plátano. Su economía es principalmente agrícola.",
            tags: ["Café", "Caña panelera", "Cordillera Central"]
        },
        "garzon": {
            nombre: "Garzón",
            tipo: "Municipio",
            poblacion: "~66.000 hab.",
            area: "1.543 km²",
            altitud: "828 m s.n.m.",
            fundacion: "1782",
            desc: "Segunda ciudad en importancia del Huila. Conocida como la 'Capital Diocesana del Huila'. Centro comercial de la región sur, con producción de cacao, café y piscicultura.",
            tags: ["Capital Diocesana", "Cacao", "Piscicultura", "Centro comercial"]
        },
        "rivera": {
            nombre: "Rivera",
            tipo: "Municipio",
            poblacion: "~20.000 hab.",
            area: "415 km²",
            altitud: "600 m s.n.m.",
            fundacion: "1875",
            desc: "Municipio conocido por sus balnearios termales de aguas sulfurosas, uno de los principales destinos turísticos del Huila. Muy cercano a Neiva por la vía al sur.",
            tags: ["Aguas termales", "Balnearios", "Turismo", "Recreación"]
        },
        "campoalegre": {
            nombre: "Campoalegre",
            tipo: "Municipio",
            poblacion: "~33.000 hab.",
            area: "648 km²",
            altitud: "527 m s.n.m.",
            fundacion: "1850",
            desc: "Importante municipio del centro del Huila, conocido como el 'Granero del Huila' por su producción arrocera. También produce algodón, maíz y sorgo.",
            tags: ["Granero del Huila", "Arroz", "Algodón", "Agroindustria"]
        },
        "aipe": {
            nombre: "Aipe",
            tipo: "Municipio",
            poblacion: "~16.000 hab.",
            area: "1.549 km²",
            altitud: "420 m s.n.m.",
            fundacion: "1882",
            desc: "Municipio de clima cálido en el norte del Huila, a orillas del Magdalena. Produce petróleo, arroz y algodón. Limita con el Desierto de la Tatacoa.",
            tags: ["Tatacoa", "Petróleo", "Río Magdalena", "Clima cálido"]
        },
        "tarqui": {
            nombre: "Tarqui",
            tipo: "Municipio",
            poblacion: "~18.000 hab.",
            area: "795 km²",
            altitud: "1.540 m s.n.m.",
            fundacion: "1783",
            desc: "Municipio montañoso productor de café de excelente calidad, plátano y caña. Conocido por sus tradiciones campesinas y paisajes cafeteros.",
            tags: ["Café de especialidad", "Paisaje cafetero", "Tradición campesina"]
        },
        "yaguara": {
            nombre: "Yaguará",
            tipo: "Municipio",
            poblacion: "~8.000 hab.",
            area: "310 km²",
            altitud: "740 m s.n.m.",
            fundacion: "1897",
            desc: "Municipio a orillas del embalse de Betania, uno de los complejos hidroeléctricos más importantes de Colombia. La pesca en el embalse es una actividad económica central.",
            tags: ["Embalse de Betania", "Hidroeléctrica", "Pesca", "Turismo acuático"]
        },
        "tesalia": {
            nombre: "Tesalia",
            tipo: "Municipio",
            poblacion: "~12.000 hab.",
            area: "303 km²",
            altitud: "830 m s.n.m.",
            fundacion: "1898",
            desc: "Municipio con vocación agrícola en el centro occidente del Huila. Produce café, cacao y frutales. La pesca en sus ríos y el embalse de Betania son actividades relevantes.",
            tags: ["Cacao", "Café", "Embalse de Betania", "Agricultura"]
        },
        "nataga": {
            nombre: "Nátaga",
            tipo: "Municipio",
            poblacion: "~7.000 hab.",
            area: "284 km²",
            altitud: "1.390 m s.n.m.",
            fundacion: "1958",
            desc: "Municipio de relieve montañoso en el occidente del Huila. Su economía se basa en la producción de café, cacao, plátano y la ganadería extensiva.",
            tags: ["Café", "Ganadería", "Montaña", "Cacao"]
        },
        "elias": {
            nombre: "Elías",
            tipo: "Municipio",
            poblacion: "~4.000 hab.",
            area: "135 km²",
            altitud: "1.800 m s.n.m.",
            fundacion: "1968",
            desc: "Uno de los municipios más pequeños y de menor población del Huila. Ubicado en zona de alta montaña, produce café, plátano y caña panelera.",
            tags: ["Alta montaña", "Café", "Municipio pequeño"]
        },
        "pitalito": {
            nombre: "Pitalito",
            tipo: "Municipio",
            poblacion: "~120.000 hab.",
            area: "666 km²",
            altitud: "1.318 m s.n.m.",
            fundacion: "1818",
            desc: "Segundo municipio más poblado del Huila y capital cafetera de Colombia. Conocido como 'Corazón de América'. Sus cafés Huila son reconocidos internacionalmente por su calidad.",
            tags: ["Café de clase mundial", "Corazón de América", "Ecoturismo", "Comercio"]
        },
        "suaza": {
            nombre: "Suaza",
            tipo: "Municipio",
            poblacion: "~13.000 hab.",
            area: "702 km²",
            altitud: "950 m s.n.m.",
            fundacion: "1783",
            desc: "Municipio de la región suroriental, en el Valle del río Suaza. Produce café, cacao y ganadería. Es paso hacia el departamento del Caquetá.",
            tags: ["Café", "Cacao", "Paso al Caquetá", "Ganadería"]
        },
        "guadalupe": {
            nombre: "Guadalupe",
            tipo: "Municipio",
            poblacion: "~11.000 hab.",
            area: "535 km²",
            altitud: "1.100 m s.n.m.",
            fundacion: "1758",
            desc: "Municipio del centro del Huila, en la margen del río Magdalena. Produce café, maíz y ganadería. Su artesanía en tejeduría es reconocida regionalmente.",
            tags: ["Artesanía", "Tejeduría", "Café", "Río Magdalena"]
        },
        "la-plata": {
            nombre: "La Plata",
            tipo: "Municipio",
            poblacion: "~55.000 hab.",
            area: "1.993 km²",
            altitud: "1.050 m s.n.m.",
            fundacion: "1651",
            desc: "Tercer municipio más grande del Huila y puerta de entrada al Macizo Colombiano. Centro administrativo del suroccidente. Produce café, cacao, ganadería y tiene gran potencial ecoturístico.",
            tags: ["Macizo Colombiano", "Ecoturismo", "Café", "Cacao"]
        },
        "agrado": {
            nombre: "Agrado",
            tipo: "Municipio",
            poblacion: "~10.000 hab.",
            area: "286 km²",
            altitud: "770 m s.n.m.",
            fundacion: "1783",
            desc: "Municipio a orillas del río Magdalena, en la región central del Huila. Produce cacao, arroz y tiene pesca artesanal. Su embalse aporta al turismo local.",
            tags: ["Cacao", "Río Magdalena", "Pesca artesanal"]
        },
        "pital": {
            nombre: "Pital",
            tipo: "Municipio",
            poblacion: "~14.000 hab.",
            area: "564 km²",
            altitud: "900 m s.n.m.",
            fundacion: "1963",
            desc: "Municipio del centro del Huila con importante producción de cacao y café. Su posición en la cordillera Central le permite tener varios pisos térmicos.",
            tags: ["Cacao", "Café", "Pisos térmicos", "Ganadería"]
        },
        "saladoblanco": {
            nombre: "Saladoblanco",
            tipo: "Municipio",
            poblacion: "~12.000 hab.",
            area: "432 km²",
            altitud: "1.700 m s.n.m.",
            fundacion: "1870",
            desc: "Municipio del sur del Huila, productor de café de altitud reconocido en mercados internacionales. Sus fincas cafeteras ofrecen agroturismo de alta calidad.",
            tags: ["Café de altitud", "Agroturismo", "Cordillera Central"]
        },
        "baraya": {
            nombre: "Baraya",
            tipo: "Municipio",
            poblacion: "~9.000 hab.",
            area: "769 km²",
            altitud: "700 m s.n.m.",
            fundacion: "1876",
            desc: "Municipio de la región nororiental del Huila. Produce arroz, sorgo, algodón y tiene recursos petroleros. Es uno de los municipios con mayor producción agroindustrial del departamento.",
            tags: ["Arroz", "Petróleo", "Agroindustria", "Sorgo"]
        },
        "teruel": {
            nombre: "Teruel",
            tipo: "Municipio",
            poblacion: "~8.500 hab.",
            area: "433 km²",
            altitud: "1.000 m s.n.m.",
            fundacion: "1908",
            desc: "Municipio en el occidente del Huila con economía basada en la ganadería y agricultura de subsistencia. Sus montañas ofrecen potencial para el ecoturismo.",
            tags: ["Ganadería", "Agricultura", "Montaña", "Occidente huilense"]
        },
        "oporapa": {
            nombre: "Oporapa",
            tipo: "Municipio",
            poblacion: "~9.000 hab.",
            area: "344 km²",
            altitud: "1.600 m s.n.m.",
            fundacion: "1964",
            desc: "Municipio montañoso del sur del Huila. Productor de café de excelente calidad, caña panelera y fríjol. Sus paisajes montañosos atraen a los amantes de la naturaleza.",
            tags: ["Café de montaña", "Caña panelera", "Senderismo"]
        },
        "altamira": {
            nombre: "Altamira",
            tipo: "Municipio",
            poblacion: "~5.000 hab.",
            area: "207 km²",
            altitud: "900 m s.n.m.",
            fundacion: "1968",
            desc: "Municipio pequeño en el sur del Huila. Economía basada en café, cacao y plátano. Su tranquilidad y naturaleza lo convierten en destino de turismo rural.",
            tags: ["Turismo rural", "Cacao", "Café", "Municipio pequeño"]
        },
        "timana": {
            nombre: "Timaná",
            tipo: "Municipio",
            poblacion: "~19.000 hab.",
            area: "540 km²",
            altitud: "1.090 m s.n.m.",
            fundacion: "1538",
            desc: "Uno de los municipios más antiguos del Huila, fundado en la época colonial. Puerta al Parque Arqueológico de San Agustín. Produce café, mora y granadilla.",
            tags: ["Municipio histórico", "Puerta San Agustín", "Café", "Mora"]
        },
        "santa-maria": {
            nombre: "Santa María",
            tipo: "Municipio",
            poblacion: "~10.000 hab.",
            area: "562 km²",
            altitud: "1.050 m s.n.m.",
            fundacion: "1962",
            desc: "Municipio del suroriente del Huila en las estribaciones de la cordillera Oriental. Produce café, maíz y yuca. Colinda con el Parque Nacional Cordillera de los Picachos.",
            tags: ["Café", "Parque Natural", "Cordillera Oriental", "Biodiversidad"]
        },
        "paicol": {
            nombre: "Paicol",
            tipo: "Municipio",
            poblacion: "~6.500 hab.",
            area: "303 km²",
            altitud: "980 m s.n.m.",
            fundacion: "1868",
            desc: "Municipio en el occidente del Huila, a orillas del río Páez. Produce cacao, café y frutas tropicales. El río Páez le da identidad y es escenario de actividades recreativas.",
            tags: ["Río Páez", "Cacao", "Frutas tropicales"]
        },
        "acevedo": {
            nombre: "Acevedo",
            tipo: "Municipio",
            poblacion: "~28.000 hab.",
            area: "1.221 km²",
            altitud: "1.500 m s.n.m.",
            fundacion: "1902",
            desc: "Municipio del suroriente huilense, productor de café de alta calidad reconocido internacionalmente. Limita con el Parque Nacional Cueva de los Guácharos, el primer parque nacional de Colombia.",
            tags: ["Café especial", "Cueva de los Guácharos", "Primer parque nacional", "Ecoturismo"]
        },
        "la-argentina": {
            nombre: "La Argentina",
            tipo: "Municipio",
            poblacion: "~13.000 hab.",
            area: "582 km²",
            altitud: "1.600 m s.n.m.",
            fundacion: "1968",
            desc: "Municipio del occidente del Huila en la cordillera Central. Productor de café de altura, mora y lulo. Sus paisajes de alta montaña atraen amantes del senderismo.",
            tags: ["Café de altura", "Mora", "Lulo", "Senderismo"]
        },
        "palestina": {
            nombre: "Palestina",
            tipo: "Municipio",
            poblacion: "~11.000 hab.",
            area: "638 km²",
            altitud: "1.200 m s.n.m.",
            fundacion: "1967",
            desc: "Municipio del suroriente del Huila con importante producción cafetera y cacaotera. Colinda con el Parque Nacional Serranía de los Churumbelos, de gran biodiversidad.",
            tags: ["Café", "Cacao", "Parque Natural", "Churumbelos"]
        }
    };

    /* ---------- Referencias al DOM ---------- */
    const tooltip = document.getElementById('mh-tooltip');
    const overlay = document.getElementById('mh-modal-overlay');
    const btnClose = document.getElementById('mh-btn-cerrar');
    let muniActivo = null;

    /* ---------- Abrir modal ---------- */
    function abrirModal(info) {
        document.getElementById('mh-muni-nombre').textContent = info.nombre;
        document.getElementById('mh-muni-tipo').textContent = info.tipo + ' · Departamento del Huila';
        document.getElementById('mh-muni-poblacion').textContent = info.poblacion;
        document.getElementById('mh-muni-area').textContent = info.area;
        document.getElementById('mh-muni-altitud').textContent = info.altitud;
        document.getElementById('mh-muni-fundacion').textContent = info.fundacion;
        document.getElementById('mh-muni-desc').textContent = info.desc;
        document.getElementById('mh-muni-tags').innerHTML =
            info.tags.map(t => `<span class="mh-tag">${t}</span>`).join('');

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
    btnClose.addEventListener('click', cerrarModal);

    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) cerrarModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') cerrarModal();
    });

})();