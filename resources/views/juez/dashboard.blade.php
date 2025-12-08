<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Juez - HackZone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #134e5e 0%, #71b280 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .dashboard {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        .header {
            margin-bottom: 30px;
        }

        h1 {
            color: #134e5e;
            margin-bottom: 10px;
            font-size: 2.5rem;
        }

        .badge {
            display: inline-block;
            background: #71b280;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .alert {
            background: #e6ffe6;
            border: 1px solid #4CAF50;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
        }

        .alert p {
            color: #4CAF50;
            margin: 0;
        }

        .info {
            background: #f0f8f4;
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
            text-align: left;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #d4ebe0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 600;
        }

        .info-value {
            color: #134e5e;
            font-weight: bold;
        }

        .description {
            color: #666;
            margin: 20px 0;
            line-height: 1.6;
        }

        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-logout {
            background: linear-gradient(90deg, #134e5e, #71b280);
            color: white;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 2rem;
            }
            
            .dashboard {
                padding: 30px 20px;
            }
            
            .buttons {
                flex-direction: column;
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
