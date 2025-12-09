<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Entregas - HackZone</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-tablas.css') }}">
</head>
<body>

@include('components.navbar-admin')

<div class="admin-container">
    @include('components.sidebar-admin')

    <main class="admin-main">
        <h2 class="titulo-pagina">
            <i class="fas fa-folder-open"></i> Gestión de Entregas de Proyectos
        </h2>

        <div style="background: #e0f2fe; border-left: 4px solid #0284c7; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
            <div style="display: flex; align-items: start; gap: 1rem;">
                <i class="fas fa-info-circle" style="color: #0369a1; font-size: 1.5rem; margin-top: 0.25rem;"></i>
                <div>
                    <h3 style="color: #0369a1; margin: 0 0 0.5rem 0;">Información Importante</h3>
                    <p style="color: #075985; margin: 0; line-height: 1.6;">
                        La funcionalidad de subida de archivos ahora está disponible para los <strong>líderes de equipos</strong> desde su panel de usuario.
                        Como administrador, puedes visualizar y monitorear todas las entregas desde esta sección.
                    </p>
                </div>
            </div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h3 style="color: #1e293b; margin-bottom: 0.5rem;">Todas las Entregas</h3>
                    <p style="color: #64748b; margin: 0;">Monitoreo de archivos subidos por los equipos</p>
                </div>
                <div style="background: #f1f5f9; padding: 0.75rem 1.5rem; border-radius: 8px;">
                    <span style="font-size: 1.5rem; font-weight: 700; color: #1e293b;">{{ $entregas->count() }}</span>
                    <span style="color: #64748b; margin-left: 0.5rem;">Entregas</span>
                </div>
            </div>

            @if($entregas->isEmpty())
                <div style="text-align: center; padding: 3rem; color: #64748b;">
                    <i class="fas fa-inbox" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                    <h3>No hay entregas aún</h3>
                    <p>Cuando los equipos suban archivos, aparecerán aquí.</p>
                </div>
            @else
                <table class="tabla-gestion">
                    <thead>
                        <tr>
                            <th>Archivo</th>
                            <th>Equipo</th>
                            <th>Evento</th>
                            <th>Subido por</th>
                            <th>Versión</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entregas as $entrega)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        @if($entrega->tipo_archivo == 'zip')
                                            <i class="fas fa-file-archive" style="font-size: 1.5rem; color: #f59e0b;"></i>
                                        @elseif($entrega->tipo_archivo == 'pdf')
                                            <i class="fas fa-file-pdf" style="font-size: 1.5rem; color: #dc2626;"></i>
                                        @else
                                            <i class="fas fa-file-powerpoint" style="font-size: 1.5rem; color: #ea580c;"></i>
                                        @endif
                                        <div>
                                            <div style="font-weight: 500;">{{ $entrega->nombre_archivo }}</div>
                                            <div style="font-size: 0.85rem; color: #64748b;">{{ $entrega->formatted_size }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="color: #4a148c; font-weight: 500;">
                                        <i class="fas fa-users"></i> {{ $entrega->equipo->nombre }}
                                    </span>
                                </td>
                                <td>{{ $entrega->evento->titulo }}</td>
                                <td>{{ $entrega->usuario->name }}</td>
                                <td>
                                    <span style="background: #e0e7ff; color: #4338ca; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem;">
                                        v{{ $entrega->version }}
                                    </span>
                                </td>
                                <td class="dato-fecha">{{ $entrega->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($entrega->estado == 'pendiente')
                                        <span style="background: #fef3c7; color: #92400e; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem;">
                                            <i class="fas fa-clock"></i> Pendiente
                                        </span>
                                    @elseif($entrega->estado == 'aprobado')
                                        <span style="background: #d1fae5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem;">
                                            <i class="fas fa-check-circle"></i> Aprobado
                                        </span>
                                    @else
                                        <span style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem;">
                                            <i class="fas fa-times-circle"></i> Rechazado
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="acciones-btn">
                                        <a href="{{ route('usuario.entregas.download', $entrega->id) }}" class="btn-accion ver" title="Descargar">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </main>
</div>

</body>
</html>
