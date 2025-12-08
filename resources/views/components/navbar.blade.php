<!-- Navbar Component - HackZone -->
<style>
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
        gap: 1.5rem;
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

    .navbar-menu a.active {
        background: #ede9fe;
        color: #6b21a8;
    }

    .user-area {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .notification {
        position: relative;
        cursor: pointer;
        color: #4a0072;
        font-size: 1.2rem;
        transition: transform 0.3s;
    }

    .notification:hover {
        transform: scale(1.1);
        color: #6b21a8;
    }

    .notification-wrapper {
        position: relative;
    }

    .notification .badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #ef4444;
        color: white;
        font-size: 0.7rem;
        font-weight: 600;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .user-menu {
        position: relative;
    }

    .user-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        cursor: pointer;
        border: 2px solid #4a0072;
        transition: all 0.3s;
        overflow: hidden;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .user-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(107, 33, 168, 0.3);
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        top: 55px;
        right: 0;
        background: white;
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        border-radius: 12px;
        min-width: 220px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        animation: slideDown 0.2s ease;
        z-index: 1000;
    }

    .dropdown-menu.active {
        display: block;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-header {
        padding: 1rem;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .dropdown-header .user-name {
        font-weight: 600;
        color: #374151;
        font-size: 0.95rem;
    }

    .dropdown-header .user-email {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 0.25rem;
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
        font-size: 0.9rem;
        transition: background 0.2s;
    }

    .dropdown-menu a:hover,
    .dropdown-menu button:hover {
        background: #f3f4f6;
    }

    .dropdown-menu a i,
    .dropdown-menu button i {
        width: 20px;
        color: #6b21a8;
    }

    .dropdown-divider {
        margin: 0.5rem 0;
        border: none;
        border-top: 1px solid #e5e7eb;
    }

    .dropdown-menu button[type="submit"] {
        color: #dc2626;
    }

    .dropdown-menu button[type="submit"] i {
        color: #dc2626;
    }

    .btn-login {
        background: linear-gradient(135deg, #6b21a8 0%, #9333ea 100%);
        color: white !important;
        padding: 0.6rem 1.5rem !important;
        border-radius: 8px;
        font-weight: 600;
    }

    .btn-login:hover {
        background: linear-gradient(135deg, #581c87 0%, #7e22ce 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(107, 33, 168, 0.3);
    }

    /* Mobile Menu */
    .mobile-menu-btn {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #6b21a8;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .navbar-menu {
            display: none;
            position: absolute;
            top: 70px;
            left: 0;
            right: 0;
            background: white;
            flex-direction: column;
            gap: 0;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 1rem 0;
            z-index: 999;
        }

        .navbar-menu.active {
            display: flex;
        }

        .navbar-menu a {
            width: 100%;
            padding: 1rem 2rem;
            border-radius: 0;
        }

        .user-area {
            margin-left: auto;
            gap: 1rem;
        }

        .mobile-menu-btn {
            display: block;
            margin-left: 1rem;
        }

        .notification {
            font-size: 1.1rem;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
        }
    }

    /* Estilos para el dropdown de notificaciones */
    .notifications-dropdown {
        position: absolute;
        top: 55px;
        right: 0;
        background: white;
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        border-radius: 12px;
        min-width: 400px;
        max-width: 500px;
        max-height: 600px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        z-index: 1000;
        display: none;
    }

    .notifications-dropdown.active {
        display: block;
        animation: slideDown 0.2s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .notifications-header {
        padding: 1rem;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .notifications-header h3 {
        margin: 0;
        font-size: 1.1rem;
        color: #374151;
        font-weight: 600;
    }

    .unread-badge {
        background: #ef4444;
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
    }

    .notifications-list {
        max-height: 500px;
        overflow-y: auto;
    }

    .notification-item {
        padding: 1rem;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.2s;
        position: relative;
    }

    .notification-item:hover {
        background: #f9fafb;
    }

    .notification-item.unread {
        background: #fef3c7;
    }

    .notification-item.unread:hover {
        background: #fde68a;
    }

    .notification-content {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .notification-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
    }

    .notification-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .notification-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .notification-icon.accepted {
        background: #d1fae5;
        color: #059669;
    }

    .notification-icon.rejected {
        background: #fee2e2;
        color: #dc2626;
    }

    .notification-info {
        flex: 1;
        min-width: 0;
    }

    .notification-message {
        margin: 0 0 0.5rem 0;
        color: #374151;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .notification-details {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        font-size: 0.8rem;
        color: #6b7280;
    }

    .notification-details span {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .notification-details i {
        width: 14px;
        color: #9ca3af;
    }

    .solicitante-name, .equipo-name {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .solicitud-mensaje {
        margin-top: 0.5rem;
        padding: 0.5rem;
        background: #f3f4f6;
        border-radius: 6px;
        font-size: 0.85rem;
        color: #4b5563;
        font-style: italic;
    }

    .notification-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid #e5e7eb;
    }

    .btn-accept, .btn-reject {
        flex: 1;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-accept {
        background: #10b981;
        color: white;
    }

    .btn-accept:hover {
        background: #059669;
        transform: translateY(-1px);
    }

    .btn-reject {
        background: #ef4444;
        color: white;
    }

    .btn-reject:hover {
        background: #dc2626;
        transform: translateY(-1px);
    }

    .btn-mark-read {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        font-size: 0.6rem;
        padding: 0.25rem;
        transition: color 0.2s;
    }

    .btn-mark-read:hover {
        color: #6b7280;
    }

    .no-notifications {
        padding: 3rem 1rem;
        text-align: center;
        color: #9ca3af;
    }

    .no-notifications i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .no-notifications p {
        margin: 0;
        font-size: 0.9rem;
    }

    .rol-solicitado {
        margin-top: 0.5rem;
        padding: 0.5rem;
        background: #f3f4f6;
        border-radius: 6px;
        font-size: 0.85rem;
        color: #4b5563;
        font-style: italic;
    }

    .select-rol {
        padding: 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 0.85rem;
        cursor: pointer;
    }

    .miembro-acciones {
        margin-top: 0.5rem;
    }

    .rol-sin-asignar {
        color: #ef4444;
        font-style: italic;
    }

    @media (max-width: 768px) {
        .notifications-dropdown {
            min-width: 300px;
            max-width: 90vw;
        }
    }
</style>

<!-- NAVBAR -->
<nav class="navbar">
    <a href="{{ route('inicio.index') }}" class="navbar-brand">
        <i class="fas fa-shield-alt"></i>
        HackZone
    </a>
    
    <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
        <i class="fas fa-bars"></i>
    </button>
    
    <div class="navbar-menu" id="navbarMenu">
        <a href="{{ route('inicio.index') }}" class="{{ request()->routeIs('inicio.index') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>
        <a href="{{ route('eventos.index') }}" class="{{ request()->routeIs('eventos.*') ? 'active' : '' }}">
            <i class="fas fa-trophy"></i>
            <span>Eventos</span>
        </a>

        {{-- Solo usuarios normales pueden ver Equipos --}}
        @auth
            @if(auth()->user()->isUsuario())
                <a href="{{ route('equipos.index') }}" class="{{ request()->routeIs('equipos.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Equipos</span>
                </a>
            @endif
        @else
            <a href="{{ route('equipos.index') }}" class="{{ request()->routeIs('equipos.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Equipos</span>
            </a>
        @endauth

        @auth
            <a href="{{ route('mensajes.index') }}" class="{{ request()->routeIs('mensajes.*') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i>
                <span>Mensajes</span>
            </a>
        @endauth
        
        @auth
        <div class="user-area">
            <!-- Notificaciones -->
            <div class="notification-wrapper">
                <div class="notification" onclick="toggleNotifications()">
                    <i class="fas fa-bell"></i>
                    @php
                        $unreadCount = auth()->user()->unreadNotifications->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="badge" id="notificationBadge">{{ $unreadCount }}</span>
                    @endif
                </div>
                @livewire('notifications-dropdown')
            </div>
            
            <!-- Menú de usuario -->
            <div class="user-menu">
                <div class="user-avatar" onclick="toggleUserMenu()">
                    @auth
                        <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=4a148c&color=fff' }}" alt="{{ Auth::user()->name }}">
                    @else
                        <img src="https://ui-avatars.com/api/?name=User&background=random" alt="User">
                    @endauth
                </div>
                
                <div class="dropdown-menu" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-email">{{ auth()->user()->email }}</div>
                    </div>
                    
                    <a href="{{ route('perfil.index') }}">
                        <i class="fas fa-user"></i> Mi Perfil
                    </a>

                    <a href="{{ route('mensajes.index') }}">
                        <i class="fas fa-envelope"></i> Mensajes
                    </a>
                    
                    @if(auth()->user()->rol === 'usuario')
                    <a href="{{ route('usuario.dashboard') }}">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                    @endif
                    
                    @if(auth()->user()->rol === 'administrador')
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-cog"></i> Panel Admin
                    </a>
                    @endif
                    
                    @if(auth()->user()->rol === 'juez')
                    <a href="{{ route('juez.dashboard') }}">
                        <i class="fas fa-gavel"></i> Panel Juez
                    </a>
                    @endif
                    
                    <hr class="dropdown-divider">
                    
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @else
        <a href="{{ route('login') }}" class="btn-login">
            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
        </a>
        @endauth
    </div>
</nav>

<script>
// Toggle User Menu
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('active');
}

// Toggle Mobile Menu
function toggleMobileMenu() {
    const menu = document.getElementById('navbarMenu');
    menu.classList.toggle('active');
}

// Toggle Notifications
function toggleNotifications() {
    const dropdown = document.getElementById('notificationsDropdown');
    if (dropdown) {
        dropdown.classList.toggle('active');
    }
}

// Cerrar menús al hacer clic fuera
window.addEventListener('click', function(event) {
    // Cerrar menú de usuario
    if (!event.target.closest('.user-menu') && !event.target.matches('.user-avatar')) {
        const dropdown = document.getElementById('userDropdown');
        if (dropdown && dropdown.classList.contains('active')) {
            dropdown.classList.remove('active');
        }
    }
    
    // Cerrar menú móvil
    if (!event.target.matches('.mobile-menu-btn') && !event.target.matches('.mobile-menu-btn *') &&
        !event.target.closest('.navbar-menu')) {
        const menu = document.getElementById('navbarMenu');
        if (menu && menu.classList.contains('active')) {
            menu.classList.remove('active');
        }
    }
    
    // Cerrar dropdown de notificaciones
    if (!event.target.closest('.notification-wrapper')) {
        const notificationsDropdown = document.getElementById('notificationsDropdown');
        if (notificationsDropdown && notificationsDropdown.classList.contains('active')) {
            notificationsDropdown.classList.remove('active');
        }
    }
});

// Cerrar menú al hacer clic en un enlace (en móvil)
document.querySelectorAll('.navbar-menu a').forEach(link => {
    link.addEventListener('click', () => {
        const menu = document.getElementById('navbarMenu');
        if (menu && menu.classList.contains('active')) {
            menu.classList.remove('active');
        }
    });
});
</script>