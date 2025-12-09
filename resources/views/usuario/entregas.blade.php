<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Entregas - HackZone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-tablas.css') }}">
    <style>
        .entregas-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .equipo-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .equipo-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .equipo-info h3 {
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .equipo-evento {
            color: #64748b;
            font-size: 0.9rem;
        }

        .badge-lider {
            background: #4a148c;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-miembro {
            background: #64748b;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 1.5rem;
        }

        .upload-zone:hover {
            border-color: #4a148c;
            background: #f8f4ff;
        }

        .upload-zone.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .upload-zone.disabled:hover {
            border-color: #cbd5e1;
            background: #f8f9fa;
        }

        .btn-upload {
            background: #4a148c;
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-upload:hover {
            background: #6a1b9a;
            transform: translateY(-2px);
        }

        .btn-upload:disabled {
            background: #94a3b8;
            cursor: not-allowed;
            transform: none;
        }

        .entregas-list {
            margin-top: 2rem;
        }

        .entrega-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }

        .entrega-item:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .entrega-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .file-icon {
            font-size: 2rem;
        }

        .file-icon.zip { color: #f59e0b; }
        .file-icon.pdf { color: #dc2626; }
        .file-icon.pptx { color: #ea580c; }

        .entrega-details h4 {
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .entrega-meta {
            color: #64748b;
            font-size: 0.85rem;
        }

        .estado-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .estado-pendiente {
            background: #fef3c7;
            color: #92400e;
        }

        .estado-aprobado {
            background: #d1fae5;
            color: #065f46;
        }

        .estado-rechazado {
            background: #fee2e2;
            color: #991b1b;
        }

        .acciones-entrega {
            display: flex;
            gap: 0.5rem;
        }

        .btn-accion {
            background: none;
            border: none;
            padding: 0.5rem;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .btn-accion:hover {
            background: #f1f5f9;
        }

        .btn-accion.download {
            color: #0ea5e9;
        }

        .btn-accion.delete {
            color: #dc2626;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .no-entregas {
            text-align: center;
            color: #64748b;
            padding: 2rem;
            font-style: italic;
        }

        .no-equipos {
            text-align: center;
            padding: 4rem 2rem;
            color: #64748b;
        }

        .no-equipos i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }

        .selected-file {
            margin-top: 1rem;
            display: none;
            color: #10b981;
            font-weight: 500;
        }
    </style>
</head>
<body>

@include('components.navbar')

<div class="entregas-container">
    <h2 style="color: #1e293b; margin-bottom: 0.5rem;">
        <i class="fas fa-cloud-upload-alt"></i> Mis Entregas de Proyectos
    </h2>
    <p style="color: #64748b; margin-bottom: 2rem;">Sube los archivos de los proyectos de tus equipos. Solo el líder del equipo puede subir archivos.</p>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    @if($equiposComoLider->isEmpty() && $equiposComoMiembro->isEmpty())
        <div class="no-equipos">
            <i class="fas fa-users"></i>
            <h3>No estás en ningún equipo</h3>
            <p>Únete o crea un equipo para poder subir entregas de proyectos.</p>
        </div>
    @else
        @foreach($equiposComoLider as $equipo)
            <div class="equipo-card">
                <div class="equipo-header">
                    <div class="equipo-info">
                        <h3>{{ $equipo->nombre }}</h3>
                        <p class="equipo-evento">
                            <i class="fas fa-calendar-alt"></i>
                            {{ $equipo->evento ? $equipo->evento->titulo : 'Sin evento asignado' }}
                        </p>
                    </div>
                    <span class="badge-lider">
                        <i class="fas fa-crown"></i> Líder
                    </span>
                </div>

                @if($equipo->evento)
                    <form action="{{ route('usuario.entregas.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="equipo_id" value="{{ $equipo->id }}">

                        <div class="upload-zone" onclick="document.getElementById('fileInput{{ $equipo->id }}').click()">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #4a148c; margin-bottom: 1rem;"></i>
                            <h4 style="color: #1e293b; margin-bottom: 0.5rem;">Arrastra tu archivo aquí o haz clic para seleccionar</h4>
                            <p style="color: #64748b; font-size: 0.9rem;">Formatos permitidos: ZIP, PDF, PPTX (Máx. 50MB)</p>
                            <input type="file" id="fileInput{{ $equipo->id }}" name="archivo" accept=".zip,.pdf,.pptx" style="display: none;" onchange="handleFileSelect(event, {{ $equipo->id }})" required>
                            <div id="selectedFile{{ $equipo->id }}" class="selected-file">
                                <i class="fas fa-check-circle"></i> <span id="fileName{{ $equipo->id }}"></span>
                            </div>
                        </div>

                        <button type="submit" class="btn-upload">
                            <i class="fas fa-upload"></i> Subir Archivo
                        </button>
                    </form>

                    <div class="entregas-list">
                        <h4 style="color: #1e293b; margin-bottom: 1rem;">
                            <i class="fas fa-folder-open"></i> Historial de Entregas
                        </h4>

                        @if($equipo->entregas->isEmpty())
                            <p class="no-entregas">No hay entregas aún. Sube tu primer archivo.</p>
                        @else
                            @foreach($equipo->entregas as $entrega)
                                <div class="entrega-item">
                                    <div class="entrega-info">
                                        <i class="fas fa-file-{{ $entrega->tipo_archivo }} file-icon {{ $entrega->tipo_archivo }}"></i>
                                        <div class="entrega-details">
                                            <h4>{{ $entrega->nombre_archivo }}</h4>
                                            <p class="entrega-meta">
                                                Versión {{ $entrega->version }} •
                                                {{ $entrega->formatted_size }} •
                                                {{ $entrega->created_at->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <span class="estado-badge estado-{{ $entrega->estado }}">
                                            @if($entrega->estado == 'pendiente')
                                                <i class="fas fa-clock"></i> Pendiente
                                            @elseif($entrega->estado == 'aprobado')
                                                <i class="fas fa-check-circle"></i> Aprobado
                                            @else
                                                <i class="fas fa-times-circle"></i> Rechazado
                                            @endif
                                        </span>
                                        <div class="acciones-entrega">
                                            <a href="{{ route('usuario.entregas.download', $entrega->id) }}" class="btn-accion download" title="Descargar">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <form action="{{ route('usuario.entregas.destroy', $entrega->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar esta entrega?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-accion delete" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Este equipo no está inscrito en ningún evento. Debes inscribirlo en un evento antes de poder subir archivos.
                    </div>
                @endif
            </div>
        @endforeach

        @foreach($equiposComoMiembro as $miembro)
            @php
                $equipo = $miembro->equipo;
            @endphp
            <div class="equipo-card">
                <div class="equipo-header">
                    <div class="equipo-info">
                        <h3>{{ $equipo->nombre }}</h3>
                        <p class="equipo-evento">
                            <i class="fas fa-calendar-alt"></i>
                            {{ $equipo->evento ? $equipo->evento->titulo : 'Sin evento asignado' }}
                        </p>
                    </div>
                    <span class="badge-miembro">
                        <i class="fas fa-user"></i> Miembro - {{ $miembro->rol }}
                    </span>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Solo el líder del equipo ({{ $equipo->lider->name }}) puede subir archivos.
                </div>

                <div class="entregas-list">
                    <h4 style="color: #1e293b; margin-bottom: 1rem;">
                        <i class="fas fa-folder-open"></i> Entregas del Equipo
                    </h4>

                    @if($equipo->entregas->isEmpty())
                        <p class="no-entregas">No hay entregas aún.</p>
                    @else
                        @foreach($equipo->entregas as $entrega)
                            <div class="entrega-item">
                                <div class="entrega-info">
                                    <i class="fas fa-file-{{ $entrega->tipo_archivo }} file-icon {{ $entrega->tipo_archivo }}"></i>
                                    <div class="entrega-details">
                                        <h4>{{ $entrega->nombre_archivo }}</h4>
                                        <p class="entrega-meta">
                                            Versión {{ $entrega->version }} •
                                            {{ $entrega->formatted_size }} •
                                            {{ $entrega->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <span class="estado-badge estado-{{ $entrega->estado }}">
                                        @if($entrega->estado == 'pendiente')
                                            <i class="fas fa-clock"></i> Pendiente
                                        @elseif($entrega->estado == 'aprobado')
                                            <i class="fas fa-check-circle"></i> Aprobado
                                        @else
                                            <i class="fas fa-times-circle"></i> Rechazado
                                        @endif
                                    </span>
                                    <div class="acciones-entrega">
                                        <a href="{{ route('usuario.entregas.download', $entrega->id) }}" class="btn-accion download" title="Descargar">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>

<script>
function handleFileSelect(event, equipoId) {
    const file = event.target.files[0];
    if (file) {
        const fileName = file.name;
        const fileSize = (file.size / 1024 / 1024).toFixed(2);

        document.getElementById('selectedFile' + equipoId).style.display = 'block';
        document.getElementById('fileName' + equipoId).textContent = fileName + ' (' + fileSize + ' MB)';
    }
}
</script>

</body>
</html>
