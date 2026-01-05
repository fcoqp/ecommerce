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
        <li class="nav-item"><a class="nav-link active" href="#">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="mostrar_productos.php">Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="categorias.php">Categorias</a></li>
        <li class="nav-item"><a class="nav-link" href="pedidos.php">Pedidos</a></li>
        <li class="nav-item"><a class="nav-link" href="../index.php">Ir a tienda</a></li>
        <li class="nav-item"><a class="nav-link" href="../perfil.php">Mi perfil</a></li>
      </ul>
    </div>
  </div>
</nav>

<section class="container mt-5">
  <div class="row g-3">
    <div class="col-12 col-md-5 m-5">
      <a href="mostrar_productos.php" class="btn btn-outline-dark btn-lg w-100 d-flex align-items-center justify-content-center square-btn">
        <i class="bi bi-box fs-3 me-2"></i>
        Productos
      </a>
    </div>

    <div class="col-12 col-md-5 m-5">
      <a href="categorias.php" class="btn btn-outline-dark btn-lg w-100 d-flex align-items-center justify-content-center square-btn">
        <i class="bi bi-list fs-3 me-2"></i>
        Categorías
      </a>
    </div>

    <div class="col-12 col-md-5 m-5">
      <a href="pedidos.php" class="btn btn-outline-dark btn-lg w-100 d-flex align-items-center justify-content-center square-btn">
        <i class="bi bi-cart fs-3 me-2"></i>
        Pedidos
      </a>
    </div>

    <div class="col-12 col-md-5 m-5">
      <a href="../index.php" class="btn btn-outline-dark btn-lg w-100 d-flex align-items-center justify-content-center square-btn">
        <i class="bi bi-shop fs-3 me-2"></i>
        Ir a tienda
      </a>
    </div>

    <div class="col-12 col-md-5 m-5">
      <a href="../perfil.php" class="btn btn-outline-dark btn-lg w-100 d-flex align-items-center justify-content-center square-btn">
        <i class="bi bi-person fs-3 me-2"></i>
        Mi perfil
      </a>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white pt-4 mt-5">
  <div class="container">
    <div class="text-center mb-3">
      <img src="../img/logotipo2.jpg" alt="Logo" height="60">
    </div>

    <!-- Iconos redes sociales -->
    <div class="d-flex justify-content-center mb-3">
      <a href="#" class="social-icon me-3"><img src="../img/icons/instagram.svg" alt="Instagram" style="width:24px; height:24px;"></a>
      <a href="#" class="social-icon me-3"><img src="../img/icons/facebook.svg" alt="Facebook" style="width:24px; height:24px;"></a>
      <a href="#" class="social-icon me-3"><img src="../img/icons/twitter-x.svg" alt="Twitter-x" style="width:24px; height:24px;"></a>
      <a href="#" class="social-icon me-3"><img src="../img/icons/whatsapp.svg" alt="Whatsapp" style="width:24px; height:24px;"></a>
    </div>

    <!-- Enlaces legales -->
    <div class="d-flex flex-wrap justify-content-center mb-3">
      <a href="#" class="text-white mx-2 text-decoration-none">Aviso Legal</a>
      <a href="#" class="text-white mx-2 text-decoration-none">Política de Privacidad</a>
      <a href="#" class="text-white mx-2 text-decoration-none">Política de Cookies</a>
      <a href="#" class="text-white mx-2 text-decoration-none">Configuración de Cookies</a>
      <a href="#" class="text-white mx-2 text-decoration-none">Condiciones Generales</a>
      <a href="#" class="text-white mx-2 text-decoration-none">Pago, envíos y devoluciones</a>
    </div>

    <hr class="border-top border-white">

    <!-- Datos de la empresa y correo -->
    <div class="d-flex flex-column flex-md-row justify-content-center align-items-center mb-2 text-center">
        <div class="mx-2">C/ Illueca, 28 | 03206 Elche (Alicante)</div>
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-center align-items-center mb-2 text-center">
        <div class="mx-2">info@luzaromaticashop.com</div>
    </div>

    <div class="text-center small mt-4">
      Copyright&copy; 2025 Luz Aromática. Todos los derechos reservados.
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>