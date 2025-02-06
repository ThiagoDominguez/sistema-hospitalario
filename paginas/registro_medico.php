<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sghsantarosa";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
  die("Error de conexión: " . $conn->connect_error);
}

// Procesar el formulario al enviarlo
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nombre = $_POST['Nombre'];
  $apellido = $_POST['Apellido'];
  $email = $_POST['EmailMed'];
  $celular = $_POST['CelMed'];
  $genero = $_POST['GenMed'];
  $especialidad = $_POST['EspecialidadMed'];
  $direccion = $_POST['DirMed'];
  $contraseña = $_POST['Contraseña']; // Recibir la contraseña proporcionada por el usuario
  var_dump($_POST);

  // Validar campos obligatorios
  if (empty($nombre) || empty($apellido) || empty($email) || empty($contraseña) || empty($especialidad)) {
    $error = "Por favor, completa todos los campos obligatorios.";
  } else {
    // Verificar si el correo electrónico ya existe en la base de datos
    $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($email_count);
    $stmt->fetch();
    $stmt->close();

    if ($email_count > 0) {
      // Si el correo ya existe, mostrar el mensaje de error
      $error = "Error: El correo electrónico ya está registrado.";
    } else {
      // Hash de la contraseña
      $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

      // Insertar el usuario en la tabla Usuarios
      $id_rol = 2; // Rol de médico (según la tabla Roles)
      $stmt = $conn->prepare("INSERT INTO usuarios (Email, Contraseña, ID_Rol) VALUES (?, ?, ?)");
      $stmt->bind_param("ssi", $email, $contraseña_hash, $id_rol);
      $stmt->execute();
      $id_usuario = $stmt->insert_id; // Obtener el ID del usuario recién creado
      $stmt->close();

      // Insertar el médico en la base de datos
      $stmt = $conn->prepare("INSERT INTO personalmedico (NomMed, ApellidoMed, EmailMed, CelMed, GenMed, EspecialidadMed, DirMed, ID_Usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("ssssssss", $nombre, $apellido, $email, $celular, $genero, $especialidad, $direccion, $id_usuario);

      if ($stmt->execute()) {
        $success = "Médico registrado exitosamente.";
        header("Location: dashboard.php?seccion=gestionar_medicos");
        exit();
      } else {
        $error = "Error al registrar el médico: " . $conn->error;
      }
      $stmt->close();
    }
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/styles.css">
  <title>Registro de Médico</title>
</head>

<body>
  <div class="container_both-inp">
    <h2 class="title">Registro de Médico</h2>
    <form method="POST" action="" class="form_both-inp">
      <!-- Mostrar mensajes de éxito o error -->
      <div class="message">
        <?php if (isset($success))
          echo "<p class='success'>$success</p>"; ?>
        <?php if (isset($error))
          echo "<p class='error'>$error</p>"; ?>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <label for="nombre">Nombre</label>
          <input type="text" id="nombre" name="Nombre" required>
        </div>
        <div class="input-group">
          <label for="apellido">Apellido</label>
          <input type="text" id="apellido" name="Apellido" required>
        </div>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <label for="email">Correo Electrónico</label>
          <input type="email" id="email" name="EmailMed" required>
        </div>
        <div class="input-group">
          <label for="contraseña">Contraseña</label>
          <input type="password" id="contraseña" name="Contraseña" required>
        </div>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <label for="dir">Dirección</label>
          <input type="text" id="dir" name="DirMed" required>
        </div>
        <div class="input-group">
          <label for="celular">Celular</label>
          <input type="text" id="celular" name="CelMed">
        </div>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <label for="genero">Género</label>
          <select id="genero" name="GenMed">
            <option value="M">Masculino</option>
            <option value="F">Femenino</option>
          </select>
        </div>
        <div class="input-group">
          <label for="especialidad">Especialidad</label>
          <input type="text" id="especialidad" name="EspecialidadMed" required>
        </div>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <button type="submit" class="btn blue">Guardar Cambios</button>

        </div>
        <div class="input-group">
          <a href="dashboard.php?seccion=gestionar_medicos" class="btn red">Cancelar</a>

        </div>
      </div>
    </form>
  </div>
</body>

</html>