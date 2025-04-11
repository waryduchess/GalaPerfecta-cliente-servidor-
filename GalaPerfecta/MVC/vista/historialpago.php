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
    <title>Historial de Pagos - GalaPerfecta</title>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <style>
        :root {
            --primary-color: #18213b;
            --secondary-color: #ddc3a7;
            --hover-color: #c9ae92;
            --background-color: #f5f5f5;
            --text-color: #333;
        }

        body {
            background: linear-gradient(to bottom, var(--primary-color), #2a3f7f);
            min-height: 100vh;
            font-family: 'Georgia', serif;
        }

        .container {
            padding: 2rem;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .card-header {
            background-color: var(--secondary-color);
            padding: 1.5rem;
            border: none;
        }

        .card-header h1 {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            margin: 0;
        }

        .tabla-pagos {
            margin: 1rem 0;
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background-color: var(--primary-color);
            color: var(--secondary-color);
            border: none;
            padding: 1rem;
            font-weight: bold;
        }

        .table tbody tr:hover {
            background-color: rgba(221, 195, 167, 0.2);
        }

        .monto {
            color: var(--primary-color);
            font-weight: bold;
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: var(--primary-color);
            border: none;
            padding: 0.8rem 2rem;
            font-weight: bold;
            border-radius: 30px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: var(--hover-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .badge {
            background-color: var(--secondary-color) !important;
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: normal;
        }

        .alert {
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .alert-info {
            background-color: rgba(221, 195, 167, 0.2);
            border-left: 4px solid var(--secondary-color);
            color: var(--primary-color);
        }

        .card-footer {
            background-color: rgba(221, 195, 167, 0.1);
            border-top: none;
            padding: 1.5rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .card-header h1 {
                font-size: 1.5rem;
            }

            .btn-secondary {
                padding: 0.6rem 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>Historial de Pagos</h1>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['mensaje'])): ?>
                    <div class="alert alert-info">
                        <?php echo htmlspecialchars($_SESSION['mensaje']); ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($pagos)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No hay pagos registrados en este momento.
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pagos as $pago): ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($pago['id_pago']); ?></td>
                                        <td><?php echo htmlspecialchars($pago['nombre_evento']); ?></td>
                                        <td><?php echo htmlspecialchars($pago['nombre_paquete']); ?></td>
                                        <td class="monto">$<?php echo number_format((float)$pago['monto_total'], 2); ?></td>
                                        <td><span class="badge bg-primary"><?php echo ucfirst(htmlspecialchars($pago['tipo_pago'])); ?></span></td>
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
            <div class="card-footer text-end">
                <a href="http://localhost:3001/MVC/index.php?c=principalCliente" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Regresar al Inicio
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>