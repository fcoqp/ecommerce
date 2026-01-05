<?php
// Conexión a la base de datos usando PDO
function conectar() {
    $host = 'sql100.infinityfree.com';
    $dbname = 'if0_40596577_ecommerce'; // Nombre correcto de tu base de datos
    $user = 'if0_40596577';         // Cambia si es necesario
    $pass = 'IffTgUyo53k8j4';             // Cambia si es necesario
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $opciones);
    } catch (PDOException $e) {
        die('Error de conexión: ' . $e->getMessage());
    }
}
?>