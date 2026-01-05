<?php
session_start();
include '../../includes/conectar_db.php';

$con = conectar();

// Comprobar que llega el ID
if (!isset($_GET['codigo'])) {
    header("Location: mostrar_productos.php");
    exit;
}

$id_producto = $_GET['codigo'];

// Borrado lógico (desactivar producto)
$sql = "UPDATE productos SET activo = 0 WHERE id_producto = ?";
$stmt = $con->prepare($sql);

if ($stmt->execute([$id_producto])) {
    header("Location: mostrar_productos.php?msg=desactivado");
    exit;
} else {
    die("Error al desactivar el producto");
}