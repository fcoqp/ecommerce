<?php
session_start();
include '../../includes/conectar_db.php';

$con = conectar();

// Comprobar ID
if (!isset($_GET['codigo'])) {
    header("Location: mostrar_usuarios.php");
    exit;
}

$id_usuario = $_GET['codigo'];

// Obtener usuario
$stmt = $con->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Usuario no encontrado");
}

$mensaje = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre       = $_POST['nombre'];
    $apellidos    = $_POST['apellidos'];
    $direccion    = $_POST['direccion'];
    $localidad    = $_POST['localidad'];
    $provincia    = $_POST['provincia'];
    $email        = $_POST['email'];
    $rol          = $_POST['rol'];
    $activo       = isset($_POST['activo']) ? 1 : 0;

    // UPDATE
    $sql = "UPDATE usuarios SET
                nombre = ?,
                apellidos = ?,
                direccion = ?,
                localidad = ?,
                provincia = ?,
                email = ?,
                rol = ?,
                activo = ?
            WHERE id_usuario = ?";

    $stmt = $con->prepare($sql);

    if ($stmt->execute([
        $nombre,
        $apellidos,
        $direccion,
        $localidad,
        $provincia,
        $email,
        $rol,
        $activo,
        $id_usuario
    ])) {
        $mensaje = "Usuario actualizado correctamente";

        // Recargar datos actualizados
        $stmt = $con->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $mensaje = "Error al actualizar el usuario";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Modificar Usuario</title>
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
    <ul class="navbar-nav mx-auto">
      <li class="nav-item"><a class="nav-link" href="../index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="../productos/mostrar_productos.php">Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="../categorias.php">Categorías</a></li>
        <li class="nav-item"><a class="nav-link" href="../pedidos.php">Pedidos</a></li>
        <li class="nav-item"><a class="nav-link active" href="#">Usuarios</a></li>
        <li class="nav-item"><a class="nav-link" href="../informes.php">Informes y estadísticas</a></li>
    </ul>
  </div>
</nav>

<!-- MIGAS -->
<nav aria-label="breadcrumb" class="my-3">
  <div class="container">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../index.php">Inicio</a></li>
      <li class="breadcrumb-item"><a href="mostrar_usuarios.php">Usuarios</a></li>
      <li class="breadcrumb-item active">Modificar</li>
    </ol>
  </div>
</nav>

<!-- FORMULARIO -->
<div class="container my-5">
  <h3 class="text-center mb-5">Modificar Usuario</h3>
  <div class="card shadow-sm">
    <div class="card-body">

      <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="row g-3">

          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control"
                   value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Apellidos</label>
            <input type="text" name="apellidos" class="form-control"
                   value="<?= htmlspecialchars($usuario['apellidos']) ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control"
                   value="<?= htmlspecialchars($usuario['direccion']) ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">Localidad</label>
            <input type="text" name="localidad" class="form-control"
                   value="<?= htmlspecialchars($usuario['localidad']) ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">Provincia</label>
            <input type="text" name="provincia" class="form-control"
                   value="<?= htmlspecialchars($usuario['provincia']) ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($usuario['email']) ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Rol</label>
            <select name="rol" class="form-select" required>
              <option value="admin" <?= $usuario['rol'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
              <option value="empleado" <?= $usuario['rol'] == 'empleado' ? 'selected' : '' ?>>Empleado</option>
              <option value="cliente" <?= $usuario['rol'] == 'cliente' ? 'selected' : '' ?>>Cliente</option>
            </select>
          </div>

          <div class="col-md-6 d-flex align-items-center">
            <div class="form-check mt-4">
              <input class="form-check-input" type="checkbox" name="activo"
                <?= $usuario['activo'] ? 'checked' : '' ?>>
              <label class="form-check-label">Usuario activo</label>
            </div>
          </div>

        </div>

        <div class="mt-4 d-flex gap-2">
          <button type="submit" class="btn btn-dark">
            <i class="bi bi-save"></i> Guardar cambios
          </button>

          <a href="mostrar_usuarios.php" class="btn btn-outline-secondary">
            Cancelar
          </a>
        </div>

      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>