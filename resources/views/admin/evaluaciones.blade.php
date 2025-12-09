<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluaciones - HackZone</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-tablas.css') }}">
    <style>
        .evento-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .evento-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-box {
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
        }

        .stat-box.completadas {
            background: linear-gradient(135deg, #10B981, #059669);
            color: white;
        }

        .stat-box.en-proceso {
            background: linear-gradient(135deg, #F59E0B, #D97706);
            color: white;
        }

        .stat-box.pendientes {
            background: linear-gradient(135deg, #EF4444, #DC2626);
            color: white;
        }

        .stat-box.progreso {
            background: linear-gradient(135deg, #6366F1, #8B5CF6);
            color: white;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(255,255,255,0.3);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .progress-fill {
            height: 100%;
            background: white;
            transition: width 0.5s ease;
        }

        .btn-ranking {
            background: linear-gradient(135deg, #FFD700, #FFA500);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-ranking:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 215, 0, 0.4);
        }
    </style>
</head>
<body>

    @include('components.navbar-admin')

    <div class="admin-container">
        @include('components.sidebar-admin')

        <main class="admin-main">
            <h2 class="titulo-pagina">
                <i class="fas fa-chart-bar"></i> Gestión de Evaluaciones
            </h2>
            <p style="color: #64748b; margin-bottom: 2rem;">Monitoreo del progreso de evaluaciones por evento</p>

            @if($eventosConEstadisticas->isEmpty())
                <div style="text-align: center; padding: 4rem; background: white; border-radius: 12px;">
                    <i class="fas fa-clipboard-list" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                    <h3 style="color: #64748b;">No hay eventos con evaluaciones</h3>
                    <p style="color: #94a3b8;">Crea eventos y asigna jueces para comenzar.</p>
                </div>
            @else
                @foreach($eventosConEstadisticas as $evento)
                    <div class="evento-card">
                        <div class="evento-header">
                            <div>
                                <h3 style="color: #1e293b; margin-bottom: 0.5rem;">
                                    <i class="fas fa-calendar-alt" style="color: #667eea;"></i>
                                    {{ $evento->titulo }}
                                </h3>
                                <div style="color: #64748b; font-size: 0.9rem;">
                                    <i class="fas fa-map-marker-alt"></i> {{ $evento->ubicacion }} •
                                    <i class="fas fa-calendar"></i> {{ $evento->fecha_inicio->format('d/m/Y') }}
                                </div>
                            </div>
                            <a href="{{ route('admin.eventos.ranking', $evento->id) }}" class="btn-ranking">
                                <i class="fas fa-trophy"></i> Ver Ranking
                            </a>
                        </div>

                        <div class="stats-grid">
                            <div class="stat-box completadas">
                                <div class="stat-value">{{ $evento->estadisticas['completadas'] }}</div>
                                <div class="stat-label">
                                    <i class="fas fa-check-circle"></i> Completadas
                                </div>
                            </div>
                            <div class="stat-box en-proceso">
                                <div class="stat-value">{{ $evento->estadisticas['en_proceso'] }}</div>
                                <div class="stat-label">
                                    <i class="fas fa-clock"></i> En Proceso
                                </div>
                            </div>
                            <div class="stat-box pendientes">
                                <div class="stat-value">{{ $evento->estadisticas['pendientes'] }}</div>
                                <div class="stat-label">
                                    <i class="fas fa-exclamation-circle"></i> Pendientes
                                </div>
                            </div>
                            <div class="stat-box progreso">
                                <div class="stat-value">{{ $evento->estadisticas['porcentaje_completado'] }}%</div>
                                <div class="stat-label">
                                    <i class="fas fa-chart-line"></i> Progreso Total
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $evento->estadisticas['porcentaje_completado'] }}%;"></div>
                                </div>
                            </div>
                        </div>

                        <div style="background: #f8f9fa; border-radius: 8px; padding: 1rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 0.9rem;">
                            <div>
                                <strong style="color: #4a148c;">
                                    <i class="fas fa-users"></i> {{ $evento->estadisticas['total_equipos'] }}
                                </strong>
                                <span style="color: #64748b;"> Equipos Participantes</span>
                            </div>
                            <div>
                                <strong style="color: #4a148c;">
                                    <i class="fas fa-gavel"></i> {{ $evento->estadisticas['total_jueces'] }}
                                </strong>
                                <span style="color: #64748b;"> Jueces Asignados</span>
                            </div>
                            <div>
                                <strong style="color: #4a148c;">
                                    <i class="fas fa-clipboard-check"></i> {{ $evento->estadisticas['evaluaciones_esperadas'] }}
                                </strong>
                                <span style="color: #64748b;"> Evaluaciones Esperadas</span>
                            </div>
                        </div>

                        @if($evento->evaluaciones->isNotEmpty())
                            <div style="margin-top: 1.5rem;">
                                <h4 style="color: #1e293b; margin-bottom: 1rem;">
                                    <i class="fas fa-list"></i> Evaluaciones Recientes
                                </h4>
                                <table class="tabla-gestion">
                                    <thead>
                                        <tr>
                                            <th>Equipo</th>
                                            <th>Juez</th>
                                            <th>Estado</th>
                                            <th>Calificación</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($evento->evaluaciones->take(5) as $evaluacion)
                                            <tr>
                                                <td>
                                                    <strong style="color: #4a148c;">
                                                        <i class="fas fa-users"></i> {{ $evaluacion->equipo->nombre }}
                                                    </strong>
                                                </td>
                                                <td>{{ $evaluacion->juez->name }}</td>
                                                <td>
                                                    @if($evaluacion->estado == 'completada')
                                                        <span style="background: #d1fae5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.85rem;">
                                                            <i class="fas fa-check-circle"></i> Completada
                                                        </span>
                                                    @else
                                                        <span style="background: #fef3c7; color: #92400e; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.85rem;">
                                                            <i class="fas fa-clock"></i> En Proceso
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($evaluacion->estado == 'completada')
                                                        <strong style="font-size: 1.1rem; color: #1e293b;">
                                                            {{ $evaluacion->calcularPromedio() }}/10
                                                        </strong>
                                                    @else
                                                        <span style="color: #94a3b8; font-style: italic;">Sin calificar</span>
                                                    @endif
                                                </td>
                                                <td style="color: #64748b;">
                                                    {{ $evaluacion->updated_at->format('d/m/Y H:i') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if($evento->evaluaciones->count() > 5)
                                    <p style="text-align: center; color: #64748b; margin-top: 1rem; font-size: 0.9rem;">
                                        Mostrando 5 de {{ $evento->evaluaciones->count() }} evaluaciones totales
                                    </p>
                                @endif
                            </div>
                        @else
                            <div style="text-align: center; padding: 2rem; color: #64748b; margin-top: 1rem; background: #f8f9fa; border-radius: 8px;">
                                <i class="fas fa-inbox" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 0.5rem;"></i>
                                <p>No hay evaluaciones registradas aún para este evento.</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </main>
    </div>

</body>
</html>
