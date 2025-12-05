// @ts-nocheck
document.addEventListener('DOMContentLoaded', function() {
    
    // Referencias
    const selectorMes = document.getElementById('selector-mes');
    const selectorAnio = document.getElementById('selector-anio');
    const contenedorDias = document.getElementById('contenedor-dias');
    
    const encNumDia = document.getElementById('encabezado-num-dia');
    const encNomDia = document.getElementById('encabezado-nom-dia');
    const encMesAnio = document.getElementById('encabezado-mes-anio');
    
    const btnHoy = document.getElementById('btn-hoy');
    const btnAnterior = document.getElementById('btn-mes-anterior');
    const btnSiguiente = document.getElementById('btn-mes-siguiente');

    let fechaActual = new Date(2025, 7, 31); 

    function renderizarCalendario(fecha) {
        const anio = fecha.getFullYear();
        const mes = fecha.getMonth();

        // Actualizar UI
        if (selectorMes) selectorMes.value = mes;
        if (selectorAnio) selectorAnio.value = anio;
        if (encNumDia) encNumDia.textContent = fecha.getDate();
        if (encNomDia) encNomDia.textContent = fecha.toLocaleDateString('es-ES', { weekday: 'short' });
        if (encMesAnio) encMesAnio.textContent = fecha.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });

        if (!contenedorDias) return;
        contenedorDias.innerHTML = "";
        
        const indicePrimerDia = new Date(anio, mes, 1).getDay();
        const ultimoDia = new Date(anio, mes + 1, 0).getDate();
        const ultimoDiaPrevio = new Date(anio, mes, 0).getDate();

        // Días mes anterior
        for (let x = indicePrimerDia; x > 0; x--) {
            const divDia = document.createElement('div');
            divDia.classList.add('dia', 'inactivo');
            divDia.textContent = ultimoDiaPrevio - x + 1;
            contenedorDias.appendChild(divDia);
        }

        // Días mes actual
        for (let i = 1; i <= ultimoDia; i++) {
            const divDia = document.createElement('div');
            divDia.classList.add('dia');
            divDia.textContent = i;

            // 1. Formateamos la fecha actual del bucle a YYYY-MM-DD
            // (Sumamos 1 al mes porque en JS enero es 0)
            const mesFormat = String(mes + 1).padStart(2, '0');
            const diaFormat = String(i).padStart(2, '0');
            const fechaString = `${anio}-${mesFormat}-${diaFormat}`;

            // 2. Buscamos si hay eventos en esta fecha (usando la variable global eventosBD)
            if (typeof eventosBD !== 'undefined') {
                // Filtramos eventos que coincidan con la fecha
                const hayEvento = eventosBD.some(evento => evento.fecha_inicio.startsWith(fechaString));
                
                if (hayEvento) {
                    const punto = document.createElement('div');
                    punto.classList.add('evento-punto'); // Clase para el puntito rojo
                    divDia.appendChild(punto);
                    
                    // Opcional: Poner borde morado al día
                    divDia.style.borderColor = "#9c27b0"; 
                }
            }

            // Marcar día seleccionado
            if (i === fecha.getDate() && mes === fecha.getMonth() && anio === fecha.getFullYear()) {
                divDia.classList.add('activo');
            }

            divDia.addEventListener('click', () => {
                fechaActual = new Date(anio, mes, i);
                renderizarCalendario(fechaActual);
            });
            
            contenedorDias.appendChild(divDia);
        }
        
        // Relleno mes siguiente
        const totalCuadros = contenedorDias.children.length;
        const diasFaltantes = 42 - totalCuadros;
        for(let j = 1; j <= diasFaltantes; j++) {
            const divDia = document.createElement('div');
            divDia.classList.add('dia', 'inactivo');
            divDia.textContent = j;
            contenedorDias.appendChild(divDia);
        }
    }

    // Listeners
    if (selectorMes) selectorMes.addEventListener('change', () => { fechaActual.setMonth(parseInt(selectorMes.value)); fechaActual.setDate(1); renderizarCalendario(fechaActual); });
    if (selectorAnio) selectorAnio.addEventListener('change', () => { fechaActual.setFullYear(parseInt(selectorAnio.value)); renderizarCalendario(fechaActual); });
    if (btnAnterior) btnAnterior.addEventListener('click', () => { fechaActual.setMonth(fechaActual.getMonth() - 1); renderizarCalendario(fechaActual); });
    if (btnSiguiente) btnSiguiente.addEventListener('click', () => { fechaActual.setMonth(fechaActual.getMonth() + 1); renderizarCalendario(fechaActual); });
    if (btnHoy) btnHoy.addEventListener('click', () => { fechaActual = new Date(); renderizarCalendario(fechaActual); });

    renderizarCalendario(fechaActual);
});