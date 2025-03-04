<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/login.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de eventos</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
        <h1 class="centrar">
            <img src="../../img/logo1.png" class="logo" alt="logo1">
            <a href="index.php?c=admin" class="logo"><strong>El gran dia</strong></a>
        </h1>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">

            </ul>
        </div>


    </nav>

    <div class="container">
        <div class="form-container">
            <div class="form login-form active">
                <h2>Registro de eventos</h2><br>
                <form class="login" action="index.php?c=cargaRegistroEvento" method="POST">
                    <div class="input-box">
                        <input type="text" name="nombre_evento" required>
                        <label>Nombre del evento</label>
                    </div>
                    <button type="submit" class="btn">Registrar</button>
                    <button type="button" onclick="history.back()" class="btn btn-secondary">Regresar</button>
                </form>
            </div>

        </div>
        <div class="welcome-section">
            <h1>¡Bienvenido!</h1>
            <p>Registra tus nuevos eventos.</p>
        </div>
    </div>

</body>

</html>