<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - HackZone</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #c94b4b 0%, #4b134f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .dashboard {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        .header {
            margin-bottom: 30px;
        }

        h1 {
            color: #4b134f;
            margin-bottom: 10px;
            font-size: 2.5rem;
        }

        .badge {
            display: inline-block;
            background: #c94b4b;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .alert {
            background: #e6ffe6;
            border: 1px solid #4CAF50;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
        }

        .alert p {
            color: #4CAF50;
            margin: 0;
        }

        .info {
            background: #fff5f5;
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
            text-align: left;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ffe0e0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 600;
        }

        .info-value {
            color: #4b134f;
            font-weight: bold;
        }

        .description {
            color: #666;
            margin: 20px 0;
            line-height: 1.6;
        }

        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-logout {
            background: linear-gradient(90deg, #c94b4b, #4b134f);
            color: white;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 2rem;
            }
            
            .dashboard {
                padding: 30px 20px;
            }
            
            .buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="header">
            <h1>Panel de Administrador</h1>
            <span class="badge">ROL: ADMINISTRADOR</span>
        </div>

        @if (session('success'))
            <div class="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="info">
            <div class="info-item">
                <span class="info-label">Nombre:</span>
                <span class="info-value">{{ auth()->user()->name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Usuario:</span>
                <span class="info-value">{{ auth()->user()->username }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ auth()->user()->email }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Rol:</span>
                <span class="info-value">{{ ucfirst(auth()->user()->rol) }}</span>
            </div>
        </div>

        <p class="description">
            Bienvenido al panel de administrador. Desde aquí puedes gestionar usuarios, 
            competencias, problemas y toda la plataforma HackZone.
        </p>

        <div class="buttons">
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-logout">Cerrar Sesión</button>
            </form>
        </div>
    </div>
</body>
</html>