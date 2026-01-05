<?php
session_start();
include '../../includes/conectar_db.php';

$con = conectar();
$stmt = $con->prepare("SELECT * FROM usuarios");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Actualizar el rol del usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario']) && isset($_POST['rol'])) {
    $id_usuario = $_POST['id_usuario'];
    $nuevo_rol = $_POST['rol'];

    // Validar que es el nuevo rol
    if ($nuevo_rol !== 'admin') {
        $update_stmt = $con->prepare("UPDATE usuarios SET rol = :rol WHERE id_usuario = :id_usuario");
        $update_stmt->bindParam(':rol', $nuevo_rol);
        $update_stmt->bindParam(':id_usuario', $id_usuario);
        $update_stmt->execute();

        header("Location: mostrar_usuarios.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Usuarios</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>

<!-- HEADER -->
<header class="bg-white shadow-sm">
  <div class="container d-flex justify-content-between align-items-center py-2">
    <a href="index.php" class="navbar-brand">
      <img src="../../img/logotipo.jpg" alt="Logo" height="50">
    </a>
    
    <div class="flex-grow-1 text-center">
      <h2 class="mb-0">Panel Administrador</h2>
    </div>

    <a href="../../logout.php" class="btn btn-outline-danger ms-auto">
      <i class="bi bi-box-arrow-right"></i> Cerrar sesión
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
        <li class="nav-item"><a class="nav-link" href="../index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="../productos/mostrar_productos.php">Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="../categorias.php">Categorías</a></li>
        <li class="nav-item"><a class="nav-link" href="../pedidos.php">Pedidos</a></li>
        <li class="nav-item"><a class="nav-link active" href="#">Usuarios</a></li>
        <li class="nav-item"><a class="nav-link" href="../informes.php">Informes y estadísticas</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- MIGAS -->
<nav aria-label="breadcrumb" class="my-3">
  <div class="container">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../index.php">Inicio</a></li>
      <li class="breadcrumb-item active">Usuarios</li>
    </ol>
  </div>
</nav>

<div class="container my-5">
  <h3 class="text-center mb-5">Usuarios</h3>
  <div class="d-flex flex-wrap gap-2 mb-4">
    <a href="../index.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Atrás
    </a>

    <a href="insertar_usuario.php" class="btn btn-warning">
      <i class="bi bi-plus-circle me-1"></i> Insertar usuario
    </a>
  </div>

  <!-- TABLA -->
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle text-center">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Apellidos</th>
          <th>Email</th>
          <th>Rol</th>
          <th>Estado</th>
          <th>Fecha Registro</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>

      <?php if (count($usuarios) === 0): ?>
        <tr>
          <td colspan="8">No se encontraron usuarios</td>
        </tr>
      <?php else: ?>
        <?php foreach ($usuarios as $usuario): ?>
          <tr class="<?= $usuario['activo'] ? '' : 'table-secondary' ?>">
            <td><?= htmlspecialchars($usuario['id_usuario']) ?></td>
            <td><?= htmlspecialchars($usuario['nombre']) ?></td>
            <td><?= htmlspecialchars($usuario['apellidos']) ?></td>
            <td><?= htmlspecialchars($usuario['email']) ?></td>
            <td>
              <form action="mostrar_usuarios.php" method="POST" class="d-inline-block">
                <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
                <select name="rol" class="form-select form-select-sm" onchange="this.form.submit()" 
                  <?php if ($usuario['rol'] === 'admin') echo 'disabled'; ?>>

                  <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?> disabled>Admin</option>
                  <option value="empleado" <?= $usuario['rol'] === 'empleado' ? 'selected' : '' ?>>Empleado</option>
                  <option value="cliente" <?= $usuario['rol'] === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                </select>
                <input type="submit" name="update_rol" style="display: none;">
              </form>
            </td>
            <td>
              <?php if ($usuario['activo']): ?>
                <span class="badge bg-success">Activo</span>
              <?php else: ?>
                <span class="badge bg-secondary">Inactivo</span>
              <?php endif; ?>
            </td>
            <td><?= date('d/m/Y H:i', strtotime($usuario['fecha_registro'])) ?></td>
            <td>
              <a href="modificar_usuario.php?codigo=<?= $usuario['id_usuario'] ?>"
                class="btn btn-success btn-sm">
                <i class="bi bi-pencil"></i>
              </a>
              <?php if ($usuario['activo']): ?>
                <a href="eliminar_usuario.php?codigo=<?= $usuario['id_usuario'] ?>"
                  class="btn btn-danger btn-sm"
                  onclick="return confirm('¿Seguro que quieres desactivar este usuario?')">
                  <i class="bi bi-trash"></i>
                </a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>

      </tbody>
    </table>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>