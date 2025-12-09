<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyecto Calificado - HackZone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            padding: 20px;
            line-height: 1.6;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(118, 28, 157, 0.15);
        }

        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 15s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 16px;
            position: relative;
            z-index: 1;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            position: relative;
            z-index: 1;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }

        .content {
            padding: 40px 30px;
            color: #333333;
        }

        .greeting {
            font-size: 22px;
            color: #10b981;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .content p {
            margin-bottom: 16px;
            color: #555555;
            font-size: 15px;
        }

        .evaluation-details {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-left: 4px solid #10b981;
            padding: 24px;
            margin: 30px 0;
            border-radius: 8px;
        }

        .evaluation-details h3 {
            color: #10b981;
            font-size: 18px;
            margin-bottom: 16px;
            font-weight: 600;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(16, 185, 129, 0.2);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #666;
            font-weight: 500;
        }

        .detail-value {
            color: #333;
            font-weight: 600;
        }

        .score-highlight {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 25px 0;
        }

        .score-highlight .score-number {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .score-highlight .score-label {
            font-size: 14px;
            opacity: 0.9;
        }

        .criterios-list {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .criterios-list h4 {
            color: #333;
            font-size: 16px;
            margin-bottom: 12px;
        }

        .criterio-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            color: #555;
            font-size: 14px;
        }

        .criterio-nombre {
            font-weight: 500;
        }

        .criterio-puntos {
            color: #10b981;
            font-weight: 600;
        }

        .cta-container {
            text-align: center;
            margin: 35px 0;
        }

        .button {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }

        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #10b981, transparent);
            margin: 30px 0;
            opacity: 0.3;
        }

        .signature {
            margin-top: 30px;
        }

        .signature p {
            margin-bottom: 8px;
        }

        .team-name {
            color: #10b981;
            font-weight: 600;
            font-size: 16px;
        }

        .footer {
            background: #f9f9f9;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #eeeeee;
        }

        .footer p {
            color: #888888;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .footer a {
            color: #10b981;
            text-decoration: none;
        }

        .icon-success {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            position: relative;
            z-index: 1;
        }

        .icon-success svg {
            width: 35px;
            height: 35px;
            fill: white;
        }

        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }

            .header h1 {
                font-size: 24px;
            }

            .content {
                padding: 30px 20px;
            }

            .greeting {
                font-size: 20px;
            }

            .button {
                padding: 14px 30px;
                font-size: 15px;
            }

            .score-highlight .score-number {
                font-size: 36px;
            }

            .detail-row {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <div class="icon-success">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>
            </div>
            <h1>¡Tu Proyecto ha sido Calificado!</h1>
            <p>{{ $evento->titulo }}</p>
        </div>

        <div class="content">
            <p class="greeting">¡Hola {{ $miembro->name }}!</p>

            <p>Tenemos noticias emocionantes para ti. El juez <strong>{{ $juez->name }}</strong> ha evaluado el proyecto de tu equipo <strong>"{{ $equipo->nombre }}"</strong> en el evento <strong>{{ $evento->titulo }}</strong>.</p>

            <div class="score-highlight">
                <div class="score-number">{{ number_format($puntuacionTotal, 1) }}</div>
                <div class="score-label">Puntuación Total</div>
            </div>

            <div class="evaluation-details">
                <h3>📋 Detalles de la Evaluación</h3>
                <div class="detail-row">
                    <span class="detail-label">Equipo:</span>
                    <span class="detail-value">{{ $equipo->nombre }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Evento:</span>
                    <span class="detail-value">{{ $evento->titulo }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Juez:</span>
                    <span class="detail-value">{{ $juez->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Fecha:</span>
                    <span class="detail-value">{{ $evaluacion->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            @if($evaluacion->puntuaciones && count($evaluacion->puntuaciones) > 0)
            <div class="criterios-list">
                <h4>🎯 Puntuación por Criterio:</h4>
                @foreach($evaluacion->puntuaciones as $criterioId => $puntos)
                    @php
                        $criterio = $evento->criteriosEvaluacion->where('id', $criterioId)->first();
                    @endphp
                    @if($criterio)
                    <div class="criterio-item">
                        <span class="criterio-nombre">{{ $criterio->nombre }}</span>
                        <span class="criterio-puntos">{{ $puntos }}/10</span>
                    </div>
                    @endif
                @endforeach
            </div>
            @endif

            @if($evaluacion->comentarios)
            <div class="evaluation-details" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-left-color: #f59e0b;">
                <h3 style="color: #f59e0b;">💬 Comentarios del Juez</h3>
                <p style="color: #555; font-style: italic; margin-top: 10px;">"{{ $evaluacion->comentarios }}"</p>
            </div>
            @endif

            <p>¡Sigue esforzándote! Cada evaluación es una oportunidad para aprender y mejorar. Puedes ver los resultados completos y el ranking del evento en la plataforma.</p>

            <div class="cta-container">
                <a href="{{ route('eventos.resultados', $evento->id) }}" class="button" style="color: #ffffff !important; text-decoration: none;">Ver Ranking Completo</a>
            </div>

            <div class="divider"></div>

            <div class="signature">
                <p>¡Gracias por participar!</p>
                <p>Continúa demostrando tu talento y creatividad.</p>
                <p class="team-name">El equipo de HackZone</p>
            </div>
        </div>

        <div class="footer">
            <p>Este correo fue enviado a <strong>{{ $miembro->email }}</strong></p>
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
