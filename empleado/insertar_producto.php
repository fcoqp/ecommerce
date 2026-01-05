<?php
session_start();
include '../includes/conectar_db.php';

$con = conectar();
$mensaje = "";

// Obtener categorías activas
$stmtCat = $con->prepare("SELECT id_categoria, nombre FROM categorias WHERE activo = 1");
$stmtCat->execute();
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre       = $_POST['nombre'];
    $descripcion  = $_POST['descripcion'];
    $precio       = $_POST['precio'];
    $stock        = $_POST['stock'];
    $activo       = isset($_POST['activo']) ? 1 : 0;
    $id_categoria = $_POST['id_categoria'];
    $marca        = $_POST['marca'];
    $descuento    = isset($_POST['descuento']) ? $_POST['descuento'] : 0;

    // Subida imagen
    $rutaImagen = null;

    if (!empty($_FILES['imagen']['name'])) {
        $directorio = "../uploads/";

        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $nombreImagen = time() . "_" . basename($_FILES["imagen"]["name"]);
        $rutaImagen = "../uploads/" . $nombreImagen;
        $rutaCompleta = "" . $rutaImagen;

        move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaCompleta);
    }

    // INSERT
    $sql = "INSERT INTO productos 
            (nombre, descripcion, precio, marca, imagen, stock, activo, id_categoria, descuento)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $con->prepare($sql);

    if ($stmt->execute([$nombre, $descripcion, $precio, $marca, $rutaImagen, $stock, $activo, $id_categoria, $descuento])) {
        $mensaje = "Producto insertado correctamente";
    } else {
        $mensaje = "Error al insertar el producto";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Insertar Producto</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/styles.css">
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
    <div class="collapse navbar-collapse show">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link" href="../index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link active" href="mostrar_productos.php">Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="../categorias.php">Categorías</a></li>
        <li class="nav-item"><a class="nav-link" href="../pedidos.php">Pedidos</a></li>
        <li class="nav-item"><a class="nav-link" href="../usuarios/mostrar_usuarios.php">Usuarios</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Informes y estadísticas</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- MIGAS -->
<nav aria-label="breadcrumb" class="my-3">
  <div class="container">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../index.php">Inicio</a></li>
      <li class="breadcrumb-item"><a href="mostrar_productos.php">Productos</a></li>
      <li class="breadcrumb-item active">Insertar</li>
    </ol>
  </div>
</nav>

<!-- FORMULARIO -->
<div class="container my-5">
  <h3 class="text-center mb-5">Insertar Nuevo Producto</h3>
  <div class="card shadow-sm">
    <div class="card-body">

      <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data">

        <div class="row g-3">

          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" placeholder="Nombre del producto" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Categoría</label>
            <select name="id_categoria" class="form-select" required>
              <option value="">Selecciona una categoría</option>
              <?php foreach ($categorias as $categoria): ?>
                <option value="<?= $categoria['id_categoria'] ?>">
                  <?= htmlspecialchars($categoria['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Marca</label>
            <input type="text" name="marca" class="form-control" placeholder="Marca del producto">
          </div>

          <div class="col-12">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3" placeholder="Descripción del producto"></textarea>
          </div>

          <div class="col-md-4">
            <label class="form-label">Precio (€)</label>
            <input type="number" step="0.01" name="precio" class="form-control" placeholder="Precio del producto" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" class="form-control" value="0">
          </div>

          <div class="col-md-4 d-flex align-items-center">
            <div class="form-check mt-4">
              <input class="form-check-input" type="checkbox" name="activo" checked>
              <label class="form-check-label">Producto activo</label>
            </div>
          </div>

          <div class="col-md-4">
            <label class="form-label">Descuento (%)</label>
            <input type="number" name="descuento" class="form-control" step="0.01" placeholder="Descuento en porcentaje">
          </div>

          <div class="col-12">
            <label class="form-label">Imagen</label>
            <input type="file" name="imagen" class="form-control">
          </div>

        </div>

        <div class="mt-4 d-flex gap-2">
          <button type="submit" class="btn btn-dark">
            <i class="bi bi-save"></i> Guardar
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