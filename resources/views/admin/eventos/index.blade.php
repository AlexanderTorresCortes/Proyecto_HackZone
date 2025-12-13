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
                                @php
                                    $hoy = now();
                                    $fechaInicio = $evento->fecha_inicio;
                                    $esProximo = $fechaInicio > $hoy && !$evento->estaFinalizado();
                                    $esHoy = $fechaInicio->isToday() && !$evento->estaFinalizado();
                                    $estaFinalizado = $evento->estaFinalizado();
                                @endphp
                                
                                @if($estaFinalizado)
                                    <div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); color: white; border-radius: 20px; font-size: 0.875rem; font-weight: 600; box-shadow: 0 2px 8px rgba(107, 114, 128, 0.3);">
                                        <i class="fas fa-check-circle" style="font-size: 0.9rem;"></i>
                                        <span>Finalizado</span>
                                    </div>
                                @elseif($esHoy)
                                    <div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 20px; font-size: 0.875rem; font-weight: 600; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);">
                                        <i class="fas fa-play-circle" style="font-size: 0.9rem;"></i>
                                        <span>En Curso</span>
                                    </div>
                                @else
                                    <div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border-radius: 20px; font-size: 0.875rem; font-weight: 600; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);">
                                        <i class="fas fa-clock" style="font-size: 0.9rem;"></i>
                                        <span>Próximo</span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="acciones-btn">
                                    <a href="{{ route('eventos.show', $evento->id) }}" class="btn-accion ver" target="_blank">Ver</a>
                                    <a href="{{ route('admin.eventos.edit', $evento->id) }}" class="btn-accion editar">Editar</a>
                                    @if($evento->estaFinalizado())
                                        <button type="button" class="btn-accion" style="background: #9ca3af; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: not-allowed; font-size: 0.875rem; opacity: 0.6;" disabled>
                                            <i class="fas fa-trophy"></i> Finalizado
                                        </button>
                                    @else
                                        <form action="{{ route('admin.eventos.finalizar', $evento->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de finalizar este evento? Se enviarán certificados a los ganadores (1°, 2° y 3° lugar).');">
                                            @csrf
                                            <button type="submit" class="btn-accion" style="background: #10b981; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem; transition: background 0.3s;">
                                                <i class="fas fa-trophy"></i> Finalizar
                                            </button>
                                        </form>
                                    @endif
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

            {{-- Paginación personalizada --}}
            @if($eventos->hasPages())
            <div style="margin-top: 30px; display: flex; justify-content: center; align-items: center; gap: 15px;">
                {{-- Botón Anterior --}}
                @if($eventos->onFirstPage())
                    <button disabled style="padding: 10px 20px; background: #e0e0e0; color: #999; border: none; border-radius: 8px; cursor: not-allowed; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </button>
                @else
                    <a href="{{ $eventos->previousPageUrl() }}" style="padding: 10px 20px; background: #4a148c; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: background 0.3s;">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </a>
                @endif

                {{-- Botón Siguiente --}}
                @if($eventos->hasMorePages())
                    <a href="{{ $eventos->nextPageUrl() }}" style="padding: 10px 20px; background: #4a148c; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: background 0.3s;">
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

</body>
</html>
