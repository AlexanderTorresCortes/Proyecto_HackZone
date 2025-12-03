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
                    <img src="{{ asset('images/' . $evento['imagen']) }}" alt="Banner {{ $evento['titulo'] }}">
                </div>
                <div class="card-body">
                    <div class="org-header">
                        <span class="org-icon {{ $evento['org_icon'] }}"></span> {{ $evento['organizacion'] }}
                    </div>
                    <h3 class="card-title">{{ $evento['titulo'] }}</h3>
                    <p class="card-desc">{{ $evento['descripcion'] }}</p>

                    <ul class="info-list">
                        <li><i class="fa-regular fa-calendar"></i> {{ $evento['fecha'] }}</li>
                        <li><i class="fa-regular fa-clock"></i> Inscripciones hasta: {{ $evento['fecha_limite'] }}</li>
                        <li><i class="fa-solid fa-location-dot"></i> {{ $evento['ubicacion'] }}</li>
                    </ul>

                    <div class="progress-container">
                        <div class="participants-label">
                            <span><i class="fa-solid fa-user-group"></i> Participantes</span>
                            <span>{{ $evento['participantes_actuales'] }}/{{ $evento['participantes_max'] }}</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ ($evento['participantes_actuales'] / $evento['participantes_max']) * 100 }}%"></div>
                        </div>
                    </div>

                    <a href="{{ route('eventos.show', $evento['id']) }}" class="btn-card">Ver más Información</a>
                </div>
            </article>
            @endforeach
        </section>
    </main>
@endsection
