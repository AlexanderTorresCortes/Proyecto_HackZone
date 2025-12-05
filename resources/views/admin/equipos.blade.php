<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Equipos - HackZone</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-tablas.css') }}">

    <style>
        .modal-content {
            width: 95% !important;
            max-width: 800px !important;
        }
        .input-modal {
            padding: 12px 15px;
        }
        .modal-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>

<div class="admin-container">
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <button class="btn-toggle" id="sidebarToggle">
                <i class="fas fa-chevron-left"></i>
            </button>
            <h2>Panel</h2>
        </div>
        
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-item">
                <i class="fas fa-chart-line"></i>
                <span>Reportes</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-star"></i>
                <span>Evaluaciones</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Carga de archivos</span>
            </a>
            <a href="{{ route('admin.calendario') }}" class="nav-item">
                <i class="far fa-calendar-alt"></i>
                <span>Calendario</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-trophy"></i>
                <span>Sistema de logros</span>
            </a>
            <a href="{{ route('admin.usuarios.index') }}" class="nav-item">
                <i class="fas fa-user-cog"></i>
                <span>Usuarios</span>
            </a>
            <a href="{{ route('admin.equipos.index') }}" class="nav-item highlight">
                <i class="fas fa-users"></i>
                <span>Equipos</span>
            </a>
            <a href="{{ route('admin.eventos.create') }}" class="nav-item">
                <i class="fas fa-plus-circle"></i>
                <span>Crear Evento</span>
            </a>
        </nav>
    </aside>
    
    <main class="admin-main">
        <h2 class="titulo-pagina">Gestión de Equipos</h2>

        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.equipos.index') }}" method="GET" class="buscador-container">
            <div class="input-wrapper">
                <i class="fas fa-search icono-busqueda"></i>
                <input type="text" name="buscar" 
                       placeholder="Buscar por nombre o ID..." 
                       value="{{ request('buscar') }}" 
                       class="input-busqueda">
            </div>
            <button type="submit" class="btn-buscar">Buscar</button>
        </form>

        <div class="tarjeta-tabla">
            <div class="tabla-header">
                <h3>Equipos Registrados</h3>
            </div>
            
            <div class="tabla-responsive">
                <table class="tabla-gestion">
                    <thead>
                        <tr>
                            <th>Equipo</th>
                            <th>ID</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($equipos as $equipo)
                        <tr>
                            <td>
                                <div class="info-equipo">
                                    <div class="avatar-equipo">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <span class="nombre-equipo">{{ $equipo->nombre ?? 'Sin Nombre' }}</span>
                                </div>
                            </td>
                            <td class="dato-id">
                                <span class="badge-id">{{ $equipo->id }}</span>
                            </td>
                            <td class="dato-fecha">
                                {{ $equipo->created_at ? $equipo->created_at->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                <div class="acciones-btn">
                                    <button class="btn-accion ver" onclick="verEquipo({{ $equipo->id }})">Ver</button>
                                    
                                    <button class="btn-accion editar" 
                                       onclick="abrirModalEditar(this)"
                                       data-id="{{ $equipo->id }}"
                                       data-nombre="{{ $equipo->nombre }}"
                                       data-descripcion="{{ $equipo->descripcion }}"
                                       data-miembros_max="{{ $equipo->miembros_max }}"
                                       data-estado="{{ $equipo->estado }}"
                                       data-acceso="{{ $equipo->acceso }}"
                                       data-ubicacion="{{ $equipo->ubicacion }}"
                                       data-torneo="{{ $equipo->torneo }}"
                                    >Editar</button>

                                    <button class="btn-accion eliminar" onclick="eliminarEquipo({{ $equipo->id }}, '{{ $equipo->nombre }}')">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="sin-resultados">
                                <i class="fas fa-search" style="font-size: 2rem; color: #ddd; margin-bottom: 10px;"></i>
                                <p>No se encontraron equipos registrados.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="modalEditar" class="modal-overlay" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3><i class="fas fa-edit"></i> Editar Equipo</h3>
                        <button class="btn-close" type="button" onclick="cerrarModal()">&times;</button>
                    </div>
                    
                    <form id="formEditar" method="POST" action="">
                        @csrf
                        @method('PUT')
                        
                        <div class="modal-body">
                            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                                <div class="form-group" style="width: 100px;">
                                    <label>ID</label>
                                    <input type="text" id="edit_id_display" class="input-modal" disabled style="background-color: #f0f0f0;">
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>Nombre del Equipo</label>
                                    <input type="text" name="nombre" id="edit_nombre" class="input-modal" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Descripción</label>
                                <textarea name="descripcion" id="edit_descripcion" class="input-modal" rows="4" style="resize: vertical;"></textarea>
                            </div>

                            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                                <div class="form-group" style="flex: 1;">
                                    <label>Miembros Máximos</label>
                                    <input type="number" name="miembros_max" id="edit_miembros_max" class="input-modal" required min="1">
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>Estado</label>
                                    <select name="estado" id="edit_estado" class="input-modal">
                                        <option value="Reclutando">Reclutando</option>
                                        <option value="Completo">Completo</option>
                                        <option value="Inactivo">Inactivo</option>
                                    </select>
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>Acceso</label>
                                    <select name="acceso" id="edit_acceso" class="input-modal">
                                        <option value="Público">Público</option>
                                        <option value="Privado">Privado</option>
                                    </select>
                                </div>
                            </div>

                            <div style="display: flex; gap: 20px;">
                                <div class="form-group" style="flex: 1;">
                                    <label>Ubicación / Sede</label>
                                    <input type="text" name="ubicacion" id="edit_ubicacion" class="input-modal">
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>Torneo Asociado</label>
                                    <input type="text" name="torneo" id="edit_torneo" class="input-modal">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                            <button type="submit" class="btn-guardar">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="paginacion-container">
                {{ $equipos->links() }}
            </div>
        </div>
    </main>
</div>

<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.querySelector('.admin-sidebar').classList.toggle('collapsed');
        this.querySelector('i').classList.toggle('fa-chevron-left');
        this.querySelector('i').classList.toggle('fa-chevron-right');
    });
</script>

<script src="{{ asset('js/admin-equipos.js') }}"></script>

</body>
</html>