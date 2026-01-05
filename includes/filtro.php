<?php
if (session_status() === PHP_SESSION_NONE) session_start();

try {
    $categoria = $_GET['categoria'] ?? '';
    $precio_min = $_GET['precio_min'] ?? '';
    $precio_max = $_GET['precio_max'] ?? '';
    $orden = $_GET['orden'] ?? '';

    $sql = "SELECT p.*, c.nombre AS categoria_nombre 
            FROM productos p
            JOIN categorias c ON p.id_categoria = c.id_categoria
            WHERE p.activo = 1";

    $params = [];
    if (!empty($categoria)) { 
        $sql .= " AND p.id_categoria = :categoria"; 
        $params[':categoria'] = $categoria; 
    }
    if (!empty($precio_min)) { 
        $sql .= " AND p.precio >= :precio_min"; 
        $params[':precio_min'] = $precio_min; 
    }
    if (!empty($precio_max)) { 
        $sql .= " AND p.precio <= :precio_max"; 
        $params[':precio_max'] = $precio_max; 
    }

    switch ($orden) {
        case "nombre_asc": $sql .= " ORDER BY p.nombre ASC"; break;
        case "nombre_desc": $sql .= " ORDER BY p.nombre DESC"; break;
        case "precio_asc": $sql .= " ORDER BY p.precio ASC"; break;
        case "precio_desc": $sql .= " ORDER BY p.precio DESC"; break;
        default: $sql .= " ORDER BY p.id_producto DESC"; break;
    }

    $stmt = $con->prepare($sql);
    $stmt->execute($params);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener categorías
    $catStmt = $con->prepare("SELECT * FROM categorias WHERE activo = 1");
    $catStmt->execute();
    $categorias = $catStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al obtener productos: " . $e->getMessage());
}