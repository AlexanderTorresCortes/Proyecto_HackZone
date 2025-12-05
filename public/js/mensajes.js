// === JAVASCRIPT PARA LA VISTA DE MENSAJES CON USUARIOS REALES ===

document.addEventListener('DOMContentLoaded', function() {

    // Seleccionar elementos del DOM
    const formEnviarMensaje = document.getElementById('formEnviarMensaje');
    const inputMensaje = document.getElementById('inputMensaje');
    const conversacionMensajes = document.getElementById('conversacionMensajes');
    const inputBuscarChat = document.getElementById('buscarChat');

    // === ENVIAR MENSAJE VÍA AJAX ===
    if (formEnviarMensaje) {
        formEnviarMensaje.addEventListener('submit', function(e) {
            e.preventDefault();

            const mensaje = inputMensaje.value.trim();
            const chatId = document.querySelector('input[name="chat_id"]').value;

            if (mensaje === '') return;

            // Enviar mensaje vía AJAX
            fetch('/mensajes/enviar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({
                    chat_id: chatId,
                    mensaje: mensaje
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Agregar mensaje al DOM
                    agregarMensajePropio(mensaje);

                    // Limpiar input
                    inputMensaje.value = '';

                    // Scroll hacia abajo
                    if (conversacionMensajes) {
                        conversacionMensajes.scrollTop = conversacionMensajes.scrollHeight;
                    }
                }
            })
            .catch(error => {
                console.error('Error al enviar mensaje:', error);
                alert('Hubo un error al enviar el mensaje. Intenta de nuevo.');
            });
        });
    }

    // === FUNCIÓN PARA AGREGAR MENSAJE PROPIO AL DOM ===
    function agregarMensajePropio(texto) {
        const ahora = new Date();
        const hora = ahora.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });

        const nuevoMensaje = document.createElement('div');
        nuevoMensaje.className = 'mensaje mensaje-derecha';
        nuevoMensaje.innerHTML = `
            <div class="mensaje-contenido">
                <div class="mensaje-texto">${texto}</div>
                <div class="mensaje-hora">${hora}</div>
            </div>
        `;

        if (conversacionMensajes) {
            conversacionMensajes.appendChild(nuevoMensaje);
        }
    }

    // === BUSCAR CHATS ===
    if (inputBuscarChat) {
        inputBuscarChat.addEventListener('input', function() {
            const busqueda = this.value.toLowerCase();
            const chatItems = document.querySelectorAll('.chat-item');

            chatItems.forEach(item => {
                const nombre = item.querySelector('.chat-nombre').textContent.toLowerCase();
                const preview = item.querySelector('.chat-preview').textContent.toLowerCase();

                if (nombre.includes(busqueda) || preview.includes(busqueda)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // === SCROLL AUTOMÁTICO AL CARGAR ===
    if (conversacionMensajes) {
        conversacionMensajes.scrollTop = conversacionMensajes.scrollHeight;
    }

    // === ENVIAR CON ENTER ===
    if (inputMensaje) {
        inputMensaje.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                formEnviarMensaje.dispatchEvent(new Event('submit'));
            }
        });
    }

});

// === FUNCIONES PARA EL MODAL DE NUEVO CHAT ===
function abrirModalNuevoChat() {
    const modal = document.getElementById('modalNuevoChat');
    if (modal) {
        modal.classList.add('show');
        modal.style.display = 'flex';
    }
}

function cerrarModalNuevoChat(event) {
    if (event.target.id === 'modalNuevoChat') {
        const modal = document.getElementById('modalNuevoChat');
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
        }
    }
}

// === FILTRAR USUARIOS EN EL MODAL ===
function filtrarUsuarios() {
    const busqueda = document.getElementById('buscarUsuario').value.toLowerCase();
    const usuarioItems = document.querySelectorAll('.usuario-item');

    usuarioItems.forEach(item => {
        const nombre = item.querySelector('.usuario-nombre').textContent.toLowerCase();
        const email = item.querySelector('.usuario-email').textContent.toLowerCase();

        if (nombre.includes(busqueda) || email.includes(busqueda)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

// === CERRAR MODAL CON ESC ===
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('modalNuevoChat');
        if (modal && modal.classList.contains('show')) {
            modal.classList.remove('show');
            modal.style.display = 'none';
        }
    }
});
