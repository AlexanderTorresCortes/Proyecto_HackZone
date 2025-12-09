<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Permisos - HackZone</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-tablas.css') }}">
</head>
<body>

@include('components.navbar-admin')

<div class="admin-container">
    @include('components.sidebar-admin')

    <main class="admin-main">
        @if(session('success'))
        <div class="alert alert-success" style="background: #d1fae5; color: #065f46; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem; border: 1px solid #10b981;">
            <i class="fas fa-check-circle" style="font-size: 1.25rem;"></i>
            <span style="font-weight: 600;">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-error" style="background: #fee2e2; color: #991b1b; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem; border: 1px solid #ef4444;">
            <i class="fas fa-exclamation-circle" style="font-size: 1.25rem;"></i>
            <span style="font-weight: 600;">{{ session('error') }}</span>
        </div>
        @endif

        <h2 class="titulo-pagina">
            <i class="fas fa-user-shield"></i> Gestión de Permisos y Roles
        </h2>
        <p style="color: #64748b; margin-bottom: 2rem;">Administra los roles de los usuarios del sistema</p>

        <div class="tarjeta-contenedor" style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

            <div style="margin-bottom: 2rem;">
                <h3 style="color: #667eea; margin-bottom: 1rem;">
                    <i class="fas fa-gavel"></i> Asignar rol de Juez
                </h3>
                <p style="color: #64748b; margin-bottom: 1.5rem;">
                    Selecciona usuarios para asignarles el rol de juez. Los jueces podrán evaluar equipos en los eventos.
                </p>

                <form action="{{ route('admin.permisos.asignar-juez') }}" method="POST" style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px;">
                    @csrf

                    <div style="margin-bottom: 1.5rem;">
                        <label for="usuario_id" style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">
                            Seleccionar Usuario
                        </label>
                        <select name="usuario_id" id="usuario_id" required
                                style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 1rem;">
                            <option value="">-- Selecciona un usuario --</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}">
                                    {{ $usuario->name }} ({{ $usuario->email }}) - Rol actual: {{ ucfirst($usuario->rol) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                            style="background: #667eea; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-user-check"></i>
                        Asignar como Juez
                    </button>
                </form>
            </div>

            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 2rem 0;">

            <div>
                <h3 style="color: #667eea; margin-bottom: 1rem;">
                    <i class="fas fa-users-cog"></i> Usuarios Actuales
                </h3>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f1f5f9; border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569;">ID</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569;">Nombre</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569;">Email</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569;">Rol Actual</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $usuario)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 1rem;">{{ $usuario->id }}</td>
                                <td style="padding: 1rem;">{{ $usuario->name }}</td>
                                <td style="padding: 1rem;">{{ $usuario->email }}</td>
                                <td style="padding: 1rem;">
                                    <span style="padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;
                                                 background: {{ $usuario->rol === 'administrador' ? '#e0e7ff' : ($usuario->rol === 'juez' ? '#fef3c7' : '#dbeafe') }};
                                                 color: {{ $usuario->rol === 'administrador' ? '#4338ca' : ($usuario->rol === 'juez' ? '#92400e' : '#1e40af') }};">
                                        {{ ucfirst($usuario->rol) }}
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    @if($usuario->rol !== 'administrador')
                                    <form action="{{ route('admin.permisos.cambiar-rol') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="usuario_id" value="{{ $usuario->id }}">

                                        @if($usuario->rol === 'juez')
                                            <input type="hidden" name="nuevo_rol" value="usuario">
                                            <button type="submit"
                                                    style="background: #f59e0b; color: white; padding: 0.5rem 1rem; border: none; border-radius: 6px; font-size: 0.85rem; cursor: pointer;">
                                                <i class="fas fa-user"></i> Cambiar a Usuario
                                            </button>
                                        @else
                                            <input type="hidden" name="nuevo_rol" value="juez">
                                            <button type="submit"
                                                    style="background: #10b981; color: white; padding: 0.5rem 1rem; border: none; border-radius: 6px; font-size: 0.85rem; cursor: pointer;">
                                                <i class="fas fa-gavel"></i> Convertir a Juez
                                            </button>
                                        @endif
                                    </form>
                                    @else
                                    <span style="color: #94a3b8; font-size: 0.85rem;">
                                        <i class="fas fa-lock"></i> Protegido
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<script src="{{ asset('js/admin-dashboard.js') }}"></script>

</body>
</html>
