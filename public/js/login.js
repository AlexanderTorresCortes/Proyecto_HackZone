function seleccionarRol(boton, rol) {
    let botones = document.querySelectorAll('.selector-rol button');
    botones.forEach(btn => btn.classList.remove('activo'));
    boton.classList.add('activo');
    document.getElementById('inputRol').value = rol;
}

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