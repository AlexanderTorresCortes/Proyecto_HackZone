<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Entrega de Trabajo</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            margin: -30px -30px 30px -30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin: 20px 0;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-box strong {
            color: #764ba2;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📤 Nueva Entrega de Trabajo</h1>
        </div>
        
        <div class="content">
            <p>Hola <strong>{{ $juez->name }}</strong>,</p>
            
            <p>Te informamos que el equipo <strong>{{ $equipo->nombre }}</strong> ha subido una nueva versión de su trabajo para el evento <strong>{{ $evento->titulo }}</strong>.</p>
            
            <div class="info-box">
                <p><strong>Equipo:</strong> {{ $equipo->nombre }}</p>
                <p><strong>Evento:</strong> {{ $evento->titulo }}</p>
                <p><strong>Versión:</strong> {{ $entrega->version }}</p>
                <p><strong>Archivo:</strong> {{ $entrega->nombre_archivo }}</p>
                <p><strong>Fecha de entrega:</strong> {{ $entrega->created_at->format('d/m/Y H:i') }}</p>
            </div>
            
            <p>Por favor, revisa y califica el trabajo del equipo cuando estés listo.</p>
            
            <div style="text-align: center;">
                <a href="{{ url('/juez/equipos/' . $evento->id) }}" class="button">Ver y Calificar Trabajo</a>
            </div>
        </div>
        
        <div class="footer">
            <p>Este es un correo automático de HackZone. Por favor, no respondas a este mensaje.</p>
            <p>&copy; {{ date('Y') }} HackZone - Plataforma de Eventos de Programación</p>
        </div>
    </div>
</body>
</html>

