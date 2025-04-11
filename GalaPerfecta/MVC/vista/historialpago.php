<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación
if (!isset($_SESSION['idUsuario'])) {
    header('Location: index.php?c=login');
    exit;
}

// Obtener pagos
$controlador = new PagosControlador();
$pagos = $controlador->mostrarPagos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Pagos</title>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <style>
        .tabla-pagos { 
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .tabla-pagos th {
            background-color: #18213b;
            color: white;
        }
        .btn-accion {
            margin: 2px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h1 class="h3 mb-0">Historial de Pagos</h1>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['mensaje'])): ?>
                    <div class="alert alert-info">
                        <?php echo htmlspecialchars($_SESSION['mensaje']); ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($pagos)): ?>
                    <div class="alert alert-info">
                        No hay pagos registrados.
                    </div>
                <?php else: ?>
                    <div class="table-responsive tabla-pagos">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Evento</th>
                                    <th>Paquete</th>
                                    <th>Monto</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pagos as $pago): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($pago['id_pago']); ?></td>
                                        <td><?php echo htmlspecialchars($pago['nombre_evento']); ?></td>
                                        <td><?php echo htmlspecialchars($pago['nombre_paquete']); ?></td>
                                        <td>$<?php echo number_format((float)$pago['monto_total'], 2); ?></td>
                                        <td><?php echo ucfirst(htmlspecialchars($pago['tipo_pago'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($pago['fecha_pago'])); ?></td>
                                        <td>
                                          
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <a href="http://localhost:3001/MVC/index.php?c=principalCliente" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Regresar
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-kit-code.js"></script>
</body>
</html>