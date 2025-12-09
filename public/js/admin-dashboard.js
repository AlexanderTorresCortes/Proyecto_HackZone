// Toggle Sidebar
document.getElementById('sidebarToggle').addEventListener('click', function() {
    document.querySelector('.admin-sidebar').classList.toggle('collapsed');
    this.querySelector('i').classList.toggle('fa-chevron-left');
    this.querySelector('i').classList.toggle('fa-chevron-right');
});

// Confirmar Backup
function confirmarBackup() {
    if(confirm('¿Deseas crear un backup de la base de datos?')) {
        alert('Iniciando backup...');
        window.location.href = document.querySelector('[data-backup-url]').getAttribute('data-backup-url');
    }
}

// Generar Reporte
function generarReporte() {
    if(confirm('¿Generar reporte del mes actual?')) {
        window.location.href = document.querySelector('[data-reporte-url]').getAttribute('data-reporte-url');
    }
}

// Actualizar números en tiempo real (simulado)
setInterval(function() {
    // Aquí podrías hacer peticiones AJAX para actualizar los números
}, 30000); // Cada 30 segundos

// Auto-ocultar alertas después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});
