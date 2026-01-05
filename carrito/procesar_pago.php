<?php
session_start();
include '../includes/conectar_db.php';

$con = conectar();

// Verificamos si hay productos en el carrito
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];

// Si el carrito está vacío, redirigimos a la página del carrito
if (count($carrito) == 0) {
    header('Location: carrito.php');
    exit();
}

// Si el usuario no está logueado, redirigimos a la página de inicio
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}

// Si el usuario está logueado, obtenemos sus datos
$usuario_id = $_SESSION['usuario'];
$usuario_query = $con->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$usuario_query->execute([$usuario_id]);
$usuario = $usuario_query->fetch(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pagar Pedido</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="../css/styles.css" />
</head>
<body>

<!-- Header-->
<header class="bg-black shadow-sm">
  <div class="container d-flex justify-content-between align-items-center py-2">
    <a href="../index.php" class="navbar-brand">
      <img src="../img/logotipo2.jpg" alt="Logo" height="50" />
    </a>
  </div>
</header>

<div class="container my-5">
  <h3 class="text-center mb-4">Resumen Pedido</h3>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th class="text-end">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $total = 0;
      foreach ($carrito as $id_producto => $producto) {
          $producto_info = $con->prepare("SELECT * FROM productos WHERE id_producto = ?");
          $producto_info->execute([$id_producto]);
          $producto_info = $producto_info->fetch(PDO::FETCH_ASSOC);
          $descuento = $producto_info['descuento'];
          $precio_original = $producto_info['precio'];
          $precio_con_descuento = $precio_original - ($precio_original * ($descuento / 100));
          $subtotal = $precio_con_descuento * $producto['cantidad'];
          $total += $subtotal;
      ?>
        <tr>
          <td><?= $producto_info['nombre'] ?></td>
          <td><?= $producto['cantidad'] ?></td>
          <td><?= number_format($precio_con_descuento, 2, ',', '.') ?> €</td>
          <td class="text-end"><?= number_format($subtotal, 2, ',', '.') ?> €</td>
        </tr>
      <?php } ?>
    </tbody>
  </table>

  <p class="text-end"><strong>Total a pagar: <?= number_format($total, 2, ',', '.') ?> €</strong></p>

  <form action="../checkout.php" method="POST" class="d-flex">
    <button type="submit" class="btn btn-dark w-100 mb-2">Tramitar pago</button>
  </form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>