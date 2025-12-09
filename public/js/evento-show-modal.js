function mostrarModalInscripcion() {
    document.getElementById('modalInscripcion').style.display = 'flex';
}

function cerrarModalInscripcion() {
    document.getElementById('modalInscripcion').style.display = 'none';
}

// Cerrar modal al hacer clic fuera
document.getElementById('modalInscripcion')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalInscripcion();
    }
});
