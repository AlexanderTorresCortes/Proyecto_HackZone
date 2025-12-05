// @ts-nocheck

// Referencias a los elementos del modal
const modal = document.getElementById('modalEditar');
const formEditar = document.getElementById('formEditar');

// Referencias a los inputs del formulario
const inputIdDisplay = document.getElementById('edit_id_display');
const inputNombre = document.getElementById('edit_nombre');
const inputDescripcion = document.getElementById('edit_descripcion');
const inputMiembrosMax = document.getElementById('edit_miembros_max');
const inputEstado = document.getElementById('edit_estado');
const inputAcceso = document.getElementById('edit_acceso');
const inputUbicacion = document.getElementById('edit_ubicacion');
const inputTorneo = document.getElementById('edit_torneo');

/**
 * Abre el modal y carga los datos usando los atributos data- del botón.
 * @param {HTMLButtonElement} btn - El botón "Editar" que fue presionado.
 */
function abrirModalEditar(btn) {
    // 1. Obtener todos los datos guardados en el botón
    const data = btn.dataset;

    // 2. Rellenar los campos del formulario
    inputIdDisplay.value = data.id;
    inputNombre.value = data.nombre;
    inputDescripcion.value = data.descripcion || ''; 
    inputMiembrosMax.value = data.miembros_max;
    inputEstado.value = data.estado;
    inputAcceso.value = data.acceso;
    inputUbicacion.value = data.ubicacion || '';
    inputTorneo.value = data.torneo || '';

    // 3. Actualizar la acción del formulario con el ID correcto
    // Esto es vital para que Laravel sepa qué equipo actualizar
    formEditar.action = `/admin/equipos/${data.id}`;

    // 4. Mostrar el modal
    modal.style.display = "flex";
}

/**
 * Cierra el modal ocultándolo
 */
function cerrarModal() {
    modal.style.display = "none";
}

// Cerrar si hacen clic fuera de la tarjeta blanca (en el fondo oscuro)
window.onclick = function(event) {
    if (event.target == modal) {
        cerrarModal();
    }
}


/**
 * Redirige a la página pública del equipo
 * @param {number} id 
 */
function verEquipo(id) {
    window.location.href = `/equipos/${id}`;
}

/**
 * Solicita confirmación y envía una petición para eliminar el equipo
 * @param {number} id 
 * @param {string} nombre 
 */
function eliminarEquipo(id, nombre) {
    if (confirm(`¿Estás seguro de que deseas eliminar el equipo "${nombre}"? Esta acción no se puede deshacer.`)) {
        
        // Creamos un formulario invisible temporalmente para enviar la petición DELETE
        // Esto es necesario porque los enlaces normales son GET
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/equipos/${id}`;
        
        // Token CSRF (Obligatorio en Laravel)
        // Intenta obtenerlo del meta tag, si no existe, puede fallar
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const token = tokenMeta ? tokenMeta.content : '';

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = token;
        
        // Método DELETE spoofing
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}