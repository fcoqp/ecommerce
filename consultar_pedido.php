<?php
session_start();
include 'includes/conectar_db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['usuario']; 

$con = conectar();

// Consultar los pedidos del cliente
$sql = "SELECT 
            p.id_pedido,
            p.fecha,
            p.total,
            p.estado,
            p.nombre_envio,
            p.email_envio
        FROM pedidos p
        WHERE p.id_usuario = :id_usuario
        ORDER BY p.fecha DESC";

$stmt = $con->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($stmt->rowCount() == 0) {
    $mensaje = "No tienes pedidos registrados.";
} else {
    $mensaje = null;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <ul class="navbar-nav mx-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
            <li class="nav-item"><a class="nav-link active" href="#">Mis Pedidos</a></li>
        </ul>
    </div>
</nav>

<!-- MIGAS -->
<nav aria-label="breadcrumb" class="my-3">
    <div class="container">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item active">Mis Pedidos</li>
        </ol>
    </div>
</nav>

<div class="container my-5">
    <h3 class="text-center mb-4">Mis Pedidos</h3>
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Atrás
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID Pedido</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>

                <?php if ($mensaje): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            <?= $mensaje ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td>#<?= $pedido['id_pedido'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($pedido['fecha'])) ?></td>
                            <td><strong><?= number_format($pedido['total'], 2) ?> €</strong></td>
                            <td>
                                <?php
                                if ($pedido['estado'] === 'pendiente') {
                                    echo '<span class="badge bg-warning">Pendiente</span>';
                                } elseif ($pedido['estado'] === 'enviado') {
                                    echo '<span class="badge bg-primary">Enviado</span>';
                                } elseif ($pedido['estado'] === 'entregado') {
                                    echo '<span class="badge bg-success">Entregado</span>';
                                } elseif ($pedido['estado'] === 'cancelado') {
                                    echo '<span class="badge bg-danger">Cancelado</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>