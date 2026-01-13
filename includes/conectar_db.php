<?php
// Conexión a la base de datos usando PDO
function conectar() {
    $host = '';
    $dbname = '';
    $user = '';
    $pass = '';
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
