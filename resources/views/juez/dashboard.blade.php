<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Juez - HackZone</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Figtree', sans-serif;
        }

        body {
            background: #f0f2f5;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            text-align: center;
        }

        .header h1 {
            color: #1e293b;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #64748b;
            font-size: 1.1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            gap: 1.5rem;
            align-items: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .stat-icon.purple {
            background: #ede9fe;
            color: #7c3aed;
        }

        .stat-icon.blue {
            background: #dbeafe;
            color: #2563eb;
        }

        .stat-icon.yellow {
            background: #fef3c7;
            color: #f59e0b;
        }

        .stat-icon.green {
            background: #d1fae5;
            color: #10b981;
        }

        .stat-content {
            flex: 1;
        }

        .stat-content h3 {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .stat-content p {
            color: #64748b;
            font-size: 0.9rem;
        }

        .section-title {
            font-size: 1.5rem;
            color: #1e293b;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            color: #667eea;
        }

        .empty-state {
            background: white;
            padding: 3rem;
            border-radius: 12px;
            text-align: center;
            color: #64748b;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .empty-state i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            font-size: 1rem;
        }

        .eventos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 1.5rem;
        }

        .evento-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s;
        }

        .evento-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }

        .evento-header {
            padding: 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .evento-header h3 {
            font-size: 1.3rem;
            margin-bottom: 0.75rem;
        }

        .evento-meta {
            display: flex;
            gap: 1.5rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .evento-meta span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .evento-body {
            padding: 1.5rem;
        }

        .criterios-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .criterio-badge {
            background: #e0e7ff;
            color: #4338ca;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .btn-evaluar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-evaluar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .eventos-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

@include('components.navbar')

<div class="container">
    <div class="header">
        <h1><i class="fas fa-gavel"></i> Panel de Juez</h1>
        <p>Bienvenido, {{ auth()->user()->name }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-content"><h3>{{ $totalEventos }}</h3><p>Eventos Asignados</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-clipboard-list"></i></div>
            <div class="stat-content"><h3>{{ $totalEvaluaciones }}</h3><p>Total Evaluaciones</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon yellow"><i class="fas fa-hourglass-half"></i></div>
            <div class="stat-content"><h3>{{ $evaluacionesPendientes }}</h3><p>Pendientes</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-content"><h3>{{ $evaluacionesCompletadas }}</h3><p>Completadas</p></div>
        </div>
    </div>

    <h2 class="section-title"><i class="fas fa-trophy"></i> Mis Eventos Asignados</h2>

    @if($eventos->isEmpty())
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>No tienes eventos asignados</h3>
            <p>Cuando un administrador te asigne a un evento, aparecerá aquí.</p>
        </div>
    @else
        <div class="eventos-grid">
            @foreach($eventos as $evento)
                <div class="evento-card">
                    <div class="evento-header">
                        <h3>{{ $evento->titulo }}</h3>
                        <div class="evento-meta">
                            <span><i class="far fa-calendar"></i> {{ $evento->fecha_inicio->format('d/m/Y') }}</span>
                            <span><i class="fas fa-map-marker-alt"></i> {{ $evento->ubicacion }}</span>
                        </div>
                    </div>
                    <div class="evento-body">
                        <p style="color: #64748b; margin-bottom: 1rem;">{{ Str::limit($evento->descripcion_corta, 150) }}</p>
                        @if($evento->criteriosEvaluacion->count() > 0)
                            <div style="margin-bottom: 1rem;">
                                <strong style="color: #1e293b; font-size: 0.9rem;">Criterios de Evaluación:</strong>
                                <div class="criterios-list">
                                    @foreach($evento->criteriosEvaluacion as $criterio)
                                        <span class="criterio-badge">{{ $criterio->nombre }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <a href="{{ route('juez.equipos', $evento->id) }}" class="btn-evaluar">
                            <i class="fas fa-users"></i> Ver Equipos para Evaluar
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

</body>
</html>
