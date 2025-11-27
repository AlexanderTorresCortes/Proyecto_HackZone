// Función para mostrar/ocultar contraseña
function togglePassword(inputId, iconElement) {
    const input = document.getElementById(inputId);
    
    if (input.type === 'password') {
        input.type = 'text';
        iconElement.textContent = 'visibility';
    } else {
        input.type = 'password';
        iconElement.textContent = 'visibility_off';
    }
}

// Validación de contraseñas cuando se envía el formulario
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Las contraseñas no coinciden. Por favor, verifica e intenta nuevamente.');
        return false;
    }
    
    // Validar longitud mínima de contraseña
    if (password.length < 8) {
        e.preventDefault();
        alert('La contraseña debe tener al menos 8 caracteres.');
        return false;
    }
});

// Validación en tiempo real de coincidencia de contraseñas
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword.length > 0) {
        if (password !== confirmPassword) {
            this.style.borderColor = '#ff4444';
        } else {
            this.style.borderColor = '#4CAF50';
        }
    } else {
        this.style.borderColor = 'transparent';
    }
});