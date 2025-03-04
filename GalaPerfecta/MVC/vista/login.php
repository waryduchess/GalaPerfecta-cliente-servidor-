<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/login.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y Registro</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
        <h1 class="centrar">
            <img src="../../img/logo1.png" class="logo" alt="logo1">
            <a href="index.php" class="logo"><strong>El gran dia</strong></a>
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
                <h2>Inicia Sesión</h2><br>
                <form class="login" action="index.php?c=cargalogin" method="POST">
                    <div class="input-box">
                        <input type="email" name="correo" required>
                        <label>Correo</label>
                    </div>
                    <div class="input-box">
                        <input type="password" name="password" required>
                        <label>Contraseña</label>
                    </div>
                    <button type="submit" class="btn">Entrar</button>
                    <p>¿No tienes una cuenta? <a href="#" id="show-register">Regístrate</a></p>
                </form>
            </div>
            <div class="form register-form">
                <h2>Regístrate</h2><br>
                <form class="registro" action="index.php?c=cargaRegistro" method="POST">
                    <div class="input-box">
                        <input type="text" name="nombre" required>
                        <label>Nombre</label>
                    </div>
                    <div class="input-box">
                        <input type="text" name="apellido" required>
                        <label>Apellido</label>
                    </div>
                    <div class="input-box">
                        <input type="text" name="numero_telefono" required>
                        <label>Telefono</label>
                    </div>
                    <div class="input-box">
                        <input type="email" name="correo" required>
                        <label>Correo</label>
                    </div>
                    <div class="input-box">
                        <input type="password" name="password" required>
                        <label>Contraseña</label>
                    </div>
                    <button type="submit" class="btn">Registrarse</button>
                    <p>¿Ya tienes una cuenta? <a href="#" id="show-login">Inicia Sesión</a></p>
                </form>
            </div>
        </div>
        <div class="welcome-section">
            <h1>¡Bienvenido!</h1>
            <p>Accede a tu cuenta o crea una nueva para empezar.</p>
        </div>
    </div>

    <!-- Archivo JavaScript -->
    <script src="../../script.js"></script>
</body>

</html>