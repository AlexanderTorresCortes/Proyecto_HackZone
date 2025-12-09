<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Usuario - HackZone</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Figtree', sans-serif;
        }

        /* NAVBAR */
        .navbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #4a0072;
            font-weight: 700;
            font-size: 1.5rem;
            text-decoration: none;
        }

        .navbar-menu {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .navbar-menu a {
            color: #4a0072;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .navbar-menu a:hover {
            background: #f3f0f9;
        }

        .user-menu {
            position: relative;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            cursor: pointer;
            border: 2px solid #4a0072;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 8px;
            min-width: 200px;
            overflow: hidden;
        }

        .dropdown-menu.active {
            display: block;
        }

        .dropdown-menu a,
        .dropdown-menu button {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #374151;
            text-decoration: none;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-size: 0.95rem;
            transition: background 0.2s;
        }

        .dropdown-menu a:hover,
        .dropdown-menu button:hover {
            background: #f3f4f6;
        }

        .dropdown-menu hr {
            margin: 0.5rem 0;
            border: none;
            border-top: 1px solid #e5e7eb;
        }

        /* CONTENIDO */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .dashboard-content {
            padding: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 80px);
        }

        .dashboard-card {
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
            color: #4a0072;
            margin-bottom: 10px;
            font-size: 2.5rem;
        }

        .badge {
            display: inline-block;
            background: #4a0072;
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
            background: #f3f0f9;
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
            text-align: left;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 600;
        }

        .info-value {
            color: #4a0072;
            font-weight: bold;
        }

        .description {
            color: #666;
            margin: 20px 0;
            line-height: 1.6;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin: 2rem 0;
        }

        .action-btn {
            background: #f3f0f9;
            border: 2px solid #e0e0e0;
            padding: 1.5rem 1rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: #4a0072;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .action-btn:hover {
            background: #4a0072;
            border-color: #4a0072;
            color: white;
            transform: translateY(-2px);
        }

        .action-btn i {
            font-size: 2rem;
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 2rem;
            }
            
            .dashboard-card {
                padding: 30px 20px;
            }
            
            .navbar-menu {
                gap: 0.5rem;
            }
            
            .navbar-menu a span {
                display: none;
            }

            .quick-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="{{ route('inicio.index') }}" class="navbar-brand">
            <i class="fas fa-shield-alt"></i>
            HackZone
        </a>
        
        <div class="navbar-menu">
            <a href="{{ route('inicio.index') }}">
                <i class="fas fa-home"></i>
                <span>Inicio</span>
            </a>
            <a href="{{ route('eventos.index') }}">
                <i class="fas fa-trophy"></i>
                <span>Eventos</span>
            </a>
            <a href="{{ route('equipos.index') }}">
                <i class="fas fa-users"></i>
                <span>Equipos</span>
            </a>
            
            <!-- Menú de usuario -->
            <div class="user-menu">
                <div class="user-avatar" onclick="toggleMenu()">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                
                <div class="dropdown-menu" id="userDropdown">
                    <a href="{{ route('perfil.index') }}">
                        <i class="fas fa-user"></i> Mi Perfil
                    </a>
                    <a href="{{ route('usuario.dashboard') }}">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                    <hr>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENIDO -->
    <div class="dashboard-content">
        <div class="dashboard-card">
            <div class="header">
                <h1>¡Bienvenido, {{ auth()->user()->name }}!</h1>
                <span class="badge">ROL: {{ strtoupper(auth()->user()->rol) }}</span>
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
                Estás en el dashboard de usuario. Aquí podrás participar en competencias, 
                ver tus rankings y mejorar tus habilidades de programación.
            </p>

            <!-- Acciones Rápidas -->
            <div class="quick-actions">
                <a href="{{ route('perfil.index') }}" class="action-btn">
                    <i class="fas fa-user-circle"></i>
                    <span>Ver Perfil</span>
                </a>
                <a href="{{ route('eventos.index') }}" class="action-btn">
                    <i class="fas fa-trophy"></i>
                    <span>Ver Eventos</span>
                </a>
                <a href="{{ route('equipos.index') }}" class="action-btn">
                    <i class="fas fa-users"></i>
                    <span>Mis Equipos</span>
                </a>
                <a href="#" class="action-btn">
                    <i class="fas fa-chart-line"></i>
                    <span>Estadísticas</span>
                </a>
            </div>
        </div>
    </div>

    <script>
        function toggleMenu() {
            document.getElementById('userDropdown').classList.toggle('active');
        }

        // Cerrar el menú si se hace clic fuera
        window.onclick = function(event) {
            if (!event.target.matches('.user-avatar')) {
                var dropdown = document.getElementById('userDropdown');
                if (dropdown && dropdown.classList.contains('active')) {
                    dropdown.classList.remove('active');
                }
            }
        }
    </script>
</body>
</html>