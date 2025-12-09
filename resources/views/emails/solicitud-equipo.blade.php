<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Solicitud de Equipo</title>
    <link rel="stylesheet" href="{{ asset('css/BienvenidaEmail.css') }}">
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>HackZone</h1>
        </div>
        
        <div class="email-content">
            <h2>Nueva Solicitud para unirse a tu Equipo</h2>
            
            <p>Hola <strong>{{ $solicitud->equipo->lider->name }}</strong>,</p>
            
            <p>Has recibido una nueva solicitud para unirse a tu equipo <strong>{{ $solicitud->equipo->nombre }}</strong>.</p>
            
            <div class="solicitud-info">
                <div class="info-item">
                    <strong>Solicitante:</strong> {{ $solicitud->usuario->name }} ({{ '@' . $solicitud->usuario->username }})
                </div>
                <div class="info-item">
                    <strong>Rol Solicitado:</strong> {{ $solicitud->rol_solicitado ?? 'Miembro' }}
                </div>
                @if($solicitud->mensaje)
                <div class="info-item">
                    <strong>Mensaje:</strong>
                    <p style="background: #f5f5f5; padding: 10px; border-radius: 5px; margin-top: 5px;">
                        {{ $solicitud->mensaje }}
                    </p>
                </div>
                @endif
            </div>
            
            <div class="email-button">
                <a href="{{ route('equipos.solicitudes') }}" class="btn-primary">
                    Ver Solicitud
                </a>
            </div>
            
            <p class="email-footer">
                Puedes revisar y gestionar todas las solicitudes desde tu panel de equipo.
            </p>
        </div>
        
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} HackZone. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>

