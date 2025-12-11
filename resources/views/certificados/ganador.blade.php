<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de Ganador</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .certificate-container {
            background: white;
            width: 100%;
            max-width: 950px;
            padding: 20px;
            border: 10px solid #764ba2;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            position: relative;
            height: 100%;
            max-height: 600px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .certificate-border {
            border: 2px solid #667eea;
            padding: 15px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .certificate-header {
            margin-bottom: 10px;
        }
        .certificate-title {
            font-size: 32px;
            font-weight: bold;
            color: #764ba2;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .certificate-subtitle {
            font-size: 16px;
            color: #667eea;
            margin-bottom: 15px;
            font-style: italic;
        }
        .certificate-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: 10px 0;
        }
        .certificate-text {
            font-size: 14px;
            line-height: 1.5;
            color: #333;
            margin-bottom: 8px;
        }
        .certificate-name {
            font-size: 24px;
            font-weight: bold;
            color: #764ba2;
            margin: 10px 0;
            text-decoration: underline;
            text-decoration-thickness: 2px;
        }
        .certificate-details {
            font-size: 12px;
            color: #666;
            margin: 6px 0;
        }
        .certificate-event {
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
            margin: 10px 0;
        }
        .certificate-team {
            font-size: 16px;
            color: #555;
            margin: 8px 0;
            font-style: italic;
        }
        .certificate-score {
            font-size: 16px;
            color: #764ba2;
            font-weight: bold;
            margin: 10px 0;
        }
        .certificate-footer {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .certificate-signature {
            width: 180px;
            border-top: 2px solid #333;
            padding-top: 5px;
            font-size: 11px;
        }
        .certificate-date {
            font-size: 11px;
            color: #666;
        }
        .trophy-icon {
            font-size: 40px;
            color: #FFD700;
            margin: 5px 0;
        }
        .lugar-badge {
            display: inline-block;
            padding: 6px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 20px;
            font-weight: bold;
            border-radius: 50px;
            margin: 10px 0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .certificate-info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 4px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-border">
            <div class="certificate-header">
                <div class="trophy-icon">★</div>
                <h1 class="certificate-title">Certificado de Reconocimiento</h1>
                <p class="certificate-subtitle">HackZone - Plataforma de Eventos de Programación</p>
            </div>

            <div class="certificate-body">
                <p class="certificate-text">
                    Se otorga el presente certificado a:
                </p>
                
                <div class="certificate-name">{{ $usuario->name }}</div>
                
                <p class="certificate-text">
                    Por haber obtenido el
                </p>
                
                <div class="lugar-badge">
                    @if($lugar == 1)
                        PRIMER LUGAR
                    @elseif($lugar == 2)
                        SEGUNDO LUGAR
                    @else
                        TERCER LUGAR
                    @endif
                </div>
                
                <p class="certificate-text">
                    en el evento
                </p>
                
                <div class="certificate-event">{{ $evento->titulo }}</div>
                
                <p class="certificate-text">
                    como miembro del equipo
                </p>
                
                <div class="certificate-team">"{{ $equipo->nombre }}"</div>
                
                <div class="certificate-score">
                    Calificación Final: {{ number_format($promedio, 2) }} / 10.00
                </div>
                
                <div class="certificate-info-grid">
                    <p class="certificate-details">
                        Organizado por: {{ $evento->organizacion }}
                    </p>
                    <p class="certificate-details">
                        Fecha del evento: {{ $evento->fecha_inicio->format('d de F de Y') }}
                    </p>
                    <p class="certificate-details">
                        Ubicación: {{ $evento->ubicacion }}
                    </p>
                </div>
            </div>

            <div class="certificate-footer">
                <div class="certificate-signature">
                    <div style="margin-top: 25px;"></div>
                    <div>Administrador HackZone</div>
                </div>
                <div class="certificate-date">
                    Fecha de emisión: {{ now()->format('d de F de Y') }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>

