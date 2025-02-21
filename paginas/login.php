<?php
session_start();
// Mostrar errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
include("../config.php");

/* Las líneas `$ login_success = false;` y `$ error = '';` están inicializando dos variables en PHP. */
$login_success = false;
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  /*Las líneas `= trim (['email']);` y `= trim (['password']);` son para
  recuperar los valores de los campos de 'correo electrónico' y 'contraseña' de una solicitud de publicación, recortando cualquier espacio en blanco extra
   desde el principio y el final de la entrada, y almacenarlos en las variables y
   respectivamente. Esto ayuda a limpiar los datos de entrada antes del procesamiento posterior,
  Asegurar que no haya espacios no deseados que puedan afectar la validación o comparación
  procesos.*/
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  // Verificar si el usuario existe
  $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE Email = ?");
  $stmt->bindParam(1, $email, PDO::PARAM_STR);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && isset($user['Contraseña']) && password_verify($password, $user['Contraseña'])) {
    // El login es exitoso, almacenamos información del usuario en la sesión
    $_SESSION['usuario_id'] = $user['ID_Usuario'];
    $_SESSION['rol_id'] = $user['ID_Rol'];
    $_SESSION['email'] = $user['Email'];
    $login_success = true;

    // Redirigir a diferentes páginas según el rol
    switch ($user['ID_Rol']) {
      case 1: // Paciente
        $redirect_url = "dashboard.php?seccion=turnos";
        break;
      case 2: // Médico
        $redirect_url = "dashboard.php?seccion=mis_pacientes";
        break;
      case 3: // Administrativo
        $redirect_url = "dashboard.php?seccion=gestionar_pacientes";
        break;
      default:
        $error = "Rol desconocido.";
        $login_success = false;
    }
  } else {
    $error = "Credenciales incorrectas.";
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="../assets/icons/002-enter.png" type="image/x-icon">
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <title>Login</title>
</head>

<body>
  <div class="main-container">
    <div class="image-container">
      <img src="../assets/images/3618271.jpg" alt="Imagen de inicio de sesión">
    </div>
    <div class="form-container">
      <form method="POST" action="login.php" class="form-both">
        <div class="form-group">
          <div class="input-container">
            <i class="fa-solid fa-user icon-form-login"></i>
            <input type="email" id="email" name="email" required placeholder="correo electrónico">
          </div>
          <div class="input-container">
            <i class="fa-solid fa-lock icon-form-login"></i>
            <input type="password" id="password" name="password" required placeholder="contraseña">
            <i class="fa-solid fa-eye" id="togglePassword"></i>
          </div>
        </div>
        <button type="submit" class="btn blue">Iniciar sesión</button>
        <p>¿No tienes una cuenta? <a href="../index.php">Regístrate aquí</a>.</p>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    /*El código JavaScript que proporcionó está agregando un oyente de eventos al elemento con la ID
    'TogglePassword'. Cuando se hace clic en este elemento, la función dentro del oyente del evento es
    ejecutado. */
    document.getElementById('togglePassword').addEventListener('click', function (e) {
      const passwordInput = document.getElementById('password');
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      this.classList.toggle('fa-eye-slash');
    });

    /* Este bloque de código PHP es responsable de mostrar un mensaje de éxito utilizando el Sweetalert2
    Biblioteca si el inicio de sesión fue exitoso.*/
    <?php if ($login_success): ?>
      Swal.fire({
        icon: 'success',
        title: 'Inicio de sesión exitoso',
        text: 'Redirigiendo...',
        showConfirmButton: false,
        timer: 2000
      }).then(function () {
        window.location.href = "<?= $redirect_url ?>";
      });
      /*Este bloque de código PHP está verificando si la variable `` no está vacía. Si la variable ``
      contiene cualquier mensaje de error (lo que significa que hubo un problema durante el proceso de inicio de sesión),
      Muestre un modal Sweetalert usando la función `Swal.fire ()` de la biblioteca Sweetalert2. */
    <?php elseif (!empty($error)): ?>
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?= $error ?>'
      });
    <?php endif; ?>
  </script>
</body>

</html>