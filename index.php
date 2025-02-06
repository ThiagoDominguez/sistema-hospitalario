<?php
// Mostrar errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
include("paginas/config.php");

// Procesar el formulario al enviarlo
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
        $success = "Paciente registrado exitosamente.";

        // Redirigir al login después de un registro exitoso
        header("Location: paginas/login.php");
        exit(); // Asegúrate de detener la ejecución del script
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
  <link rel="stylesheet" href="css/styles.css">
  <title>Registro</title>
</head>

<body>
  <div class="container_both-inp">

    <h2 class="title">Registro de pacientes</h2>
    <div class="message">
      <?php if (isset($success))
        echo "<p class='success'>$success</p>"; ?>
      <?php if (isset($error))
        echo "<p class='error'>$error</p>"; ?>
    </div>
    <form method="POST" action="" class="form_both-inp">
      <!-- Mostrar mensajes de éxito o error -->
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
          <label for="dni">DNI</label>
          <input type="text" id="dni" name="DNIPac" required>

        </div>
        <div class="input-group">
          <label for="email">Correo Electrónico</label>
          <input type="email" id="email" name="EmailPac" required>

        </div>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <label for="contraseña">Contraseña</label>
          <input type="password" id="contraseña" name="Contraseña" required>

        </div>
        <div class="input-group">
          <label for="dir">Dirección</label>
          <input type="text" id="dir" name="DirPac" required>

        </div>
      </div>

      <div class="both-inp">
        <div class="input-group">
          <label for="celular">Celular</label>
          <input type="text" id="celular" name="CelPac">

        </div>
        <div class="input-group">
          <label for="genero">Género</label>
          <select id="genero" name="GenPac">
            <option value="M">Masculino</option>
            <option value="F">Femenino</option>
            <option value="O">Otro</option>
          </select>

        </div>
      </div>

      <div class="both-inp">
        <div class="input-group">
          <label for="fecha_nacimiento">Fecha de Nacimiento</label>
          <input type="date" id="fecha_nacimiento" name="FechaNacimiento">

        </div>
      </div>


      <button type="submit" class="btn blue">Registrar</button>
    <p>¿Ya tienes una cuenta? <a href="paginas/login.php">Inicia sesión aquí</a>.</p>
    </form>
  </div>
</body>

</html>