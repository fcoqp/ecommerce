<?php
session_start();
include '../../includes/conectar_db.php';

$con = conectar();
$error = "";

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre     = $_POST['nombre'];
    $apellidos  = $_POST['apellidos'];
    $direccion  = $_POST['direccion'];
    $localidad  = $_POST['localidad'];
    $provincia  = $_POST['provincia'];
    $email      = $_POST['email'];
    $clave      = $_POST['clave'];
    $rol        = $_POST['rol'];
    $activo     = isset($_POST['activo']) ? 1 : 0;

    // Validación simple
    if (
        empty($nombre) || empty($apellidos) || empty($direccion) ||
        empty($localidad) || empty($provincia) || empty($email) ||
        empty($clave) || empty($rol)
    ) {
        $error = "Todos los campos son obligatorios.";
    } else {

        // Comprobar si el email ya existe
        $stmt = $con->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetchColumn() > 0) {
            $error = "El email ya está registrado.";
        } else {

            // Clave
            $clave_hash = password_hash($clave, PASSWORD_DEFAULT);

            // INSERT
            $sql = "INSERT INTO usuarios
                    (nombre, apellidos, direccion, localidad, provincia, email, clave, rol, activo)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $con->prepare($sql);
            $stmt->execute([
                $nombre,
                $apellidos,
                $direccion,
                $localidad,
                $provincia,
                $email,
                $clave_hash,
                $rol,
                $activo
            ]);

            header("Location: mostrar_usuarios.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Insertar Usuario</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>

<!-- HEADER -->
<header class="bg-white shadow-sm">
  <div class="container d-flex justify-content-between align-items-center py-2">
    <a href="../index.php" class="navbar-brand">
      <img src="../../img/logotipo.jpg" height="50">
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

<div class="container my-5">
  <h3 class="text-center mb-5">Insertar Nuevo Usuario</h3>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">

      <form method="POST">

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Apellidos</label>
            <input type="text" class="form-control" name="apellidos" required>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Dirección</label>
            <input type="text" class="form-control" name="direccion" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Localidad</label>
            <input type="text" class="form-control" name="localidad" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Provincia</label>
            <input type="text" class="form-control" name="provincia" required>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Clave</label>
            <input type="password" class="form-control" name="clave" required>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Rol</label>
          <select class="form-select" name="rol" required>
            <option value="cliente">Cliente</option>
            <option value="empleado">Empleado</option>
            <option value="admin">Admin</option>
          </select>
        </div>

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" name="activo" checked>
          <label class="form-check-label">Activo</label>
        </div>

        <div class="mt-4 d-flex gap-2">
          <button type="submit" class="btn btn-dark">
            <i class="bi bi-save"></i> Crear Usuario
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