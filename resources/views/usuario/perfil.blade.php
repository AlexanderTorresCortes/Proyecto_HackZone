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
                    <img src="{{ asset('images/avatars/default-avatar.png') }}" alt="Avatar" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&size=200&background=6b21a8&color=fff'">
                </div>
                
                <div class="perfil-info">
                    <h2>{{ Auth::user()->name }}</h2>
                    <div class="perfil-meta">
                        <span class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            Oaxaca, México
                        </span>
                        <span class="meta-item">
                            <i class="far fa-calendar"></i>
                            Miembro desde {{ Auth::user()->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
                
                <button class="btn-edit-perfil" onclick="alert('Función de editar próximamente')">
                    <i class="fas fa-edit"></i> Editar Perfil
                </button>
            </div>
            
            <div class="perfil-bio">
                <p>Amante de la programación, me encanta el lenguaje Java y un poco el C++, me gusta participar en hackatones y crear soluciones innovadoras para problemas complejos.</p>
            </div>
            
            <div class="perfil-contacto">
                <div class="contacto-item">
                    <i class="far fa-envelope"></i>
                    <span>{{ Auth::user()->email }}</span>
                </div>
                <div class="contacto-item">
                    <i class="fas fa-phone"></i>
                    <span>+52 9517896539</span>
                </div>
                <div class="contacto-item">
                    <i class="fab fa-whatsapp"></i>
                    <span>{{ Auth::user()->username }}</span>
                </div>
            </div>
            
            <div class="perfil-habilidades">
                <h3>Habilidades</h3>
                <div class="habilidades-grid">
                    <span class="badge-habilidad">JavaScript</span>
                    <span class="badge-habilidad">Python</span>
                    <span class="badge-habilidad">C++</span>
                    <span class="badge-habilidad">PostgreSQL</span>
                    <span class="badge-habilidad">Trabajo en equipo</span>
                </div>
            </div>
        </div>
        
        <!-- Columna Derecha: Estadísticas -->
        <div class="perfil-stats-card">
            <h3>Estadísticas</h3>
            <div class="stats-grid">
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
                    <div class="stat-number">{{ $misEquipos->count() }}</div>
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
            </div>
        </div>
    </div>
    
    <!-- Sección de Equipos -->
    <div class="equipos-section">
        <div class="equipos-tabs">
            <button class="tab-btn active" data-tab="disponibles">
                Equipos disponibles ({{ $equiposDisponibles->count() }})
            </button>
            <button class="tab-btn" data-tab="mis-equipos">
                Mis equipos ({{ $misEquipos->count() }})
            </button>
            <button class="tab-btn" data-tab="todos">
                Todos los equipos ({{ $todosEquipos->count() }})
            </button>
        </div>
        
        <div class="equipos-content">
            <!-- Tab: Equipos Disponibles -->
            <div class="tab-pane active" id="disponibles">
                <div class="equipos-grid">
                    @forelse($equiposDisponibles as $equipo)
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
                    @forelse($misEquipos as $equipo)
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
                    @foreach($todosEquipos as $equipo)
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