<!-- OFFCANVAS REGISTRO -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="registerPanel">
  <div class="offcanvas-header justify-content-center position-relative">
    <h5 class="offcanvas-title text-center">Registro</h5>
    <button type="button" class="btn-close position-absolute end-0" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body">
    <form action="registro.php" method="POST">

      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Apellidos</label>
        <input type="text" name="apellidos" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Dirección</label>
        <input type="text" name="direccion" class="form-control">
      </div>

      <div class="mb-3">
        <label class="form-label">Localidad</label>
        <input type="text" name="localidad" class="form-control">
      </div>

      <div class="mb-3">
        <label class="form-label">Provincia</label>
        <input type="text" name="provincia" class="form-control">
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" placeholder="email@ejemplo.com" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Clave</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-dark w-100">Registrar</button>
    </form>
  </div>
</div>