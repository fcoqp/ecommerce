<?php
session_start();
include '../includes/conectar_db.php';

$con = conectar();

// Obtener pedidos con datos del usuario
$sql = "SELECT 
            p.id_pedido,
            p.fecha,
            p.total,
            p.estado,
            p.nombre_envio,
            p.email_envio,
            u.nombre,
            u.apellidos
        FROM pedidos p
        INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
        ORDER BY p.fecha DESC";

$stmt = $con->prepare($sql);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Actualizar estado del pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pedido']) && isset($_POST['estado'])) {
    $id_pedido = $_POST['id_pedido'];
    $nuevo_estado = $_POST['estado'];

    // Actualizar el estado en la base de datos
    $update_sql = "UPDATE pedidos SET estado = :estado WHERE id_pedido = :id_pedido";
    $update_stmt = $con->prepare($update_sql);
    $update_stmt->bindParam(':estado', $nuevo_estado);
    $update_stmt->bindParam(':id_pedido', $id_pedido);
    $update_stmt->execute();

    // Redirigir para evitar resubir el formulario al recargar
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pedidos</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<!-- HEADER -->
<header class="bg-white shadow-sm">
  <div class="container d-flex justify-content-between align-items-center py-2">
    <a href="../index.php" class="navbar-brand">
      <img src="../img/logotipo.jpg" height="50">
    </a>

    <div class="flex-grow-1 text-center">
      <h2 class="mb-0">Panel Administrador</h2>
    </div>

    <a href="../logout.php" class="btn btn-outline-danger ms-auto">
      <i class="bi bi-box-arrow-right"></i> Cerrar sesión
    </a>
  </div>
</header>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <ul class="navbar-nav mx-auto">
      <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
      <li class="nav-item"><a class="nav-link" href="productos/mostrar_productos.php">Productos</a></li>
      <li class="nav-item"><a class="nav-link" href="categorias.php">Categorías</a></li>
      <li class="nav-item"><a class="nav-link active" href="#">Pedidos</a></li>
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
      <li class="breadcrumb-item active">Pedidos</li>
    </ol>
  </div>
</nav>

<div class="container my-5">
  <h3 class="text-center mb-4">Listado de Pedidos</h3>

  <div class="d-flex flex-wrap gap-2 mb-4">
    <a href="index.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Atrás
    </a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body table-responsive">

      <table class="table table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID Pedido</th>
            <th>Cliente</th>
            <th>Email</th>
            <th>Fecha</th>
            <th>Total</th>
            <th>Estado</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>

        <?php if (count($pedidos) === 0): ?>
          <tr>
            <td colspan="7" class="text-center text-muted">
              No hay pedidos registrados
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($pedidos as $pedido): ?>
            <tr>
              <td>#<?= $pedido['id_pedido'] ?></td>
              <td><?= htmlspecialchars($pedido['nombre'] . ' ' . $pedido['apellidos']) ?></td>
              <td><?= htmlspecialchars($pedido['email_envio']) ?></td>
              <td><?= date('d/m/Y H:i', strtotime($pedido['fecha'])) ?></td>
              <td><strong><?= number_format($pedido['total'], 2) ?> €</strong></td>
              <td class="text-center">

                <!-- Formulario para cambiar el estado -->
                <form action="" method="POST" class="d-inline">
                  <input type="hidden" name="id_pedido" value="<?= $pedido['id_pedido'] ?>">
                  <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="pendiente" <?= $pedido['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="enviado" <?= $pedido['estado'] === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                    <option value="entregado" <?= $pedido['estado'] === 'entregado' ? 'selected' : '' ?>>Entregado</option>
                    <option value="cancelado" <?= $pedido['estado'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                  </select>
                </form>
              </td>  
              <td class="text-center">

                <!-- Ver detalle del pedido -->
                <a href="ver_detalle_pedido.php?id_pedido=<?= $pedido['id_pedido'] ?>" 
                   class="btn btn-outline-dark btn-sm">
                  <i class="bi bi-eye"></i>
                </a>
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