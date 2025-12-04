/**
 * Funciones para la gestión de equipos
 */

// Abrir el modal de registro
function abrirModal() {
    document.getElementById('modalRegistro').style.display = 'flex';
}

// Cerrar el modal (solo si se hace clic en el fondo)
function cerrarModal(e) {
    if (e.target.id === 'modalRegistro') {
        document.getElementById('modalRegistro').style.display = 'none';
    }
}

// Filtrar equipos según criterio
function filtrar(criterio, btn) {
    // Actualizar botones activos
    document.querySelectorAll('.tab-item').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Filtrar elementos
    const tarjetas = document.querySelectorAll('.tarjeta-item');
    tarjetas.forEach(t => {
        const esMio = t.getAttribute('data-mio') === 'si';

        if (criterio === 'todos') {
            t.style.display = 'block';
        } else if (criterio === 'mios') {
            t.style.display = esMio ? 'block' : 'none';
        } else if (criterio === 'disponibles') {
            t.style.display = 'block'; // Lógica simplificada
        }
    });
}