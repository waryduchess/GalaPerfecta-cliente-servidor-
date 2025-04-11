<?php
session_start(); 
require_once 'datosUsuario.php';
if ($control->getStatus()) {
    // Guardar datos en sesión
    $_SESSION['nombre_usuario'] = $control->getNombreUsuario();
    $_SESSION['tipo_usuario'] = $control->getTipoUsuario();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/validacionLogin.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
<div class="loading-container">
    <?php if ($control->getStatus()): ?>
        <div class="loading-circle"></div>

        <h2 class="loading-text">¡BIENVENIDO, <?= htmlspecialchars($control->getNombreUsuario()) ?></h2>

        <p class="loading-text">Redirigiendo a tu página...</p>
    <?php else: ?>
        <div class="loading-circle"></div>
        <h2 class="loading-text">Error al cargar los datos. Inténtalo nuevamente.</h2>
    <?php endif; ?>
</div>

<script>
    <?php if ($control->getStatus()): ?>
        // Reducción de tiempo de redirección (5 segundos es demasiado)
        setTimeout(function() {
            window.location.href = "<?= ($_SESSION['tipo_usuario'] == 2) ? 'index.php?c=admin' : 'index.php?c=principalCliente' ?>";
        }, 2000); // 2 segundos es suficiente
    <?php else: ?>
        setTimeout(function() {
            window.location.href = "index.php?c=login";
        }, 2000);
    <?php endif; ?>
</script>
</body>
</html>