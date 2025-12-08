<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a HackZone</title>
    <link rel="stylesheet" href="{{ asset('css/BienvenidaEmail.css') }}">
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <div class="logo">
                <img src="{{ asset('img/logoHackZoneBlancosintitulo.png') }}" alt="HackZone Logo">
            </div>
            <h1>¡Bienvenido a HackZone!</h1>
            <p>Tu nueva aventura comienza aquí</p>
        </div>
        
        <div class="content">
            <p class="greeting">Hola {{ $user->name }},</p>
            
            <p>Nos llena de emoción darte la bienvenida a <strong>HackZone</strong>. Tu cuenta ha sido creada exitosamente y ya formas parte de nuestra comunidad.</p>
            
            <div class="features">
                <h3>Lo que te espera en HackZone:</h3>
                <ul>
                    <li>Acceso ilimitado a contenido exclusivo y actualizado</li>
                    <li>Comunidad activa de desarrolladores y profesionales</li>
                    <li>Recursos, herramientas y tutoriales especializados</li>
                    <li>Eventos, webinars y oportunidades de networking</li>
                    <li>Soporte técnico dedicado a tu crecimiento</li>
                </ul>
            </div>
            
            <p>Estamos comprometidos en brindarte la mejor experiencia y acompañarte en cada paso de tu desarrollo profesional.</p>
            
            <div class="cta-container">
                <a href="{{ url('/') }}" class="button">Comenzar mi experiencia</a>
            </div>
            
            <div class="divider"></div>
            
            <div class="signature">
                <p>¿Tienes preguntas? Estamos aquí para ayudarte.</p>
                <p>¡Nos vemos dentro!</p>
                <p class="team-name">El equipo de HackZone</p>
            </div>
        </div>
        
        <div class="footer">
            <p>Este correo fue enviado a <strong>{{ $user->email }}</strong></p>
            <p>© {{ date('Y') }} HackZone. Todos los derechos reservados.</p>
            <p style="margin-top: 15px; font-size: 12px;">
                <a href="#">Política de Privacidad</a> | 
                <a href="#">Términos de Servicio</a> | 
                <a href="#">Contacto</a>
            </p>
        </div>
    </div>
</body>
</html>