// Configuración del carrusel
let indiceActual = 0;
let intervaloCarrusel;
const tiempoRotacion = 5000; // 5 segundos

// Elementos del DOM
// Los selectores ahora deben ser válidos para el HTML
const slides = document.querySelectorAll('.slide');
const contenedorIndicadores = document.getElementById('indicadores');
const btnAnterior = document.getElementById('btnAnterior');
const btnSiguiente = document.getElementById('btnSiguiente');

// Inicializar el carrusel
function inicializarCarrusel() {
    // Si no hay slides, no hacemos nada
    if (slides.length === 0) return;

    // Crear indicadores
    slides.forEach((_, index) => {
        const indicador = document.createElement('div');
        indicador.classList.add('indicador');
        if (index === 0) {
            indicador.classList.add('activo');
        }
        indicador.addEventListener('click', () => {
            detenerRotacionAutomatica();
            irASlide(index);
            iniciarRotacionAutomatica(); // Reiniciar el temporizador
        });
        contenedorIndicadores.appendChild(indicador);
    });

    // Iniciar rotación automática
    iniciarRotacionAutomatica();

    // Event listeners para botones
    btnAnterior.addEventListener('click', () => {
        detenerRotacionAutomatica();
        slideAnterior();
        iniciarRotacionAutomatica();
    });

    btnSiguiente.addEventListener('click', () => {
        detenerRotacionAutomatica();
        slideSiguiente();
        iniciarRotacionAutomatica();
    });

    // Pausar al pasar el mouse
    const carrusel = document.querySelector('.carrusel');
    carrusel.addEventListener('mouseenter', detenerRotacionAutomatica);
    carrusel.addEventListener('mouseleave', iniciarRotacionAutomatica);
}

// Mostrar slide específico
function mostrarSlide(indice) {
    // Ocultar todos los slides
    slides.forEach(slide => {
        slide.classList.remove('activo');
        // Quitar el estilo de transición temporalmente para un cambio inmediato
        slide.style.transition = 'none';
        slide.style.opacity = '0';
    });

    // Actualizar indicadores
    const indicadores = document.querySelectorAll('.indicador');
    indicadores.forEach(indicador => {
        indicador.classList.remove('activo');
    });

    // Mostrar slide actual y reactivar transición
    const currentSlide = slides[indice];
    const currentIndicador = indicadores[indice];
    
    currentSlide.classList.add('activo');

    // Forzar reflow/repaint para que la transición de opacidad funcione
    void currentSlide.offsetWidth; 
    
    currentSlide.style.transition = 'opacity 0.5s ease-in-out';
    currentSlide.style.opacity = '1'; 
    currentIndicador.classList.add('activo');
}

// Ir al slide siguiente
function slideSiguiente() {
    indiceActual++;
    if (indiceActual >= slides.length) {
        indiceActual = 0;
    }
    mostrarSlide(indiceActual);
}

// Ir al slide anterior
function slideAnterior() {
    indiceActual--;
    if (indiceActual < 0) {
        indiceActual = slides.length - 1;
    }
    mostrarSlide(indiceActual);
}

// Ir a un slide específico
function irASlide(indice) {
    indiceActual = indice;
    mostrarSlide(indiceActual);
}

// Iniciar rotación automática
function iniciarRotacionAutomatica() {
    // Asegurarse de que no hay múltiples intervalos corriendo
    detenerRotacionAutomatica(); 
    intervaloCarrusel = setInterval(slideSiguiente, tiempoRotacion);
}

// Detener rotación automática
function detenerRotacionAutomatica() {
    clearInterval(intervaloCarrusel);
}


// Animación de entrada para las tarjetas de estadísticas
function animarTarjetas() {
    const tarjetas = document.querySelectorAll('.tarjeta-stat');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                // Configurar el estado inicial para la animación
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(30px)';
                entry.target.style.transition = 'all 0.6s ease';

                // Usar setTimeout para un efecto de aparición secuencial (escalonado)
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 150);

                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.2
    });

    tarjetas.forEach(tarjeta => {
        observer.observe(tarjeta);
    });
}

// Contador animado para las estadísticas
function animarContadores() {
    const numeroStats = document.querySelectorAll('.numero-stat');
    let hasAnimated = false;

    // Solo animar la primera vez
    if (document.body.getAttribute('data-stats-animated')) return;
    document.body.setAttribute('data-stats-animated', 'true');


    numeroStats.forEach(stat => {
        // Limpiar el texto para obtener solo números (quitar $, .)
        const textoLimpio = stat.textContent.replace(/[^0-9]/g, ''); 
        const valorFinal = parseInt(textoLimpio);
        
        // Si no es un número válido, saltar la animación (ej: "Top")
        if (isNaN(valorFinal)) return;

        const duracion = 1500; // 1.5 segundos
        const step = 50; // número de pasos
        const incremento = valorFinal / step;
        let valorActual = 0;
        let contadorIteraciones = 0;
        
        const isCurrency = stat.textContent.includes('$');
        const originalText = stat.textContent;

        stat.textContent = isCurrency ? '$0' : '0';

        const timer = setInterval(() => {
            contadorIteraciones++;
            valorActual += incremento;

            if (contadorIteraciones >= step) {
                valorActual = valorFinal;
                clearInterval(timer);
                stat.textContent = originalText; // Usar el texto original para formato final
                return;
            }
            
            // Redondear y formatear (ej: con $)
            let displayValue = Math.floor(valorActual).toLocaleString('es-ES');
            if (isCurrency) {
                displayValue = '$' + displayValue;
            }

            stat.textContent = displayValue;
        }, duracion / step);
    });
}

// Soporte para teclado en el carrusel
document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft') {
        detenerRotacionAutomatica();
        slideAnterior();
        iniciarRotacionAutomatica();
    } else if (e.key === 'ArrowRight') {
        detenerRotacionAutomatica();
        slideSiguiente();
        iniciarRotacionAutomatica();
    }
});

// Soporte para gestos táctiles (swipe)
let touchStartX = 0;
let touchEndX = 0;

const carruselContenedor = document.querySelector('.carrusel-contenedor');

if (carruselContenedor) {
    carruselContenedor.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    });

    carruselContenedor.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        manejarGesto();
    });
}


function manejarGesto() {
    // Si el movimiento es mayor a 50px
    if (Math.abs(touchEndX - touchStartX) > 50) {
        detenerRotacionAutomatica();
        if (touchEndX < touchStartX) {
            // Swipe izquierda (Siguiente)
            slideSiguiente();
        } else {
            // Swipe derecha (Anterior)
            slideAnterior();
        }
        iniciarRotacionAutomatica();
    }
}

// Inicializar todo cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // 1. Inicializar Carrusel
    inicializarCarrusel();
    
    // 2. Inicializar Animaciones de Tarjetas y Contadores
    animarTarjetas();

    // Animar contadores cuando sean visibles
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animarContadores();
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    const seccionStats = document.querySelector('.seccion-stats');
    if (seccionStats) {
        observer.observe(seccionStats);
    }
    
    // 3. Prevenir arrastre de imágenes
    document.querySelectorAll('img').forEach(img => {
        img.addEventListener('dragstart', (e) => e.preventDefault());
    });
});