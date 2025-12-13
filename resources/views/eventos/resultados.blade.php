<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados - {{ $evento->titulo }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Figtree', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }

        .container { max-width: 1400px; margin: 2rem auto; padding: 0 2rem; }

        .hero-section {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            text-align: center;
        }

        .hero-section h1 {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .hero-section p {
            color: #64748b;
            font-size: 1.1rem;
        }

        .podium-container {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .podium-title {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 3rem;
            color: #1e293b;
        }

        .podium-grid {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 2rem;
            margin: 0 auto;
            max-width: 900px;
        }

        .podium-place {
            text-align: center;
            transition: all 0.3s;
            flex: 1;
            max-width: 250px;
        }

        .podium-place:hover {
            transform: translateY(-10px);
        }

        .podium-medal {
            font-size: 5rem;
            margin-bottom: 1rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .podium-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 2rem 1.5rem;
            color: white;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }

        .first-place .podium-box {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            min-height: 280px;
        }

        .second-place .podium-box {
            background: linear-gradient(135deg, #C0C0C0 0%, #A8A8A8 100%);
            min-height: 230px;
        }

        .third-place .podium-box {
            background: linear-gradient(135deg, #CD7F32 0%, #B8860B 100%);
            min-height: 180px;
        }

        .podium-rank {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .podium-team {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .podium-score {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .podium-details {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #64748b;
            font-size: 1rem;
        }

        .ranking-section {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .ranking-section h2 {
            font-size: 2rem;
            color: #1e293b;
            margin-bottom: 2rem;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 1rem;
        }

        thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.25rem;
            text-align: left;
            font-weight: 600;
            font-size: 1.1rem;
        }

        thead th:first-child {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        thead th:last-child {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        tbody tr {
            background: #f8fafc;
            transition: all 0.3s;
        }

        tbody tr:hover {
            background: #e0e7ff;
            transform: translateX(5px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        tbody td {
            padding: 1.5rem 1.25rem;
        }

        tbody tr td:first-child {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        tbody tr td:last-child {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-weight: 700;
            font-size: 1.4rem;
        }

        .rank-1 {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: white;
            box-shadow: 0 3px 15px rgba(255, 215, 0, 0.4);
        }

        .rank-2 {
            background: linear-gradient(135deg, #C0C0C0 0%, #A8A8A8 100%);
            color: white;
            box-shadow: 0 3px 15px rgba(192, 192, 192, 0.4);
        }

        .rank-3 {
            background: linear-gradient(135deg, #CD7F32 0%, #B8860B 100%);
            color: white;
            box-shadow: 0 3px 15px rgba(205, 127, 50, 0.4);
        }

        .rank-other {
            background: #e2e8f0;
            color: #64748b;
        }

        .score-display {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .score-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
        }

        .progress-bar {
            flex: 1;
            height: 10px;
            background: #e2e8f0;
            border-radius: 5px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 5px;
            transition: width 0.5s ease;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #64748b;
        }

        .empty-state i {
            font-size: 5rem;
            color: #cbd5e1;
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>

@include('components.navbar')

<div class="container">
    <div class="hero-section">
        <h1><i class="fas fa-trophy"></i> {{ $evento->titulo }}</h1>
        <p>Resultados Oficiales del Torneo</p>
        <div style="margin-top: 1.5rem; color: #64748b;">
            <i class="fas fa-calendar"></i> {{ $evento->fecha_inicio->format('d/m/Y') }} •
            <i class="fas fa-map-marker-alt"></i> {{ $evento->ubicacion }}
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-section">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-value">{{ $estadisticas['total_equipos'] }}</div>
            <div class="stat-label">Equipos Participantes</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-gavel"></i></div>
            <div class="stat-value">{{ $estadisticas['total_jueces'] }}</div>
            <div class="stat-label">Jueces Evaluadores</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-clipboard-check"></i></div>
            <div class="stat-value">{{ $estadisticas['completadas'] }}</div>
            <div class="stat-label">Evaluaciones Realizadas</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-percentage"></i></div>
            <div class="stat-value">{{ $estadisticas['porcentaje_completado'] }}%</div>
            <div class="stat-label">Progreso Completado</div>
        </div>
    </div>

    <!-- Podium -->
    @if(count($primerosLugares) > 0)
        <div class="podium-container">
            <h2 class="podium-title">
                <i class="fas fa-crown" style="color: #FFD700;"></i> Primeros Lugares
            </h2>

            <div class="podium-grid">
                @if(isset($primerosLugares[1]))
                    <div class="podium-place second-place">
                        <div class="podium-medal">🥈</div>
                        <div class="podium-box">
                            <div class="podium-rank">2°</div>
                            <div class="podium-team">{{ $primerosLugares[1]['equipo']->nombre }}</div>
                            <div class="podium-score">{{ $primerosLugares[1]['promedio'] }}</div>
                            <div class="podium-details">
                                Líder: {{ $primerosLugares[1]['equipo']->lider->name }}<br>
                                {{ $primerosLugares[1]['evaluaciones_recibidas'] }} evaluaciones
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($primerosLugares[0]))
                    <div class="podium-place first-place">
                        <div class="podium-medal">🥇</div>
                        <div class="podium-box">
                            <div class="podium-rank">1°</div>
                            <div class="podium-team">{{ $primerosLugares[0]['equipo']->nombre }}</div>
                            <div class="podium-score">{{ $primerosLugares[0]['promedio'] }}</div>
                            <div class="podium-details">
                                Líder: {{ $primerosLugares[0]['equipo']->lider->name }}<br>
                                {{ $primerosLugares[0]['evaluaciones_recibidas'] }} evaluaciones
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($primerosLugares[2]))
                    <div class="podium-place third-place">
                        <div class="podium-medal">🥉</div>
                        <div class="podium-box">
                            <div class="podium-rank">3°</div>
                            <div class="podium-team">{{ $primerosLugares[2]['equipo']->nombre }}</div>
                            <div class="podium-score">{{ $primerosLugares[2]['promedio'] }}</div>
                            <div class="podium-details">
                                Líder: {{ $primerosLugares[2]['equipo']->lider->name }}<br>
                                {{ $primerosLugares[2]['evaluaciones_recibidas'] }} evaluaciones
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Ranking completo -->
    <div class="ranking-section">
        <h2><i class="fas fa-list-ol"></i> Clasificación General</h2>

        @if(count($ranking) == 0)
            <div class="empty-state">
                <i class="fas fa-hourglass-half"></i>
                <h3>Resultados Pendientes</h3>
                <p>Los resultados se mostrarán cuando las evaluaciones estén completadas.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Pos.</th>
                        <th>Equipo</th>
                        <th>Líder</th>
                        <th>Puntuación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ranking as $index => $item)
                        <tr>
                            <td>
                                <span class="rank-badge rank-{{ $index + 1 <= 3 ? ($index + 1) : 'other' }}">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td>
                                <strong style="font-size: 1.2rem; color: #1e293b;">
                                    <i class="fas fa-users" style="color: #667eea;"></i>
                                    {{ $item['equipo']->nombre }}
                                </strong>
                            </td>
                            <td style="color: #64748b;">
                                {{ $item['equipo']->lider->name }}
                            </td>
                            <td>
                                <div class="score-display">
                                    <span class="score-number">{{ $item['promedio'] }}/10</span>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: {{ ($item['promedio'] / 10) * 100 }}%;"></div>
                                    </div>
                                </div>
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
