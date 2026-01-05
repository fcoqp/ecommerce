<?php
session_start();

// Verificar si el carrito existe en la sesión
if (isset($_SESSION['carrito'])) {
    unset($_SESSION['carrito']);
    
    // Redirigir a la página del carrito
    header("Location: mostrar_carrito.php?mensaje=Carrito vaciado con éxito");
    exit();
} else {
    // Si no existe el carrito, redirigir al carrito
    header("Location: mostrar_carrito.php?mensaje=El carrito ya está vacío");
    exit();
}
?>