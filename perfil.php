<?php
session_start();
include 'includes/conectar_db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['usuario'];

$con = conectar();

// Obtiene los datos del usuario
$stmt = $con->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Usuario no encontrado");
}

$mensaje = "";

// Procesa formulario para actualizar los datos del perfil
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!empty($_POST['nueva_clave']) && !empty($_POST['confirmar_clave'])) {
        $nueva_clave = $_POST['nueva_clave'];
        $confirmar_clave = $_POST['confirmar_clave'];

        if ($nueva_clave === $confirmar_clave) {
            $hashed_clave = password_hash($nueva_clave, PASSWORD_BCRYPT);

            // Actualiza la contraseña en la base de datos
            $sql = "UPDATE usuarios SET clave = ? WHERE id_usuario = ?";
            $stmt = $con->prepare($sql);

            if ($stmt->execute([$hashed_clave, $id_usuario])) {
                $mensaje = "Contraseña actualizada correctamente";
            } else {
                $mensaje = "Error al actualizar la contraseña";
            }
        } else {
            $mensaje = "Las contraseñas no coinciden";
        }
    } else {
        // Actualiza datos
        $nombre    = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $direccion = $_POST['direccion'];
        $localidad = $_POST['localidad'];
        $provincia = $_POST['provincia'];
        $email     = $_POST['email'];
        $activo    = isset($_POST['activo']) ? 1 : 0;

        // Actualiza los datos en la base de datos
        $sql = "UPDATE usuarios SET
                    nombre = ?,
                    apellidos = ?,
                    direccion = ?,
                    localidad = ?,
                    provincia = ?,
                    email = ?,
                    activo = ?
                WHERE id_usuario = ?";

        $stmt = $con->prepare($sql);

        if ($stmt->execute([
            $nombre,
            $apellidos,
            $direccion,
            $localidad,
            $provincia,
            $email,
            $activo,
            $id_usuario
        ])) {
            $mensaje = "Perfil actualizado correctamente";

            // Recarga los datos actualizados
            $stmt = $con->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
            $stmt->execute([$id_usuario]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $mensaje = "Error al actualizar el perfil";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Modificar Perfil</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
      aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link" href="#">Inicio</a></li>
        <li class="nav-item"><a class="nav-link active" href="#">Mi perfil</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- MIGAS -->
<nav aria-label="breadcrumb" class="my-3">
  <div class="container d-flex justify-content-between">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
    </ol>
  </div>
</nav>

<!-- FORMULARIO -->
<div class="container my-5">
  <h3 class="text-center mb-5">Mi Perfil</h3>
  <div class="card shadow-sm">
    <div class="card-body">

      <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="row g-3">

          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control"
                   value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Apellidos</label>
            <input type="text" name="apellidos" class="form-control"
                   value="<?= htmlspecialchars($usuario['apellidos']) ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control"
                   value="<?= htmlspecialchars($usuario['direccion']) ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">Localidad</label>
            <input type="text" name="localidad" class="form-control"
                   value="<?= htmlspecialchars($usuario['localidad']) ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">Provincia</label>
            <input type="text" name="provincia" class="form-control"
                   value="<?= htmlspecialchars($usuario['provincia']) ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($usuario['email']) ?>" required>
          </div>

          <!-- Botón para mostrar los campos de cambiar la contraseña -->
          <div class="mt-4 d-flex gap-2">
            <button type="button" class="btn btn-warning" id="cambiarClaveBtn">
              <i class="bi bi-lock"></i> Cambiar clave
            </button>
          </div>

          <div id="cambiarClaveContainer" style="display: none;">
            <div class="col-md-6">
              <label class="form-label">Nueva Contraseña</label>
              <input type="password" name="nueva_clave" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirmar Nueva Contraseña</label>
              <input type="password" name="confirmar_clave" class="form-control">
            </div>
          </div>

          <div class="col-md-6 d-flex align-items-center">
            <div class="form-check mt-4">
              <input class="form-check-input" type="checkbox" name="activo"
                <?= $usuario['activo'] ? 'checked' : '' ?> disabled>
              <label class="form-check-label">Cuenta activa</label>
            </div>
          </div>

        </div>

        <div class="mt-4 d-flex gap-2">
          <button type="submit" class="btn btn-dark">
            <i class="bi bi-save"></i> Guardar cambios
          </button>

          <a href="index.php" class="btn btn-outline-secondary">
            Cancelar
          </a>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
  // Capturar el evento de clic en el botón "Cambiar clave"
  document.getElementById('cambiarClaveBtn').addEventListener('click', function() {
    // Muestra contenedor oculto de clave
    var contenedorClave = document.getElementById('cambiarClaveContainer');
    if (contenedorClave.style.display === "none") {
      contenedorClave.style.display = "block";
    } else {
      contenedorClave.style.display = "none";
    }
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>