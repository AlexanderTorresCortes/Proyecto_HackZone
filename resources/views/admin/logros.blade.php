<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Logros - HackZone</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-tablas.css') }}">
</head>
<body>

@include('components.navbar-admin')

<div class="admin-container">
    @include('components.sidebar-admin')

    <main class="admin-main">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2 class="titulo-pagina">Gestión de Badges y Logros</h2>
            <button class="btn-guardar" onclick="mostrarModalNuevoLogro()">
                <i class="fas fa-plus-circle"></i> Agregar Badge
            </button>
        </div>

        <p style="color: #666; margin-bottom: 2rem;">Administra los badges y logros de tu plataforma de torneos de programación</p>

        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Grid de Badges -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
            <!-- Badge 1 -->
            <div class="badge-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: 2px solid #e5e7eb;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                        🏆
                    </div>
                    <span style="background: #10b981; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                        Competencia
                    </span>
                </div>

                <h3 style="color: #1e293b; font-size: 1.1rem; margin-bottom: 0.5rem;">Primer Lugar</h3>
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1rem;">Otorgado al ganador del torneo</p>

                <div style="background: #f8f9fa; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem;">
                    <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.25rem;">Condición:</div>
                    <div style="color: #1e293b; font-weight: 500;">Ganar un torneo</div>
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <button class="btn-accion editar" style="flex: 1;">
                        <i class="fas fa-edit"></i> Editar Perfil
                    </button>
                    <button class="btn-accion eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Badge 2 -->
            <div class="badge-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: 2px solid #e5e7eb;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                        🎯
                    </div>
                    <span style="background: #10b981; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                        Competencia
                    </span>
                </div>

                <h3 style="color: #1e293b; font-size: 1.1rem; margin-bottom: 0.5rem;">Participante Activo</h3>
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1rem;">Para programadores comprometidos</p>

                <div style="background: #f8f9fa; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem;">
                    <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.25rem;">Condición:</div>
                    <div style="color: #1e293b; font-weight: 500;">Participar en 5 eventos</div>
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <button class="btn-accion editar" style="flex: 1;">
                        <i class="fas fa-edit"></i> Editar Perfil
                    </button>
                    <button class="btn-accion eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Placeholder para nuevo badge -->
            <div onclick="mostrarModalNuevoLogro()" style="background: #f8f9fa; border-radius: 12px; padding: 3rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 2px dashed #cbd5e1; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s; min-height: 300px;">
                <i class="fas fa-plus-circle" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                <div style="color: #64748b; font-weight: 500;">Crear Nuevo Badge</div>
            </div>
        </div>

    </main>
</div>

<!-- Modal Nuevo Logro -->
<div id="modalNuevoLogro" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; max-width: 500px; width: 90%; padding: 2rem;">
        <h3 style="margin-bottom: 1.5rem; color: #1e293b;">Crear Nuevo Badge</h3>

        <form>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: #374151; font-weight: 500;">Nombre del Badge</label>
                <input type="text" class="form-control" placeholder="Ej: Maestro del Código" required>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: #374151; font-weight: 500;">Descripción</label>
                <textarea class="form-control" rows="3" placeholder="Describe qué representa este badge" required></textarea>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: #374151; font-weight: 500;">Emoji/Icono</label>
                <input type="text" class="form-control" placeholder="🏆" required>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: #374151; font-weight: 500;">Condición para obtenerlo</label>
                <input type="text" class="form-control" placeholder="Ej: Ganar 3 torneos" required>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="button" class="btn-cancelar" onclick="cerrarModalNuevoLogro()" style="flex: 1;">Cancelar</button>
                <button type="submit" class="btn-guardar" style="flex: 1;">Crear Badge</button>
            </div>
        </form>
    </div>
</div>

<script>
function mostrarModalNuevoLogro() {
    document.getElementById('modalNuevoLogro').style.display = 'flex';
}

function cerrarModalNuevoLogro() {
    document.getElementById('modalNuevoLogro').style.display = 'none';
}

// Cerrar modal al hacer clic fuera
document.getElementById('modalNuevoLogro')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalNuevoLogro();
    }
});
</script>

</body>
</html>
