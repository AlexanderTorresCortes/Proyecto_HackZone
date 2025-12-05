@extends('layouts.inicio')

@section('title', 'Mensajes')

@section('content')

{{-- Enlazar CSS --}}
<link rel="stylesheet" href="{{ asset('css/mensajes.css') }}">

<div class="mensajes-section">
    <div class="contenedor-mensajes">

        {{-- Sidebar con lista de chats --}}
        <div class="sidebar-chats">
            <div class="sidebar-header">
                <input type="text" class="input-buscar-chat" placeholder="Buscar un chat" id="buscarChat">
                <button class="btn-nuevo-chat" onclick="abrirModalNuevoChat()" title="Nuevo chat">
                    <i class="fas fa-plus"></i>
                </button>
            </div>

            <div class="lista-chats" id="listaChats">
                @forelse($chats as $chat)
                    @php
                        $otroUsuario = $chat->obtenerOtroUsuario(Auth::id());
                        $ultimoMensaje = $chat->ultimoMensaje;
                        $mensajesNoLeidos = $chat->mensajesNoLeidos(Auth::id());
                    @endphp

                    <a href="{{ route('mensajes.ver', $chat->id) }}"
                       class="chat-item {{ $chatActivo && $chatActivo->id == $chat->id ? 'active' : '' }}"
                       data-chat-id="{{ $chat->id }}">
                        <div class="chat-avatar chat-avatar-user">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($otroUsuario->name) }}&background=random"
                                 alt="{{ $otroUsuario->name }}">
                        </div>
                        <div class="chat-info">
                            <div class="chat-nombre">{{ $otroUsuario->name }}</div>
                            <div class="chat-preview">
                                @if($ultimoMensaje)
                                    {{ Str::limit($ultimoMensaje->mensaje, 30) }}
                                @else
                                    Inicia una conversación
                                @endif
                            </div>
                        </div>
                        <div class="chat-meta">
                            <div class="chat-hora">
                                @if($ultimoMensaje)
                                    {{ $ultimoMensaje->created_at->format('g:i a') }}
                                @endif
                            </div>
                            @if($mensajesNoLeidos > 0)
                                <span class="chat-badge">{{ $mensajesNoLeidos }}</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="no-chats">
                        <i class="fas fa-comments"></i>
                        <p>No tienes conversaciones aún</p>
                        <button onclick="abrirModalNuevoChat()" class="btn-iniciar-chat">Iniciar un chat</button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Panel de conversación --}}
        <div class="panel-conversacion">
            @if($chatActivo)
                @php
                    $otroUsuario = $chatActivo->obtenerOtroUsuario(Auth::id());
                @endphp

                {{-- Header del chat activo --}}
                <div class="conversacion-header">
                    <div class="header-info">
                        <div class="header-avatar">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($otroUsuario->name) }}&background=random"
                                 alt="{{ $otroUsuario->name }}">
                        </div>
                        <div class="header-detalles">
                            <div class="header-nombre">{{ $otroUsuario->name }}</div>
                            <div class="header-estado">{{ $otroUsuario->email }}</div>
                        </div>
                    </div>
                    <div class="header-acciones">
                        <button class="btn-header-accion" title="Buscar en conversación">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                {{-- Mensajes --}}
                <div class="conversacion-mensajes" id="conversacionMensajes">
                    @foreach($mensajes as $mensaje)
                        @if($mensaje->user_id == Auth::id())
                            {{-- Mensaje propio (derecha) --}}
                            <div class="mensaje mensaje-derecha">
                                <div class="mensaje-contenido">
                                    <div class="mensaje-texto">{{ $mensaje->mensaje }}</div>
                                    <div class="mensaje-hora">{{ $mensaje->created_at->format('g:i a') }}</div>
                                </div>
                            </div>
                        @else
                            {{-- Mensaje del otro usuario (izquierda) --}}
                            <div class="mensaje mensaje-izquierda">
                                <div class="mensaje-avatar">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($mensaje->usuario->name) }}&background=random"
                                         alt="{{ $mensaje->usuario->name }}">
                                </div>
                                <div class="mensaje-contenido">
                                    <div class="mensaje-autor">{{ $mensaje->usuario->name }}</div>
                                    <div class="mensaje-texto">{{ $mensaje->mensaje }}</div>
                                    <div class="mensaje-hora">{{ $mensaje->created_at->format('g:i a') }}</div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Input para escribir mensaje --}}
                <form id="formEnviarMensaje" class="conversacion-input">
                    @csrf
                    <input type="hidden" name="chat_id" value="{{ $chatActivo->id }}">

                    <button type="button" class="btn-adjuntar" title="Adjuntar archivo">
                        <i class="fas fa-paperclip"></i>
                    </button>

                    <input type="text"
                           name="mensaje"
                           class="input-mensaje"
                           placeholder="Escribe un mensaje..."
                           id="inputMensaje"
                           required>

                    <button type="submit" class="btn-enviar" title="Enviar mensaje">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            @else
                {{-- No hay chat seleccionado --}}
                <div class="panel-vacio">
                    <i class="fas fa-comments"></i>
                    <h3>Selecciona un chat para comenzar</h3>
                    <p>Elige una conversación de la lista o inicia una nueva</p>
                    <button onclick="abrirModalNuevoChat()" class="btn-nuevo-chat-vacio">
                        <i class="fas fa-plus-circle"></i> Nuevo Chat
                    </button>
                </div>
            @endif
        </div>

    </div>
</div>

{{-- Modal para iniciar nuevo chat --}}
<div id="modalNuevoChat" class="modal-backdrop" onclick="cerrarModalNuevoChat(event)">
    <div class="modal-caja" onclick="event.stopPropagation()">
        <button type="button" class="btn-cerrar-modal" onclick="cerrarModalNuevoChat({target: document.getElementById('modalNuevoChat')})" title="Cerrar">
            <i class="fas fa-times"></i>
        </button>
        <h2 class="modal-titulo">NUEVO CHAT</h2>
        <p class="modal-subtitulo">Selecciona un usuario para iniciar una conversación</p>

        <form action="{{ route('mensajes.iniciar') }}" method="POST">
            @csrf

            <div class="input-wrapper">
                <input type="text"
                       id="buscarUsuario"
                       class="modal-field"
                       placeholder="Buscar usuario..."
                       onkeyup="filtrarUsuarios()">
                <i class="fas fa-search"></i>
            </div>

            <div class="lista-usuarios" id="listaUsuarios">
                @forelse($usuarios as $usuario)
                    <label class="usuario-item">
                        <input type="radio" name="user_id" value="{{ $usuario->id }}" required>
                        <div class="usuario-avatar">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($usuario->name) }}&background=random"
                                 alt="{{ $usuario->name }}">
                        </div>
                        <div class="usuario-info">
                            <div class="usuario-nombre">{{ $usuario->name }}</div>
                            <div class="usuario-email">{{ $usuario->email }}</div>
                        </div>
                        <i class="fas fa-check usuario-check"></i>
                    </label>
                @empty
                    <div style="padding: 30px; text-align: center; color: #999;">
                        <i class="fas fa-user-slash" style="font-size: 3rem; margin-bottom: 15px; color: #ddd;"></i>
                        <p>No hay otros usuarios registrados en el sistema</p>
                    </div>
                @endforelse
            </div>

            @if(count($usuarios) > 0)
                <button type="submit" class="btn-modal-submit">Iniciar Chat</button>
            @endif
        </form>
    </div>
</div>

{{-- Enlazar JavaScript --}}
<script>
    const CHAT_ID = {{ $chatActivo->id ?? 'null' }};
    const CSRF_TOKEN = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/mensajes.js') }}"></script>

@endsection
