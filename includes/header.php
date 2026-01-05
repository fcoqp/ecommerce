<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Calcular el total de productos en el carrito
$totalItems = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $producto) {
        $totalItems += $producto['cantidad'];
    }
}
?>

<?php include 'includes/offcanvas-login.php'; ?>
<?php include 'includes/offcanvas-user.php'; ?>
<?php include 'includes/offcanvas-registro.php'; ?>
<?php include 'includes/offcanvas-clave.php'; ?>

<header class="bg-white shadow-sm">
  <div class="container d-flex justify-content-between align-items-center py-2">

    <!-- Logo -->
    <a href="index.php" class="navbar-brand">
      <img src="img/logotipo.jpg" alt="Logo" height="50">
    </a>

    <div class="d-flex align-items-center gap-3">

      

      <?php if (isset($_SESSION['usuario_nombre'])): ?>
          <!-- Usuario logueado -->
          <button type="button"
                  class="btn p-0 border-0 bg-transparent"
                  data-bs-toggle="offcanvas"
                  data-bs-target="#userPanel">
            <i class="bi bi-person-fill fs-3" alt="Usuario"></i>
          </button>
      <?php else: ?>
          <!-- Usuario no logueado -->
          <button type="button"
                  class="btn p-0 border-0 bg-transparent"
                  data-bs-toggle="offcanvas"
                  data-bs-target="#loginPanel">
            <i class="bi bi-person fs-3" alt="Usuario"></i>
          </button>
      <?php endif; ?>

<?php

$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$numero_articulos = 0;

// Contamos los artículos en el carrito
foreach ($carrito as $producto) {
    $numero_articulos += $producto['cantidad'];
}
?>

<!-- Carrito con el número de artículos -->
<a href="carrito/mostrar_carrito.php" class="btn p-0 border-0 bg-transparent position-relative">
  <img src="img/icons/cart4.svg" alt="Carrito" width="28">
  
  <!-- Badge con el número de artículos -->
  <?php if ($numero_articulos > 0): ?>
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
      <?= $numero_articulos ?>
    </span>
  <?php endif; ?>
</a>

    </div>
  </div>

</header>