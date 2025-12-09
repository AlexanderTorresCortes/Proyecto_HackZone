<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud Aceptada</title>
    <link rel="stylesheet" href="{{ asset('css/BienvenidaEmail.css') }}">
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>HackZone</h1>
        </div>
        
        <div class="email-content">
            <h2>¡Felicidades! Tu solicitud ha sido aceptada</h2>
            
            <p>Hola <strong>{{ $solicitud->usuario->name }}</strong>,</p>
            
            <p>¡Excelentes noticias! Tu solicitud para unirte al equipo <strong>{{ $solicitud->equipo->nombre }}</strong> ha sido aceptada.</p>
            
            <div class="solicitud-info">
                <div class="info-item">
                    <strong>Equipo:</strong> {{ $solicitud->equipo->nombre }}
                </div>
                <div class="info-item">
                    <strong>Rol Asignado:</strong> {{ $solicitud->rol_solicitado ?? 'Miembro' }}
                </div>
                <div class="info-item">
                    <strong>Líder del Equipo:</strong> {{ $solicitud->equipo->lider->name }}
                </div>
            </div>
            
            <div class="email-button">
                <a href="{{ route('equipos.show', $solicitud->equipo->id) }}" class="btn-primary">
                    Ver Mi Equipo
                </a>
            </div>
            
            <p class="email-footer">
                ¡Bienvenido al equipo! Ahora puedes comenzar a colaborar y trabajar en proyectos juntos.
            </p>
        </div>
        
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} HackZone. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>

