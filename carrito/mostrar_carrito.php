<?php
session_start();
include '../includes/conectar_db.php';

$con = conectar();

// Verificamos si hay productos en el carrito
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];

// Calcular el total de artículos en el carrito
$totalArticulos = 0;
foreach ($carrito as $producto) {
    $totalArticulos += $producto['cantidad']; 
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Carrito de Compra</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<!-- HEADER -->
<header class="bg-black shadow-sm">
  <div class="container d-flex justify-content-between align-items-center py-2">
    <a href="../index.php" class="navbar-brand">
      <img src="../img/logotipo2.jpg" alt="Logo" height="50">
    </a>
</header>

<div class="container my-5">
  <h3 class="text-center mb-4">Carrito de Compra</h3>
  <div class="d-flex justify-content-between mb-4">
    <a href="../index.php" class="btn btn-outline-secondary">
      <i class="bi bi-cart-fill"></i> Seguir comprando
    </a>

    <div class="d-flex gap-2">
      <a href="vaciar_carrito.php" class="btn btn-danger btn-sm"
        onclick="return confirm('¿Seguro que quieres vaciar el carrito?')">
        <i class="bi bi-trash"></i> Vaciar Carrito
      </a>
      <a href="procesar_pago.php" class="btn btn-success">
        <i class="bi bi-check-circle"></i> Procesar Pedido
      </a>
    </div>
  </div>

  <div class="d-flex justify-content-between mb-4">
    <div>
      <strong>Total Artículos: <?= $totalArticulos ?></strong>
    </div>
  </div>

  <!-- TABLA -->
  <div class="table-responsive">
    <form action="actualizar_carrito.php" method="POST">
      <table class="table table-bordered table-hover align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>Producto</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Total</th>
            <th>Eliminar</th>
          </tr>
        </thead>
        <tbody>

        <?php if (count($carrito) === 0): ?>
          <tr>
            <td colspan="6">No hay productos en el carrito</td>
          </tr>
        <?php else: ?>
          <?php 
          $total = 0;
          foreach ($carrito as $id_producto => $producto):
            $producto_info = $con->prepare("SELECT * FROM productos WHERE id_producto = ?");
            $producto_info->execute([$id_producto]);
            $producto_info = $producto_info->fetch(PDO::FETCH_ASSOC);

            if (!$producto_info) {
              echo "<tr><td colspan='6'>Producto no encontrado</td></tr>";
              continue;
            }

            // Obtenemos el descuento (si existe)
            $descuento = $producto_info['descuento'];
            $precio_original = $producto_info['precio'];

            $precio_con_descuento = $precio_original - ($precio_original * ($descuento / 100));

            // Calculamos el subtotal con el precio con descuento
            $subtotal = $precio_con_descuento * $producto['cantidad'];
            $total += $subtotal;
          ?>
            <tr>
              <td>
                <?php if ($producto['imagen']): ?>
                  <img src="../admin/<?= htmlspecialchars($producto['imagen']) ?>"
                    class="img-fluid rounded"
                    style="max-width: 80px;">
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($producto_info['nombre']) ?></td>
              <td>
                <?php if ($descuento > 0): ?>
                  <del><?= number_format($precio_original, 2, ',', '.') ?> €</del><br>
                  <strong><?= number_format($precio_con_descuento, 2, ',', '.') ?> €</strong>
                <?php else: ?>
                  <?= number_format($precio_original, 2, ',', '.') ?> €
                <?php endif; ?>
              </td>
              <td>
                <div class="d-flex justify-content-center">
                  <button type="submit" name="cantidad[<?= $id_producto ?>]" value="<?= $producto['cantidad'] - 1 ?>" class="btn btn-secondary btn-sm" <?php if($producto['cantidad'] <= 1) echo 'disabled'; ?>><i class="bi bi-dash"></i></button>
                  <input type="text" class="form-control mx-2" value="<?= $producto['cantidad'] ?>" readonly style="width: 60px; text-align: center;">
                  <button type="submit" name="cantidad[<?= $id_producto ?>]" value="<?= $producto['cantidad'] + 1 ?>" class="btn btn-secondary btn-sm"><i class="bi bi-plus"></i></button>
                </div>
              </td>
              <td><?= number_format($subtotal, 2, ',', '.') ?> €</td>
              <td>
                <a href="eliminar_producto.php?codigo=<?= $producto_info['id_producto'] ?>"
                  class="btn btn-danger btn-sm"
                  onclick="return confirm('¿Seguro que quieres eliminar este producto del carrito?')">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>

        </tbody>
      </table>

    </form>

    <!-- Total del carrito -->
    <?php if (count($carrito) > 0): ?>
      <div class="d-flex justify-content-end mt-3">
        <h4>Total: <?= number_format($total, 2, ',', '.') ?> €</h4>
      </div>
    <?php endif; ?>

  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>