<?php
session_start();
include 'includes/conectar_db.php';

$conexion = conectar();

if (!isset($_SESSION['usuario'])) {
    die('Usuario no identificado');
}

if (empty($_SESSION['carrito'])) {
    die('Carrito vacío');
}

$usuario_id = $_SESSION['usuario'];
$carrito = $_SESSION['carrito'];

$stmt = $conexion->prepare("
    SELECT nombre, apellidos, direccion, email
    FROM usuarios
    WHERE id_usuario = ?
");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die('Usuario no encontrado');
}

$nombre_envio    = $usuario['nombre'] . ' ' . $usuario['apellidos'];
$direccion_envio = $usuario['direccion'];
$email_envio     = $usuario['email'];


if (!$nombre_envio || !$direccion_envio || !$email_envio) {
    die('Datos de envío incompletos');
}

try {
    $conexion->beginTransaction();

    $total = 0;
    $productos_pedido = [];

    // Calcula total y prepara detalle
    foreach ($carrito as $id_producto => $item) {

        $stmt = $conexion->prepare(
            "SELECT precio, descuento FROM productos WHERE id_producto = ?"
        );
        $stmt->execute([$id_producto]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            throw new Exception('Producto no encontrado');
        }

        $precio = $producto['precio'];
        $descuento = $producto['descuento'];
        $precio_final = $precio - ($precio * ($descuento / 100));
        $cantidad = $item['cantidad'];
        $subtotal = $precio_final * $cantidad;

        $total += $subtotal;

        $productos_pedido[] = [
            'id_producto' => $id_producto,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_final,
            'subtotal' => $subtotal
        ];
    }

    // Inserta pedido
    $stmt = $conexion->prepare("
        INSERT INTO pedidos 
        (id_usuario, total, estado, nombre_envio, direccion_envio, email_envio)
        VALUES (?, ?, 'pendiente', ?, ?, ?)
    ");
    $stmt->execute([
        $usuario_id,
        $total,
        $nombre_envio,
        $direccion_envio,
        $email_envio
    ]);

    $pedido_id = $conexion->lastInsertId();

    // Inserta detalle_pedidos
    $stmt_detalle = $conexion->prepare("
        INSERT INTO detalle_pedidos
        (id_pedido, id_producto, cantidad, precio_unitario, subtotal)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($productos_pedido as $detalle) {
        $stmt_detalle->execute([
            $pedido_id,
            $detalle['id_producto'],
            $detalle['cantidad'],
            $detalle['precio_unitario'],
            $detalle['subtotal']
        ]);
    }

    $conexion->commit();

    // Limpiar sesión
    unset($_SESSION['carrito']);
    unset($_SESSION['nombre_envio']);
    unset($_SESSION['direccion_envio']);
    unset($_SESSION['email_envio']);

} catch (Exception $e) {
    $conexion->rollBack();
    error_log($e->getMessage());
    die('Error al procesar el pedido');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gracias por tu compra | Luz Aromática</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
      aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Categorías</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Promociones</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Nosotros</a></li>
        <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="alert alert-success">
        <h2>¡Gracias por tu compra!</h2>
        <p>Tu pago ha sido procesado con éxito.</p>
        <p>Esperamos que disfrutes de nuestros productos y que llenen tu hogar de paz y buen aroma.</p>
        <p>¡Que tengas un excelente día! <i class="bi bi-emoji-smile"></i></p>
        <a href="index.php" class="btn btn-dark">Volver a la tienda</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>