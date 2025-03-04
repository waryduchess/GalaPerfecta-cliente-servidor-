<?php
require_once 'controlador/inicio.controlador.php';
$controlador = new inicioControladorMenu();
$evento = $controlador->mostrarEventos();

if (!$evento) {
    die("No se pudo obtener la información del evento.");
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/menu.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo htmlspecialchars($evento->nombre_evento); ?></title>
</head>

<body>
    <div class="container">
        <div class="row">
            <h1 class="centrar">
                <img src="../../img/logo1.png" class="logo" alt="logo1">
                <a href="index.php"><strong>El gran día</strong></a>
            </h1>
        </div>

        <main class="content">
            <div class="image-gallery">
                <img id="mainImage" class="main-image" src="<?php echo htmlspecialchars($evento->paquetes[0]['ruta_imagen'] ?? '../../img/default.jpg'); ?>" alt="Imagen principal del paquete">

                <div class="thumbnail-gallery">
                    <?php foreach ($evento->paquetes as $paquete): ?>
                        <?php if (!empty($paquete['ruta_imagen1'])): ?>
                            <img class="thumbnail" src="<?php echo htmlspecialchars($paquete['ruta_imagen1']); ?>" alt="Miniatura 1">
                        <?php endif; ?>
                        <?php if (!empty($paquete['ruta_imagen2'])): ?>
                            <img class="thumbnail" src="<?php echo htmlspecialchars($paquete['ruta_imagen2']); ?>" alt="Miniatura 2">
                        <?php endif; ?>
                        <?php if (!empty($paquete['ruta_imagen3'])): ?>
                            <img class="thumbnail" src="<?php echo htmlspecialchars($paquete['ruta_imagen3']); ?>" alt="Miniatura 3">
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>


            <div class="description">
                <h2><?php echo htmlspecialchars($evento->nombre_evento); ?></h2>
                <p>
                    <strong><?php echo htmlspecialchars($evento->nombre_evento); ?></strong> es un evento con los siguientes paquetes y servicios:
                </p>
                <ul class="tags">
                    <?php foreach ($evento->paquetes as $paquete): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($paquete['nombre_paquete']); ?>:</strong>
                            <p><?php echo htmlspecialchars($paquete['descripcion']); ?></p> <!-- Descripción añadida -->
                            <span>Total del paquete: <?php echo '$' . number_format($paquete['total_paquete'], 2); ?></span>
                            <ul>
                                <?php foreach ($paquete['servicios'] as $servicio): ?>
                                    <li><?php echo htmlspecialchars($servicio['nombre_servicio']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="buttons">
                    <button class="btn primary" onclick="location.href='?c=cotizacion';">Cotizar Gratis</button>
                    <button class="btn secondary" onclick="location.href='https://web.whatsapp.com/';">Mándanos un WhatsApp</button>
                    <button class="btn terciario" onclick="location.href='index.php';">Regresar</button>
                </div>
            </div>
        </main>
        <!--<footer class="footer">
            <div>Entregas y servicios</div>
            <div>Te cuidamos</div>
        </footer>-->
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Obtener la imagen principal y las miniaturas
            const mainImage = document.getElementById("mainImage");
            const thumbnails = document.querySelectorAll(".thumbnail");

            // Agregar un evento de clic a cada miniatura
            thumbnails.forEach(thumbnail => {
                thumbnail.addEventListener("click", function() {
                    // Actualizar la imagen principal con la miniatura seleccionada
                    mainImage.src = this.src;
                    mainImage.alt = this.alt; // Cambiar también el texto alternativo
                });
            });
        });
    </script>

</body>

</html>