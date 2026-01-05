<?php
session_start();

if (isset($_GET['codigo'])) {
    // Obtenemos el ID del producto a eliminar
    $id_producto = $_GET['codigo'];

    // Verificamos si hay productos en el carrito
    if (isset($_SESSION['carrito']) && isset($_SESSION['carrito'][$id_producto])) {
        unset($_SESSION['carrito'][$id_producto]);

        header('Location: mostrar_carrito.php?mensaje=Producto eliminado con éxito');
        exit();
    } else {
        // Si no encontramos el producto en el carrito, redirigimos con un error
        header('Location: mostrar_carrito.php?mensaje=El producto no está en el carrito');
        exit();
    }
} else {
    header('Location: mostrar_carrito.php?mensaje=No se proporcionó un ID de producto');
    exit();
}
?>