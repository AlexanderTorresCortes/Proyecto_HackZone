@extends('layouts.inicio')

@section('title', 'Eventos de Programación')

@section('content')
    <header class="hero">
        <div class="contenedor">
            <h1>Descubre y Participa en los Mejores Eventos de Programación</h1>
            <p>Conecta con desarrolladores, diseñadores y emprendedores. Forma equipos increíbles y contruye el futuro</p>
        </div>
    </header>
    <section class="seccion-carrusel">
        <div class="contenedor">
            <div class="carrusel">
                <button class="boton-carrusel izq" id="btnAnterior">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>

                <div class="carrusel-contenedor">
                    <div class="carrusel-slides" id="carruselSlides">
                        @foreach($slides as $index => $slide)
                        <div class="slide {{ $index === 0 ? 'activo' : '' }}">
                            @php
                                $imagenSrc = (str_starts_with($slide['imagen'], 'http://') || str_starts_with($slide['imagen'], 'https://'))
                                    ? $slide['imagen']
                                    : asset('images/anuncios/' . $slide['imagen']);
                            @endphp

                            @if(isset($slide['link']) && $slide['link'])
                                <a href="{{ $slide['link'] }}" class="slide-link">
                                    <img src="{{ $imagenSrc }}"
                                         alt="{{ $slide['titulo'] ?? 'Anuncio ' . ($index + 1) }}"
                                         class="slide-imagen-completa">
                                </a>
                            @else
                                <img src="{{ $imagenSrc }}"
                                     alt="{{ $slide['titulo'] ?? 'Anuncio ' . ($index + 1) }}"
                                     class="slide-imagen-completa">
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <button class="boton-carrusel der" id="btnSiguiente">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>

                <div class="indicadores" id="indicadores"></div>
            </div>
        </div>
    </section>

    <!-- Estadísticas -->
    <section class="seccion-stats">
        <div class="contenedor">
            <div class="tarjetas-stats">
                @foreach($stats as $stat)
                <div class="tarjeta-stat morada">
                    <div class="icono-stat">
                        @if($stat['icon'] === 'shield')
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="currentColor">
                            <path d="M24 4L6 10v12c0 11.11 7.67 21.47 18 24 10.33-2.53 18-12.89 18-24V10L24 4zm-2 32l-8-8 2.83-2.83L22 30.34l11.17-11.17L36 22 22 36z"/>
                        </svg>
                        @elseif($stat['icon'] === 'users')
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="currentColor">
                            <path d="M24 4C12.95 4 4 12.95 4 24s8.95 20 20 20 20-8.95 20-20S35.05 4 24 4zm0 6c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6 2.69-6 6-6zm0 28.4c-5 0-9.42-2.56-12-6.44.06-3.98 8-6.16 12-6.16s11.94 2.18 12 6.16c-2.58 3.88-7 6.44-12 6.44z"/>
                        </svg>
                        @elseif($stat['icon'] === 'chart')
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="currentColor">
                            <path d="M20 12H8v28h12V12zm12 28h-8V8h8v32zm8 0h-4V20h4v20z"/>
                        </svg>
                        @endif
                    </div>
                    <div class="numero-stat">{{ $stat['number'] }}</div>
                    <div class="texto-stat">{{ $stat['label'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
