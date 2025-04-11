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
    <title>Eventos y Paquetes</title>
    <link href="https://fonts.googleapis.com/css2?family=Georgia&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Georgia', serif;
            color: #333;
            line-height: 1.6;
            background-color: #f5f5f5;
        }

        .parent {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            grid-template-rows: auto auto auto auto auto auto;
            grid-column-gap: 0px;
            grid-row-gap: 0px;
            min-height: 100vh;
        }

        .div1 {
            grid-area: 1 / 1 / 2 / 13;
            background-color: #ddc3a7;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .div2 {
            grid-area: 2 / 1 / 3 / 13;
            background-color: #18213b;
            padding: 30px;
            color: white;
            text-align: center;
        }

        .div3 {
            grid-area: 3 / 1 / 4 / 13;
            padding: 20px;
            background-color: white;
        }

        .div4 {
            grid-area: 4 / 1 / 5 / 13;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .div5 {
            grid-area: 5 / 1 / 6 / 13;
            padding: 20px;
            background-color: white;
        }

        .div6 {
            grid-area: 6 / 1 / 7 / 13;
            background-color: #18213b;
            color: white;
            padding: 20px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .presentacion {
            text-align: center;
            font-family: 'Georgia', serif;
            color: #18213b;
            font-weight: bold;
        }

        h1 {
            font-family: 'Georgia', serif;
            text-align: center;
            color: white;
            margin-bottom: 20px;
        }

        h2 {
            font-family: 'Georgia', serif;
            text-align: center;
            color: #18213b;
            margin-bottom: 15px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #18213b;
        }

        select, input[type="date"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddc3a7;
            border-radius: 6px;
            font-family: 'Georgia', serif;
            font-size: 16px;
        }

        select:focus, input[type="date"]:focus {
            outline: none;
            border-color: #18213b;
            box-shadow: 0 0 0 2px rgba(24, 33, 59, 0.2);
        }

        button {
            background-color: #18213b;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 30px;
            font-family: 'Georgia', serif;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #111a2f;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .alert-success {
            background-color: rgba(221, 195, 167, 0.2);
            color: #18213b;
            border-left: 4px solid #ddc3a7;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        .panel-titulo {
            background-color: #ddc3a7;
            color: #18213b;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
        }

        #monto_total {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            padding: 15px;
            background-color: rgba(24, 33, 59, 0.1);
            border-radius: 6px;
            text-align: center;
            color: #18213b;
        }

        #plazos_detalles {
            display: none;
            padding: 15px;
            background-color: #f8f8f8;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #ddc3a7;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .btn-regresar {
            background-color: #ddc3a7;
            color: #18213b;
        }

        .btn-regresar:hover {
            background-color: #c9ae92;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
        }
    </style>
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
    <div class="parent">
        <div class="div1">
            <div class="presentacion">GESTIÓN DE EVENTOS Y PAQUETES</div>
        </div>
        
        <div class="div2">
            <h1>Selección de Eventos y Paquetes</h1>
        </div>
        
        <div class="div3">
            <div class="container">
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
                <div class="panel-titulo">SELECCIÓN DE EVENTO</div>
                <form method="POST">
                    <div class="form-group">
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
                    </div>
                    <div class="form-actions">
                        <a href="http://localhost:3001/MVC/index.php?c=mandarTipoPago" class="btn-regresar" style="text-decoration: none; padding: 12px 20px; border-radius: 30px; display: inline-block;">Regresar</a>
                        <button type="submit" name="ver_paquetes">Ver Paquetes</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Formulario para seleccionar paquete -->
        <div class="div4">
            <?php if (!empty($paquetes)): ?>
                <div class="container">
                    <div class="panel-titulo">SELECCIÓN DE PAQUETE</div>
                    <form method="POST">
                        <div class="form-group">
                            <label for="id_paquete">Paquete Disponible:</label>
                            <select name="id_paquete" id="id_paquete" required>
                                <option value="">-- Seleccionar --</option>
                                <?php foreach ($paquetes as $paquete): ?>
                                    <option value="<?= $paquete['id_paquete']; ?>"
                                        <?= $paqueteSeleccionado == $paquete['id_paquete'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($paquete['nombre_paquete']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Mantener el ID del evento seleccionado para la próxima solicitud -->
                        <input type="hidden" name="id_evento" value="<?= htmlspecialchars($eventoSeleccionado); ?>">
                        <div class="form-actions">
                            <a href="http://localhost:3001/MVC/index.php?c=mandarTipoPago" class="btn-regresar" style="text-decoration: none; padding: 12px 20px; border-radius: 30px; display: inline-block;">Regresar</a>
                            <button type="submit" name="seleccionar_paquete">Seleccionar Paquete</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Formulario para registrar pago -->
        <div class="div5">
            <?php if (!empty($paqueteSeleccionado)): ?>
                <div class="container">
                    <div class="panel-titulo">REGISTRO DE PAGO</div>
                    <form method="POST">
                        <input type="hidden" name="id_evento" value="<?= htmlspecialchars($eventoSeleccionado); ?>">
                        <input type="hidden" name="id_paquete" value="<?= htmlspecialchars($paqueteSeleccionado); ?>">
                        <input type="hidden" name="id_usuarios" value="<?= htmlspecialchars($idUsuarioREAL); ?>">

                        <!-- Mostrar el monto total -->
                        <div id="monto_total">
                            Monto Total del Paquete: $<?= number_format($controlador->obtenerTotalServiciosPorEvento($paqueteSeleccionado), 2); ?>
                        </div>

                        <div class="form-group">
                            <label for="fecha_pago">Fecha de Pago:</label>
                            <input type="date" id="fecha_pago" name="fecha_pago" required>
                        </div>

                        <div class="form-group">
                            <label for="tipo_pago">Tipo de Pago:</label>
                            <select id="tipo_pago" name="tipo_pago" required>
                                <option value="contado">Contado</option>
                            </select>
                        </div>
                        
                        <div id="plazos_detalles">
                            <div class="form-group">
                                <label for="numero_plazo">Número de Plazos:</label>
                                <input type="number" id="numero_plazo" name="numero_plazo" min="1" onchange="calcularMontoPlazo()">
                            </div>
                            <div class="form-group">
                                <label for="fecha_plazo">Fecha del Primer Plazo:</label>
                                <input type="date" id="fecha_plazo" name="fecha_plazo">
                            </div>
                            <div id="monto_plazo" style="margin-top: 10px; font-weight: bold;"></div>
                        </div>

                        <div class="form-actions">
                            <a href="http://localhost:3001/MVC/index.php?c=mandarTipoPago" class="btn-regresar" style="text-decoration: none; padding: 12px 20px; border-radius: 30px; display: inline-block;">Regresar</a>
                            <button type="submit">Registrar Pago</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="div6">
            <p>Sistema de Gestión de Eventos © 2025</p>
        </div>
    </div>

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