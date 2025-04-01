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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Usuarios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .btn {
            padding: 6px 12px;
            margin: 2px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .btn-editar {
            background-color: #4CAF50;
            color: white;
        }
        .btn-eliminar {
            background-color: #f44336;
            color: white;
        }
        /* Estilos para el modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 60%;
            border-radius: 5px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: black;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-actions {
            text-align: right;
            margin-top: 20px;
        }
        .btn-guardar {
            background-color: #2196F3;
            color: white;
        }
        .btn-cancelar {
            background-color: #ccc;
            color: black;
        }
    </style>
</head>
<body>
    <h1>Lista de Usuarios</h1>
    
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
                                )"
                            >
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