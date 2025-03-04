<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/cotizaciones.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de paquetes</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
        <h1 class="centrar">
            <img src="../../img/logo1.png" class="logo" alt="logo1">
            <a href="index.php" class="logo"><strong>El gran día</strong></a>
        </h1>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav"></ul>
        </div>
    </nav>

    <div class="container-paquete">
        <div class="form-container-paq">
            <!-- Formulario de selección de evento y servicios -->
            <div class="form paquete-form active">
                <h2>Cotización</h2><br>
                <form id="cotizacionForm">
                    <div class="input-box">
                        <label for="nombre_evento">Selecciona el evento:</label>
                        <select name="nombre_evento" id="nombre_evento" required>
                            <option value="" disabled selected>Selecciona un evento</option>
                            <?php
                            // Incluye tu archivo de consultas
                            require_once 'modelo/consultasBD.php';

                            // Crea la instancia de la clase
                            $paqueteInsercion = new cotizacionInsercion();

                            // Obtén los eventos
                            $eventos = $paqueteInsercion->obtenerEventosCotizacion();

                            // Verifica si los eventos están siendo recuperados correctamente
                            echo "<pre>";
                            print_r($eventos);  // Esto imprimirá los eventos
                            echo "</pre>";

                            // Si los eventos están disponibles, se generan las opciones
                            if (!empty($eventos)) {
                                foreach ($eventos as $evento) {
                                    echo "<option value='{$evento['id_eventos']}'>{$evento['nombre_evento']}</option>";
                                }
                            } else {
                                // Si no hay eventos, muestra un mensaje
                                echo "<option value='' disabled>No hay eventos disponibles</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="input-box">
                        <input type="text" name="nombre_paquete" required>
                        <label>Nombra tu paquete</label>
                    </div>

                    <!-- Checkboxes de servicios -->
                    <div class="input-box-check">
                        <label>Selecciona los servicios:</label>
                        <div class="checkbox-group">
                            <?php
                            require_once 'modelo/consultasBD.php';
                            $paqueteInsercion = new cotizacionInsercion();
                            $servicios = $paqueteInsercion->obtenerServiciosCotizacion();

                            if (!empty($servicios)) {
                                foreach ($servicios as $servicio) {
                                    echo "<label><input type='checkbox' name='servicios[]' value='{$servicio['id_servicio']}'> {$servicio['nombre_servicio']}</label>";
                                }
                            } else {
                                echo "<p>No hay servicios disponibles.</p>";
                            }
                            ?>
                        </div>
                    </div>



                    <button type="button" onclick="mostrarCotizacion()" class="btn btn-secondary">Cotizar</button>
                    <button type="button" onclick="history.back()" class="btn btn-secondary">Regresar</button>

                    <button type="button" onclick="location.href='?c=pagos';" class="btn btn-secondary">Pagar</button>
                </form>

            </div>

        </div>

        <div class="welcome-section">
        <h2>Resultado de la Cotización</h2><br>
        <div id="detalleCotizacion"></div>
        </div>

    </div>
  

    <script src="../../scriptcoti.js"></script>
    <!-- Formulario de resultado de la cotización-->

</body>

</html>