<?php
session_start();
include '../includes/conectar_db.php';

if (isset($_GET['id'])) {
    $id_producto = (int) $_GET['id'];
    $cantidad = 1;

    $con = conectar();

    // Verificar que el producto existe y esté activo
    $stmt = $con->prepare("SELECT * FROM productos WHERE id_producto = :id_producto AND activo = 1");
    $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
    $stmt->execute();
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        if (isset($_SESSION['carrito'][$id_producto])) {
            $_SESSION['carrito'][$id_producto]['cantidad'] += $cantidad;
        } else {
            $_SESSION['carrito'][$id_producto] = [
                'id_producto' => $producto['id_producto'],
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'cantidad' => $cantidad,
                'imagen' => $producto['imagen'],
            ];
        }

        // Devuelve respuesta de éxito
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}
?>