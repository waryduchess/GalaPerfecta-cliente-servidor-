<?php
require_once "controlador/inicio.controlador.php";
$controladorUsuarios = new inicioControladorTablaUsuarios();
$usuarios = $controladorUsuarios->mostrarTablaUsuarios();

// Procesar la eliminación si se ha enviado el formulario
if (isset($_POST['eliminar_usuario']) && isset($_POST['id_usuario'])) {
    $resultado = $controladorUsuarios->eliminarUsuario($_POST['id_usuario']);
    if ($resultado) {
        // Recargar la página para actualizar la tabla
        header("Location: index.php?c=tablaUsuario");
        exit;
    }
}

// Procesar la actualización si se ha enviado el formulario
if (isset($_POST['actualizar_usuario'])) {
    $resultado = $controladorUsuarios->actualizarUsuario(
        $_POST['id_usuarios'],
        $_POST['nombre'],
        $_POST['apellido'],
        $_POST['correo'],
        $_POST['numero_telefono'],
        $_POST['password'],
        $_POST['id_tipo_user']
    );
    if ($resultado) {
        // Recargar la página para actualizar la tabla
        header("Location: index.php?c=tablaUsuario");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../../css/tablaUsuarios.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Usuarios</title>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
        <h1 class="centrar">
            <img src="../../img/logo1.png" class="logo" alt="logo1">
            <a href="index.php?c=admin" class="logo"><strong>Gala Perfecta</strong></a>
        </h1>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">

            </ul>
        </div>


    </nav>
    <br>
    <br>
    <br>


    <h1>Lista de Usuarios</h1>
    <div class="container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Correo</th>
                <th>Número de Teléfono</th>
                <th>Tipo de Usuario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($usuarios)): ?>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['id_usuarios']) ?></td>
                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                        <td><?= htmlspecialchars($usuario['apellido']) ?></td>
                        <td><?= htmlspecialchars($usuario['correo']) ?></td>
                        <td><?= htmlspecialchars($usuario['numero_telefono']) ?></td>
                        <td><?= htmlspecialchars($usuario['id_tipo_user']) ?></td>
                        <td>
                            <button
                                class="btn btn-editar"
                                onclick="abrirModalEditar(
                                    '<?= htmlspecialchars($usuario['id_usuarios']) ?>', 
                                    '<?= htmlspecialchars($usuario['nombre']) ?>', 
                                    '<?= htmlspecialchars($usuario['apellido']) ?>', 
                                    '<?= htmlspecialchars($usuario['correo']) ?>', 
                                    '<?= htmlspecialchars($usuario['numero_telefono']) ?>', 
                                    '<?= htmlspecialchars($usuario['password']) ?>', 
                                    '<?= htmlspecialchars($usuario['id_tipo_user']) ?>'
                                )">
                                Editar
                            </button>
                            <form method="post" style="display: inline;" onsubmit="return confirmarEliminacion()">
                                <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['id_usuarios']) ?>">
                                <button type="submit" name="eliminar_usuario" class="btn btn-eliminar">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No hay usuarios disponibles.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>

    <!-- Modal para editar usuario -->
    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h2>Editar Usuario</h2>
            <form method="post" id="formEditar">
                <input type="hidden" id="id_usuarios" name="id_usuarios">

                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" required>
                </div>

                <div class="form-group">
                    <label for="correo">Correo:</label>
                    <input type="email" id="correo" name="correo" required>
                </div>

                <div class="form-group">
                    <label for="numero_telefono">Número de Teléfono:</label>
                    <input type="text" id="numero_telefono" name="numero_telefono" required>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="id_tipo_user">Tipo de Usuario:</label>
                    <input type="number" id="id_tipo_user" name="id_tipo_user" required>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" name="actualizar_usuario" class="btn btn-guardar">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Función para abrir el modal de edición y rellenar los campos con los datos del usuario
        function abrirModalEditar(id, nombre, apellido, correo, telefono, password, tipoUser) {
            document.getElementById('id_usuarios').value = id;
            document.getElementById('nombre').value = nombre;
            document.getElementById('apellido').value = apellido;
            document.getElementById('correo').value = correo;
            document.getElementById('numero_telefono').value = telefono;
            document.getElementById('password').value = password;
            document.getElementById('id_tipo_user').value = tipoUser;

            document.getElementById('modalEditar').style.display = 'block';
        }

        // Función para cerrar el modal
        function cerrarModal() {
            document.getElementById('modalEditar').style.display = 'none';
        }

        // Función para confirmar la eliminación
        function confirmarEliminacion() {
            return confirm('¿Está seguro de que desea eliminar este usuario? Esta acción no se puede deshacer.');
        }

        // Cerrar el modal si se hace clic fuera de él
        window.onclick = function(event) {
            var modal = document.getElementById('modalEditar');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>

</html>