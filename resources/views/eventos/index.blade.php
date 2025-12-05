@extends('layouts.inicio')

@section('title', 'Torneos Disponibles')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/eventos.css') }}">
@endpush

@section('content')
    <main class="main-eventos">
        <h1 class="page-title">Torneos Disponibles</h1>

        <section class="search-container">
            <div class="input-wrapper">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" class="search-input" placeholder="Buscar torneos...">
            </div>
            <button class="filter-btn">
                <i class="fa-solid fa-filter"></i> Filtros
            </button>
        </section>

        <section class="cards-grid">
            @foreach($eventos as $evento)
            <article class="card">
                <div class="card-image">
                    @php
                        // Si la imagen contiene 'eventos/' es del formulario (storage)
                        // Si no, es del seeder (public/images)
                        $imagenUrl = str_contains($evento->imagen, 'eventos/') 
                            ? asset('storage/' . $evento->imagen) 
                            : asset('images/' . $evento->imagen);
                    @endphp
                    <img src="{{ $imagenUrl }}" 
                         alt="Banner {{ $evento->titulo }}"
                         onerror="this.src='{{ asset('images/default-event.jpg') }}'">
                </div>
                <div class="card-body">
                    <div class="org-header">
                        <span class="org-icon {{ $evento->org_icon }}"></span> {{ $evento->organizacion }}
                    </div>
                    <h3 class="card-title">{{ $evento->titulo }}</h3>
                    <!-- CORREGIDO: Ahora es descripcion_corta -->
                    <p class="card-desc">{{ $evento->descripcion_corta }}</p>

                    <ul class="info-list">
                        <!-- CORREGIDO: Formato de fecha -->
                        <li><i class="fa-regular fa-calendar"></i> {{ $evento->fecha_inicio->format('d/m/Y') }}</li>
                        <!-- CORREGIDO: Nombre del campo es fecha_limite_inscripcion -->
                        <li><i class="fa-regular fa-clock"></i> Inscripciones hasta: {{ $evento->fecha_limite_inscripcion->format('d/m/Y') }}</li>
                        <li><i class="fa-solid fa-location-dot"></i> {{ $evento->ubicacion }}</li>
                    </ul>

                    <div class="progress-container">
                        <div class="participants-label">
                            <span><i class="fa-solid fa-user-group"></i> Participantes</span>
                            <span>{{ $evento->participantes_actuales }}/{{ $evento->participantes_max }}</span>
                        </div>
                        <div class="progress-bar">
                            @php
                                $porcentaje = $evento->participantes_max > 0 
                                    ? ($evento->participantes_actuales / $evento->participantes_max) * 100 
                                    : 0;
                            @endphp
                            <div class="progress-fill" style="width: {{ $porcentaje }}%"></div>
                        </div>
                    </div>

                    <a href="{{ route('eventos.show', $evento->id) }}" class="btn-card">Ver más Información</a>
                </div>
            </article>
            @endforeach
        </section>
    </main>
@endsection