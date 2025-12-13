function mostrarModalInscripcion() {
    const modal = document.getElementById('modalInscripcion');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Prevenir scroll del body
    } else {
        console.error('Modal de inscripción no encontrado');
    }
}

function cerrarModalInscripcion() {
    const modal = document.getElementById('modalInscripcion');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = ''; // Restaurar scroll del body
    }
}

// Cerrar modal al hacer clic fuera
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalInscripcion');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModalInscripcion();
            }
        });
    }
});

// Cerrar modal con la tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalInscripcion();
    }
});
