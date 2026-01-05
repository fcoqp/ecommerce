<?php
session_start();
include '../includes/conectar_db.php';

$con = conectar();

// Comprobar ID
if (!isset($_GET['codigo'])) {
    header("Location: mostrar_productos.php");
    exit;
}

$id_producto = $_GET['codigo'];

// Obtener producto
$stmt = $con->prepare("SELECT * FROM productos WHERE id_producto = ?");
$stmt->execute([$id_producto]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    die("Producto no encontrado");
}

// Obtener categorías activas
$stmtCat = $con->prepare("SELECT id_categoria, nombre FROM categorias WHERE activo = 1");
$stmtCat->execute();
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

$mensaje = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre       = $_POST['nombre'];
    $descripcion  = $_POST['descripcion'];
    $precio       = $_POST['precio'];
    $stock        = $_POST['stock'];
    $activo       = isset($_POST['activo']) ? 1 : 0;
    $id_categoria = $_POST['id_categoria'];

    $rutaImagen = $producto['imagen'];

    if (!empty($_FILES['imagen']['name'])) {
        $directorio = "../uploads/productos/";

        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $nombreImagen = time() . "_" . basename($_FILES["imagen"]["name"]);
        $rutaImagen = "uploads/productos/" . $nombreImagen;
        move_uploaded_file($_FILES["imagen"]["tmp_name"], "../" . $rutaImagen);
    }

    // UPDATE
    $sql = "UPDATE productos SET
                nombre = ?,
                descripcion = ?,
                precio = ?,
                imagen = ?,
                stock = ?,
                activo = ?,
                id_categoria = ?
            WHERE id_producto = ?";

    $stmt = $con->prepare($sql);

    if ($stmt->execute([
        $nombre,
        $descripcion,
        $precio,
        $rutaImagen,
        $stock,
        $activo,
        $id_categoria,
        $id_producto
    ])) {
        $mensaje = "Producto actualizado correctamente";

        // Recarga datos actualizados
        $stmt = $con->prepare("SELECT * FROM productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $mensaje = "Error al actualizar el producto";
    }
}
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
      <li class="breadcrumb-item"><a href="mostrar_productos.php">Productos</a></li>
      <li class="breadcrumb-item active">Modificar Producto</li>
    </ol>
  </div>
</nav>

<!-- FORMULARIO -->
<div class="container my-5">
  <h3 class="text-center mb-4">Modificar Producto</h3>
  <div class="card shadow-sm">
    <div class="card-body">

      <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data">
        <div class="row g-3">

          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control"
                   value="<?= htmlspecialchars($producto['nombre']) ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Categoría</label>
            <select name="id_categoria" class="form-select" required>
              <?php foreach ($categorias as $categoria): ?>
                <option value="<?= $categoria['id_categoria'] ?>"
                  <?= $categoria['id_categoria'] == $producto['id_categoria'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($categoria['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
          </div>

          <div class="col-md-4">
            <label class="form-label">Precio (€)</label>
            <input type="number" step="0.01" name="precio" class="form-control"
                   value="<?= $producto['precio'] ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" class="form-control"
                   value="<?= $producto['stock'] ?>">
          </div>

          <div class="col-md-4 d-flex align-items-center">
            <div class="form-check mt-4">
              <input class="form-check-input" type="checkbox" name="activo"
                <?= $producto['activo'] ? 'checked' : '' ?>>
              <label class="form-check-label">Producto activo</label>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label">Imagen actual</label><br>
            <?php if ($producto['imagen']): ?>
              <img src="../<?= $producto['imagen'] ?>" class="img-thumbnail mb-2" style="max-width: 150px;">
            <?php else: ?>
              <p class="text-muted">Sin imagen</p>
            <?php endif; ?>
          </div>

          <div class="col-12">
            <label class="form-label">Cambiar imagen</label>
            <input type="file" name="imagen" class="form-control">
          </div>

        </div>

        <div class="mt-4 d-flex gap-2">
          <button type="submit" class="btn btn-dark">
            <i class="bi bi-save"></i> Guardar cambios
          </button>

          <a href="mostrar_productos.php" class="btn btn-outline-secondary">
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