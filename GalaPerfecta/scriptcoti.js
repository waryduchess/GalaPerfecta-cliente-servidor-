function mostrarCotizacion() {
    const serviciosSeleccionados = [];
    let total = 0;
    
    // Obtener todos los checkboxes marcados
    document.querySelectorAll('input[name="servicios[]"]:checked').forEach(checkbox => {
        const precio = parseFloat(checkbox.dataset.precio);
        total += precio;
        
        serviciosSeleccionados.push({
            nombre: checkbox.parentNode.textContent.split('-')[0].trim(),
            precio: precio
        });
    });

    // Generar HTML para el detalle
    let htmlDetalle = '<div class="detalle-cotizacion">';
    htmlDetalle += '<h4>Servicios seleccionados:</h4>';
    htmlDetalle += '<ul class="lista-servicios">';
    
    serviciosSeleccionados.forEach(servicio => {
        htmlDetalle += `
            <li>
                <span class="nombre-servicio">${servicio.nombre}</span>
                <span class="precio-servicio">\$${servicio.precio.toFixed(2)}</span>
            </li>`;
    });
    
    htmlDetalle += '</ul>';
    htmlDetalle += `<div class="total-cotizacion">
                      <strong>Total:</strong> \$${total.toFixed(2)}
                   </div>`;
    htmlDetalle += '</div>';

    document.getElementById('detalleCotizacion').innerHTML = htmlDetalle;
}