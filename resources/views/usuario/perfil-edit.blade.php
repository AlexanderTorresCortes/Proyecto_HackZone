<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - HackZone</title>

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        .edit-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .form-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-header h2 {
            color: #1e293b;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #334155;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #6b21a8;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .avatar-upload {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .avatar-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid #e2e8f0;
        }

        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .upload-btn {
            position: relative;
            display: inline-block;
        }

        .upload-btn input[type="file"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .upload-btn label {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #6b21a8 0%, #9333ea 100%);
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .upload-btn label:hover {
            transform: translateY(-2px);
        }

        .habilidades-input {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .habilidad-tag {
            background: #e0e7ff;
            color: #4338ca;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .habilidad-tag button {
            background: none;
            border: none;
            color: #4338ca;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 0;
            line-height: 1;
        }

        .add-habilidad {
            display: flex;
            gap: 0.5rem;
        }

        .add-habilidad input {
            flex: 1;
        }

        .add-habilidad button {
            background: #6b21a8;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 1rem;
        }

        .btn-cancel {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-cancel:hover {
            background: #cbd5e1;
        }

        .btn-save {
            background: linear-gradient(135deg, #6b21a8 0%, #9333ea 100%);
            color: white;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(107, 33, 168, 0.4);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }
    </style>
</head>
<body>

@include('components.navbar')

<div class="edit-container">
    <div class="form-card">
        <div class="form-header">
            <h2><i class="fas fa-user-edit"></i> Editar Perfil</h2>
            <a href="{{ route('perfil.index') }}" style="color: #6b7280; text-decoration: none;">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Avatar --}}
            <div class="form-group">
                <label>Foto de Perfil</label>
                <div class="avatar-upload">
                    <div class="avatar-preview">
                        <img id="avatar-img" src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&size=200&background=6b21a8&color=fff' }}" alt="Avatar">
                    </div>
                    <div class="upload-btn">
                        <label for="avatar">
                            <i class="fas fa-camera"></i> Cambiar Foto
                        </label>
                        <input type="file" id="avatar" name="avatar" accept="image/*" onchange="previewAvatar(event)">
                    </div>
                </div>
            </div>

            {{-- Nombre --}}
            <div class="form-group">
                <label for="name">Nombre Completo</label>
                <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
            </div>

            {{-- Username --}}
            <div class="form-group">
                <label for="username">Nombre de Usuario</label>
                <input type="text" id="username" name="username" value="{{ old('username', Auth::user()->username) }}" required>
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
            </div>

            {{-- Ubicación --}}
            <div class="form-group">
                <label for="ubicacion">Ubicación</label>
                <input type="text" id="ubicacion" name="ubicacion" value="{{ old('ubicacion', Auth::user()->ubicacion) }}" placeholder="Ej: Oaxaca, México">
            </div>

            {{-- Teléfono --}}
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="tel" id="telefono" name="telefono" value="{{ old('telefono', Auth::user()->telefono) }}" placeholder="+52 951 123 4567">
            </div>

            {{-- Biografía --}}
            <div class="form-group">
                <label for="bio">Biografía</label>
                <textarea id="bio" name="bio" placeholder="Cuéntanos sobre ti...">{{ old('bio', Auth::user()->bio) }}</textarea>
            </div>

            {{-- Habilidades --}}
            <div class="form-group">
                <label>Habilidades</label>
                <div class="habilidades-input" id="habilidades-container">
                    @if(Auth::user()->habilidades)
                        @foreach(Auth::user()->habilidades as $habilidad)
                            <div class="habilidad-tag">
                                <span>{{ $habilidad }}</span>
                                <button type="button" onclick="removeHabilidad(this)">×</button>
                                <input type="hidden" name="habilidades[]" value="{{ $habilidad }}">
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="add-habilidad">
                    <input type="text" id="nueva-habilidad" placeholder="Ej: JavaScript, Python...">
                    <button type="button" onclick="addHabilidad()">
                        <i class="fas fa-plus"></i> Agregar
                    </button>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('perfil.index') }}" class="btn btn-cancel">Cancelar</a>
                <button type="submit" class="btn btn-save">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Preview avatar
function previewAvatar(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-img').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}

// Agregar habilidad
function addHabilidad() {
    const input = document.getElementById('nueva-habilidad');
    const habilidad = input.value.trim();

    if (habilidad) {
        const container = document.getElementById('habilidades-container');
        const tag = document.createElement('div');
        tag.className = 'habilidad-tag';
        tag.innerHTML = `
            <span>${habilidad}</span>
            <button type="button" onclick="removeHabilidad(this)">×</button>
            <input type="hidden" name="habilidades[]" value="${habilidad}">
        `;
        container.appendChild(tag);
        input.value = '';
    }
}

// Eliminar habilidad
function removeHabilidad(button) {
    button.closest('.habilidad-tag').remove();
}

// Agregar con Enter
document.getElementById('nueva-habilidad').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        addHabilidad();
    }
});
</script>

</body>
</html>
