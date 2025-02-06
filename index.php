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
    $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($email_count);
    $stmt->fetch();
    $stmt->close();

    // Verificar si el DNI ya existe en la base de datos
    $stmt = $conn->prepare("SELECT COUNT(*) FROM pacientes WHERE DNIPac = ?");
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $stmt->bind_result($dni_count);
    $stmt->fetch();
    $stmt->close();

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
      $stmt = $conn->prepare("INSERT INTO usuarios (Email, Contraseña, ID_Rol) VALUES (?, ?, ?)");
      $stmt->bind_param("ssi", $email, $contraseña_hash, $id_rol);
      $stmt->execute();
      $id_usuario = $stmt->insert_id; // Obtener el ID del usuario recién creado
      $stmt->close();

      // Insertar el paciente en la base de datos
      $stmt = $conn->prepare("INSERT INTO pacientes (Nombre, Apellido, DNIPac, EmailPac, CelPac, GenPac, FechaNacimiento, DirPac, ID_Usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("ssssssssi", $nombre, $apellido, $dni, $email, $celular, $genero, $fecha_nacimiento, $direccion, $id_usuario);

      if ($stmt->execute()) {
        $success = "Paciente registrado exitosamente.";

        // Redirigir al login después de un registro exitoso
        header("Location: paginas/login.php");
        exit(); // Asegúrate de detener la ejecución del script
      } else {
        $error = "Error al registrar el paciente: " . $conn->error;
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
  <link rel="stylesheet" href="css/styles.css">
  <title>Registro</title>
</head>

<body>
  <h2 class="title">Registro</h2>
  <form method="POST" action="">
    <!-- Mostrar mensajes de éxito o error -->
    <div class="message">
      <?php if (isset($success))
        echo "<p class='success'>$success</p>"; ?>
      <?php if (isset($error))
        echo "<p class='error'>$error</p>"; ?>
    </div>
    <label for="nombre">Nombre</label>
    <input type="text" id="nombre" name="Nombre" required>

    <label for="apellido">Apellido</label>
    <input type="text" id="apellido" name="Apellido" required>

    <label for="dni">DNI</label>
    <input type="text" id="dni" name="DNIPac" required>

    <label for="email">Correo Electrónico</label>
    <input type="email" id="email" name="EmailPac" required>
    <label for="contraseña">Contraseña</label>
    <input type="password" id="contraseña" name="Contraseña" required>

    <label for="dir">Dirección</label>
    <input type="text" id="dir" name="DirPac" required>

    <label for="celular">Celular</label>
    <input type="text" id="celular" name="CelPac">

    <label for="genero">Género</label>
    <select id="genero" name="GenPac">
      <option value="M">Masculino</option>
      <option value="F">Femenino</option>
      <option value="O">Otro</option>
    </select>

    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
    <input type="date" id="fecha_nacimiento" name="FechaNacimiento">


    <button type="submit">Registrar</button>
    <p>¿Ya tienes una cuenta? <a href="paginas/login.php">Inicia sesión aquí</a>.</p>
  </form>
</body>

</html>