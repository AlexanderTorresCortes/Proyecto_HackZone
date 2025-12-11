<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de Ganador</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
        }
        .email-body {
            padding: 30px;
        }
        .trophy {
            font-size: 60px;
            text-align: center;
            margin: 20px 0;
        }
        .lugar-badge {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 24px;
            font-weight: bold;
            border-radius: 50px;
            margin: 20px 0;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #764ba2;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box strong {
            color: #764ba2;
        }
        .email-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🎉 ¡Felicitaciones!</h1>
            <p>Has obtenido un lugar destacado</p>
        </div>
        
        <div class="email-body">
            <div class="trophy">
                @if($lugar == 1)
                    🥇
                @elseif($lugar == 2)
                    🥈
                @else
                    🥉
                @endif
            </div>
            
            <h2 style="text-align: center; color: #764ba2;">
                Hola {{ $usuario->name }},
            </h2>
            
            <p style="font-size: 18px; text-align: center; margin: 20px 0;">
                ¡Felicitaciones por obtener el
            </p>
            
            <div style="text-align: center;">
                <div class="lugar-badge">
                    @if($lugar == 1)
                        PRIMER LUGAR
                    @elseif($lugar == 2)
                        SEGUNDO LUGAR
                    @else
                        TERCER LUGAR
                    @endif
                </div>
            </div>
            
            <p style="text-align: center; font-size: 18px; margin: 20px 0;">
                en el evento <strong>{{ $evento->titulo }}</strong>
            </p>
            
            <div class="info-box">
                <p><strong>Equipo:</strong> {{ $equipo->nombre }}</p>
                <p><strong>Calificación Final:</strong> {{ number_format($promedio, 2) }} / 10.00</p>
                <p><strong>Evento:</strong> {{ $evento->titulo }}</p>
                <p><strong>Organizador:</strong> {{ $evento->organizacion }}</p>
                <p><strong>Fecha:</strong> {{ $evento->fecha_inicio->format('d/m/Y') }}</p>
            </div>
            
            <p style="margin-top: 30px;">
                Tu certificado oficial se encuentra adjunto a este correo en formato PDF. 
                Puedes descargarlo y compartirlo en tus redes profesionales.
            </p>
            
            <p style="margin-top: 20px;">
                ¡Gracias por participar y felicitaciones por tu excelente desempeño!
            </p>
        </div>
        
        <div class="email-footer">
            <p><strong>HackZone</strong></p>
            <p>Plataforma de Eventos de Programación</p>
            <p style="margin-top: 10px; font-size: 12px;">
                Este es un correo automático, por favor no respondas a este mensaje.
            </p>
        </div>
    </div>
</body>
</html>

