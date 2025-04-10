<?php
require_once 'modelo/consultasBD.php';
$paqueteInsercion = new PaqueteInsercion();
$servicios = $paqueteInsercion->obtenerServicios();
$eventos = $paqueteInsercion->obtenerEventos(); // Obtener los eventos
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/paquetes.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de paquetes</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
        <h1 class="centrar">
            <img src="../../img/logo1.png" class="logo" alt="logo1">
            <a href="index.php?c=admin" class="logo"><strong>Gala Perfecta</strong></a>
        </h1>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">

            </ul>
        </div>


    </nav>

    <div class="container-paquete">
        <div class="form-container-paq">
            <div class="form paquete-form active">
                <h2>Registro de paquetes</h2><br>
                <form class="login" action="index.php?c=cargaRegistroPaquete" method="POST">
                    <!-- Combo box para seleccionar el evento -->
                    <div class="input-box">
                        <label for="id_eventos">Selecciona un evento</label>
                        <select name="id_eventos" id="id_eventos" required>
                            <option value="" disabled selected>Selecciona un evento</option>
                            <?php
                            if (!empty($eventos)) {
                                foreach ($eventos as $evento) {
                                    echo "<option value='{$evento['id_eventos']}'>{$evento['nombre_evento']}</option>";
                                }
                            } else {
                                echo "<option value='' disabled>No hay eventos disponibles</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-box">
                        <input type="text" name="nombre_paquete" required>
                        <label>Nombre del paquete</label>
                    </div>
                    <div class="input-box">
                        <input type="file" name="ruta_imagen" required>
                        <label>Selecciona la primera imagen</label>
                    </div>
                    <div class="input-box">
                        <input type="text" name="descripcion" required>
                        <label>Descripcion del paquete</label>
                    </div>
                    <div class="input-box">
                        <input type="file" name="ruta_imagen1" required>
                        <label>Selecciona la segunda imagen</label>
                    </div>
                    <div class="input-box">
                        <input type="file" name="ruta_imagen2" required>
                        <label>Selecciona la tercera imagen</label>
                    </div>
                    <div class="input-box">
                        <input type="file" name="ruta_imagen3" required>
                        <label>Selecciona la cuarta imagen</label>
                    </div>

                    <!-- Checkboxes de servicios -->
                    <div class="input-box-check">
                        <label>Selecciona los servicios:</label>
                        <div class="checkbox-group">
                            <?php
                    
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
                    <button type="submit" class="btn">Registrar</button>
                    <button type="button" onclick="history.back()" class="btn btn-secondary">Regresar</button>
                </form>
            </div>

        </div>
        <div class="welcome-section">
            <h1>¡Bienvenido!</h1>
            <p>Registra tus nuevos paquetes.</p>
        </div>
    </div>

</body>

</html>