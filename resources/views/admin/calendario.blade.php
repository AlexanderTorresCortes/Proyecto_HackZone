<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario - HackZone</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/calendario.css') }}">
</head>
<body>

<div class="admin-container">
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <button class="btn-toggle" id="sidebarToggle">
                <i class="fas fa-chevron-left"></i>
            </button>
            <h2>Panel</h2>
        </div>
        
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-item">
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
            <a href="{{ route('admin.calendario') }}" class="nav-item highlight">
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
            <a href="{{ route('admin.equipos.index') }}" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Equipos</span>
            </a>
            <a href="{{ route('admin.eventos.create') }}" class="nav-item">
                <i class="fas fa-plus-circle"></i>
                <span>Crear Evento</span>
            </a>
        </nav>
    </aside>
    
    <main class="admin-main">
        <h2 class="titulo-pagina" style="color: #4a148c; margin-bottom: 20px;">Calendario</h2>

        <div class="tarjeta-calendario">
            <div class="encabezado-calendario">
                <div class="encabezado-izq">
                    <div class="fecha-grande">
                        <span class="numero-dia" id="encabezado-num-dia">31</span>
                        <div class="texto-fecha">
                            <span class="nombre-dia" id="encabezado-nom-dia">Dom</span>
                            <span class="mes-anio" id="encabezado-mes-anio">Agosto 2025</span>
                        </div>
                    </div>
                    <div class="subtexto-encabezado">
                        <span>Agenda HackZone</span>
                        <small>Explora las maravillas de cada día</small>
                    </div>
                </div>
                <div class="ilustracion-cal">
                    <i class="fas fa-calendar-day" style="font-size: 3rem; color: #5d9cec; opacity: 0.5;"></i>
                </div>
            </div>

            <div class="controles-calendario">
                <div class="controles-izq">
                    <button id="btn-mes-anterior" class="btn-nav"><i class="fas fa-chevron-left"></i></button>
                    
                    <select id="selector-mes" class="input-seleccion">
                        <option value="0">Enero</option>
                        <option value="1">Febrero</option>
                        <option value="2">Marzo</option>
                        <option value="3">Abril</option>
                        <option value="4">Mayo</option>
                        <option value="5">Junio</option>
                        <option value="6">Julio</option>
                        <option value="7">Agosto</option>
                        <option value="8">Septiembre</option>
                        <option value="9">Octubre</option>
                        <option value="10">Noviembre</option>
                        <option value="11">Diciembre</option>
                    </select>

                    <select id="selector-anio" class="input-seleccion">
                        <option value="2024">2024</option>
                        <option value="2025" selected>2025</option>
                        <option value="2026">2026</option>
                    </select>

                    <span class="etiqueta-festivo">Festivo</span>
                    
                    <button id="btn-mes-siguiente" class="btn-nav"><i class="fas fa-chevron-right"></i></button>
                </div>
                <button class="btn-hoy" id="btn-hoy">Hoy</button>
            </div>

            <div class="grid-encabezado-dias">
                <div>Do</div><div>Lu</div><div>Ma</div><div>Mi</div><div>Ju</div><div>Vi</div><div>Sa</div>
            </div>
            <div id="contenedor-dias" class="grid-dias">
                </div>
        </div>
    </main>
</div>

<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.querySelector('.admin-sidebar').classList.toggle('collapsed');
        this.querySelector('i').classList.toggle('fa-chevron-left');
        this.querySelector('i').classList.toggle('fa-chevron-right');
    });
</script>

<script>
    const eventosBD = @json($eventos);
</script>

<script src="{{ asset('js/calendario.js') }}"></script>

</body>
</html>