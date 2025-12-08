<!-- Navbar Admin Component - HackZone -->
<style>
    /* NAVBAR ADMIN */
    .navbar {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 1rem 2rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.2);
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
        color: #fff;
        font-weight: 700;
        font-size: 1.5rem;
        text-decoration: none;
    }

    .navbar-brand .admin-badge {
        background: #ef4444;
        color: white;
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-weight: 600;
    }

    .navbar-menu {
        display: flex;
        gap: 1.5rem;
        align-items: center;
    }

    /* Estilos del menú móvil y enlaces eliminados mantenidos por si acaso se necesitan en el futuro para el área de usuario */
    .navbar-menu a {
        color: #cbd5e1;
        text-decoration: none;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .user-area {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .notification {
        position: relative;
        cursor: pointer;
        color: #cbd5e1;
        font-size: 1.2rem;
        transition: transform 0.3s;
    }

    .notification:hover {
        transform: scale(1.1);
        color: #fff;
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
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        cursor: pointer;
        border: 2px solid #6366f1;
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
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.5);
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

    .dropdown-header .user-role {
        font-size: 0.75rem;
        color: white;
        background: #ef4444;
        display: inline-block;
        padding: 0.125rem 0.5rem;
        border-radius: 4px;
        margin-top: 0.25rem;
        font-weight: 600;
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
        color: #6366f1;
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

    /* Mobile Menu */
    .mobile-menu-btn {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #cbd5e1;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .navbar-menu {
            display: none;
            position: absolute;
            top: 70px;
            left: 0;
            right: 0;
            background: #1e293b;
            flex-direction: column;
            gap: 0;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            padding: 1rem 0;
            z-index: 999;
        }

        .navbar-menu.active {
            display: flex;
        }

        .user-area {
            margin-left: auto;
            gap: 1rem;
            /* Ajuste para centrar en móvil si es lo único que queda */
            justify-content: center;
            width: 100%;
            padding: 1rem;
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
</style>

<!-- NAVBAR ADMIN -->
<nav class="navbar">
    <a href="{{ route('admin.dashboard') }}" class="navbar-brand">
        <i class="fas fa-shield-alt"></i>
        HackZone
        <span class="admin-badge">ADMIN</span>
    </a>

    <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="navbar-menu" id="navbarMenu">
        
        <!-- ENLACES CENTRALES ELIMINADOS -->
        
        @auth
        <div class="user-area">
            <!-- Notificaciones -->
            <div class="notification" onclick="toggleNotifications()">
                <i class="fas fa-bell"></i>
                <span class="badge">0</span>
            </div>

            <!-- Menú de usuario -->
            <div class="user-menu">
                <div class="user-avatar" onclick="toggleUserMenu()">
                    <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=6366f1&color=fff' }}" alt="{{ Auth::user()->name }}">
                </div>

                <div class="dropdown-menu" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">ADMINISTRADOR</div>
                    </div>

                    <a href="{{ route('perfil.index') }}">
                        <i class="fas fa-user"></i> Mi Perfil
                    </a>

                    <a href="{{ route('admin.calendario') }}">
                        <i class="fas fa-calendar"></i> Calendario
                    </a>

                    <a href="{{ route('inicio.index') }}">
                        <i class="fas fa-eye"></i> Ver sitio público
                    </a>

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
    alert('Sistema de notificaciones para administradores');
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
});
</script>