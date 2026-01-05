<?php
session_start();
include '../includes/conectar_db.php';

$con = conectar();

// Obtener el total de ventas realizadas (todos los pedidos, no solo 'entregado')
$sql_ventas_totales = "
    SELECT SUM(p.total) AS ventas_totales
    FROM pedidos p
";
$stmt = $con->prepare($sql_ventas_totales);
$stmt->execute();
$ventas_totales = $stmt->fetch(PDO::FETCH_ASSOC)['ventas_totales'];

// Obtener ventas por producto (sumar la cantidad y el total de ventas)
$sql_ventas_producto = "
    SELECT pr.nombre, SUM(dp.cantidad) AS cantidad_vendida, SUM(dp.subtotal) AS ventas_totales_producto
    FROM detalle_pedidos dp
    JOIN productos pr ON dp.id_producto = pr.id_producto
    JOIN pedidos p ON dp.id_pedido = p.id_pedido
    GROUP BY pr.nombre
    ORDER BY ventas_totales_producto DESC
";
$stmt = $con->prepare($sql_ventas_producto);
$stmt->execute();
$ventas_producto_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener ventas por categoría (sumar la cantidad y el total de ventas)
$sql_ventas_categoria = "
    SELECT c.nombre AS categoria, SUM(dp.cantidad) AS cantidad_vendida, SUM(dp.subtotal) AS ventas_totales_categoria
    FROM detalle_pedidos dp
    JOIN productos pr ON dp.id_producto = pr.id_producto
    JOIN categorias c ON pr.id_categoria = c.id_categoria
    JOIN pedidos p ON dp.id_pedido = p.id_pedido
    GROUP BY c.nombre
    ORDER BY ventas_totales_categoria DESC
";
$stmt = $con->prepare($sql_ventas_categoria);
$stmt->execute();
$ventas_categoria_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Productos</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>

<!-- HEADER -->
<header class="bg-white shadow-sm">
  <div class="container d-flex justify-content-between align-items-center py-2">
    <a href="index.php" class="navbar-brand">
      <img src="../img/logotipo.jpg" alt="Logo" height="50">
    </a>
    
    <div class="flex-grow-1 text-center">
      <h2 class="mb-0">Panel Administrador</h2>
    </div>

    <a href="../logout.php" class="btn btn-outline-danger ms-auto">
      <i class="bi bi-power"></i> Cerrar sesión
    </a>
  </div>
</header>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="productos/mostrar_productos.php">Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="categorias.php">Categorías</a></li>
        <li class="nav-item"><a class="nav-link" href="pedidos.php">Pedidos</a></li>
        <li class="nav-item"><a class="nav-link" href="usuarios/mostrar_usuarios.php">Usuarios</a></li>
        <li class="nav-item"><a class="nav-link active" href="#">Informes y estadísticas</a></li>
      </ul>
    </div>
  </div>
</nav>

<section class="container mt-5">
  <h3 class="mb-4">Resumen de Ventas</h3>
  
  <!-- Ventas Totales -->
  <div class="card mb-4">
    <div class="card-header bg-success text-white">
      <h5 class="card-title">Ventas Totales</h5>
    </div>
    <div class="card-body">
      <p class="card-text">Total de ventas realizadas: <strong><?php echo number_format($ventas_totales, 2, ',', '.'); ?>€</strong></p>
    </div>
  </div>

  <!-- Ventas por Producto -->
  <div class="card mb-4">
    <div class="card-header bg-dark text-white">
      <h5 class="card-title">Ventas por Producto</h5>
    </div>
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>Producto</th>
            <th>Cantidad Vendida</th>
            <th>Ventas Totales</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($ventas_producto_result as $producto): ?>
            <tr>
              <td><?php echo $producto['nombre']; ?></td>
              <td><?php echo $producto['cantidad_vendida']; ?></td>
              <td><?php echo number_format($producto['ventas_totales_producto'], 2, ',', '.'); ?>€</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Ventas por Categoría -->
  <div class="card mb-4">
    <div class="card-header bg-warning text-white">
      <h5 class="card-title">Ventas por Categoría</h5>
    </div>
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>Categoría</th>
            <th>Cantidad Vendida</th>
            <th>Ventas Totales</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($ventas_categoria_result as $categoria): ?>
            <tr>
              <td><?php echo $categoria['categoria']; ?></td>
              <td><?php echo $categoria['cantidad_vendida']; ?></td>
              <td><?php echo number_format($categoria['ventas_totales_categoria'], 2, ',', '.'); ?>€</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
