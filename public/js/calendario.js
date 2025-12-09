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

    let fechaActual = new Date(); // Inicializar con la fecha actual
    let diaTooltipAbierto = null; // Guardar referencia al día que tiene el tooltip abierto 

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
                const eventosEnFecha = eventosBD.filter(evento => 
                    evento.fecha_inicio && evento.fecha_inicio.startsWith(fechaString)
                );
                
                if (eventosEnFecha.length > 0) {
                    // Verificar si hay eventos pasados y futuros
                    const hayEventosPasados = eventosEnFecha.some(e => e.es_pasado);
                    const hayEventosFuturos = eventosEnFecha.some(e => !e.es_pasado);
                    
                    // Crear punto de evento
                    const punto = document.createElement('div');
                    
                    if (hayEventosPasados && hayEventosFuturos) {
                        // Si hay ambos, mostrar como mixto (evento futuro tiene prioridad visual)
                        punto.classList.add('evento-punto', 'evento-futuro');
                    } else if (hayEventosPasados) {
                        // Solo eventos pasados
                        punto.classList.add('evento-punto', 'evento-pasado');
                    } else {
                        // Solo eventos futuros
                        punto.classList.add('evento-punto', 'evento-futuro');
                    }
                    
                    divDia.appendChild(punto);
                    
                    // Aplicar estilo al borde del día según el tipo de evento
                    if (hayEventosPasados && !hayEventosFuturos) {
                        // Solo eventos pasados: borde gris
                        divDia.style.borderColor = "#94a3b8";
                        divDia.style.borderWidth = "2px";
                        divDia.classList.add('dia-con-evento-pasado');
                    } else {
                        // Eventos futuros o mixtos: borde rojo
                        divDia.style.borderColor = "#e91e63";
                        divDia.style.borderWidth = "2px";
                    }
                    
                    // Agregar cursor pointer y eventos para el tooltip
                    divDia.style.cursor = 'pointer';
                    divDia.classList.add('dia-con-evento');
                    
                    // Guardar eventos en el elemento para acceso en los event listeners
                    divDia.dataset.eventos = JSON.stringify(eventosEnFecha);
                    
                    // Event listener para mostrar/ocultar tooltip al hacer click
                    divDia.addEventListener('click', function(e) {
                        e.stopPropagation(); // Evitar que se propague el click
                        
                        // Si este día ya tiene el tooltip abierto, cerrarlo
                        if (diaTooltipAbierto === divDia) {
                            ocultarTooltipEvento();
                            diaTooltipAbierto = null;
                        } else {
                            // Cerrar tooltip anterior si existe
                            if (diaTooltipAbierto) {
                                ocultarTooltipEvento();
                            }
                            // Mostrar tooltip para este día
                            mostrarTooltipEvento(divDia, eventosEnFecha);
                            diaTooltipAbierto = divDia;
                        }
                    });
                }
            }

            // Marcar día seleccionado
            if (i === fecha.getDate() && mes === fecha.getMonth() && anio === fecha.getFullYear()) {
                divDia.classList.add('activo');
            }

            // Click para cambiar fecha (solo si no tiene eventos)
            if (!divDia.classList.contains('dia-con-evento')) {
                divDia.addEventListener('click', () => {
                    // Cerrar tooltip si está abierto
                    if (diaTooltipAbierto) {
                        ocultarTooltipEvento();
                        diaTooltipAbierto = null;
                    }
                    fechaActual = new Date(anio, mes, i);
                    renderizarCalendario(fechaActual);
                });
            }
            
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
    if (selectorMes) selectorMes.addEventListener('change', () => { 
        if (diaTooltipAbierto) {
            ocultarTooltipEvento();
            diaTooltipAbierto = null;
        }
        fechaActual.setMonth(parseInt(selectorMes.value)); 
        fechaActual.setDate(1); 
        renderizarCalendario(fechaActual); 
    });
    if (selectorAnio) selectorAnio.addEventListener('change', () => { 
        if (diaTooltipAbierto) {
            ocultarTooltipEvento();
            diaTooltipAbierto = null;
        }
        fechaActual.setFullYear(parseInt(selectorAnio.value)); 
        renderizarCalendario(fechaActual); 
    });
    if (btnAnterior) btnAnterior.addEventListener('click', () => { 
        if (diaTooltipAbierto) {
            ocultarTooltipEvento();
            diaTooltipAbierto = null;
        }
        fechaActual.setMonth(fechaActual.getMonth() - 1); 
        renderizarCalendario(fechaActual); 
    });
    if (btnSiguiente) btnSiguiente.addEventListener('click', () => { 
        if (diaTooltipAbierto) {
            ocultarTooltipEvento();
            diaTooltipAbierto = null;
        }
        fechaActual.setMonth(fechaActual.getMonth() + 1); 
        renderizarCalendario(fechaActual); 
    });
    if (btnHoy) btnHoy.addEventListener('click', () => { 
        if (diaTooltipAbierto) {
            ocultarTooltipEvento();
            diaTooltipAbierto = null;
        }
        fechaActual = new Date(); 
        renderizarCalendario(fechaActual); 
    });

    // Cerrar tooltip al hacer click fuera
    document.addEventListener('click', function(e) {
        const tooltip = document.getElementById('evento-tooltip');
        const contenedorDias = document.getElementById('contenedor-dias');
        
        // Si el click no fue en el tooltip ni en un día con evento, cerrar tooltip
        if (tooltip && !tooltip.contains(e.target) && 
            contenedorDias && !contenedorDias.contains(e.target)) {
            if (diaTooltipAbierto) {
                ocultarTooltipEvento();
                diaTooltipAbierto = null;
            }
        }
    });

    renderizarCalendario(fechaActual);
});

// Funciones para manejar el tooltip de eventos
function mostrarTooltipEvento(elementoDia, eventos) {
    const tooltip = document.getElementById('evento-tooltip');
    if (!tooltip || eventos.length === 0) return;
    
    // Si hay múltiples eventos, mostrar el primero (o el más próximo)
    const evento = eventos[0];
    
    // Llenar información del tooltip
    document.getElementById('tooltip-titulo').textContent = evento.titulo;
    document.getElementById('tooltip-fecha-inicio').querySelector('span').textContent = `Inicio: ${evento.fecha_inicio_formateada}`;
    document.getElementById('tooltip-fecha-limite').querySelector('span').textContent = `Inscripción hasta: ${evento.fecha_limite_formateada}`;
    document.getElementById('tooltip-ubicacion').querySelector('span').textContent = evento.ubicacion;
    document.getElementById('tooltip-participantes').querySelector('span').textContent = `${evento.participantes_actuales}/${evento.participantes_max} participantes`;
    
    // Mostrar organización si existe
    const tooltipOrg = document.getElementById('tooltip-organizacion');
    if (evento.organizacion && evento.organizacion.trim() !== '') {
        tooltipOrg.style.display = 'flex';
        tooltipOrg.querySelector('span').textContent = evento.organizacion;
    } else {
        tooltipOrg.style.display = 'none';
    }
    
    // Configurar link de editar
    const linkEditar = document.getElementById('tooltip-link-editar');
    if (linkEditar) {
        linkEditar.href = `/admin/eventos/${evento.id}/editar`;
    }
    
    // Mostrar tooltip
    tooltip.style.display = 'block';
    actualizarPosicionTooltip(elementoDia);
}

function ocultarTooltipEvento() {
    const tooltip = document.getElementById('evento-tooltip');
    if (tooltip) {
        tooltip.style.display = 'none';
    }
}

function actualizarPosicionTooltip(elementoDia) {
    const tooltip = document.getElementById('evento-tooltip');
    if (!tooltip || tooltip.style.display === 'none' || !elementoDia) return;
    
    // Obtener posición del día en la pantalla
    const rectDia = elementoDia.getBoundingClientRect();
    const tooltipWidth = tooltip.offsetWidth || 300;
    const tooltipHeight = tooltip.offsetHeight || 200;
    const windowWidth = window.innerWidth;
    const windowHeight = window.innerHeight;
    const offset = 15; // Distancia del día
    
    // Calcular posición X - intentar a la derecha primero
    let left = rectDia.right + offset;
    if (left + tooltipWidth > windowWidth) {
        // Si no cabe a la derecha, ponerlo a la izquierda
        left = rectDia.left - tooltipWidth - offset;
    }
    if (left < 0) left = 10; // Si tampoco cabe a la izquierda, pegar al borde
    
    // Calcular posición Y - centrar verticalmente con el día
    let top = rectDia.top + (rectDia.height / 2) - (tooltipHeight / 2);
    if (top < 10) top = 10; // Si se sale por arriba, pegar arriba
    if (top + tooltipHeight > windowHeight - 10) {
        // Si se sale por abajo, ajustar
        top = windowHeight - tooltipHeight - 10;
    }
    
    tooltip.style.left = left + 'px';
    tooltip.style.top = top + 'px';
}