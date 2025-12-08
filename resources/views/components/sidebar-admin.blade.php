<aside class="admin-sidebar">
    <div class="sidebar-header">
        <button class="btn-toggle" id="sidebarToggle">
            <i class="fas fa-chevron-left"></i>
        </button>
        <h2>Panel</h2>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'highlight' : '' }}">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.usuarios.index') }}" class="nav-item {{ request()->routeIs('admin.usuarios.*') ? 'highlight' : '' }}">
            <i class="fas fa-user-cog"></i>
            <span>Usuarios</span>
        </a>
        <a href="{{ route('admin.equipos.index') }}" class="nav-item {{ request()->routeIs('admin.equipos.*') ? 'highlight' : '' }}">
            <i class="fas fa-users"></i>
            <span>Equipos</span>
        </a>
        <a href="{{ route('admin.eventos.index') }}" class="nav-item {{ request()->routeIs('admin.eventos.index') ? 'highlight' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Eventos</span>
        </a>
        <a href="{{ route('admin.eventos.create') }}" class="nav-item {{ request()->routeIs('admin.eventos.create') ? 'highlight' : '' }}">
            <i class="fas fa-plus-circle"></i>
            <span>Crear Evento</span>
        </a>
        <a href="{{ route('admin.calendario') }}" class="nav-item {{ request()->routeIs('admin.calendario') ? 'highlight' : '' }}">
            <i class="far fa-calendar-alt"></i>
            <span>Calendario</span>
        </a>
        <a href="{{ route('admin.evaluaciones') }}" class="nav-item {{ request()->routeIs('admin.evaluaciones') ? 'highlight' : '' }}">
            <i class="fas fa-clipboard-check"></i>
            <span>Evaluaciones</span>
        </a>
        <a href="{{ route('admin.logros') }}" class="nav-item {{ request()->routeIs('admin.logros') ? 'highlight' : '' }}">
            <i class="fas fa-trophy"></i>
            <span>Logros</span>
        </a>
        <a href="{{ route('admin.archivos') }}" class="nav-item {{ request()->routeIs('admin.archivos') ? 'highlight' : '' }}">
            <i class="fas fa-folder-open"></i>
            <span>Archivos</span>
        </a>
    </nav>
</aside>

<script>
document.getElementById('sidebarToggle')?.addEventListener('click', function() {
    document.querySelector('.admin-sidebar').classList.toggle('collapsed');
    this.querySelector('i').classList.toggle('fa-chevron-left');
    this.querySelector('i').classList.toggle('fa-chevron-right');
});
</script>
