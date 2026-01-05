<!-- OFFCANVAS RESTABLECER CLAVE -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="restablecerClavePanel">
  <div class="offcanvas-header justify-content-center position-relative">
    <h5 class="offcanvas-title text-center">Restablecer Clave</h5>
    <button type="button" class="btn-close position-absolute end-0" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form action="restablecer_clave.php" method="POST">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Nueva Clave</label>
        <input type="password" name="nuevaClave" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirmar Clave</label>
        <input type="password" name="confirmarClave" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-dark w-100">Restablecer Clave</button>
    </form>
  </div>
</div>