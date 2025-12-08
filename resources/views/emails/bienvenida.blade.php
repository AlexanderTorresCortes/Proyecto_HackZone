<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a HackZone</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #4CAF50;
        }
        .header h1 {
            color: #4CAF50;
            margin: 0;
        }
        .content {
            padding: 20px 0;
            line-height: 1.6;
            color: #333;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            margin: 20px 0;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #777;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 ¡Bienvenido a HackZone!</h1>
        </div>
        
        <div class="content">
            <h2>Hola {{ $user->name }},</h2>
            
            <p>¡Nos alegra mucho tenerte con nosotros! Tu cuenta ha sido creada exitosamente.</p>
            
            <p>En HackZone podrás disfrutar de:</p>
            <ul>
                <li>✅ Acceso a contenido exclusivo</li>
                <li>✅ Comunidad de desarrolladores</li>
                <li>✅ Recursos y herramientas</li>
                <li>✅ Y mucho más...</li>
            </ul>
            
            <p>Estamos emocionados de comenzar este viaje contigo.</p>
            
            <center>
                <a href="{{ url('/') }}" class="button">Explorar HackZone</a>
            </center>
            
            <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
            
            <p>¡Saludos!</p>
            <p><strong>El equipo de HackZone</strong></p>
        </div>
        
        <div class="footer">
            <p>Este correo fue enviado a {{ $user->email }}</p>
            <p>&copy; {{ date('Y') }} HackZone. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>