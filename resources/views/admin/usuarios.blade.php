<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - HackZone</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-tablas.css') }}">
</head>
<body>

@include('components.navbar-admin')

<div class="admin-container">
    @include('components.sidebar-admin')

    <main class="admin-main">
        <h2 class="titulo-pagina">Gestión de Usuarios</h2>

        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="tarjeta-tabla">
            <div class="tabla-header">
                <h3>Usuarios Registrados</h3>
                <div class="stats-row" style="display: flex; gap: 2rem; margin-top: 1rem;">
                    <div style="text-align: center;">
                        <div style="font-size: 2rem; font-weight: 700; color: #4a148c;">{{ $usuarios->where('rol', 'usuario')->count() }}</div>
                        <div style="font-size: 0.85rem; color: #666;">Usuarios</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 2rem; font-weight: 700; color: #1976d2;">{{ $usuarios->where('rol', 'juez')->count() }}</div>
                        <div style="font-size: 0.85rem; color: #666;">Jueces</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 2rem; font-weight: 700; color: #f57c00;">{{ $usuarios->where('rol', 'administrador')->count() }}</div>
                        <div style="font-size: 0.85rem; color: #666;">Administradores</div>
                    </div>
                </div>
            </div>

            <div class="tabla-responsive">
                <table class="tabla-gestion">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Registro</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                        <tr>
                            <td>
                                <div class="info-equipo">
                                    @if($usuario->avatar)
                                        <img src="{{ asset('storage/' . $usuario->avatar) }}" alt="{{ $usuario->name }}" class="avatar-equipo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid {{ $usuario->rol === 'administrador' ? '#f57c00' : ($usuario->rol === 'juez' ? '#1976d2' : '#4a148c') }};">
                                    @else
                                        <div class="avatar-equipo" style="background: {{ $usuario->rol === 'administrador' ? '#f57c00' : ($usuario->rol === 'juez' ? '#1976d2' : '#4a148c') }}; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">
                                            {{ strtoupper(substr($usuario->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div style="margin-left: 12px;">
                                        <span class="nombre-equipo">{{ $usuario->name }}</span>
                                        <small style="display: block; color: #666;">{{ '@' . $usuario->username }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                @if($usuario->rol === 'administrador')
                                    <span class="badge" style="background: #f57c00; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem;">
                                        <i class="fas fa-user-shield"></i> Administrador
                                    </span>
                                @elseif($usuario->rol === 'juez')
                                    <span class="badge" style="background: #1976d2; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem;">
                                        <i class="fas fa-gavel"></i> Juez
                                    </span>
                                @else
                                    <span class="badge" style="background: #4a148c; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem;">
                                        <i class="fas fa-user"></i> Usuario
                                    </span>
                                @endif
                            </td>
                            <td class="dato-fecha">
                                {{ $usuario->created_at ? $usuario->created_at->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                <span class="badge" style="background: #10b981; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem;">
                                    <i class="fas fa-check-circle"></i> Activo
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="sin-resultados">
                                <i class="fas fa-users" style="font-size: 2rem; color: #ddd; margin-bottom: 10px;"></i>
                                <p>No se encontraron usuarios.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        document.querySelector('.admin-sidebar').classList.toggle('collapsed');
        this.querySelector('i').classList.toggle('fa-chevron-left');
        this.querySelector('i').classList.toggle('fa-chevron-right');
    });
</script>

</body>
</html>
