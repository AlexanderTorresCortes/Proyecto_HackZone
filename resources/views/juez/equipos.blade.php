<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipos - {{ $evento->titulo }} - HackZone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; }
        .container { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        .header { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .header h1 { color: #1e293b; font-size: 1.8rem; margin-bottom: 0.5rem; }
        .header p { color: #64748b; }
        .btn-back { display: inline-flex; align-items: center; gap: 0.5rem; color: #6366f1; text-decoration: none; margin-bottom: 1rem; font-weight: 500; }
        .btn-back:hover { color: #4f46e5; }
        .equipos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; }
        .equipo-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; transition: all 0.3s; border: 2px solid transparent; }
        .equipo-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
        .equipo-card.evaluado { border-color: #10b981; }
        .equipo-header { padding: 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; position: relative; }
        .equipo-header.evaluado { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .status-badge { position: absolute; top: 1rem; right: 1rem; background: rgba(255,255,255,0.2); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .equipo-header h3 { font-size: 1.3rem; margin-bottom: 0.5rem; }
        .equipo-body { padding: 1.5rem; }
        .miembros-section { margin-bottom: 1rem; }
        .miembros-section h4 { color: #1e293b; font-size: 0.9rem; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem; }
        .miembro-item { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: #f8f9fa; border-radius: 6px; margin-bottom: 0.5rem; font-size: 0.9rem; color: #475569; }
        .lider-badge { background: #fbbf24; color: white; font-size: 0.7rem; padding: 0.125rem 0.5rem; border-radius: 4px; font-weight: 600; }
        .puntuacion-display { background: #e0e7ff; color: #4338ca; padding: 1rem; border-radius: 8px; text-align: center; margin-bottom: 1rem; }
        .puntuacion-display .score { font-size: 2rem; font-weight: 700; }
        .puntuacion-display .label { font-size: 0.85rem; margin-top: 0.25rem; }
        .btn-evaluar, .btn-ver { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s; width: 100%; }
        .btn-evaluar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-evaluar:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4); }
        .btn-ver { background: #e0e7ff; color: #4338ca; }
        .btn-ver:hover { background: #c7d2fe; }
        .empty-state { background: white; padding: 3rem; border-radius: 12px; text-align: center; color: #64748b; }
        .empty-state i { font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem; }
    </style>
</head>
<body>

@include('components.navbar')

<div class="container">
    <a href="{{ route('juez.dashboard') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i> Volver al Dashboard
    </a>

    <div class="header">
        <h1><i class="fas fa-trophy"></i> {{ $evento->titulo }}</h1>
        <p><i class="far fa-calendar"></i> {{ $evento->fecha_inicio->format('d/m/Y') }} • <i class="fas fa-map-marker-alt"></i> {{ $evento->ubicacion }}</p>
    </div>

    @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if($equipos->isEmpty())
        <div class="empty-state">
            <i class="fas fa-users-slash"></i>
            <h3>No hay equipos inscritos</h3>
            <p>Aún no hay equipos registrados para este evento.</p>
        </div>
    @else
        <div class="equipos-grid">
            @foreach($equipos as $equipo)
                <div class="equipo-card {{ $equipo->evaluado ? 'evaluado' : '' }}">
                    <div class="equipo-header {{ $equipo->evaluado ? 'evaluado' : '' }}">
                        @if($equipo->evaluado)
                            <span class="status-badge"><i class="fas fa-check"></i> EVALUADO</span>
                        @else
                            <span class="status-badge"><i class="fas fa-clock"></i> PENDIENTE</span>
                        @endif
                        <h3>{{ $equipo->nombre }}</h3>
                        <p style="font-size: 0.9rem; opacity: 0.9;">
                            <i class="fas fa-users"></i> {{ $equipo->miembros_actuales }}/{{ $equipo->miembros_max }} miembros
                        </p>
                    </div>

                    <div class="equipo-body">
                        @if($equipo->descripcion)
                            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1rem;">{{ Str::limit($equipo->descripcion, 100) }}</p>
                        @endif

                        <div class="miembros-section">
                            <h4><i class="fas fa-user-tie"></i> Líder</h4>
                            <div class="miembro-item">
                                <i class="fas fa-crown" style="color: #fbbf24;"></i>
                                <span>{{ $equipo->lider->name }}</span>
                            </div>

                            @if($equipo->miembros->count() > 1)
                                <h4 style="margin-top: 1rem;"><i class="fas fa-users"></i> Miembros</h4>
                                @foreach($equipo->miembros as $miembro)
                                    @if($miembro->user_id != $equipo->user_id)
                                        <div class="miembro-item">
                                            <i class="fas fa-user"></i>
                                            <span>{{ $miembro->usuario->name }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>

                        @if($equipo->evaluado && $equipo->evaluacion)
                            <div class="puntuacion-display">
                                <div class="score">{{ number_format($equipo->evaluacion->calcularPromedio(), 1) }}/10</div>
                                <div class="label">Puntuación Promedio</div>
                            </div>
                            <a href="{{ route('juez.evaluar', [$evento->id, $equipo->id]) }}" class="btn-ver">
                                <i class="fas fa-eye"></i> Ver Evaluación
                            </a>
                        @else
                            <a href="{{ route('juez.evaluar', [$evento->id, $equipo->id]) }}" class="btn-evaluar">
                                <i class="fas fa-clipboard-check"></i> Evaluar Equipo
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

</body>
</html>
