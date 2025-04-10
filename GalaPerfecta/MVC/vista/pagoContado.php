<?php
session_start();
require_once 'modelo/consultasBD.php';

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

        // Agregar validaciones adicionales
        if ($montoTotal <= 0) {
            $mensaje = "Error: El monto total debe ser mayor a cero.";
            // Detener el proceso
            return;
        }

        if (strtotime($fechaPago) < strtotime('today')) {
            $mensaje = "Error: La fecha de pago no puede ser anterior a hoy.";
            // Detener el proceso
            return;
        }

        if ($_POST['tipo_pago'] === 'contado') {
            try {
                // Crear instancia de la clase Pagos
                $pagos = new Pagos();
                
                // Registrar el pago al contado
                $resultado = $pagos->registrarPagoContado(
                    $idUsuarioREAL,
                    $paqueteSeleccionado,
                    $montoTotal,
                    $fechaPago
                );

                if (!$resultado['error']) {
                    $mensaje = $resultado['mensaje'];
                } else {
                    $mensaje = "Error: " . $resultado['mensaje'];
                }
            } catch (Exception $e) {
                $mensaje = "Error: " . $e->getMessage();
            }
        } else {
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
            $mensaje = $resultado;
        }
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
        <div class="alert <?= strpos($mensaje, 'Error') !== false ? 'alert-danger' : 'alert-success' ?>">
            <?= htmlspecialchars($mensaje); ?>
            <?php if (isset($resultado['idPago'])): ?>
                <br>ID del Pago: <?= htmlspecialchars($resultado['idPago']); ?>
            <?php endif; ?>
        </div>
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
            <input type="hidden" name="id_usuarios" value="<?= htmlspecialchars($idUsuarioREAL); ?>">

            <!-- Mostrar el monto total -->
            <p id="monto_total">
                <strong>Monto Total del Paquete: $</strong>
                <?= number_format($controlador->obtenerTotalServiciosPorEvento($paqueteSeleccionado), 2); ?>
            </p>

            <label for="fecha_pago">Fecha de Pago:</label>
            <input type="date" id="fecha_pago" name="fecha_pago" required><br><br>

            <label for="tipo_pago">Tipo de Pago:</label>
            <select id="tipo_pago" name="tipo_pago" required>
                <option value="contado">Contado</option>
                <option value="plazos">Plazos</option>
            </select><br><br>

            <button type="submit">Registrar Pago</button>
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