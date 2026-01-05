<?php
session_start();
include '../includes/conectar_db.php';

$con = conectar();
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre']);
    $id_categoria = $_POST['id_categoria'] ?? null;

    if ($id_categoria) {
        $stmt = $con->prepare("UPDATE categorias SET nombre = ? WHERE id_categoria = ?");
        $stmt->execute([$nombre, $id_categoria]);
        $mensaje = "Categoría actualizada";
    } else {
        $stmt = $con->prepare("INSERT INTO categorias (nombre, activo) VALUES (?, 1)");
        $stmt->execute([$nombre]);
        $mensaje = "Categoría creada";
    }
}

if (isset($_GET['toggle'])) {
    $stmt = $con->prepare("UPDATE categorias SET activo = IF(activo=1,0,1) WHERE id_categoria = ?");
    $stmt->execute([$_GET['toggle']]);
    header("Location: categorias.php");
    exit;
}

$stmt = $con->query("SELECT * FROM categorias ORDER BY id_categoria DESC");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categoriaEditar = null;
if (isset($_GET['editar'])) {
    $stmt = $con->prepare("SELECT * FROM categorias WHERE id_categoria = ?");
    $stmt->execute([$_GET['editar']]);
    $categoriaEditar = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Admin - Categorías</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/styles.css">
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
    <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="productos/mostrar_productos.php">Productos</a></li>
        <li class="nav-item"><a class="nav-link active" href="#">Categorías</a></li>
        <li class="nav-item"><a class="nav-link" href="pedidos.php">Pedidos</a></li>
        <li class="nav-item"><a class="nav-link" href="usuarios/mostrar_usuarios.php">Usuarios</a></li>
        <li class="nav-item"><a class="nav-link" href="informes.php">Informes y estadísticas</a></li>
    </ul>
  </div>
</nav>

<!-- MIGAS -->
<nav aria-label="breadcrumb" class="my-3">
  <div class="container">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
      <li class="breadcrumb-item active">Categorías</li>
    </ol>
  </div>
</nav>

<div class="container my-5">
  <h3 class="text-center mb-5">Categorías</h3>
  <div class="d-flex flex-wrap gap-2 mb-4">
    <a href="index.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Atrás
    </a>
  </div>

  <?php if ($mensaje): ?>
    <div class="alert alert-success"><?= $mensaje ?></div>
  <?php endif; ?>

  <!-- FORMULARIO -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="id_categoria" value="<?= $categoriaEditar['id_categoria'] ?? '' ?>">

        <div class="row g-3 align-items-end">
          <div class="col-md-8">
            <label class="form-label">Nombre de la categoría</label>
            <input type="text" name="nombre" class="form-control"
                   value="<?= $categoriaEditar['nombre'] ?? '' ?>" required>
          </div>

          <div class="col-md-4">
            <button type="submit" class="btn btn-dark w-100">
              <i class="bi bi-save"></i>
              <?= $categoriaEditar ? 'Actualizar' : 'Crear' ?>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- TABLA -->
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle text-center">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>

      <?php foreach ($categorias as $cat): ?>
        <tr class="<?= $cat['activo'] ? '' : 'table-secondary' ?>">
          <td><?= $cat['id_categoria'] ?></td>
          <td><?= htmlspecialchars($cat['nombre']) ?></td>
          <td>
            <span class="badge <?= $cat['activo'] ? 'bg-success' : 'bg-secondary' ?>">
              <?= $cat['activo'] ? 'Activa' : 'Inactiva' ?>
            </span>
          </td>
          <td>
            <a href="?editar=<?= $cat['id_categoria'] ?>" class="btn btn-success btn-sm">
              <i class="bi bi-pencil"></i>
            </a>
            <a href="?toggle=<?= $cat['id_categoria'] ?>"
               class="btn btn-warning btn-sm"
               onclick="return confirm('¿Cambiar estado de la categoría?')">
              <i class="bi bi-arrow-repeat"></i>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>

      </tbody>
    </table>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>