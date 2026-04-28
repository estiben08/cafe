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
