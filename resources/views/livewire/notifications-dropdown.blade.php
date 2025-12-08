<div class="notifications-dropdown" id="notificationsDropdown">
    <div class="notifications-header">
        <h3>Notificaciones</h3>
        @if($unreadCount > 0)
            <span class="unread-badge">{{ $unreadCount }}</span>
        @endif
    </div>
    
    <div class="notifications-list">
        @forelse($notifications as $notification)
            <div class="notification-item {{ $notification->read_at ? 'read' : 'unread' }}" 
                 data-notification-id="{{ $notification->id }}">
                
                @if($notification->data['type'] === 'equipo_solicitud')
                    <div class="notification-content">
                        <div class="notification-avatar">
                            @if(isset($notification->data['solicitante_avatar']) && $notification->data['solicitante_avatar'])
                                <img src="{{ asset('storage/' . $notification->data['solicitante_avatar']) }}" alt="{{ $notification->data['solicitante_nombre'] }}">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($notification->data['solicitante_nombre']) }}&background=4a148c&color=fff" alt="{{ $notification->data['solicitante_nombre'] }}">
                            @endif
                        </div>
                        <div class="notification-info">
                            <p class="notification-message">{{ $notification->data['message'] }}</p>
                            <div class="notification-details">
                                <span class="solicitante-name">
                                    <i class="fas fa-user"></i> {{ $notification->data['solicitante_nombre'] }}
                                </span>
                                <span class="equipo-name">
                                    <i class="fas fa-users"></i> {{ $notification->data['equipo_nombre'] }}
                                </span>
                            </div>
                            @if(isset($notification->data['mensaje']) && $notification->data['mensaje'])
                                <p class="solicitud-mensaje">{{ $notification->data['mensaje'] }}</p>
                            @endif
                            @if(isset($notification->data['rol_solicitado']) && $notification->data['rol_solicitado'])
                                <div class="rol-solicitado">
                                    <i class="fas fa-user-tag"></i> 
                                    <strong>Rol solicitado:</strong> {{ $notification->data['rol_solicitado'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="notification-actions">
                        <button wire:click="acceptRequest('{{ $notification->id }}')" 
                                class="btn-accept" 
                                title="Aceptar solicitud">
                            <i class="fas fa-check"></i> Aceptar
                        </button>
                        <button wire:click="rejectRequest('{{ $notification->id }}')" 
                                class="btn-reject" 
                                title="Rechazar solicitud">
                            <i class="fas fa-times"></i> Rechazar
                        </button>
                    </div>
                @elseif($notification->data['type'] === 'equipo_invitacion')
                    <div class="notification-content">
                        <div class="notification-avatar">
                            @if(isset($notification->data['invitador_avatar']) && $notification->data['invitador_avatar'])
                                <img src="{{ asset('storage/' . $notification->data['invitador_avatar']) }}" alt="{{ $notification->data['invitador_nombre'] }}">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($notification->data['invitador_nombre']) }}&background=4a148c&color=fff" alt="{{ $notification->data['invitador_nombre'] }}">
                            @endif
                        </div>
                        <div class="notification-info">
                            <p class="notification-message">{{ $notification->data['message'] }}</p>
                            <div class="notification-details">
                                <span class="invitador-name">
                                    <i class="fas fa-user"></i> {{ $notification->data['invitador_nombre'] }}
                                </span>
                                <span class="equipo-name">
                                    <i class="fas fa-users"></i> {{ $notification->data['equipo_nombre'] }}
                                </span>
                            </div>
                            @if(isset($notification->data['mensaje']) && $notification->data['mensaje'])
                                <p class="solicitud-mensaje">{{ $notification->data['mensaje'] }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="notification-actions">
                        <button wire:click="acceptInvitation('{{ $notification->id }}')" 
                                class="btn-accept" 
                                title="Aceptar invitación">
                            <i class="fas fa-check"></i> Aceptar
                        </button>
                        <button wire:click="rejectInvitation('{{ $notification->id }}')" 
                                class="btn-reject" 
                                title="Rechazar invitación">
                            <i class="fas fa-times"></i> Rechazar
                        </button>
                    </div>
                @elseif($notification->data['type'] === 'miembro_unido_equipo')
                    <div class="notification-content">
                        <div class="notification-avatar">
                            @if(isset($notification->data['miembro_avatar']) && $notification->data['miembro_avatar'])
                                <img src="{{ asset('storage/' . $notification->data['miembro_avatar']) }}" alt="{{ $notification->data['miembro_nombre'] }}">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($notification->data['miembro_nombre']) }}&background=4a148c&color=fff" alt="{{ $notification->data['miembro_nombre'] }}">
                            @endif
                        </div>
                        <div class="notification-info">
                            <p class="notification-message">{{ $notification->data['message'] }}</p>
                            <span class="equipo-name">
                                <i class="fas fa-users"></i> {{ $notification->data['equipo_nombre'] }}
                            </span>
                        </div>
                    </div>
                @elseif($notification->data['type'] === 'equipo_solicitud_respuesta')
                    <div class="notification-content">
                        <div class="notification-icon {{ $notification->data['aceptada'] ? 'accepted' : 'rejected' }}">
                            @if($notification->data['aceptada'])
                                <i class="fas fa-check-circle"></i>
                            @else
                                <i class="fas fa-times-circle"></i>
                            @endif
                        </div>
                        <div class="notification-info">
                            <p class="notification-message">{{ $notification->data['message'] }}</p>
                            <span class="equipo-name">
                                <i class="fas fa-users"></i> {{ $notification->data['equipo_nombre'] }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="notification-content">
                        <div class="notification-info">
                            <p class="notification-message">{{ $notification->data['message'] ?? 'Nueva notificación' }}</p>
                        </div>
                    </div>
                @endif
                
                @if(!$notification->read_at)
                    <button wire:click="markAsRead('{{ $notification->id }}')" 
                            class="btn-mark-read" 
                            title="Marcar como leída">
                        <i class="fas fa-circle"></i>
                    </button>
                @endif
            </div>
        @empty
            <div class="no-notifications">
                <i class="fas fa-bell-slash"></i>
                <p>No tienes notificaciones</p>
            </div>
        @endforelse
    </div>
</div>
