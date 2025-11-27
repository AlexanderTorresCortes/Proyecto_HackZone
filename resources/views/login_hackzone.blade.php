<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login HackZone</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="contenedor">
        
        <div class="panel-izquierdo">
            <div class="contenido-form">
                <h1>Bienvenido</h1>
                <p class="subtitulo">Inicie sesión en su cuenta para continuar</p>

                <div class="selector-rol">
                    <button type="button" class="activo" onclick="seleccionarRol(this, 'usuario')">Usuario</button>
                    <button type="button" onclick="seleccionarRol(this, 'administrador')">Administrador</button>
                    <button type="button" onclick="seleccionarRol(this, 'juez')">Juez</button>
                </div>

                <form action="{{ route('login.submit') }}" method="POST" style="width: 100%;">
                    @csrf
                    
                    {{-- Mostrar errores de validación --}}
                    @if ($errors->any())
                        <div style="background: #ffe6e6; border: 1px solid #ff4444; border-radius: 10px; padding: 15px; margin-bottom: 20px;">
                            @foreach ($errors->all() as $error)
                                <p style="color: #ff4444; margin: 5px 0; font-size: 0.9rem;">• {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    {{-- Mostrar mensaje de éxito --}}
                    @if (session('success'))
                        <div style="background: #e6ffe6; border: 1px solid #4CAF50; border-radius: 10px; padding: 15px; margin-bottom: 20px;">
                            <p style="color: #4CAF50; margin: 0; font-size: 0.9rem;">{{ session('success') }}</p>
                        </div>
                    @endif

                    <input type="hidden" name="rol" id="inputRol" value="usuario">

                    <div class="grupo-input">
                        <span class="material-icons-outlined icono">person</span>
                        <input type="text" name="usuario" placeholder="Ingresar usuario o email" value="{{ old('usuario') }}" required>
                    </div>

                    <div class="grupo-input">
                        <span class="material-icons-outlined icono">lock</span>
                        <input type="password" id="password" name="password" placeholder="**************" required>
                        <span class="material-icons-outlined icono-ojo" onclick="togglePassword('password', this)">visibility_off</span>
                    </div>

                    <div class="acciones">
                        <div class="contenedor-checkbox">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Recordar contraseña</label>
                        </div>
                        <a href="#">¿Olvidaste tu contraseña?</a>
                    </div>

                    <div style="text-align: center;">
                        <button type="submit" class="boton-entrar">Entrar</button>
                    </div>
                </form>

                <a href="{{ route('register.form') }}" class="enlace-registro">¿No tienes cuenta? <span>Regístrate aquí</span></a>
            </div>
        </div>

        <div class="panel-derecho">
            <div class="capsula capsula-1"></div>
            <div class="capsula capsula-2"></div>
            <div class="capsula capsula-3"></div>

            <div class="contenido-logo">
                <img src="{{ asset('img/logoHackZoneBlancosintitulo.png') }}" alt="Logo HackZone" class="icono-logo">
                <div class="texto-logo">HackZone</div>
            </div>
        </div>

    </div>

    <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>