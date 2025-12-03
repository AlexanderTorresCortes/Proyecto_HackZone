@extends('layouts.inicio')

@section('title', 'Gestión de Equipos')

@section('content')

{{-- ESTILOS ESPECÍFICOS (Solo afectan a esta sección) --}}
<style>
    /* Contenedor principal ajustado a tu diseño */
    .equipos-section {
        padding: 40px 0;
        background-color: #fff;
        min-height: 80vh;
    }

    .contenedor-equipos {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Título y Header */
    .header-equipos {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .header-equipos h1 {
        color: #4A148C;
        /* Morado oscuro */
        font-size: 2.2rem;
        margin-bottom: 5px;
        font-weight: 700;
    }

    .header-equipos p {
        color: #666;
        margin-top: 0;
    }

    /* Botón Crear Equipo (Estilo "Píldora" morada) */
    .btn-crear-equipo {
        background-color: #4A148C;
        color: white;
        padding: 10px 25px;
        border-radius: 50px;
        /* Bordes redondos */
        text-decoration: none;
        font-weight: bold;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(74, 20, 140, 0.3);
        transition: transform 0.2s;
    }

    .btn-crear-equipo:hover {
        transform: translateY(-2px);
    }

    /* Filtros y Buscador */
    .filtros-container {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
    }

    .input-busqueda {
        flex-grow: 1;
        padding: 12px 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        background: #fff;
        font-size: 1rem;
    }

    .select-filtro {
        padding: 10px 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        background: white;
        color: #555;
    }

    /* Pestañas (Tabs) estilo morado */
    .tabs-bar {
        display: flex;
        background-color: #9575CD;
        /* Morado claro */
        border-radius: 10px 10px 0 0;
        padding: 8px;
        gap: 10px;
    }

    .tab-item {
        flex: 1;
        text-align: center;
        padding: 10px;
        color: white;
        cursor: pointer;
        border-radius: 8px;
        background: transparent;
        border: none;
        font-weight: 500;
        transition: background 0.3s;
    }

    .tab-item.active {
        background-color: white;
        color: #4A148C;
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Tarjetas de Equipo */
    .lista-equipos {
        border: 1px solid #eee;
        border-top: none;
        padding: 20px;
        border-radius: 0 0 10px 10px;
    }

    .card-equipo {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        position: relative;
    }

    .card-equipo h3 {
        color: #311B92;
        margin: 0 0 5px 0;
        font-size: 1.4rem;
    }

    /* Badge estado */
    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-reclutando {
        background-color: #C8E6C9;
        color: #2E7D32;
        border: 1px solid #A5D6A7;
    }

    /* Indicador miembros (Flecha) */
    .indicador-miembros {
        position: absolute;
        top: 20px;
        right: 20px;
        background: #fff;
        border: 1px solid #ddd;
        padding: 5px 15px;
        font-size: 0.85rem;
        color: #666;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        /* Forma decorativa simple */
        border-radius: 4px;
        border-bottom: 3px solid #ccc;
    }

    /* Botones de acción en tarjeta */
    .acciones-card {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
    }

    .btn-detalles {
        background: white;
        color: #333;
        border: 1px solid #ccc;
        padding: 8px 20px;
        border-radius: 20px;
        cursor: pointer;
    }

    .btn-unirse {
        background: #4A148C;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* === ESTILOS DEL MODAL (Idénticos a tu imagen) === */
    .modal-backdrop {
        display: none;
        /* Oculto por defecto */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(50, 0, 100, 0.5);
        /* Fondo morado oscuro semi-transparente */
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .modal-caja {
        background: white;
        width: 100%;
        max-width: 450px;
        padding: 40px 30px;
        border-radius: 40px;
        /* Bordes muy curvos */
        border: 2px solid #6A1B9A;
        /* Borde morado grueso */
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .modal-titulo {
        color: #4A148C;
        font-size: 1.8rem;
        margin-bottom: 30px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Inputs del Modal */
    .input-wrapper {
        position: relative;
        margin-bottom: 20px;
    }

    .input-wrapper i {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #555;
        font-size: 1.1rem;
    }

    .modal-field {
        width: 100%;
        padding: 15px 15px 15px 50px;
        /* Espacio para el icono */
        border-radius: 30px;
        /* Forma de píldora */
        border: none;
        background: #fff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        /* Sombra difusa */
        font-size: 1rem;
        color: #333;
        outline: none;
    }

    .modal-field::placeholder {
        color: #999;
    }

    /* Botón Registrarse del Modal */
    .btn-modal-submit {
        background: white;
        color: #4A148C;
        border: 2px solid #f0f0f0;
        /* Borde sutil */
        padding: 12px 50px;
        border-radius: 30px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        margin-top: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
    }

    .btn-modal-submit:hover {
        background: #f9f9f9;
        transform: translateY(-2px);
    }
    select.modal-field {
        appearance: none;
        /* Quita el estilo por defecto del navegador */
        -webkit-appearance: none;
        -moz-appearance: none;
        background-color: white;
        cursor: pointer;
        /* Flechita personalizada simple */
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23555' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 15px;
    }
</style>

<div class="equipos-section">
    <div class="contenedor-equipos">

        {{-- Alerta de éxito --}}
        @if(session('success'))
        <div style="background: #D4EDDA; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #C3E6CB;">
            {{ session('success') }}
        </div>
        @endif

        {{-- Header --}}
        <div class="header-equipos">
            <div>
                <h1>Gestión de Equipos</h1>
                <p>Encuentra equipos para unirte o crea tu propio equipo para participar en hackatons</p>
            </div>
            <button class="btn-crear-equipo" onclick="abrirModal()">
                <i class="fas fa-plus-circle"></i> Crear Equipo
            </button>
        </div>

        {{-- Filtros --}}
        <div class="filtros-container">
            <input type="text" class="input-busqueda" placeholder="Buscar equipos por nombre, descripción o tecnologías...">
            <select class="select-filtro">
                <option>Todos los torneos</option>
            </select>
            <select class="select-filtro">
                <option>Todos los roles</option>
            </select>
        </div>

        {{-- Pestañas --}}
        <div class="tabs-bar">
            <button class="tab-item active" onclick="filtrar('disponibles', this)">Equipos disponibles ({{ count($equipos) }})</button>
            <button class="tab-item" onclick="filtrar('mios', this)">Mis equipos</button>
            <button class="tab-item" onclick="filtrar('todos', this)">Todos los equipos</button>
        </div>

        {{-- Lista de Equipos --}}
        <div class="lista-equipos" id="contenedor-tarjetas">
            @foreach($equipos as $equipo)
            @php
            $esMio = (Auth::check() && $equipo->user_id == Auth::id());
            @endphp

            <div class="card-equipo tarjeta-item" data-mio="{{ $esMio ? 'si' : 'no' }}">
                <div class="indicador-miembros">
                    {{ $equipo->miembros_actuales }}/{{ $equipo->miembros_max }} Miembros
                </div>

                <h3>{{ $equipo->nombre }} <span class="badge badge-reclutando">Reclutando</span></h3>
                <p style="color: #444; margin-bottom: 5px;">{{ $equipo->descripcion }}</p>

                <div style="color: #777; font-size: 0.9rem; margin: 10px 0;">
                    <span style="margin-right: 15px;"><i class="fas fa-trophy"></i> {{ $equipo->torneo }}</span>
                    <span style="margin-right: 15px;"><i class="fas fa-map-marker-alt"></i> {{ $equipo->ubicacion }}</span>
                    <span><i class="far fa-calendar-alt"></i> Creado {{ $equipo->created_at->format('d/m/Y') }}</span>
                </div>

                <div style="margin-top: 10px;">
                    <strong style="color: #4A148C; font-size: 0.9rem;">Roles Disponibles:</strong><br>
                    <span style="background: #4A148C; color: white; padding: 4px 12px; border-radius: 15px; font-size: 0.8rem; display: inline-block; margin-top: 5px;">Diseñador</span>
                </div>

                <div class="acciones-card">
                    <button class="btn-detalles">Ver Detalles</button>
                    @if(!$esMio)
                    <button class="btn-unirse"><i class="fas fa-user-plus"></i> Solicitar Unirse</button>
                    @else
                    <button class="btn-unirse" style="background:#555">Gestionar</button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

    </div>
</div>

{{-- MODAL FLOTANTE (Oculto por defecto) --}}
<div id="modalRegistro" class="modal-backdrop" onclick="cerrarModal(event)">
    <div class="modal-caja">
        <h2 class="modal-titulo">REGISTRAR EQUIPO</h2>

        <form action="{{ route('equipos.store') }}" method="POST">
            @csrf

            <div class="input-wrapper">
                <i class="far fa-user"></i>
                <input type="text" name="nombre" class="modal-field" placeholder="Nombre del equipo" required>
            </div>

            <div class="input-wrapper">
                <i class="far fa-file-alt"></i>
                <input type="text" name="descripcion" class="modal-field" placeholder="Descripción" required>
            </div>

            <div class="input-wrapper">
                <i class="far fa-envelope"></i>
                <input type="text" name="ubicacion" class="modal-field" placeholder="Ubicación" required>
            </div>

            <div class="input-wrapper">
                <i class="fas fa-trophy"></i>
                <select name="torneo" class="modal-field" required>
                    <option value="" disabled selected>Selecciona un Torneo</option>
                    @foreach($torneos as $torneo)
                    {{-- Asegúrate que en tu BD events la columna se llame 'titulo' --}}
                    <option value="{{ $torneo->titulo }}">{{ $torneo->titulo }}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-wrapper">
                <i class="fas fa-lock"></i>
                <select name="acceso" class="modal-field" required>
                    <option value="" disabled selected>Tipo de Acceso</option>
                    <option value="Público">Público</option>
                    <option value="Privado">Privado</option>
                </select>
            </div>

            <button type="submit" class="btn-modal-submit">Registrarse</button>
        </form>
    </div>
</div>

{{-- JAVASCRIPT --}}
<script>
    function abrirModal() {
        document.getElementById('modalRegistro').style.display = 'flex';
    }

    function cerrarModal(e) {
        // Solo cierra si clickea el fondo oscuro, no el contenido
        if (e.target.id === 'modalRegistro') {
            document.getElementById('modalRegistro').style.display = 'none';
        }
    }

    function filtrar(criterio, btn) {
        // Actualizar botones
        document.querySelectorAll('.tab-item').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Filtrar elementos
        const tarjetas = document.querySelectorAll('.tarjeta-item');
        tarjetas.forEach(t => {
            const esMio = t.getAttribute('data-mio') === 'si';

            if (criterio === 'todos') t.style.display = 'block';
            else if (criterio === 'mios') t.style.display = esMio ? 'block' : 'none';
            else if (criterio === 'disponibles') t.style.display = 'block'; // Lógica simplificada
        });
    }
</script>

@endsection