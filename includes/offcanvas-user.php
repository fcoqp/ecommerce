<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Si el usuario no está logueado redirigir al index.php
if (!isset($_SESSION['usuario_nombre']) && basename($_SERVER['PHP_SELF']) != 'index.php') {
    header("Location: index.php");
    exit();
}
?>

<div class="offcanvas offcanvas-end" tabindex="-1" id="userPanel">
  <div class="offcanvas-header justify-content-center position-relative">
    <h5 class="offcanvas-title text-center">Mi Cuenta</h5>
    <button type="button" class="btn-close position-absolute end-0" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <div class="mb-3 text-center">
      <strong>Hola, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></strong>
    </div>
    <div class="list-group">
      <a href="consultar_pedido.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
        Consultar pedido
        <i class="bi bi-chevron-right"></i>
      </a>
      <a href="perfil.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
        Mis datos
        <i class="bi bi-chevron-right"></i>
      </a>

      <!-- Si el rol es empleado, mostrar opción para ir al panel del empleado -->
      <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'empleado'): ?>
        <a href="empleado/index.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
          Panel de empleado
          <i class="bi bi-chevron-right"></i>
        </a>
      <?php endif; ?>

      <a href="logout.php" class="list-group-item list-group-item-action text-danger d-flex justify-content-between align-items-center">
        Cerrar sesión
        <i class="bi bi-power"></i>
      </a>
    </div>
  </div>
</div>