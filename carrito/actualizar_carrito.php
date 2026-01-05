<?php
session_start();

// Verificar si el carrito existe en la sesión
if (isset($_SESSION['carrito']) && isset($_POST['cantidad'])) {
    foreach ($_POST['cantidad'] as $id_producto => $nueva_cantidad) {
        // Asegurarse de que la nueva cantidad sea mayor que cero
        if ($nueva_cantidad > 0) {
            // Actualizar la cantidad del producto en el carrito
            $_SESSION['carrito'][$id_producto]['cantidad'] = $nueva_cantidad;
        }
    }
}

// Redirigir de vuelta a la página del carrito
header("Location: mostrar_carrito.php");
exit();