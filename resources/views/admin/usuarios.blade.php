<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Usuarios - HackZone</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-tablas.css') }}">
    <style>
        .btn-exportar {
            transition: all 0.3s ease;
        }
        .btn-exportar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            opacity: 0.9;
        }
    </style>
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

        @if(session('error'))
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="tarjeta-tabla">
            <div class="tabla-header">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="margin: 0;">Usuarios Registrados</h3>
                    <div style="display: flex; gap: 10px;">
                        <a href="{{ route('admin.usuarios.exportar.pdf') }}" 
                           class="btn-exportar" 
                           style="background: #dc3545; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 500; transition: background 0.3s;">
                            <i class="fas fa-file-pdf"></i> Exportar PDF
                        </a>
                        <a href="{{ route('admin.usuarios.exportar.excel') }}" 
                           class="btn-exportar" 
                           style="background: #28a745; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 500; transition: background 0.3s;">
                            <i class="fas fa-file-excel"></i> Exportar Excel
                        </a>
                    </div>
                </div>
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
                            <th>Acciones</th>
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
                            <td>
                                <div class="acciones-btn">
                                    <button class="btn-accion eliminar" onclick="eliminarUsuario({{ $usuario->id }}, '{{ $usuario->name }}')">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="sin-resultados">
                                <i class="fas fa-users" style="font-size: 2rem; color: #ddd; margin-bottom: 10px;"></i>
                                <p>No se encontraron usuarios.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación personalizada --}}
            @if($usuarios->hasPages())
            <div style="margin-top: 30px; display: flex; justify-content: center; align-items: center; gap: 15px;">
                {{-- Botón Anterior --}}
                @if($usuarios->onFirstPage())
                    <button disabled style="padding: 10px 20px; background: #e0e0e0; color: #999; border: none; border-radius: 8px; cursor: not-allowed; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </button>
                @else
                    <a href="{{ $usuarios->previousPageUrl() }}" style="padding: 10px 20px; background: #4a148c; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: background 0.3s;">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </a>
                @endif

                {{-- Botón Siguiente --}}
                @if($usuarios->hasMorePages())
                    <a href="{{ $usuarios->nextPageUrl() }}" style="padding: 10px 20px; background: #4a148c; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: background 0.3s;">
                        Siguiente <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <button disabled style="padding: 10px 20px; background: #e0e0e0; color: #999; border: none; border-radius: 8px; cursor: not-allowed; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                        Siguiente <i class="fas fa-chevron-right"></i>
                    </button>
                @endif
            </div>
            @endif
        </div>
    </main>
</div>

<script>
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        document.querySelector('.admin-sidebar').classList.toggle('collapsed');
        this.querySelector('i').classList.toggle('fa-chevron-left');
        this.querySelector('i').classList.toggle('fa-chevron-right');
    });

    /**
     * Solicita confirmación y envía una petición para eliminar el usuario
     * @param {number} id 
     * @param {string} nombre 
     */
    function eliminarUsuario(id, nombre) {
        if (confirm(`¿Estás seguro de que deseas eliminar al usuario "${nombre}"? Esta acción no se puede deshacer y eliminará todos sus datos relacionados.`)) {
            
            // Creamos un formulario invisible temporalmente para enviar la petición DELETE
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/usuarios/${id}`;
            
            // Token CSRF (Obligatorio en Laravel)
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const token = tokenMeta ? tokenMeta.content : '';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = token;
            
            // Método DELETE spoofing
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';

            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>

</body>
</html>
