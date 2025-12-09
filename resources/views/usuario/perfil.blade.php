<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - HackZone</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS del Perfil -->
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
    <style>
        .insignias-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .perfil-insignias {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
        }
        .perfil-insignias h3 {
            color: #1e293b;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        .insignia-item {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .insignia-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<!-- INCLUIR NAVBAR -->
@include('components.navbar')

<div class="perfil-container">
    <h1 class="page-title">Mi Perfil</h1>
    
    <div class="perfil-layout">
        <!-- Columna Izquierda: Información del Usuario -->
        <div class="perfil-card">
            <div class="perfil-header">
                <div class="perfil-avatar">
                    <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&size=200&background=6b21a8&color=fff' }}" alt="Avatar">
                </div>
                
                <div class="perfil-info">
                    <h2>{{ Auth::user()->name }}</h2>
                    <div class="perfil-meta">
                        <span class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ Auth::user()->ubicacion ?? 'No especificado' }}
                        </span>
                        <span class="meta-item">
                            <i class="far fa-calendar"></i>
                            Miembro desde {{ Auth::user()->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                </div>

                <button class="btn-edit-perfil" onclick="window.location.href='{{ route('perfil.edit') }}'">
                    <i class="fas fa-edit"></i> Editar Perfil
                </button>
            </div>

            <div class="perfil-bio">
                <p>{{ Auth::user()->bio ?? 'No hay biografía disponible.' }}</p>
            </div>

            <div class="perfil-contacto">
                <div class="contacto-item">
                    <i class="far fa-envelope"></i>
                    <span>{{ Auth::user()->email }}</span>
                </div>
                @if(Auth::user()->telefono)
                <div class="contacto-item">
                    <i class="fas fa-phone"></i>
                    <span>{{ Auth::user()->telefono }}</span>
                </div>
                @endif
                <div class="contacto-item">
                    <i class="fas fa-user"></i>
                    <span>{{ '@' . Auth::user()->username }}</span>
                </div>
            </div>

            @if(Auth::user()->habilidades && count(Auth::user()->habilidades) > 0)
            <div class="perfil-habilidades">
                <h3>Habilidades</h3>
                <div class="habilidades-grid">
                    @foreach(Auth::user()->habilidades as $habilidad)
                        <span class="badge-habilidad">{{ $habilidad }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @php
                $insignias = Auth::user()->insignias()->withPivot('equipo_id', 'event_id')->get();
            @endphp
            @if($insignias->count() > 0)
            <div class="perfil-insignias">
                <h3><i class="fas fa-trophy"></i> Insignias</h3>
                <div class="insignias-grid">
                    @foreach($insignias as $insignia)
                        @php
                            $equipo = $insignia->pivot->equipo_id ? \App\Models\Equipo::find($insignia->pivot->equipo_id) : null;
                            $evento = $insignia->pivot->event_id ? \App\Models\Event::find($insignia->pivot->event_id) : null;
                        @endphp
                        <div class="insignia-item" style="background: linear-gradient(135deg, {{ $insignia->color }}15 0%, {{ $insignia->color }}05 100%); border: 2px solid {{ $insignia->color }}; border-radius: 12px; padding: 15px; text-align: center; position: relative;">
                            <div class="insignia-icon" style="font-size: 2.5rem; color: {{ $insignia->color }}; margin-bottom: 10px;">
                                <i class="{{ $insignia->icono }}"></i>
                            </div>
                            <div class="insignia-nombre" style="font-weight: bold; color: #1e293b; margin-bottom: 5px;">
                                {{ $insignia->nombre }}
                            </div>
                            @if($equipo && $evento)
                            <div class="insignia-descripcion" style="font-size: 0.85rem; color: #64748b;">
                                {{ $equipo->nombre }} - {{ $evento->titulo }}
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        
        <!-- Columna Derecha: Estadísticas -->
        <div class="perfil-stats-card">
            <h3>Estadísticas</h3>
            <div class="stats-grid">
                @if(Auth::user()->isAdmin())
                    {{-- Estadísticas para Administrador --}}
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number">{{ $data['totalUsuarios'] }}</div>
                        <div class="stat-label">Usuarios</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div class="stat-number">{{ $data['totalEquipos'] }}</div>
                        <div class="stat-label">Equipos</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-number">{{ $data['totalEventos'] }}</div>
                        <div class="stat-label">Eventos</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-number">{{ $data['eventosActivos'] }}</div>
                        <div class="stat-label">Activos</div>
                    </div>
                @elseif(Auth::user()->isJuez())
                    {{-- Estadísticas para Juez --}}
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-number">{{ $data['totalEventos'] }}</div>
                        <div class="stat-label">Eventos Asignados</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="stat-number">{{ $data['totalEvaluaciones'] }}</div>
                        <div class="stat-label">Evaluaciones</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number">{{ $data['evaluacionesCompletadas'] }}</div>
                        <div class="stat-label">Completadas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="stat-number">{{ $data['evaluacionesPendientes'] }}</div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                @else
                    {{-- Estadísticas para Usuario Normal --}}
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="stat-number">6</div>
                        <div class="stat-label">Torneos</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number">{{ $data['misEquipos']->count() }}</div>
                        <div class="stat-label">Equipos</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="stat-number">4</div>
                        <div class="stat-label">Proyectos</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-medal"></i>
                        </div>
                        <div class="stat-number">1</div>
                        <div class="stat-label">Victoria</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Sección de Equipos (solo para usuarios normales) -->
    @if(Auth::user()->isUsuario())
    <div class="equipos-section">
        <div class="equipos-tabs">
            <button class="tab-btn active" data-tab="disponibles">
                Equipos disponibles ({{ $data['equiposDisponibles']->count() }})
            </button>
            <button class="tab-btn" data-tab="mis-equipos">
                Mis equipos ({{ $data['misEquipos']->count() }})
            </button>
            <button class="tab-btn" data-tab="todos">
                Todos los equipos ({{ $data['todosEquipos']->count() }})
            </button>
        </div>
        
        <div class="equipos-content">
            <!-- Tab: Equipos Disponibles -->
            <div class="tab-pane active" id="disponibles">
                <div class="equipos-grid">
                    @forelse($data['equiposDisponibles'] as $equipo)
                    <div class="equipo-card">
                        <div class="equipo-header">
                            <h4>{{ $equipo->nombre }}</h4>
                            <span class="badge-estado {{ $equipo->estado == 'Reclutando' ? 'reclutando' : 'completo' }}">
                                {{ $equipo->estado }}
                            </span>
                        </div>
                        <p class="equipo-descripcion">{{ Str::limit($equipo->descripcion, 100) }}</p>
                        <div class="equipo-info">
                            <span><i class="fas fa-map-marker-alt"></i> {{ $equipo->ubicacion }}</span>
                            <span><i class="fas fa-trophy"></i> {{ $equipo->torneo }}</span>
                        </div>
                        <div class="equipo-miembros">
                            <span>{{ $equipo->miembros_actuales }}/{{ $equipo->miembros_max }} miembros</span>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ ($equipo->miembros_actuales / $equipo->miembros_max) * 100 }}%"></div>
                            </div>
                        </div>
                        <button class="btn-unirse" onclick="alert('Solicitud enviada (función próximamente)')">Solicitar unirse</button>
                    </div>
                    @empty
                    <div class="empty-state">
                        <i class="fas fa-users-slash"></i>
                        <p>No hay equipos disponibles en este momento</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Tab: Mis Equipos -->
            <div class="tab-pane" id="mis-equipos">
                <div class="equipos-grid">
                    @forelse($data['misEquipos'] as $equipo)
                    <div class="equipo-card mi-equipo">
                        <div class="equipo-header">
                            <h4>{{ $equipo->nombre }}</h4>
                            @if($equipo->user_id == Auth::id())
                                <span class="badge-lider">Líder</span>
                            @endif
                        </div>
                        <p class="equipo-descripcion">{{ Str::limit($equipo->descripcion, 100) }}</p>
                        <div class="equipo-info">
                            <span><i class="fas fa-map-marker-alt"></i> {{ $equipo->ubicacion }}</span>
                            <span><i class="fas fa-trophy"></i> {{ $equipo->torneo }}</span>
                        </div>
                        <button class="btn-gestionar" onclick="alert('Gestionar equipo (función próximamente)')">
                            Gestionar equipo
                        </button>
                    </div>
                    @empty
                    <div class="empty-state">
                        <i class="fas fa-users-slash"></i>
                        <p>Aún no perteneces a ningún equipo</p>
                        <button class="btn-primary" onclick="window.location.href='{{ route('equipos.index') }}'">
                            Explorar equipos
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Tab: Todos los Equipos -->
            <div class="tab-pane" id="todos">
                <div class="equipos-grid">
                    @foreach($data['todosEquipos'] as $equipo)
                    <div class="equipo-card">
                        <div class="equipo-header">
                            <h4>{{ $equipo->nombre }}</h4>
                            <span class="badge-estado {{ $equipo->estado == 'Reclutando' ? 'reclutando' : 'completo' }}">
                                {{ $equipo->estado }}
                            </span>
                        </div>
                        <p class="equipo-descripcion">{{ Str::limit($equipo->descripcion, 100) }}</p>
                        <div class="equipo-info">
                            <span><i class="fas fa-map-marker-alt"></i> {{ $equipo->ubicacion }}</span>
                            <span><i class="fas fa-trophy"></i> {{ $equipo->torneo }}</span>
                        </div>
                        <button class="btn-ver" onclick="alert('Ver detalles (función próximamente)')">
                            Ver detalles
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
// Funcionalidad de tabs
document.querySelectorAll('.tab-btn').forEach(button => {
    button.addEventListener('click', () => {
        const tabName = button.getAttribute('data-tab');
        
        // Remover clase active de todos los botones y panes
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
        
        // Agregar clase active al botón y pane seleccionado
        button.classList.add('active');
        document.getElementById(tabName).classList.add('active');
    });
});
</script>

</body>
</html>