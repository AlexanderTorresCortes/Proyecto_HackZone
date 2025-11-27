<!-- Navbar Compartido -->
<header class="header-navbar">
    <div class="logo-area">
        <div class="hamburger"><i class="fa-solid fa-bars"></i></div>
        <div class="brand">
            <i class="fa-solid fa-shield-halved"></i> HackZone
        </div>
    </div>

    <nav>
        <ul>
            <li>
                <a href="{{ route('inicio.index') }}" class="{{ request()->routeIs('inicio.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i> Inicio
                </a>
            </li>
            <li>
                <a href="{{ route('eventos.index') }}" class="{{ request()->routeIs('eventos.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-trophy"></i> Eventos
                </a>
            </li>
            <li>
                <a href="#" class="">
                    <i class="fa-solid fa-users"></i> Equipos
                </a>
            </li>
            <li>
                <a href="#" class="">
                    <i class="fa-regular fa-envelope"></i> Mensajes
                </a>
            </li>
        </ul>
    </nav>

    <div class="user-area">
        <div class="notification">
            <i class="fa-regular fa-bell"></i>
            <span class="badge">3</span>
        </div>
        <div class="avatar">
            @auth
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4a148c&color=fff" alt="{{ Auth::user()->name }}">
            @else
                <img src="https://ui-avatars.com/api/?name=User&background=random" alt="User">
            @endauth
        </div>
    </div>
</header>
