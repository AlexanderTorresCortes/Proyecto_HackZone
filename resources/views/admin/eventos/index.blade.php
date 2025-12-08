<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Eventos - HackZone</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-tablas.css') }}">
</head>
<body>

@include('components.navbar-admin')

<div class="admin-container">
    @include('components.sidebar-admin')

    <main class="admin-main">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2 class="titulo-pagina">Gestión de Eventos</h2>
            <a href="{{ route('admin.eventos.create') }}" class="btn-guardar" style="text-decoration: none;">
                <i class="fas fa-plus-circle"></i> Crear Nuevo Evento
            </a>
        </div>

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
                <h3>Eventos Creados</h3>
            </div>

            <div class="tabla-responsive">
                <table class="tabla-gestion">
                    <thead>
                        <tr>
                            <th>Evento</th>
                            <th>Fecha</th>
                            <th>Ubicación</th>
                            <th>Participantes</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($eventos as $evento)
                        <tr>
                            <td>
                                <div class="info-equipo">
                                    <div class="avatar-equipo" style="background: #4a148c;">
                                        <i class="fas fa-trophy"></i>
                                    </div>
                                    <span class="nombre-equipo">{{ $evento->titulo }}</span>
                                </div>
                            </td>
                            <td class="dato-fecha">
                                {{ $evento->fecha_inicio->format('d/m/Y') }}
                            </td>
                            <td>{{ $evento->ubicacion }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="flex: 1; background: #e0e0e0; border-radius: 10px; height: 8px; overflow: hidden;">
                                        @php
                                            $porcentaje = $evento->participantes_max > 0
                                                ? ($evento->participantes_actuales / $evento->participantes_max) * 100
                                                : 0;
                                        @endphp
                                        <div style="width: {{ $porcentaje }}%; height: 100%; background: #4a148c;"></div>
                                    </div>
                                    <span style="font-size: 0.85rem; color: #666;">
                                        {{ $evento->participantes_actuales }}/{{ $evento->participantes_max }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if($evento->fecha_inicio > now())
                                    <span class="badge" style="background: #3b82f6; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem;">
                                        <i class="fas fa-clock"></i> Próximo
                                    </span>
                                @elseif($evento->fecha_inicio->isToday())
                                    <span class="badge" style="background: #10b981; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem;">
                                        <i class="fas fa-play"></i> En curso
                                    </span>
                                @else
                                    <span class="badge" style="background: #6b7280; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem;">
                                        <i class="fas fa-check"></i> Finalizado
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="acciones-btn">
                                    <a href="{{ route('eventos.show', $evento->id) }}" class="btn-accion ver" target="_blank">Ver</a>
                                    <a href="{{ route('admin.eventos.edit', $evento->id) }}" class="btn-accion editar">Editar</a>
                                    <form action="{{ route('admin.eventos.destroy', $evento->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar este evento?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-accion eliminar">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="sin-resultados">
                                <i class="fas fa-calendar" style="font-size: 2rem; color: #ddd; margin-bottom: 10px;"></i>
                                <p>No hay eventos creados aún.</p>
                                <a href="{{ route('admin.eventos.create') }}" style="color: #4a148c; text-decoration: none; font-weight: 600;">
                                    <i class="fas fa-plus-circle"></i> Crear primer evento
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>
