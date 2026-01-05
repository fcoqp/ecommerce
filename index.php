<?php
session_start();
include 'includes/conectar_db.php';

// Conexión a la base de datos
$con = conectar();

// Si se seleccionó una categoría, filtramos los productos por esa categoría
if (isset($_GET['categoria'])) {
    $categoriaId = $_GET['categoria'];
    $stmt = $con->prepare("SELECT * FROM productos WHERE activo = 1 AND id_categoria = :categoria_id");
    $stmt->bindParam(':categoria_id', $categoriaId, PDO::PARAM_INT);
} else {
    // Si no se seleccionó categoría, mostramos todos los productos
    $stmt = $con->prepare("SELECT * FROM productos WHERE activo = 1");
}

$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener las categorías desde la base de datos
$stmtCategorias = $con->prepare("SELECT * FROM categorias WHERE activo = 1");
$stmtCategorias->execute();
$categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Luz Aromática</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php include 'includes/header.php'; ?>
<?php include 'includes/filtro.php'; ?>

<!-- Navbar-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
      aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link active" href="index.php">Inicio</a></li>
        
        <!-- Menú desplegable Categorías -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownCategorias" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Categorías
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdownCategorias">
            <?php foreach ($categorias as $categoria): ?>
              <li><a class="dropdown-item" href="index.php?categoria=<?= $categoria['id_categoria'] ?>"><?= htmlspecialchars($categoria['nombre']) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="#">Promociones</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Nosotros</a></li>
        <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Migas-->
<nav aria-label="breadcrumb" class="my-3">
  <div class="container d-flex justify-content-between">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
      <?php if (isset($_GET['categoria'])): ?>
        <?php
          // Obtener el nombre de la categoría seleccionada
          $categoriaId = $_GET['categoria'];
          $stmtCategoria = $con->prepare("SELECT nombre FROM categorias WHERE id_categoria = :id_categoria");
          $stmtCategoria->bindParam(':id_categoria', $categoriaId, PDO::PARAM_INT);
          $stmtCategoria->execute();
          $categoriaSeleccionada = $stmtCategoria->fetch(PDO::FETCH_ASSOC);
        ?>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($categoriaSeleccionada['nombre']) ?></li>
      <?php endif; ?>
    </ol>

    <div class="d-flex align-items-center">
      <!-- Buscador -->
      <form action="index.php" method="GET" class="d-flex me-3">
        <div class="input-group" style="max-width: 250px;">
          <input type="text" name="buscar" class="form-control border-0 border-bottom" placeholder="Buscar" value="<?= isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : '' ?>">
          <button type="submit" class="input-group-text bg-white border-0 border-bottom">
            <img src="img/icons/search.svg" alt="Buscar" width="18">
          </button>
        </div>
      </form>

      <!-- Filtro -->
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
    </div>
  </div>
</nav>

<!-- Publicidad -->
<div class="container my-3">
  <div class="publicidad-fija text-center p-4">
    <h4 class="mb-2">¡Oferta Especial!</h4>
    <p class="mb-0">Hasta 20% de descuento en productos seleccionados. ¡Aprovecha ahora!</p>
  </div>
</div>

<!-- Productos -->
<div class="container my-5">
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
    <?php foreach ($productos as $producto): ?>
      <div class="col-6 col-md-3 col-lg-4">
        
        <div class="card h-100 d-flex flex-column position-relative">

          <!-- Badge Descuento -->
          <?php if (!empty($producto['descuento']) && $producto['descuento'] > 0): ?>
            <span class="badge bg-danger position-absolute top-0 start-0 m-2">
              -<?= (int)$producto['descuento'] ?>%
            </span>
          <?php endif; ?>

          <!-- Imagen -->
          <img src="uploads/<?= htmlspecialchars($producto['imagen']) ?>" 
               alt="<?= htmlspecialchars($producto['nombre']) ?>" 
               class="card-img-top mx-auto mt-3"
               style="width: 200px; height: auto; object-fit: contain;">

          <div class="card-body d-flex flex-column text-center">
            <h4 class="card-title"><?= htmlspecialchars($producto['nombre']) ?></h4>
            <p class="card-text mb-1"><?= htmlspecialchars($producto['marca']) ?></p>

            <?php
              // Cálculo precio final
              if (!empty($producto['descuento']) && $producto['descuento'] > 0) {
                  $precioFinal = $producto['precio'] * (1 - $producto['descuento'] / 100);
              } else {
                  $precioFinal = $producto['precio'];
              }
            ?>

            <!-- Precio original (tachado si hay descuento) -->
            <?php if (!empty($producto['descuento']) && $producto['descuento'] > 0): ?>
              <p class="text-muted text-decoration-line-through mb-0">
                <?= number_format($producto['precio'], 2, ',', '.') ?> €</p>
            <?php endif; ?>

            <!-- Precio final -->
            <h5 class="text-success mb-1">
              <?= number_format($precioFinal, 2, ',', '.') ?> €
            </h5>

            <p class="card-text mb-3"><?= htmlspecialchars($producto['descripcion']) ?></p>

            <!-- Botón Añadir al Carrito -->
            <div class="mt-auto">
              <a href="javascript:void(0);" 
                 class="btn btn-dark btn-sm add-to-cart" 
                 data-id="<?= $producto['id_producto'] ?>" 
                 data-cantidad="1">
                <i class="bi bi-cart3 me-1"></i> Añadir al carrito
              </a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
  const buttons = document.querySelectorAll('.add-to-cart');

  buttons.forEach(button => {
    button.addEventListener('click', function() {
      const id_producto = this.getAttribute('data-id');
      const cantidad = this.getAttribute('data-cantidad');

      // Realizar la llamada AJAX
      const xhr = new XMLHttpRequest();
      xhr.open('GET', `carrito/agregar_al_carrito.php?id=${id_producto}&cantidad=${cantidad}`, true);
      xhr.onload = function() {
        if (xhr.status === 200) {
          if (xhr.responseText === 'success') {
            // Recargar la página para ver los cambios en el carrito
            location.reload();
          } else {
            console.error('Hubo un problema al añadir el producto al carrito');
          }
        }
      };
      xhr.send();
    });
  });
});
</script>

<style>
  .card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;}
  .card:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);}
  .add-to-cart:hover {
  background-color: #696969;
  }
</style>

<!-- Paginado -->
<div class="container my-4">
  <nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
      <li class="page-item disabled">
        <a class="page-link" href="#" tabindex="-1">Anterior</a>
      </li>
      <li class="page-item active"><a class="page-link" href="#">1</a></li>
      <li class="page-item"><a class="page-link" href="#">2</a></li>
      <li class="page-item"><a class="page-link" href="#">3</a></li>
      <li class="page-item">
        <a class="page-link" href="#">Siguiente</a>
      </li>
    </ul>
  </nav>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>