<?php
// Mostrar errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
include("config.php");

$registro_exitoso = false;
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nombre = $_POST['Nombre'];
  $apellido = $_POST['Apellido'];
  $dni = $_POST['DNIPac'];
  $email = $_POST['EmailPac'];
  $celular = $_POST['CelPac'];
  $genero = $_POST['GenPac'];
  $fecha_nacimiento = $_POST['FechaNacimiento'];
  $direccion = $_POST['DirPac'];
  $contraseña = $_POST['Contraseña']; // Recibir la contraseña proporcionada por el usuario

  // Validar campos obligatorios
  if (empty($nombre) || empty($apellido) || empty($dni) || empty($email) || empty($contraseña)) {
    $error = "Por favor, completa todos los campos obligatorios.";
  } else {
    // Verificar si el correo electrónico ya existe en la base de datos
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE Email = ?");
    $stmt->bindParam(1, $email, PDO::PARAM_STR);
    $stmt->execute();
    $email_count = $stmt->fetchColumn();

    // Verificar si el DNI ya existe en la base de datos
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pacientes WHERE DNIPac = ?");
    $stmt->bindParam(1, $dni, PDO::PARAM_STR);
    $stmt->execute();
    $dni_count = $stmt->fetchColumn();

    if ($email_count > 0) {
      // Si el correo ya existe, mostrar el mensaje de error
      $error = "Error: El correo electrónico ya está registrado.";
    } elseif ($dni_count > 0) {
      // Si el DNI ya existe, mostrar el mensaje de error
      $error = "Error: El DNI ya está registrado.";
    } else {
      // Hash de la contraseña
      $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

      // Insertar el usuario en la tabla Usuarios
      $id_rol = 1; // Rol de paciente (según la tabla Roles)
      $stmt = $pdo->prepare("INSERT INTO usuarios (Email, Contraseña, ID_Rol) VALUES (?, ?, ?)");
      $stmt->bindParam(1, $email, PDO::PARAM_STR);
      $stmt->bindParam(2, $contraseña_hash, PDO::PARAM_STR);
      $stmt->bindParam(3, $id_rol, PDO::PARAM_INT);
      $stmt->execute();
      $id_usuario = $pdo->lastInsertId(); // Obtener el ID del usuario recién creado

      // Insertar el paciente en la base de datos
      $stmt = $pdo->prepare("INSERT INTO pacientes (Nombre, Apellido, DNIPac, EmailPac, CelPac, GenPac, FechaNacimiento, DirPac, ID_Usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->bindParam(1, $nombre, PDO::PARAM_STR);
      $stmt->bindParam(2, $apellido, PDO::PARAM_STR);
      $stmt->bindParam(3, $dni, PDO::PARAM_STR);
      $stmt->bindParam(4, $email, PDO::PARAM_STR);
      $stmt->bindParam(5, $celular, PDO::PARAM_STR);
      $stmt->bindParam(6, $genero, PDO::PARAM_STR);
      $stmt->bindParam(7, $fecha_nacimiento, PDO::PARAM_STR);
      $stmt->bindParam(8, $direccion, PDO::PARAM_STR);
      $stmt->bindParam(9, $id_usuario, PDO::PARAM_INT);

      if ($stmt->execute()) {
        $registro_exitoso = true;
      } else {
        $error = "Error al registrar el paciente: " . $pdo->errorInfo()[2];
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="assets/icons/003-add.png" type="image/x-icon">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <title>Registro</title>
</head>

<body>
  <div class="main-container">
    <div class="image-container">
      <img src="assets/images/male_doctor_inviting_people_from_queue_into_office.jpg" alt="Imagen de registro">
    </div>
    <div class="form-container">
      <form method="POST" action="" class="form_both-inp">
        <div class="both-inp">
          <div class="input-group">
            <div class="input-container">
              <i class="fa-solid fa-user icon-form-register"></i>
              <input type="text" id="nombre" name="Nombre" required placeholder="Nombre">
            </div>
          </div>
          <div class="input-group">
            <div class="input-container">
              <i class="fa-solid fa-user-group icon-form-register"></i>
              <input type="text" id="apellido" name="Apellido" required placeholder="Apellido">
            </div>
          </div>
        </div>
        <div class="both-inp">
          <div class="input-group">
            <div class="input-container">
              <i class="fa-solid fa-id-card icon-form-register"></i>
              <input type="text" id="dni" name="DNIPac" required placeholder="DNI">
            </div>
          </div>
          <div class="input-group">
            <div class="input-container">
              <i class="fa-solid fa-envelope icon-form-register"></i>
              <input type="email" id="email" name="EmailPac" required placeholder="Correo electrónico">
            </div>
          </div>
        </div>
        <div class="both-inp">
          <div class="input-group">
            <div class="input-container">
              <i class="fa-solid fa-lock icon-form-register"></i>
              <input type="password" id="contraseña" name="Contraseña" required placeholder="Contraseña">
              <i class="fa-solid fa-eye" id="togglePassword"></i>
            </div>
          </div>
          <div class="input-group">
            <div class="input-container">
              <i class="fa-solid fa-location-dot icon-form-register"></i>
              <input type="text" id="dir" name="DirPac" required placeholder="Dirección">
            </div>
          </div>
        </div>
        <div class="both-inp">
          <div class="input-group">
            <div class="input-container">
              <i class="fa-solid fa-mobile-screen-button  icon-form-register"></i>
              <input type="text" id="celular" name="CelPac" placeholder="Celular">
            </div>
          </div>
          <div class="input-group">
            <select id="genero" name="GenPac">
              <option value="M">Masculino</option>
              <option value="F">Femenino</option>
              <option value="O">Otro</option>
            </select>
          </div>
        </div>
        <div class="both-inp">
          <div class="input-group">
            <div class="input-container">
              <label for="fecha_nacimiento">Fecha de Nacimiento</label>
            </div>
            <input type="date" id="fecha_nacimiento" name="FechaNacimiento" required>
          </div>
        </div>
        <button type="submit" class="btn blue">Registrar</button>
        <p>¿Ya tienes una cuenta? <a href="paginas/login.php">Inicia sesión aquí</a>.</p>
      </form>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.getElementById('togglePassword').addEventListener('click', function (e) {
      const passwordInput = document.getElementById('contraseña');
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      this.classList.toggle('fa-eye-slash');
    });

    <?php if ($registro_exitoso): ?>
      Swal.fire({
        icon: 'success',
        title: 'Registro exitoso',
        text: 'Redirigiendo al login...',
        showConfirmButton: false,
        timer: 2000
      }).then(function () {
        window.location.href = "paginas/login.php";
      });
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