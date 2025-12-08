<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga de Archivos - HackZone</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-tablas.css') }}">
</head>
<body>

@include('components.navbar-admin')

<div class="admin-container">
    @include('components.sidebar-admin')

    <main class="admin-main">
        <h2 class="titulo-pagina">Entrega de Proyecto / Subida de Archivos</h2>

        <p style="color: #666; margin-bottom: 2rem;">Sube tu código, documentación o presentación en formato .zip, .pdf o .pptx para participar en los torneos de programación</p>

        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Zona de carga -->
        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                <i class="fas fa-folder-open" style="font-size: 1.5rem; color: #4a148c;"></i>
                <h3 style="color: #1e293b; margin: 0;">Primer Lugar</h3>
            </div>

            <p style="color: #64748b; margin-bottom: 1.5rem;">Selecciona el archivo y completa la información requerida</p>

            <div style="border: 2px dashed #cbd5e1; border-radius: 12px; padding: 3rem; text-align: center; background: #f8f9fa; cursor: pointer; transition: all 0.3s; margin-bottom: 1.5rem;" onclick="document.getElementById('fileInput').click()">
                <i class="fas fa-cloud-upload-alt" style="font-size: 3.5rem; color: #4a148c; margin-bottom: 1rem;"></i>
                <h4 style="color: #1e293b; margin-bottom: 0.5rem;">Arrastra tu archivo aquí o haz clic para seleccionar</h4>
                <p style="color: #64748b; font-size: 0.9rem;">Formatos permitidos: ZIP, PDF, PPTX (Máx. 50MB)</p>
                <input type="file" id="fileInput" accept=".zip,.pdf,.pptx" style="display: none;" onchange="handleFileSelect(event)">
                <div id="selectedFile" style="margin-top: 1rem; display: none; color: #10b981; font-weight: 500;">
                    <i class="fas fa-check-circle"></i> <span id="fileName"></span>
                </div>
            </div>

            <button class="btn-guardar" style="width: 100%;">
                <i class="fas fa-upload"></i> Subir Archivo
            </button>
        </div>

        <!-- Archivos subidos -->
        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3 style="color: #1e293b; margin-bottom: 1rem;">Archivos Subidos</h3>
            <p style="color: #64748b; margin-bottom: 1.5rem;">Historial de tus entregas y su estado de revisión</p>

            <table class="tabla-gestion">
                <thead>
                    <tr>
                        <th>Archivos</th>
                        <th>Torneo</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <i class="fas fa-file-archive" style="font-size: 1.5rem; color: #f59e0b;"></i>
                                <span>algoritmo-ordenamiento.zip</span>
                            </div>
                        </td>
                        <td>ACM ICPC Regional</td>
                        <td class="dato-fecha">2024-01-16</td>
                        <td>
                            <span style="background: #10b981; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem;">
                                <i class="fas fa-check-circle"></i> Aprobado
                            </span>
                        </td>
                        <td>
                            <div class="acciones-btn">
                                <button class="btn-accion ver"><i class="fas fa-download"></i></button>
                                <button class="btn-accion eliminar"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <i class="fas fa-file-pdf" style="font-size: 1.5rem; color: #dc2626;"></i>
                                <span>documentacion.pdf.zip</span>
                            </div>
                        </td>
                        <td>CodeForces Contest</td>
                        <td class="dato-fecha">2024-01-14</td>
                        <td>
                            <span style="background: #fbbf24; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem;">
                                <i class="fas fa-clock"></i> Pendiente
                            </span>
                        </td>
                        <td>
                            <div class="acciones-btn">
                                <button class="btn-accion ver"><i class="fas fa-download"></i></button>
                                <button class="btn-accion eliminar"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file) {
        document.getElementById('selectedFile').style.display = 'block';
        document.getElementById('fileName').textContent = file.name;
    }
}
</script>

</body>
</html>
