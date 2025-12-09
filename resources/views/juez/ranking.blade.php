<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking - {{ $evento->titulo }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; }

        .navbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .container { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }

        .header-section {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .podium-container {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 2rem;
            margin: 3rem 0;
            padding: 2rem;
        }

        .podium-place {
            text-align: center;
            transition: all 0.3s;
        }

        .podium-place:hover {
            transform: translateY(-10px);
        }

        .podium-medal {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .podium-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 1.5rem;
            color: white;
            min-width: 200px;
        }

        .first-place .podium-box {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            height: 250px;
        }

        .second-place .podium-box {
            background: linear-gradient(135deg, #C0C0C0 0%, #A8A8A8 100%);
            height: 200px;
        }

        .third-place .podium-box {
            background: linear-gradient(135deg, #CD7F32 0%, #B8860B 100%);
            height: 150px;
        }

        .podium-rank {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .podium-team {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .podium-score {
            font-size: 1.5rem;
            font-weight: 700;
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

        .ranking-table {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        tr:hover {
            background: #f8fafc;
        }

        .rank-position {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .rank-1 {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: white;
        }

        .rank-2 {
            background: linear-gradient(135deg, #C0C0C0 0%, #A8A8A8 100%);
            color: white;
        }

        .rank-3 {
            background: linear-gradient(135deg, #CD7F32 0%, #B8860B 100%);
            color: white;
        }

        .rank-other {
            background: #f1f5f9;
            color: #64748b;
        }

        .progress-bar-custom {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.5s ease;
        }

        .btn-back {
            background: #667eea;
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

        .btn-back:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .info-banner {
            background: #e0f2fe;
            border-left: 4px solid #0284c7;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .info-banner h3 {
            color: #0369a1;
            margin-bottom: 0.5rem;
        }

        .info-banner p {
            color: #075985;
            margin: 0;
        }
    </style>
</head>
<body>

@include('components.navbar')

<div class="container">
    <div class="header-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div>
                <h1 style="color: #1e293b; font-size: 1.8rem; margin-bottom: 0.5rem;">
                    <i class="fas fa-trophy"></i> Ranking - {{ $evento->titulo }}
                </h1>
                <p style="color: #64748b;">Clasificación basada en evaluaciones de todos los jueces</p>
            </div>
            <a href="{{ route('juez.equipos', $evento->id) }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <div class="info-banner">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <i class="fas fa-info-circle" style="font-size: 1.5rem; color: #0369a1;"></i>
                <div>
                    <h3 style="font-size: 1rem; margin: 0 0 0.25rem 0;">Vista de Solo Lectura</h3>
                    <p style="font-size: 0.9rem;">Este ranking se calcula automáticamente basado en las evaluaciones de todos los jueces asignados al evento.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $estadisticas['total_equipos'] }}</div>
            <div class="stat-label">Equipos Participantes</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $estadisticas['total_jueces'] }}</div>
            <div class="stat-label">Jueces Asignados</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $estadisticas['completadas'] }}/{{ $estadisticas['evaluaciones_esperadas'] }}</div>
            <div class="stat-label">Evaluaciones Completadas</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $estadisticas['porcentaje_completado'] }}%</div>
            <div class="stat-label">Progreso Total</div>
            <div class="progress-bar-custom">
                <div class="progress-fill" style="width: {{ $estadisticas['porcentaje_completado'] }}%;"></div>
            </div>
        </div>
    </div>

    <!-- Podium - Primeros 3 lugares -->
    @if(count($primerosLugares) > 0)
        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <h3 style="text-align: center; color: #1e293b; margin-bottom: 2rem; font-size: 1.5rem;">
                <i class="fas fa-crown" style="color: #FFD700;"></i> Primeros Lugares
            </h3>

            <div class="podium-container">
                @if(isset($primerosLugares[1]))
                    <div class="podium-place second-place">
                        <div class="podium-medal">🥈</div>
                        <div class="podium-box">
                            <div class="podium-rank">2°</div>
                            <div class="podium-team">{{ $primerosLugares[1]['equipo']->nombre }}</div>
                            <div class="podium-score">{{ $primerosLugares[1]['promedio'] }}/10</div>
                            <p style="font-size: 0.85rem; margin-top: 0.5rem; opacity: 0.9;">
                                {{ $primerosLugares[1]['evaluaciones_recibidas'] }} evaluaciones
                            </p>
                        </div>
                    </div>
                @endif

                @if(isset($primerosLugares[0]))
                    <div class="podium-place first-place">
                        <div class="podium-medal">🥇</div>
                        <div class="podium-box">
                            <div class="podium-rank">1°</div>
                            <div class="podium-team">{{ $primerosLugares[0]['equipo']->nombre }}</div>
                            <div class="podium-score">{{ $primerosLugares[0]['promedio'] }}/10</div>
                            <p style="font-size: 0.85rem; margin-top: 0.5rem; opacity: 0.9;">
                                {{ $primerosLugares[0]['evaluaciones_recibidas'] }} evaluaciones
                            </p>
                        </div>
                    </div>
                @endif

                @if(isset($primerosLugares[2]))
                    <div class="podium-place third-place">
                        <div class="podium-medal">🥉</div>
                        <div class="podium-box">
                            <div class="podium-rank">3°</div>
                            <div class="podium-team">{{ $primerosLugares[2]['equipo']->nombre }}</div>
                            <div class="podium-score">{{ $primerosLugares[2]['promedio'] }}/10</div>
                            <p style="font-size: 0.85rem; margin-top: 0.5rem; opacity: 0.9;">
                                {{ $primerosLugares[2]['evaluaciones_recibidas'] }} evaluaciones
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Tabla completa de ranking -->
    <div class="ranking-table">
        <h3 style="color: #1e293b; margin-bottom: 1.5rem;">
            <i class="fas fa-list-ol"></i> Ranking Completo
        </h3>

        @if(count($ranking) == 0)
            <div style="text-align: center; padding: 3rem; color: #64748b;">
                <i class="fas fa-inbox" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                <h3>No hay datos de ranking aún</h3>
                <p>Los equipos aparecerán aquí una vez que reciban evaluaciones completadas.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Posición</th>
                        <th>Equipo</th>
                        <th>Líder</th>
                        <th>Calificación Promedio</th>
                        <th>Evaluaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ranking as $index => $item)
                        <tr>
                            <td>
                                <span class="rank-position rank-{{ $index + 1 <= 3 ? ($index + 1) : 'other' }}">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: #667eea;">
                                    <i class="fas fa-users"></i> {{ $item['equipo']->nombre }}
                                </strong>
                            </td>
                            <td>{{ $item['equipo']->lider->name }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <strong style="font-size: 1.2rem; color: #1e293b;">{{ $item['promedio'] }}</strong>
                                    <span style="color: #64748b;">/10</span>
                                    <div style="flex: 1; max-width: 100px;">
                                        <div class="progress-bar-custom">
                                            <div class="progress-fill" style="width: {{ ($item['promedio'] / 10) * 100 }}%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="background: #e0e7ff; color: #4338ca; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.85rem;">
                                    {{ $item['evaluaciones_recibidas'] }}/{{ $item['total_jueces'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

</body>
</html>
