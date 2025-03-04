<?php
session_start();
require_once 'controlador/inicio.controlador.php';

$controladorTarjeta = new ControladorTarjeta();
$mensaje = "";
$exito = false;

if (isset($_SESSION['idUsuario'])) {
    $idUsuarioREAL = $_SESSION['idUsuario'];
} else {
    echo "<p style='color: red;'>No se ha iniciado sesión o no hay ID disponible.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUsuario = $idUsuarioREAL;
    $nombreTitular = $_POST['nombreTitular'] ?? '';
    $numeroTarjeta = $_POST['numeroTarjeta'] ?? '';
    $fechaVencimiento = $_POST['fechaVencimiento'] ?? '';
    $cvv = $_POST['cvv'] ?? '';

    $datosFormulario = [
        'id_usuarios' => $idUsuario,
        'nombreTitular' => $nombreTitular,
        'numeroTarjeta' => $numeroTarjeta,
        'fechaVencimiento' => $fechaVencimiento,
        'cvv' => $cvv,
    ];

    $resultado = $controladorTarjeta->procesarFormulario($datosFormulario);
    $exito = ($resultado === "Tarjeta registrada exitosamente.");

    if ($exito) {
        // Redirige a la siguiente página si el proceso es exitoso
        header("Location: index.php?c=procesoPago");
        exit();
    } else {
        // Muestra un mensaje de error
        $mensaje = $resultado;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/formapago.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos</title>
</head>

<body>

    <div class="container mt-5">

        <?php if ($mensaje): ?>
            <div class="alert <?php echo $exito ? 'alert-success' : 'alert-danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Opciones de pago -->
        <div class="payment-options d-flex justify-content-between mb-5">
            <button class="btn btn-personalizado" onclick="location.href='https://www.paypal.com/mx/home';">
                <img src="../../img/pago1.png" class="logo" alt="PayPal">
            </button>
            <button class="btn btn-personalizado" onclick="location.href='https://www.apple.com/mx/apple-pay/';">
                <img src="../../img/pago2.png" class="logo" alt="Apple Pay">
            </button>
            <button class="btn btn-personalizado" onclick="location.href='https://pay.google.com/intl/es_mx/about/';">
                <img src="../../img/pago3.png" class="logo" alt="Google Pay">
            </button>
        </div>

        <!-- Formulario de pago con tarjeta -->
        <form action="" method="POST">
            <div class="credit-card-info">
                <div class="input_container mb-3">
                    <label for="name" class="input_label">Nombre completo del titular de la tarjeta</label>
                    <input id="name" name="nombreTitular" class="form-control" type="text" placeholder="Ingresa tu nombre completo" required>
                </div>
                <div class="input_container mb-3">
                    <label for="card-number" class="input_label">Número de Tarjeta</label>
                    <input id="card-number" name="numeroTarjeta" class="form-control" type="text" placeholder="0000 0000 0000 0000" required>
                </div>
                <div class="input_container mb-4">
                    <label for="expiry-cvv" class="input_label">Fecha de Vencimiento / CVV</label>
                    <div class="split d-flex gap-3">
                        <input id="expiry-date" name="fechaVencimiento" class="form-control" type="text" placeholder="MM/AA" required>
                        <input id="cvv" name="cvv" class="form-control" type="text" placeholder="CVV" required>
                    </div>
                </div>

                <!-- Botones -->
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Confirmar</button>
                    <button type="button" class="btn btn-secondary" onclick="location.href='?c=cotizacion';">Regresar</button>
                </div>
            </div>
        </form>

    </div>

    <script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>