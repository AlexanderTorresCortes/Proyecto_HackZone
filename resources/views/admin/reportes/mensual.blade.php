<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Mensual - HackZone</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
</head>
<body>

@include('components.navbar-admin')

<div class="admin-container">
    @include('components.sidebar-admin')

    <main class="admin-main">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h2 class="titulo-pagina">
                    <i class="fas fa-chart-line"></i> Reporte Mensual
                </h2>
                <p style="color: #64748b;">{{ $datos['mes'] }}</p>
            </div>
            <button onclick="window.print()" style="background: #667eea; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-print"></i>
                Imprimir Reporte
            </button>
        </div>

        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">

                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem; border-radius: 12px; color: white;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-user-plus" style="font-size: 2rem; opacity: 0.8;"></i>
                        <div>
                            <div style="font-size: 2rem; font-weight: 700;">{{ $datos['nuevosUsuarios'] }}</div>
                            <div style="opacity: 0.9; font-size: 0.9rem;">Nuevos Usuarios</div>
                        </div>
                    </div>
                </div>

                <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 1.5rem; border-radius: 12px; color: white;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-users" style="font-size: 2rem; opacity: 0.8;"></i>
                        <div>
                            <div style="font-size: 2rem; font-weight: 700;">{{ $datos['nuevosEquipos'] }}</div>
                            <div style="opacity: 0.9; font-size: 0.9rem;">Nuevos Equipos</div>
                        </div>
                    </div>
                </div>

                <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 1.5rem; border-radius: 12px; color: white;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-calendar-check" style="font-size: 2rem; opacity: 0.8;"></i>
                        <div>
                            <div style="font-size: 2rem; font-weight: 700;">{{ $datos['eventosRealizados'] }}</div>
                            <div style="opacity: 0.9; font-size: 0.9rem;">Eventos Realizados</div>
                        </div>
                    </div>
                </div>

            </div>

            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 2rem 0;">

            <div>
                <h3 style="color: #1e293b; margin-bottom: 1rem;">
                    <i class="fas fa-info-circle"></i> Resumen del Periodo
                </h3>
                <p style="color: #64748b; line-height: 1.6;">
                    Durante el mes de <strong>{{ $datos['mes'] }}</strong>, la plataforma HackZone ha experimentado
                    un crecimiento significativo con <strong>{{ $datos['nuevosUsuarios'] }} nuevos usuarios</strong> registrados,
                    <strong>{{ $datos['nuevosEquipos'] }} equipos creados</strong> y
                    <strong>{{ $datos['eventosRealizados'] }} eventos realizados</strong>.
                </p>
            </div>

            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 2rem 0;">

            <div style="text-align: center; color: #94a3b8; font-size: 0.85rem;">
                <p>Reporte generado el {{ now()->format('d/m/Y H:i:s') }}</p>
                <p>HackZone - Sistema de Gestión de Eventos</p>
            </div>

        </div>
    </main>
</div>

<script src="{{ asset('js/admin-dashboard.js') }}"></script>

<style>
@media print {
    .admin-sidebar, .admin-navbar, button {
        display: none !important;
    }

    .admin-main {
        margin: 0 !important;
        padding: 0 !important;
    }
}
</style>

</body>
</html>
