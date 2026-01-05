<!-- OFFCANVAS LOGIN -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="loginPanel">
  <div class="offcanvas-header justify-content-center position-relative">
    <h5 class="offcanvas-title text-center">Iniciar sesión</h5>
    <button type="button" class="btn-close position-absolute end-0" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <div id="loginError" class="alert alert-danger d-none" role="alert"></div>

    <form id="loginForm" method="POST">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control" placeholder="email@ejemplo.com" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Clave</label>
        <input type="password" name="clave" id="clave" class="form-control" placeholder="Clave de acceso" required>
      </div>
      <button type="submit" class="btn btn-dark w-100">Entrar</button>
      <div class="text-center mt-2">
        <a href="includes/offcanvas-registro" class="small text-dark"
           data-bs-toggle="offcanvas"
           data-bs-target="#registerPanel"
           onclick="var offcanvasLogin = bootstrap.Offcanvas.getInstance(document.getElementById('loginPanel')); offcanvasLogin.hide();">
           Registrarse
        </a>
      </div>
      <div class="text-center mt-2"> 
        <a href="includes/offcanvas-clave" class="small text-dark"
           data-bs-toggle="offcanvas"
           data-bs-target="#restablecerClavePanel"
           onclick="var offcanvasLogin = bootstrap.Offcanvas.getInstance(document.getElementById('loginPanel')); offcanvasLogin.hide();">
           Restablecer clave
        </a> 
      </div>  
    </form> 
  </div> 
</div>

<!-- Script de AJAX para procesar el login -->
<script>
  document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Evitar el envío normal del formulario

    const email = document.getElementById('email').value;
    const clave = document.getElementById('clave').value;
    
    // Limpiar mensaje de error previo
    document.getElementById('loginError').classList.add('d-none');
    
    // Crear la solicitud AJAX
    const formData = new FormData();
    formData.append('email', email);
    formData.append('clave', clave);

    fetch('login.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.status === 'error') {
        // Si hay error, mostrar el mensaje
        document.getElementById('loginError').textContent = data.message;
        document.getElementById('loginError').classList.remove('d-none');
      } else if (data.status === 'success') {
        // Si el login es exitoso, redirigir
        window.location.href = data.redirectUrl || 'index.php'; // O cualquier página que desees
      }
    })
    .catch(error => {
      console.error('Error en la solicitud AJAX:', error);
    });
  });
</script>