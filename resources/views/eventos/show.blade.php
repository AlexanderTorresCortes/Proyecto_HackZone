@extends('layouts.inicio')

@section('title', $event->titulo)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/evento-detalle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/evento-show-modal.css') }}">
@endpush

@section('content')
<div class="container-detalle">
    <h1 class="page-title">Información del Evento</h1>

    <div class="grid-layout">
        <div class="main-content">
            <div class="hero-image">
                @php
                    // Si contiene 'eventos/' es del formulario (storage)
                    // Si no, es del seeder (public/images)
                    $imagenUrl = str_contains($event->imagen, 'eventos/') 
                        ? asset('storage/' . $event->imagen) 
                        : asset('images/' . $event->imagen);
                @endphp
                <img src="{{ $imagenUrl }}" 
                     alt="Banner {{ $event->titulo }}"
                     onerror="this.src='{{ asset('images/default-event.jpg') }}'">
            </div>

            <div class="event-header">
                <h2 class="event-title">{{ $event->titulo }}</h2>
                <div class="organizer">
                    <span class="organized-by">Organizado</span>
                    <div class="social-icons">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" alt="Google" width="20">
                        <i class="fa-brands fa-facebook text-blue-600"></i>
                        <i class="fa-brands fa-instagram text-pink-600"></i>
                    </div>
                </div>
                <div class="brand-badge">
                    <i class="{{ $event->org_icon }} fa-2x"></i> 
                    <span>{{ $event->organizacion }}</span>
                </div>
                <p class="intro-text">{{ $event->descripcion_corta }}</p>
            </div>

            <div class="content-box">
                <h3>Descripción completa</h3>
                <p>{{ $event->descripcion_larga }}</p>
            </div>

            <div class="content-box">
                <h3>Requisitos</h3>
                <ul class="requirements-list">
                    @foreach($event->requisitos as $req)
                        <li>
                            <i class="fa-solid fa-circle-check"></i> 
                            {{ $req }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <aside class="sidebar">
            
            <div class="sidebar-card participate-card">
                <h3 class="sidebar-title text-blue">Participar</h3>
                
                <div class="info-row">
                    <i class="fa-regular fa-calendar"></i>
                    <div>
                        <span class="label">Fecha del evento</span>
                        <span class="value">{{ $event->fecha_inicio->format('d/m/Y') }}</span>
                    </div>
                </div>

                <div class="info-row">
                    <i class="fa-regular fa-clock"></i>
                    <div>
                        <span class="label">Inscripciones hasta:</span>
                        <span class="value">{{ $event->fecha_limite_inscripcion->format('d M Y') }}</span>
                    </div>
                </div>

                <div class="info-row">
                    <i class="fa-solid fa-location-dot"></i>
                    <div>
                        <span class="value">{{ $event->ubicacion }}</span>
                    </div>
                </div>

                <div class="participants-progress">
                    <div class="icons">
                        <i class="fa-solid fa-user-group"></i>
                    </div>
                    <div class="bar-container">
                        @php
                            $porcentaje = $event->participantes_max > 0 
                                ? ($event->participantes_actuales / $event->participantes_max) * 100 
                                : 0;
                        @endphp
                        <div class="bar-fill" style="width: {{ $porcentaje }}%"></div>
                    </div>
                </div>

                @auth
                    @if(auth()->user()->isUsuario())
                        @php
                            // Obtener equipos donde el usuario es líder
                            $misEquipos = auth()->user()->equiposComoLider;
                            // Verificar si alguno de mis equipos ya está inscrito
                            $equipoInscrito = $misEquipos->where('event_id', $event->id)->first();
                        @endphp

                        @if($equipoInscrito)
                            <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; text-align: center; margin-bottom: 1rem;">
                                <i class="fas fa-check-circle"></i>
                                <strong>¡Inscrito!</strong> Tu equipo "{{ $equipoInscrito->nombre }}" está registrado
                            </div>
                        @elseif(!$event->inscripcionesAbiertas())
                            <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; text-align: center; margin-bottom: 1rem;">
                                <i class="fas fa-times-circle"></i>
                                <strong>Inscripciones Cerradas</strong><br>
                                <small>Las inscripciones para este evento ya finalizaron</small>
                            </div>
                        @elseif($misEquipos->count() > 0)
                            <button type="button" class="btn-join" onclick="mostrarModalInscripcion()">
                                Inscribir equipo al evento
                            </button>
                        @else
                            <a href="{{ route('equipos.create') }}" class="btn-join" style="display: block; text-decoration: none;">
                                Crear equipo para participar
                            </a>
                        @endif
                    @elseif(auth()->user()->isAdmin())
                        <div style="background: #e0e7ff; color: #4338ca; padding: 1rem; border-radius: 8px; text-align: center;">
                            <i class="fas fa-user-shield"></i>
                            <strong>Vista de Administrador</strong>
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="btn-join" style="display: block; text-decoration: none; margin-top: 1rem;">
                            <i class="fas fa-cog"></i> Ir a Panel de Administración
                        </a>
                    @elseif(auth()->user()->isJuez())
                        <div style="background: #fef3c7; color: #92400e; padding: 1rem; border-radius: 8px; text-align: center;">
                            <i class="fas fa-gavel"></i>
                            <strong>Vista de Juez</strong>
                        </div>
                        <a href="{{ route('juez.dashboard') }}" class="btn-join" style="display: block; text-decoration: none; margin-top: 1rem;">
                            <i class="fas fa-clipboard-check"></i> Ir a Panel de Evaluación
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn-join" style="display: block; text-decoration: none;">
                        Iniciar sesión para participar
                    </a>
                @endauth

                <p class="warning-text">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <strong>Importante:</strong> Las inscripciones se cierran el {{ $event->fecha_limite_inscripcion->format('d M Y') }}. ¡No te quedes sin tu lugar!
                </p>
            </div>

            <div class="sidebar-card">
                <h3 class="sidebar-title text-blue">Premio</h3>
                <div class="prizes-list">
                    <div class="prize-item">
                        <i class="fa-solid fa-trophy text-gold"></i>
                        <span>1er lugar</span>
                        <span class="amount">{{ $event->premios['1'] ?? 'Por definir' }}</span>
                    </div>
                    <div class="prize-item">
                        <i class="fa-solid fa-trophy text-silver"></i>
                        <span>2do lugar</span>
                        <span class="amount">{{ $event->premios['2'] ?? 'Por definir' }}</span>
                    </div>
                    <div class="prize-item">
                        <i class="fa-solid fa-trophy text-bronze"></i>
                        <span>3er lugar</span>
                        <span class="amount">{{ $event->premios['3'] ?? 'Por definir' }}</span>
                    </div>
                </div>
            </div>

            <div class="sidebar-card">
                <h3 class="sidebar-title text-blue">Resultados y Ranking</h3>
                <div style="text-align: center; padding: 1rem 0;">
                    <i class="fa-solid fa-medal" style="font-size: 3rem; color: #FFD700; margin-bottom: 1rem;"></i>
                    <p style="color: #666; margin-bottom: 1.5rem; font-size: 0.95rem;">
                        Consulta el ranking de equipos y las puntuaciones de los jueces
                    </p>
                    <a href="{{ route('eventos.resultados', $event->id) }}" class="btn-join" style="display: block; text-decoration: none;">
                        <i class="fa-solid fa-trophy"></i> Ver Ranking Completo
                    </a>
                </div>
            </div>

            <div class="sidebar-card">
                <h3 class="sidebar-title text-blue">Cronograma</h3>
                <div class="timeline">
                    @foreach($event->cronograma as $actividad)
                    <div class="timeline-item">
                        <span class="time-pill">{{ $actividad['hora'] }}</span>
                        <span class="activity-name">{{ $actividad['actividad'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="sidebar-card">
                <h3 class="sidebar-title text-blue">Jueces</h3>
                <div class="judges-list">
                    @foreach($event->jueces as $juez)
                    <div class="judge-item">
                        <div class="judge-avatar">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($juez['nombre']) }}&background=random" alt="{{ $juez['nombre'] }}">
                        </div>
                        <div class="judge-info">
                            <h4>{{ $juez['nombre'] }}</h4>
                            <p>{{ $juez['rol'] }}</p>
                        </div>
                        <div class="judge-tag">
                            <span>{{ implode(', ', $juez['tags']) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            @if($event->criteriosEvaluacion && $event->criteriosEvaluacion->count() > 0)
            <div class="sidebar-card">
                <h3 class="sidebar-title text-blue">Criterios de Evaluación</h3>
                <div class="criterios-list-public">
                    @foreach($event->criteriosEvaluacion->sortBy('orden') as $criterio)
                    <div class="criterio-item-public">
                        <div class="criterio-header-public">
                            <h4><i class="fas fa-star"></i> {{ $criterio->nombre }}</h4>
                            <span class="criterio-peso">Peso: {{ $criterio->peso }}/10</span>
                        </div>
                        @if($criterio->descripcion)
                            <p class="criterio-descripcion">{{ $criterio->descripcion }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </aside>
    </div>
</div>

@auth
@if(auth()->user()->isUsuario() && isset($misEquipos) && $misEquipos->count() > 0 && !$equipoInscrito)
<!-- Modal de selección de equipo -->
<div id="modalInscripcion" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; max-width: 500px; width: 90%; padding: 2rem; position: relative;">
        <button onclick="cerrarModalInscripcion()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #666;">&times;</button>

        <h2 style="color: var(--primary-purple); margin-bottom: 0.5rem;">Inscribir Equipo</h2>
        <p style="color: #666; margin-bottom: 1.5rem;">Selecciona el equipo con el que participarás en este evento</p>

        <form action="{{ route('eventos.inscribir', $event->id) }}" method="POST">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                @foreach($misEquipos as $equipo)
                    <label style="display: block; padding: 1rem; border: 2px solid #e0e0e0; border-radius: 8px; margin-bottom: 0.75rem; cursor: pointer; transition: all 0.3s;" class="equipo-option">
                        <input type="radio" name="equipo_id" value="{{ $equipo->id }}" required style="margin-right: 0.75rem;">
                        <strong>{{ $equipo->nombre }}</strong>
                        <span style="display: block; font-size: 0.85rem; color: #666; margin-top: 0.25rem;">
                            <i class="fas fa-users"></i> {{ $equipo->miembros_actuales }}/{{ $equipo->miembros_max }} miembros
                        </span>
                    </label>
                @endforeach
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="button" onclick="cerrarModalInscripcion()" style="flex: 1; padding: 0.75rem; background: #e0e0e0; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Cancelar
                </button>
                <button type="submit" style="flex: 1; padding: 0.75rem; background: var(--primary-purple); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Inscribir Equipo
                </button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/evento-show-modal.js') }}"></script>
@endif
@endauth

@endsection