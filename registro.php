<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'includes/conectar_db.php';

$pdo = conectar();
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre     = trim($_POST['nombre'] ?? '');
    $apellidos  = trim($_POST['apellidos'] ?? '');
    $direccion  = trim($_POST['direccion'] ?? '');
    $localidad  = trim($_POST['localidad'] ?? '');
    $provincia  = trim($_POST['provincia'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';

    // Validaciones
    if ($nombre === '' || $apellidos === '') {
        $errores[] = "El nombre y apellidos son obligatorios.";
    }

    if ($localidad === '' || $provincia === '') {
        $errores[] = "La localidad y provincia son obligatorias.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Email no válido.";
    }

    if (strlen($password) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    }

    if (empty($errores)) {
        try {
            // Verificar si el email ya existe
            $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $errores[] = "Este email ya está registrado.";
            } else {
                // Hash de la contraseña
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                // Insertar cliente en la DB
                $stmt = $pdo->prepare(
                    "INSERT INTO usuarios 
                    (nombre, apellidos, direccion, localidad, provincia, email, clave, rol)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'cliente')"
                );
                $stmt->execute([$nombre, $apellidos, $direccion, $localidad, $provincia, $email, $passwordHash]);

                // Login automático
                $_SESSION['usuario_id'] = $pdo->lastInsertId();
                $_SESSION['usuario_nombre'] = $nombre;
                $_SESSION['usuario_apellidos'] = $apellidos;
                $_SESSION['usuario_rol'] = 'cliente';

                // Redirigir a la página principal
                header("Location: index.php");
                exit;
            }

        } catch (PDOException $e) {
            die("Error en el servidor: " . $e->getMessage());
        }
    }
}
?>

<!-- Mostrar errores si existen -->
<?php if (!empty($errores)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errores as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Formulario de registro -->
<form method="POST" action="registro.php">
    <div class="form-group">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($nombre) ?>" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="apellidos">Apellidos</label>
        <input type="text" id="apellidos" name="apellidos" value="<?= htmlspecialchars($apellidos) ?>" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="direccion">Dirección</label>
        <input type="text" id="direccion" name="direccion" value="<?= htmlspecialchars($direccion) ?>" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="localidad">Localidad</label>
        <input type="text" id="localidad" name="localidad" value="<?= htmlspecialchars($localidad) ?>" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="provincia">Provincia</label>
        <input type="text" id="provincia" name="provincia" value="<?= htmlspecialchars($provincia) ?>" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Registrar</button>
</form>