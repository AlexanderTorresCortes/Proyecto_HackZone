@extends('layouts.inicio')

@section('title', $event->titulo)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/evento-detalle.css') }}">
@endpush

@section('content')
<div class="container-detalle">
    <h1 class="page-title">Información del Evento</h1>

    <div class="grid-layout">
        <div class="main-content">
            <div class="hero-image">
                <img src="{{ asset('images/' . $event->imagen) }}" alt="Banner {{ $event->titulo }}">
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
                        <span class="label">El primero de agosto</span>
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
                        <div class="bar-fill" style="width: {{ ($event->participantes_actuales / $event->participantes_max) * 100 }}%"></div>
                    </div>
                </div>

                <button class="btn-join">Unirse al evento</button>
                
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
                        <span class="amount">${{ $event->premios['1'] ?? 0 }}</span>
                    </div>
                    <div class="prize-item">
                        <i class="fa-solid fa-trophy text-silver"></i>
                        <span>2do lugar</span>
                        <span class="amount">${{ $event->premios['2'] ?? 0 }}</span>
                    </div>
                    <div class="prize-item">
                        <i class="fa-solid fa-trophy text-bronze"></i>
                        <span>3er lugar</span>
                        <span class="amount">${{ $event->premios['3'] ?? 0 }}</span>
                    </div>
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
                            <span>{{ $juez['tag'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </aside>
    </div>
</div>
@endsection