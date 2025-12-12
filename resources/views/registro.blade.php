<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro HackZone</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    

    <link rel="stylesheet" href="{{ asset('css/registro.css') }}">

</head>
<body>
    <div class="contenedor">
        
        <!-- Panel Izquierdo - Formulario de Registro -->
        <div class="panel-izquierdo">
            <div class="contenido-form">
                <h1>REGISTRARSE</h1>

                @if ($errors->any())
                    <div style="background-color: #fee; border: 2px solid #fcc; border-radius: 8px; padding: 15px; margin-bottom: 20px; color: #c33;">
                        <strong style="display: block; margin-bottom: 10px;">⚠️ Errores de validación:</strong>
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($errors->has('email'))
                    <div style="background-color: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; padding: 15px; margin-bottom: 20px; color: #856404;">
                        <strong>📧 Correo ya registrado:</strong> {{ $errors->first('email') }}
                    </div>
                @endif

                @if ($errors->has('usuario'))
                    <div style="background-color: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; padding: 15px; margin-bottom: 20px; color: #856404;">
                        <strong>👤 Usuario ya registrado:</strong> {{ $errors->first('usuario') }}
                    </div>
                @endif

                @if (session('error'))
                    <div style="background-color: #fee; border: 2px solid #fcc; border-radius: 8px; padding: 15px; margin-bottom: 20px; color: #c33;">
                        <strong>⚠️ Error:</strong> {{ session('error') }}
                    </div>
                @endif

                <form action="/register-data" method="POST" style="width: 100%;">
                    @csrf

                    <div class="grupo-input">
                        <span class="material-icons-outlined icono">person</span>
                        <input type="text" name="nombre" placeholder="Digita tu nombre" required>
                    </div>

                    <div class="grupo-input">
                        <span class="material-icons-outlined icono">person_outline</span>
                        <input type="text" name="usuario" placeholder="Digita tu usuario" value="{{ old('usuario') }}" required>
                        @if ($errors->has('usuario'))
                            <small style="color: #c33; display: block; margin-top: 5px;">{{ $errors->first('usuario') }}</small>
                        @endif
                    </div>

                    <div class="grupo-input">
                        <span class="material-icons-outlined icono">email</span>
                        <input type="email" name="email" placeholder="Digita tu correo electrónico" value="{{ old('email') }}" required>
                        @if ($errors->has('email'))
                            <small style="color: #c33; display: block; margin-top: 5px;">{{ $errors->first('email') }}</small>
                        @endif
                    </div>

                    <div class="grupo-input">
                        <span class="material-icons-outlined icono">lock</span>
                        <input type="password" id="password" name="password" placeholder="Digita tu contraseña" required>
                        <span class="material-icons-outlined icono-ojo" onclick="togglePassword('password', this)">visibility_off</span>
                    </div>

                    <div class="grupo-input">
                        <span class="material-icons-outlined icono">lock</span>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirma tu contraseña" required>
                        <span class="material-icons-outlined icono-ojo" onclick="togglePassword('password_confirmation', this)">visibility_off</span>
                    </div>

                    <button type="submit" class="boton-registrar">Registrarse</button>
                </form>

                <!-- Redes Sociales -->
                <div class="redes-sociales">
                    <a href="#" class="boton-red gmail">
                        <img src="https://cdn.cdnlogo.com/logos/g/24/gmail-icon.svg" alt="Gmail">
                    </a>
                    <a href="#" class="boton-red facebook">
                        <img src="https://cdn.cdnlogo.com/logos/f/83/facebook.svg" alt="Facebook">
                    </a>
                    <a href="#" class="boton-red instagram">
                        <img src="https://cdn.cdnlogo.com/logos/i/92/instagram.svg" alt="Instagram">
                    </a>
                </div>
            </div>
        </div>

        <!-- Panel Derecho - Logo y Botón Login -->
        <div class="panel-derecho">
            <div class="capsula capsula-1"></div>
            <div class="capsula capsula-2"></div>
            <div class="capsula capsula-3"></div>

            <div class="contenido-logo">
                <div class="circulo-logo">
                    <img src="{{ asset('img/logoHackZoneBlancosintitulo.png') }}" alt="Logo HackZone" class="icono-logo">
                </div>
                <div class="texto-logo">BIENVENIDO</div>
                <a href="{{ url('/login') }}" class="boton-ir-login">Ir para login</a>
            </div>
        </div>

    </div>


    <script src="{{ asset('js/registro.js') }}"></script>
    

</body>
</html>