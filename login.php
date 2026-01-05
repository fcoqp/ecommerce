<?php
session_start();
include 'includes/conectar_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $clave = $_POST['clave'] ?? '';

    $response = ['status' => 'error', 'message' => ''];

    if (empty($email) || empty($clave)) {
        $response['message'] = 'Debes rellenar todos los campos.';
        echo json_encode($response);
        exit();
    }

    try {
        $con = conectar();

        // Busca usuario activo por email
        $stmt = $con->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($clave, $usuario['clave'])) {
            // Usuario correcto: crear sesión
            $_SESSION['usuario'] = $usuario['id_usuario'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            // Redirigir según rol
            $response['status'] = 'success';
            $response['message'] = 'Login exitoso.';

            // Chequeamos el rol y redirigimos según corresponda
            if ($_SESSION['rol'] === 'admin') {
                $response['redirectUrl'] = 'admin/index.php';
            } else if ($_SESSION['rol'] === 'empleado') {
                $response['redirectUrl'] = 'empleado/index.php';
            } else {
                $response['redirectUrl'] = 'index.php'; 
            }

            echo json_encode($response);
            exit();
        } else {
            $response['message'] = 'Email o clave incorrectos.';
            echo json_encode($response);
            exit();
        }

    } catch (PDOException $e) {
        $response['message'] = "Error en el servidor: " . $e->getMessage();
        echo json_encode($response);
        exit();
    }

} else {
    die("Acceso no permitido.");
}
?>