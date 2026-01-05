<?php
session_start();
include '../includes/conectar_db.php';

$con = conectar();

$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

$sql = "SELECT * FROM productos";

if ($buscar !== '') {
    $sql .= " WHERE nombre LIKE :nombre OR descripcion LIKE :descripcion";
}

$stmt = $con->prepare($sql);

if ($buscar !== '') {
    $like_buscar = "%$buscar%";
    $stmt->bindParam(':nombre', $like_buscar);
    $stmt->bindParam(':descripcion', $like_buscar);
}

$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Empleado - Luz Aromatica</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<!-- Header -->
<header class="bg-white shadow-sm">
  <div class="container d-flex justify-content-between align-items-center py-2">
    <a href="index.php" class="navbar-brand">
      <img src="../img/logotipo.jpg" alt="Logo" height="50">
    </a>
    
    <div class="flex-grow-1 text-center">
      <h2 class="mb-0">Panel Empleado</h2>
    </div>

    <a href="../logout.php" class="btn btn-outline-danger ms-auto">
      <i class="bi bi-box-arrow-right"></i> Cerrar sesión
    </a>
  </div>
</header>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link" href="#">Inicio</a></li>
        <li class="nav-item"><a class="nav-link active" href="mostrar_productos.php">Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="categorias.php">Categorias</a></li>
        <li class="nav-item"><a class="nav-link" href="pedidos.php">Pedidos</a></li>
        <li class="nav-item"><a class="nav-link" href="../index.php">Ir a tienda</a></li>
        <li class="nav-item"><a class="nav-link" href="../perfil.php">Mi perfil</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- MIGAS -->
<nav aria-label="breadcrumb" class="my-3">
  <div class="container">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
      <li class="breadcrumb-item active">Productos</li>
    </ol>
  </div>
</nav>

<div class="container my-5">
  <h3 class="text-center mb-4">Productos</h3>
  <div class="d-flex flex-wrap gap-2 mb-4">
    <a href="index.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Atrás
    </a>

    <a href="insertar_producto.php" class="btn btn-warning">
      <i class="bi bi-plus-circle me-1"></i> Insertar producto
    </a>

  <!-- Formulario de Búsqueda -->
    <form action="" method="GET" class="d-flex gap-2 ms-auto">
      <input type="text" name="buscar" class="form-control" placeholder="Buscar productos" value="<?= isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : '' ?>">
      <button type="submit" class="btn btn-dark">Buscar</button>
    </form>

      <!-- Botón de Filtro -->
    <div class="dropdown">
      <button class="btn btn-outline-dark dropdown-toggle" type="button" id="dropdownFiltro" data-bs-toggle="dropdown" aria-expanded="false">
        Filtros
      </button>
      <ul class="dropdown-menu" aria-labelledby="dropdownFiltro">
        <li><a class="dropdown-item" href="?orden=precio_asc">Precio: Bajo a Alto</a></li>
        <li><a class="dropdown-item" href="?orden=precio_desc">Precio: Alto a Bajo</a></li>
        <li><a class="dropdown-item" href="?orden=nombre_asc">Nombre A-Z</a></li>
        <li><a class="dropdown-item" href="?orden=nombre_desc">Nombre Z-A</a></li>
      </ul>
    </div>

  <!-- TABLA -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Categoría</th>
            <th>Precio</th>
            <th>Imagen</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>

        <?php if (count($productos) === 0): ?>
          <tr>
            <td colspan="7">No se encontraron productos</td>
          </tr>
        <?php else: ?>
          <?php foreach ($productos as $producto): ?>
            <tr class="<?= $producto['activo'] ? '' : 'table-secondary' ?>">
              <td><?= htmlspecialchars($producto['id_producto']) ?></td>
              <td><?= htmlspecialchars($producto['nombre']) ?></td>
              <td><?= htmlspecialchars($producto['descripcion']) ?></td>
              <td><?= htmlspecialchars($producto['id_categoria']) ?></td>
              <td><?= number_format($producto['precio'], 2, ',', '.') ?> €</td>
              <td>
                <?php if ($producto['imagen']): ?>
                  <img src="../uplodas/<?= htmlspecialchars($producto['imagen']) ?>"
                    class="img-fluid rounded"
                    style="max-width: 80px;">
                <?php endif; ?>
              </td>
              <td>
                <?php if ($producto['activo']): ?>
                  <span class="badge bg-success">Activo</span>
                <?php else: ?>
                  <span class="badge bg-secondary">Inactivo</span>
                <?php endif; ?>
              </td>
              <td>
                <a href="modificar_producto.php?codigo=<?= $producto['id_producto'] ?>"
                  class="btn btn-success btn-sm">
                  <i class="bi bi-pencil"></i>
                </a>
                <?php if ($producto['activo']): ?>
                  <a href="eliminar_producto.php?codigo=<?= $producto['id_producto'] ?>"
                    class="btn btn-danger btn-sm"
                    onclick="return confirm('¿Seguro que quieres desactivar este producto?')">
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>