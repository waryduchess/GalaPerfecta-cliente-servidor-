    function mostrarCotizacion() {
    const cotizacionForm = document.getElementById('formPaquete');  // Primer formulario (cotización)
    const resultadoForm = document.getElementById('formResultado');  // Segundo formulario (resultado)

    const formulario = document.getElementById('cotizacionForm');
    
    const nombrePaquete = formulario.nombre_paquete.value;
    const nombreEvento = formulario.nombre_evento.options[formulario.nombre_evento.selectedIndex]?.text || "Evento no seleccionado";
    const servicios = formulario.querySelectorAll('input[name="servicios[]"]:checked');
    
    let totalCotizacion = 0;
    let detalleServicios = '';
    const tipoCambio = 18.5; // Tipo de cambio de ejemplo, actualízalo según sea necesario

    // Itera sobre los servicios seleccionados
    servicios.forEach((servicio) => {
        const precioServicioDolares = parseFloat(servicio.value);  // Obtiene el precio en USD
        const precioServicioMXN = precioServicioDolares * tipoCambio;  // Convierte a MXN
        const descuento = precioServicioMXN * 0.10;  // Calcula el 10% de descuento
        const precioFinal = precioServicioMXN - descuento;  // Aplica el descuento

        totalCotizacion += precioFinal;  // Acumula el total
        detalleServicios += `${servicio.parentElement.textContent.trim()} - ${precioFinal.toFixed(2)} MXN<br>`;  // Detalle del servicio con el precio con descuento
    });

    // Muestra los resultados de la cotización
    const detalleCotizacionHTML = document.getElementById('detalleCotizacion');
    detalleCotizacionHTML.innerHTML = `
        <strong>Nombre del Paquete:</strong> ${nombrePaquete}<br>
        <strong>Evento seleccionado:</strong> ${nombreEvento}<br>
        <strong>Servicios seleccionados:</strong><br> ${detalleServicios}
        <strong>Total de la Cotización (con descuento):</strong> ${totalCotizacion.toFixed(2)} MXN
    `;

    // Oculta el formulario de cotización y muestra el formulario de resultados
    cotizacionForm.classList.remove('active');
    resultadoForm.classList.add('active');
}
