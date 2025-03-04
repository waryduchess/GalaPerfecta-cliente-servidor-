<?php

$eventoController = new inicioControladorEvento();
$eventoController->handleRequest();
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
    <?php if ($eventoController->insertada): ?>
        <div class="loading-circle"></div>
        <h2 class="loading-text">¡Registro exitoso!</h2>
        <p class="loading-text">Redirigiendo a tu página...</p>
    <?php else: ?>
        <div class="loading-circle"></div>
        <h2 class="loading-text">Error al cargar los datos. Inténtalo nuevamente.</h2>
    <?php endif; ?>
</div>

<script>
    <?php if ($eventoController->insertada): ?>
        setTimeout(function() {
            window.location.href = "index.php?c=admin";
        }, 5000);
    <?php else: ?>
        setTimeout(function() {
            window.location.href = "index.php?c=crearEvento";
        }, 5000);
    <?php endif; ?>
</script>
</body>
</html>