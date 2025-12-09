<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario - HackZone</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-tablas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/calendario.css') }}">
    <style>
        .eventos-timeline {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .evento-item {
            border-left: 4px solid #667eea;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background: #f8f9fa;
            border-radius: 0 8px 8px 0;
            transition: all 0.3s;
        }

        .evento-item:hover {
            background: #e0e7ff;
            transform: translateX(5px);
        }

        .evento-item.activo {
            border-color: #10b981;
            background: #d1fae5;
        }

        .evento-item.finalizado {
            border-color: #94a3b8;
            background: #f1f5f9;
            opacity: 0.7;
        }

        .evento-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .evento-titulo {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e293b;
        }

        .evento-estado {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .estado-proximo {
            background: #e0f2fe;
            color: #0369a1;
        }

        .estado-activo {
            background: #d1fae5;
            color: #065f46;
        }

        .estado-finalizado {
            background: #f1f5f9;
            color: #475569;
        }

        .evento-detalles {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .detalle-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.9rem;
        }

        .detalle-item i {
            color: #667eea;
        }

        .evento-etapas {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .etapa {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .etapa.completada {
            border-color: #10b981;
            background: #ecfdf5;
            color: #065f46;
        }

        .etapa.activa {
            border-color: #f59e0b;
            background: #fffbeb;
            color: #92400e;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

@include('components.navbar-admin')

<div class="admin-container">
    @include('components.sidebar-admin')

    <main class="admin-main">
        <h2 class="titulo-pagina">
            <i class="fas fa-calendar-alt"></i> Calendario de Eventos
        </h2>
        <p style="color: #64748b; margin-bottom: 2rem;">Gestión y cronograma de todos los eventos del sistema</p>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $eventos->count() }}</div>
                <div class="stat-label">Total de Eventos</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">
                    {{ $eventos->filter(function($e) {
                        return $e->fecha_inicio >= now() && $e->fecha_limite_inscripcion >= now();
                    })->count() }}
                </div>
                <div class="stat-label">Eventos Activos</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">
                    {{ $eventos->filter(function($e) {
                        return $e->fecha_inicio < now();
                    })->count() }}
                </div>
                <div class="stat-label">Eventos Finalizados</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">
                    {{ $eventos->sum('participantes_actuales') }}
                </div>
                <div class="stat-label">Participantes Totales</div>
            </div>
        </div>

        <!-- Calendario Gráfico -->
        <div class="tarjeta-calendario">
            <div class="encabezado-calendario">
                <div class="fecha-grande">
                    <span class="numero-dia" id="encabezado-num-dia">{{ now()->day }}</span>
                    <div class="texto-fecha">
                        <span class="nombre-dia" id="encabezado-nom-dia">{{ now()->locale('es')->isoFormat('ddd') }}</span>
                        <span class="mes-anio" id="encabezado-mes-anio">{{ now()->locale('es')->isoFormat('MMMM YYYY') }}</span>
                    </div>
                </div>
                <div class="subtexto-encabezado">
                    Agenda HackZone - Eventos programados
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

                    <button id="btn-mes-siguiente" class="btn-nav"><i class="fas fa-chevron-right"></i></button>
                </div>
                <button class="btn-hoy" id="btn-hoy">Hoy</button>
            </div>

            <div class="grid-encabezado-dias">
                <div>Do</div><div>Lu</div><div>Ma</div><div>Mi</div><div>Ju</div><div>Vi</div><div>Sa</div>
            </div>
            <div id="contenedor-dias" class="grid-dias"></div>
        </div>

        <!-- Tooltip flotante para eventos -->
        <div id="evento-tooltip" class="evento-tooltip" style="display: none;">
            <div class="tooltip-header">
                <div class="tooltip-icono">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="tooltip-titulo" id="tooltip-titulo"></div>
            </div>
            <div class="tooltip-body">
                <div class="tooltip-item" id="tooltip-fecha-inicio">
                    <i class="fas fa-calendar-day"></i>
                    <span></span>
                </div>
                <div class="tooltip-item" id="tooltip-fecha-limite">
                    <i class="fas fa-calendar-times"></i>
                    <span></span>
                </div>
                <div class="tooltip-item" id="tooltip-ubicacion">
                    <i class="fas fa-map-marker-alt"></i>
                    <span></span>
                </div>
                <div class="tooltip-item" id="tooltip-participantes">
                    <i class="fas fa-users"></i>
                    <span></span>
                </div>
                <div class="tooltip-item" id="tooltip-organizacion" style="display: none;">
                    <i class="fas fa-building"></i>
                    <span></span>
                </div>
            </div>
            <div class="tooltip-footer">
                <a href="#" id="tooltip-link-editar" class="tooltip-link">
                    <i class="fas fa-edit"></i> Editar
                </a>
            </div>
        </div>

        <!-- Timeline de eventos -->
        <div class="eventos-timeline">
            <h3 style="color: #1e293b; margin-bottom: 1.5rem; font-size: 1.5rem;">
                <i class="fas fa-stream"></i> Timeline de Eventos
            </h3>

            @if($eventos->isEmpty())
                <div style="text-align: center; padding: 3rem; color: #64748b;">
                    <i class="fas fa-calendar-times" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                    <h3>No hay eventos programados</h3>
                    <p>Crea tu primer evento para comenzar.</p>
                </div>
            @else
                @foreach($eventos->sortBy('fecha_inicio') as $evento)
                    @php
                        $hoy = now();
                        // Determinar si el evento ya pasó (fecha_inicio es anterior a hoy)
                        $esPasado = $evento->fecha_inicio && $evento->fecha_inicio->isPast();
                        // Determinar si el evento está activo (ya comenzó pero aún acepta inscripciones)
                        $esActivo = $evento->fecha_inicio && $evento->fecha_inicio <= $hoy && $evento->fecha_limite_inscripcion && $evento->fecha_limite_inscripcion >= $hoy;
                        // Determinar si el evento es próximo (aún no ha comenzado)
                        $esProximo = $evento->fecha_inicio && $evento->fecha_inicio->isFuture();

                        $inscripcionAbierta = $evento->fecha_limite_inscripcion && $evento->fecha_limite_inscripcion >= $hoy;
                        // Usar copy() para evitar modificar la fecha original
                        $fechaInicioCopy = $evento->fecha_inicio ? $evento->fecha_inicio->copy() : null;
                        $enProgreso = $fechaInicioCopy && $fechaInicioCopy <= $hoy && $fechaInicioCopy->addDays(7) >= $hoy;
                    @endphp

                    <div class="evento-item {{ $esActivo ? 'activo' : ($esPasado ? 'finalizado' : '') }}">
                        <div class="evento-header">
                            <div class="evento-titulo">
                                <i class="fas fa-trophy"></i> {{ $evento->titulo }}
                            </div>
                            <span class="evento-estado estado-{{ $esActivo ? 'activo' : ($esPasado ? 'finalizado' : 'proximo') }}">
                                @if($esActivo)
                                    <i class="fas fa-circle" style="font-size: 0.5rem;"></i> Activo
                                @elseif($esPasado)
                                    <i class="fas fa-check"></i> Finalizado
                                @else
                                    <i class="fas fa-clock"></i> Próximo
                                @endif
                            </span>
                        </div>

                        <div class="evento-detalles">
                            <div class="detalle-item">
                                <i class="fas fa-calendar-day"></i>
                                <span><strong>Inicio:</strong> {{ $evento->fecha_inicio ? $evento->fecha_inicio->format('d/m/Y') : 'No definido' }}</span>
                            </div>
                            <div class="detalle-item">
                                <i class="fas fa-calendar-times"></i>
                                <span><strong>Inscripción hasta:</strong> {{ $evento->fecha_limite_inscripcion ? $evento->fecha_limite_inscripcion->format('d/m/Y') : 'No definido' }}</span>
                            </div>
                            <div class="detalle-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $evento->ubicacion ?? 'No especificado' }}</span>
                            </div>
                            <div class="detalle-item">
                                <i class="fas fa-users"></i>
                                <span>{{ $evento->participantes_actuales }}/{{ $evento->participantes_max }} participantes</span>
                            </div>
                        </div>

                        <div class="evento-etapas">
                            <div class="etapa {{ $inscripcionAbierta ? 'activa' : 'completada' }}">
                                <i class="fas {{ $inscripcionAbierta ? 'fa-hourglass-half' : 'fa-check' }}"></i>
                                <span>Inscripciones {{ $inscripcionAbierta ? 'Abiertas' : 'Cerradas' }}</span>
                            </div>
                            <div class="etapa {{ $enProgreso ? 'activa' : '' }}">
                                <i class="fas fa-code"></i>
                                <span>Desarrollo</span>
                            </div>
                            <div class="etapa {{ $esPasado ? 'completada' : '' }}">
                                <i class="fas fa-gavel"></i>
                                <span>Evaluación</span>
                            </div>
                            <div class="etapa {{ $esPasado ? 'completada' : '' }}">
                                <i class="fas fa-trophy"></i>
                                <span>Premiación</span>
                            </div>
                        </div>

                        <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                            <a href="{{ route('admin.eventos.edit', $evento->id) }}" style="background: #667eea; color: white; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-size: 0.85rem;">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <a href="{{ route('admin.eventos.ranking', $evento->id) }}" style="background: #FFD700; color: white; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-size: 0.85rem;">
                                <i class="fas fa-trophy"></i> Ver Ranking
                            </a>
                        </div>
                    </div>
                @endforeach
            @endif
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
    // Preparar eventos con información completa para el tooltip
    @php
        $eventosFormateados = $eventos->map(function($evento) {
            return [
                'id' => $evento->id,
                'titulo' => $evento->titulo,
                'descripcion_corta' => $evento->descripcion_corta ?? '',
                'fecha_inicio' => $evento->fecha_inicio ? $evento->fecha_inicio->format('Y-m-d') : null,
                'fecha_inicio_formateada' => $evento->fecha_inicio ? $evento->fecha_inicio->format('d/m/Y') : 'No definido',
                'fecha_limite_inscripcion' => $evento->fecha_limite_inscripcion ? $evento->fecha_limite_inscripcion->format('Y-m-d') : null,
                'fecha_limite_formateada' => $evento->fecha_limite_inscripcion ? $evento->fecha_limite_inscripcion->format('d/m/Y') : 'No definido',
                'ubicacion' => $evento->ubicacion ?? 'No especificado',
                'participantes_actuales' => $evento->participantes_actuales ?? 0,
                'participantes_max' => $evento->participantes_max ?? 0,
                'organizacion' => $evento->organizacion ?? '',
                'es_pasado' => $evento->fecha_inicio ? $evento->fecha_inicio->isPast() : false,
            ];
        });
    @endphp
    const eventosBD = @json($eventosFormateados);
</script>

<script src="{{ asset('js/calendario.js') }}"></script>

</body>
</html>
