<?php
session_start();
if (isset($_SESSION['idUsuario'])) {
    $idUsuarioREAL = $_SESSION['idUsuario'];
} else {
    echo "<p style='color: red;'>No se ha iniciado sesión o no hay ID disponible.</p>";
    exit;
}
require_once 'controlador/inicio.controlador.php';

// Crear el controlador para los paquetes
$controlador = new PaqueteController();

// Obtener todos los eventos
$eventos = $controlador->obtenerEventos();

// Variables para manejar datos de paquetes y servicios
$paquetes = [];
$totalServicios = 0;
$mensaje = "";
$eventoSeleccionado = "";
$paqueteSeleccionado = "";

// Verificar si se envió una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ver_paquetes']) && isset($_POST['id_evento'])) {
        // Obtener los paquetes del evento seleccionado
        $eventoSeleccionado = (int)$_POST['id_evento'];
        $paquetes = $controlador->obtenerPaquetesPorEvento($eventoSeleccionado);
    } elseif (isset($_POST['seleccionar_paquete']) && isset($_POST['id_paquete'])) {
        // Seleccionar el paquete
        $paqueteSeleccionado = (int)$_POST['id_paquete'];
        $eventoSeleccionado = (int)$_POST['id_evento'];
        $paquetes = $controlador->obtenerPaquetesPorEvento($eventoSeleccionado);
    } elseif (isset($_POST['id_paquete']) && isset($_POST['fecha_pago'])) {
        // Manejar registro de pago
        $paqueteSeleccionado = (int)$_POST['id_paquete'];
        $eventoSeleccionado = (int)$_POST['id_evento'];
        $fechaPago = $_POST['fecha_pago'];
        $montoTotal = $controlador->obtenerTotalServiciosPorEvento($paqueteSeleccionado);

        // Crear array de datos comunes
        $datos = [
            'id_usuarios' => $idUsuarioREAL,
            'id_paquete' => $paqueteSeleccionado,
            'id_evento' => $eventoSeleccionado,
            'monto_total' => $montoTotal,
            'fecha_pago' => $fechaPago,
            'plazos' => []
        ];

        if ($_POST['tipo_pago'] === 'plazos') {
            // Validar y añadir plazos si están presentes
            if (!empty($_POST['numero_plazo']) && !empty($_POST['fecha_plazo'])) {
                $numeroPlazo = (int)$_POST['numero_plazo'];
                $montoPlazo = $montoTotal / $numeroPlazo;
                $datos['plazos'][] = [
                    'numero_plazo' => $numeroPlazo,
                    'monto_plazo' => $montoPlazo,
                    'fecha_pago' => $_POST['fecha_plazo'],
                    'estado_pago' => 'pendiente'
                ];
            }

            // Procesar los datos mediante la clase ProcesarPagoPlazos
            $controladorPago = new ProcesarPagoPlazos($datos);
            $resultado = $controladorPago->procesar();
        } else {
            // Procesar los datos mediante la clase ProcesarPagoContado
            $controladorPago = new ProcesarPagoContado($datos);
            $resultado = $controladorPago->procesar();
        }
        $mensaje = $resultado;
    } else {
        $mensaje = "Por favor, complete todos los campos requeridos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/pagosForm.css">
    <title>Eventos y Paquetes</title>
    <script>
        function calcularMontoPlazo() {
            var montoTotal = parseFloat(document.getElementById('monto_total').innerText.replace('Monto Total del Paquete: $', ''));
            var numeroPlazo = parseInt(document.getElementById('numero_plazo').value);
            if (!isNaN(montoTotal) && !isNaN(numeroPlazo) && numeroPlazo > 0) {
                var montoPlazo = montoTotal / numeroPlazo;
                document.getElementById('monto_plazo').innerText = "Monto por Plazo: $" + montoPlazo.toFixed(2);
            } else {
                document.getElementById('monto_plazo').innerText = "";
            }
        }
    </script>
</head>

<body>
    <h1>Eventos y Paquetes</h1>

    <!-- Mostrar mensaje -->
    <?php if (!empty($mensaje)): ?>
        <p><strong><?= htmlspecialchars($mensaje); ?></strong></p>
        <?php if (strpos($mensaje, 'exitosamente') !== false): ?>
            <p style="color: green;">Pago confirmado.</p>
        <?php else: ?>
            <p style="color: red;">Hubo un error en el pago.</p>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Formulario para seleccionar evento -->
    <form method="POST">
        <label for="id_evento">Evento:</label>
        <select name="id_evento" id="id_evento" required>
            <option value="">-- Seleccionar --</option>
            <?php foreach ($eventos as $evento): ?>
                <option value="<?= $evento['id_eventos']; ?>"
                    <?= $eventoSeleccionado == $evento['id_eventos'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($evento['nombre_evento']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="ver_paquetes">Ver Paquetes</button>
    </form>

    <!-- Formulario para seleccionar paquete -->
    <?php if (!empty($paquetes)): ?>
        <form method="POST">
            <label for="id_paquete">Paquete Seleccionado:</label>
            <select name="id_paquete" id="id_paquete" required>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($paquetes as $paquete): ?>
                    <option value="<?= $paquete['id_paquete']; ?>"
                        <?= $paqueteSeleccionado == $paquete['id_paquete'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($paquete['nombre_paquete']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <!-- Mantener el ID del evento seleccionado para la próxima solicitud -->
            <input type="hidden" name="id_evento" value="<?= htmlspecialchars($eventoSeleccionado); ?>">
            <button type="submit" name="seleccionar_paquete">Seleccionar Paquete</button>
        </form>
    <?php endif; ?>

    <!-- Formulario para registrar pago -->
    <?php if (!empty($paqueteSeleccionado)): ?>
        <form method="POST">
            <input type="hidden" name="id_evento" value="<?= htmlspecialchars($eventoSeleccionado); ?>">
            <input type="hidden" name="id_paquete" value="<?= htmlspecialchars($paqueteSeleccionado); ?>">

            <!-- Usar el ID de usuario almacenado en la sesión -->
            <input type="hidden" name="id_usuarios" value="<?= htmlspecialchars($idUsuarioREAL); ?>">

            <!-- El monto total se calcula dinámicamente -->
            <p id="monto_total"><strong>Monto Total del Paquete:</strong>
                <?= $controlador->obtenerTotalServiciosPorEvento($paqueteSeleccionado); ?>
            </p>

            <label for="fecha_pago">Fecha de Pago:</label>
            <input type="date" id="fecha_pago" name="fecha_pago" required><br><br>

            <label for="tipo_pago">Tipo de Pago:</label>
            <select id="tipo_pago" name="tipo_pago" required>
                <option value="contado">Contado</option>
                <option value="plazos">Plazos</option>
            </select><br><br>

            <div id="plazos_detalles" style="display: none;">
                <h3>Detalles de los Plazos</h3>
                <label for="numero_plazo">Número de Plazo:</label>
                <input type="number" id="numero_plazo" name="numero_plazo" oninput="calcularMontoPlazo()" required><br><br>

                <p id="monto_plazo"></p>

                <label for="fecha_plazo">Fecha del Primer Plazo:</label>
                <input type="date" id="fecha_plazo" name="fecha_plazo" required><br><br>
            </div>

            <button type="submit">Registrar</button>
            <button type="submit" onclick="location.href='?c=procesoPago';">Regresar</button>


        </form>
    <?php endif; ?>

    <script>
        document.getElementById('tipo_pago').addEventListener('change', function() {
            var tipoPago = this.value;
            var plazosDetalles = document.getElementById('plazos_detalles');
            if (tipoPago === 'plazos') {
                plazosDetalles.style.display = 'block';
            } else {
                plazosDetalles.style.display = 'none';
            }
        });
    </script>
</body>

</html>