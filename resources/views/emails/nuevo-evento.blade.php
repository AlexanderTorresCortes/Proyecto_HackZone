<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Evento Disponible</title>
    <link rel="stylesheet" href="{{ asset('css/BienvenidaEmail.css') }}">
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>HackZone</h1>
        </div>
        
        <div class="email-content">
            <h2>¡Nuevo Evento Disponible!</h2>
            
            <p>Hola,</p>
            
            <p>Te informamos que se ha creado un nuevo evento en HackZone:</p>
            
            <div class="evento-info">
                <h3 style="color: #4a148c; margin-bottom: 15px;">{{ $evento->titulo }}</h3>
                
                <div class="info-item">
                    <strong>Organizador:</strong> {{ $evento->organizacion }}
                </div>
                
                <div class="info-item">
                    <strong>Fecha de Inicio:</strong> {{ $evento->fecha_inicio->format('d/m/Y H:i') }}
                </div>
                
                <div class="info-item">
                    <strong>Fecha Límite de Inscripción:</strong> {{ $evento->fecha_limite_inscripcion->format('d/m/Y') }}
                </div>
                
                <div class="info-item">
                    <strong>Ubicación:</strong> {{ $evento->ubicacion }}
                </div>
                
                @if($evento->descripcion_corta)
                <div class="info-item">
                    <strong>Descripción:</strong>
                    <p style="background: #f5f5f5; padding: 10px; border-radius: 5px; margin-top: 5px;">
                        {{ $evento->descripcion_corta }}
                    </p>
                </div>
                @endif
                
                @if($evento->premios && count($evento->premios) > 0)
                <div class="info-item">
                    <strong>Premios:</strong>
                    <ul style="margin-top: 5px;">
                        @foreach($evento->premios as $lugar => $premio)
                            <li>{{ $lugar }}° Lugar: {{ $premio }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            
            <div class="email-button">
                <a href="{{ route('eventos.show', $evento->id) }}" class="btn-primary">
                    Ver Detalles del Evento
                </a>
            </div>
            
            <p class="email-footer">
                ¡No te pierdas esta oportunidad! Inscríbete antes de que se agoten los cupos.
            </p>
        </div>
        
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} HackZone. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>

