<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - HackZone</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS del Dashboard -->
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
</head>
<body>

<!-- NAVBAR ADMIN -->
@include('components.navbar')

<div class="admin-container">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <button class="btn-toggle" id="sidebarToggle">
                <i class="fas fa-chevron-left"></i>
            </button>
            <h2>Panel</h2>
        </div>
        
        <nav class="sidebar-nav">
            <a href="#" class="nav-item">
                <i class="fas fa-chart-line"></i>
                <span>Reportes</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-star"></i>
                <span>Evaluaciones</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Carga de archivos</span>
            </a>
           <a href="{{ route('admin.calendario') }}" class="nav-item">
               <i class="far fa-calendar-alt"></i>
               <span>Calendario</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-trophy"></i>
                <span>Sistema de logros</span>
            </a>
            <a href="{{ route('admin.usuarios.index') }}" class="nav-item">
                <i class="fas fa-user-cog"></i>
                <span>Usuarios</span>
            </a>
            <a href="{{ route('admin.equipos.index') }}" class="nav-item {{ request()->routeIs('admin.equipos.index') ? 'highlight' : '' }}">
               <i class="fas fa-users"></i>
                <span>Equipos</span>
            </a>
            <a href="{{ route('admin.eventos.create') }}" class="nav-item highlight">
                <i class="fas fa-plus-circle"></i>
                <span>Crear Evento</span>
            </a>
        </nav>
    </aside>
    
    <!-- Contenido Principal -->
    <main class="admin-main">
        
        <!-- MENSAJE DE ÉXITO (NUEVO) -->
        @if(session('success'))
        <div class="alert alert-success" style="background: #d1fae5; color: #065f46; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem; border: 1px solid #10b981; animation: slideDown 0.3s ease;">
            <i class="fas fa-check-circle" style="font-size: 1.25rem;"></i>
            <span style="font-weight: 600;">{{ session('success') }}</span>
        </div>
        @endif
        
        @if(session('error'))
        <div class="alert alert-error" style="background: #fee2e2; color: #991b1b; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem; border: 1px solid #ef4444; animation: slideDown 0.3s ease;">
            <i class="fas fa-exclamation-circle" style="font-size: 1.25rem;"></i>
            <span style="font-weight: 600;">{{ session('error') }}</span>
        </div>
        @endif
        <!-- FIN MENSAJES -->
        
        <!-- Header del Dashboard -->
        <div class="dashboard-header">
            <div class="header-content">
                <i class="fas fa-shield-alt header-icon"></i>
                <div>
                    <h1>Panel de Administrador</h1>
                    <p>Gestiona y supervisa toda la plataforma HackZone</p>
                </div>
            </div>
            <div class="sistema-status activo">
                <i class="fas fa-circle"></i>
                Sistema Activo
            </div>
        </div>
        
        <!-- Tarjetas de Estadísticas -->
        <div class="stats-cards">
            <div class="stat-card usuarios">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Usuarios Totales</div>
                    <div class="stat-number">{{ $totalUsuarios }}</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        +12% desde el mes pasado
                    </div>
                </div>
            </div>
            
            <div class="stat-card equipos">
                <div class="stat-icon">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Equipos Activos</div>
                    <div class="stat-number">{{ $equiposActivos }}</div>
                    <div class="stat-change neutral">
                        En competencias actuales
                    </div>
                </div>
            </div>
            
            <div class="stat-card eventos">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Eventos Programados</div>
                    <div class="stat-number">{{ $eventosProgramados }}</div>
                    <div class="stat-change">
                        Próximos 30 días
                    </div>
                </div>
            </div>
            
            <div class="stat-card alertas">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Alertas Pendientes</div>
                    <div class="stat-number">{{ $alertasPendientes }}</div>
                    <div class="stat-change negative">
                        Requieren atención
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contenido en Dos Columnas -->
        <div class="dashboard-content">
            <!-- Columna Izquierda: Acciones Rápidas -->
            <div class="content-card acciones-rapidas">
                <div class="card-header">
                    <i class="fas fa-bolt"></i>
                    <h3>Acciones Rápidas de Administrador</h3>
                </div>
                <p class="card-subtitle">Herramientas de gestión más utilizadas</p>
                
                <div class="acciones-grid">
                    <button class="accion-btn" onclick="window.location.href='{{ route('admin.usuarios.aprobar') }}'">
                        <i class="fas fa-user-check"></i>
                        <span>Aprobar Nuevos Usuarios</span>
                    </button>
                    
                    <button class="accion-btn" onclick="confirmarBackup()">
                        <i class="fas fa-database"></i>
                        <span>Backup de Base de Datos</span>
                    </button>
                    
                    <button class="accion-btn" onclick="window.location.href='{{ route('admin.permisos') }}'">
                        <i class="fas fa-lock"></i>
                        <span>Gestionar Permisos</span>
                    </button>
                    
                    <button class="accion-btn" onclick="generarReporte()">
                        <i class="fas fa-chart-bar"></i>
                        <span>Generar Reporte Mensual</span>
                    </button>
                </div>
            </div>
            
            <!-- Columna Derecha: Alertas del Sistema -->
            <div class="content-card alertas-sistema">
                <div class="card-header">
                    <i class="fas fa-bell"></i>
                    <h3>Alertas del Sistema</h3>
                </div>
                <p class="card-subtitle">Notificaciones que requieren tu atención</p>
                
                <div class="alertas-list">
                    <!-- Alerta Crítica -->
                    <div class="alerta-item critica">
                        <div class="alerta-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="alerta-content">
                            <h4>Servidor con alta carga</h4>
                            <p>CPU al 85% - Revisar procesos</p>
                        </div>
                    </div>
                    
                    <!-- Alerta Importante -->
                    <div class="alerta-item importante">
                        <div class="alerta-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="alerta-content">
                            <h4>Usuarios pendientes de aprobación</h4>
                            <p>12 solicitudes esperando revisión</p>
                        </div>
                    </div>
                    
                    <!-- Alerta Informativa -->
                    <div class="alerta-item info">
                        <div class="alerta-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="alerta-content">
                            <h4>Actualización disponible</h4>
                            <p>Nueva versión del sistema lista</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabla de Actividad Reciente -->
        <div class="content-card actividad-reciente">
            <div class="card-header">
                <i class="fas fa-history"></i>
                <h3>Actividad Reciente</h3>
            </div>
            
            <div class="tabla-container">
                <table class="tabla-actividad">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($actividadReciente as $actividad)
                        <tr>
                            <td>
                                <div class="usuario-cell">
                                    <div class="usuario-avatar">
                                        {{ substr($actividad->usuario->name, 0, 1) }}
                                    </div>
                                    <span>{{ $actividad->usuario->name }}</span>
                                </div>
                            </td>
                            <td>{{ $actividad->descripcion }}</td>
                            <td>{{ $actividad->created_at->diffForHumans() }}</td>
                            <td>
                                <span class="badge-status {{ $actividad->estado }}">
                                    {{ ucfirst($actividad->estado) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<style>
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
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
        window.location.href = '{{ route("admin.backup") }}';
    }
}

// Generar Reporte
function generarReporte() {
    if(confirm('¿Generar reporte del mes actual?')) {
        window.location.href = '{{ route("admin.reportes.generar") }}';
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
</script>

</body>
</html>