<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'includes/conectar_db.php';

$pdo = conectar();
$errores = [];
$mensajeExito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $nuevaClave = $_POST['nuevaClave'] ?? '';
    $confirmarClave = $_POST['confirmarClave'] ?? '';

    // Validaciones
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no es válido.";
    }

    if (empty($nuevaClave) || strlen($nuevaClave) < 6) {
        $errores[] = "La nueva clave debe tener al menos 6 caracteres.";
    }

    if ($nuevaClave !== $confirmarClave) {
        $errores[] = "Las claves no coinciden.";
    }

    if (empty($errores)) {
        try {
            // Verificar si el email existe
            $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                // Hash de la nueva cclave
                $nuevaClaveHash = password_hash($nuevaClave, PASSWORD_DEFAULT);

                // Actualizar la clave en la base de datos
                $stmt = $pdo->prepare("UPDATE usuarios SET clave = ? WHERE email = ?");
                $stmt->execute([$nuevaClaveHash, $email]);

                $mensajeExito = "Tu clave ha sido restablecida exitosamente.";
            } else {
                $errores[] = "El email no está registrado.";
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

<!-- Mostrar mensaje de éxito si la clave fue actualizada -->
<?php if ($mensajeExito): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($mensajeExito) ?>
    </div>
<?php endif; ?>

<!-- Formulario de restablecimiento de clave -->
<form method="POST" action="restablecer_clave.php">
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="nuevaClave">Nueva Contraseña</label>
        <input type="password" id="nuevaClave" name="nuevaClave" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="confirmarClave">Confirmar Contraseña</label>
        <input type="password" id="confirmarClave" name="confirmarClave" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
</form>