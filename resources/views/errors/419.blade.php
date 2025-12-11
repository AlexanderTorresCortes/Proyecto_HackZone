<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Sesión Expirada | HackZone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 60px 40px;
            text-align: center;
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .error-icon {
            font-size: 120px;
            color: #4facfe;
            margin-bottom: 30px;
            animation: rotate 3s infinite linear;
        }
        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        .error-code {
            font-size: 72px;
            font-weight: 700;
            color: #4facfe;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 32px;
            color: #333;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .error-message {
            font-size: 18px;
            color: #666;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-error {
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 172, 254, 0.4);
        }
        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }
        .btn-secondary:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="error-code">419</div>
        <h1 class="error-title">Sesión Expirada</h1>
        <p class="error-message">
            Tu sesión ha expirado por seguridad. Por favor, vuelve a iniciar sesión 
            para continuar navegando.
        </p>
        <div class="error-actions">
            <a href="{{ route('login') }}" class="btn-error btn-primary">
                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
            </a>
            <a href="{{ route('inicio.index') }}" class="btn-error btn-secondary">
                <i class="fas fa-home"></i> Ir al Inicio
            </a>
        </div>
    </div>
</body>
</html>

