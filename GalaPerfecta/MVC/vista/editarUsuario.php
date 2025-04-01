<!-- filepath: c:\Users\santi\OneDrive\Documentos\GitHub\GalaPerfecta-cliente-servidor-\GalaPerfecta\MVC\vista\editarUsuario.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
</head>
<body>
    <h1>Editar Usuario</h1>
    <form action="index.php?action=actualizarUsuario&id=<?= htmlspecialchars($usuario['id_usuarios']) ?>" method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required><br>

        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required><br>

        <label for="correo">Correo:</label>
        <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required><br>

        <label for="numero_telefono">Número de Teléfono:</label>
        <input type="text" id="numero_telefono" name="numero_telefono" value="<?= htmlspecialchars($usuario['numero_telefono']) ?>" required><br>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" value="<?= htmlspecialchars($usuario['password']) ?>" required><br>

        <label for="id_tipo_user">Tipo de Usuario:</label>
        <input type="number" id="id_tipo_user" name="id_tipo_user" value="<?= htmlspecialchars($usuario['id_tipo_user']) ?>" required><br>

        <button type="submit">Actualizar Usuario</button>
    </form>
</body>
</html>