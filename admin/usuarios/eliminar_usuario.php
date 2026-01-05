<?php
session_start();
include '../../includes/conectar_db.php';

$con = conectar();

// Comprobar que llega el ID
if (!isset($_GET['codigo'])) {
    header("Location: mostrar_usuarios.php");
    exit;
}

$id_usuario = $_GET['codigo'];

// Borrado lógico (desactivar usuario)
$sql = "UPDATE usuarios SET activo = 0 WHERE id_usuario = ?";
$stmt = $con->prepare($sql);

if ($stmt->execute([$id_usuario])) {
    header("Location: mostrar_usuarios.php?msg=desactivado");
    exit;
} else {
    die("Error al desactivar el usuario");
}