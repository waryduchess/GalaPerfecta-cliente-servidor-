<?php
require_once "controlador/inicio.controlador.php";

$controladorUsuarios = new inicioControladorTablaUsuarios();
$usuarios= $controladorUsuarios->mostrarTablaUsuarios();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Usuarios</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
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
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No hay usuarios disponibles.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>