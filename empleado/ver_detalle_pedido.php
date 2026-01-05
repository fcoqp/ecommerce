<?php
session_start();
include '../includes/conectar_db.php';

$con = conectar();

// Obtener el ID del pedido
$id_pedido = isset($_GET['id_pedido']) ? (int) $_GET['id_pedido'] : 0;

// Si no existe un pedido válido, redirigir a la lista de pedidos
if ($id_pedido <= 0) {
    header('Location: pedidos.php');
    exit;
}

// Obtener los detalles del pedido
$sql = "SELECT 
            p.id_pedido,
            p.fecha,
            p.total,
            p.estado,
            p.nombre_envio,
            p.direccion_envio,
            p.email_envio,
            u.nombre AS usuario_nombre,
            u.apellidos AS usuario_apellidos
        FROM pedidos p
        INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
        WHERE p.id_pedido = :id_pedido";

$stmt = $con->prepare($sql);
$stmt->bindParam(':id_pedido', $id_pedido);
$stmt->execute();
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

// Si el pedido no existe, redirigir
if (!$pedido) {
    header('Location: pedidos.php');
    exit;
}

// Obtener los detalles del pedido (productos)
$sql_detalles = "SELECT 
                    dp.id_detalle,
                    p.nombre AS producto_nombre,
                    dp.cantidad,
                    dp.precio_unitario,
                    dp.subtotal
                  FROM detalle_pedidos dp
                  INNER JOIN productos p ON dp.id_producto = p.id_producto
                  WHERE dp.id_pedido = :id_pedido";

$stmt_detalles = $con->prepare($sql_detalles);
$stmt_detalles->bindParam(':id_pedido', $id_pedido);
$stmt_detalles->execute();
$detalles = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Empleado - Luz Aromatica</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<!-- Header -->
<header class="bg-white shadow-sm">
  <div class="container d-flex justify-content-between align-items-center py-2">
    <a href="index.php" class="navbar-brand">
      <img src="../img/logotipo.jpg" alt="Logo" height="50">
    </a>
    
    <div class="flex-grow-1 text-center">
      <h2 class="mb-0">Panel Empleado</h2>
    </div>

    <a href="../logout.php" class="btn btn-outline-danger ms-auto">
      <i class="bi bi-box-arrow-right"></i> Cerrar sesión
    </a>
  </div>
</header>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link" href="#">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="mostrar_productos.php">Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="categorias.php">Categorias</a></li>
        <li class="nav-item"><a class="nav-link active" href="pedidos.php">Pedidos</a></li>
        <li class="nav-item"><a class="nav-link" href="../index.php">Ir a tienda</a></li>
        <li class="nav-item"><a class="nav-link" href="../perfil.php">Mi perfil</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- MIGAS -->
<nav aria-label="breadcrumb" class="my-3">
  <div class="container">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
      <li class="breadcrumb-item"><a href="pedidos.php">Pedidos</a></li>
      <li class="breadcrumb-item active">Detalle del Pedido</li>
    </ol>
  </div>
</nav>

<div class="container my-5">
  <h3 class="text-center mb-4">Detalle del Pedido #<?= $pedido['id_pedido'] ?></h3>
  <div class="d-flex flex-wrap gap-2 mb-4">
    <a href="pedidos.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Atrás
    </a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <h5>Información del Pedido</h5>
      <p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['usuario_nombre'] . ' ' . $pedido['usuario_apellidos']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($pedido['email_envio']) ?></p>
      <p><strong>Dirección de Envío:</strong> <?= htmlspecialchars($pedido['direccion_envio']) ?></p>
      <p><strong>Fecha de Pedido:</strong> <?= date('d/m/Y H:i', strtotime($pedido['fecha'])) ?></p>
      <p><strong>Total:</strong> <?= number_format($pedido['total'], 2) ?> €</p>
      <p><strong>Estado:</strong> <?= ucfirst($pedido['estado']) ?></p>
    </div>
  </div>

  <div class="card shadow-sm mt-4">
    <div class="card-body">
      <h5>Productos en el Pedido</h5>
      <table class="table table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
        <?php if (count($detalles) === 0): ?>
          <tr>
            <td colspan="4" class="text-center text-muted">
              No hay productos en este pedido
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($detalles as $detalle): ?>
            <tr>
              <td><?= htmlspecialchars($detalle['producto_nombre']) ?></td>
              <td><?= $detalle['cantidad'] ?></td>
              <td><?= number_format($detalle['precio_unitario'], 2) ?> €</td>
              <td><?= number_format($detalle['subtotal'], 2) ?> €</td>
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