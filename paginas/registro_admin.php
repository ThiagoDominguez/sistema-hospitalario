<?php
// Se incluye el archivo de configuración que contiene la conexión a la base de datos y otras configuraciones necesarias.
require_once '../config.php';

// Verificar si el formulario fue enviado mediante el método POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Se obtienen los datos enviados desde el formulario.
  $nombre = $_POST['Nombre']; // Nombre del administrador.
  $apellido = $_POST['Apellido']; // Apellido del administrador.
  $email = $_POST['EmailAdmin']; // Correo electrónico del administrador.
  $celular = $_POST['CelAdmin']; // Número de celular del administrador (opcional).
  $direccion = $_POST['DirAdmin']; // Dirección del administrador.
  $contraseña = $_POST['Contraseña']; // Contraseña proporcionada por el usuario.

  // Validar que los campos obligatorios no estén vacíos.
  if (empty($nombre) || empty($apellido) || empty($email) || empty($contraseña)) {
    // Si algún campo obligatorio está vacío, se genera un mensaje de error.
    $error = "Por favor, completa todos los campos obligatorios.";
  } else {
    // Verificar si el correo electrónico ya está registrado en la base de datos.
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE Email = ?");
    $stmt->execute([$email]);
    $email_count = $stmt->fetchColumn(); // Se obtiene el número de registros con el mismo correo.

    if ($email_count > 0) {
      // Si el correo ya existe, se genera un mensaje de error.
      $error = "Error: El correo electrónico ya está registrado.";
    } else {
      // Si el correo no existe, se procede a registrar al usuario.

      // Se genera un hash de la contraseña para almacenarla de forma segura.
      $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

      // Insertar el usuario en la tabla 'usuarios'.
      $id_rol = 3; // ID del rol de administrador (según la estructura de la tabla 'Roles').
      $stmt = $pdo->prepare("INSERT INTO usuarios (Email, Contraseña, ID_Rol) VALUES (?, ?, ?)");
      $stmt->execute([$email, $contraseña_hash, $id_rol]);
      $id_usuario = $pdo->lastInsertId(); // Se obtiene el ID del usuario recién insertado.

      // Insertar los datos del administrador en la tabla 'personaladministrativo'.
      $stmt = $pdo->prepare("INSERT INTO personaladministrativo (NomAdmin, ApellidoAdmin, EmailAdmin, CelAdmin, DirAdmin, ID_Usuario) VALUES (?, ?, ?, ?, ?, ?)");
      if ($stmt->execute([$nombre, $apellido, $email, $celular, $direccion, $id_usuario])) {
        // Si la inserción es exitosa, se genera un mensaje de éxito y se redirige al usuario.
        $success = "Administrador registrado exitosamente.";
        header("Location: ../dashboard.php?seccion=gestionar_admin"); // Redirige al panel de administración.
        exit(); // Termina la ejecución del script después de la redirección.
      } else {
        // Si ocurre un error al insertar en 'personaladministrativo', se genera un mensaje de error.
        $error = "Error al registrar el administrador: " . $stmt->errorInfo()[2];
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
  <link rel="stylesheet" href="../css/styles.css">
  <title>Registro de Administrador</title>
</head>

<body>
  <div class="container_both-inp">
    <h2 class="title">Registro de Administrador</h2>
    <!-- Formulario para registrar un nuevo administrador -->
    <form method="POST" action="" class="form_both-inp">
      <div class="both-inp">
        <!-- Campo para el nombre del administrador -->
        <div class="input-group">
          <input type="text" id="nombre" name="Nombre" required placeholder="Nombre">
        </div>
        <!-- Campo para el apellido del administrador -->
        <div class="input-group">
          <input type="text" id="apellido" name="Apellido" required placeholder="Apellido">
        </div>
      </div>
      <div class="both-inp">
        <!-- Campo para el correo electrónico del administrador -->
        <div class="input-group">
          <input type="email" id="email" name="EmailAdmin" required placeholder="Email">
        </div>
        <!-- Campo para la contraseña del administrador -->
        <div class="input-group">
          <input type="password" id="contraseña" name="Contraseña" required placeholder="Contraseña">
        </div>
      </div>
      <div class="both-inp">
        <!-- Campo para la dirección del administrador -->
        <div class="input-group">
          <input type="text" id="dir" name="DirAdmin" required placeholder="Dirección">
        </div>
        <!-- Campo para el número de celular del administrador -->
        <div class="input-group">
          <input type="text" id="celular" name="CelAdmin" placeholder="Celular">
        </div>
      </div>
      <div class="both-inp">
        <!-- Botón para enviar el formulario -->
        <div class="input-group">
          <button type="submit" class="btn blue">Guardar Cambios</button>
        </div>
        <!-- Botón para cancelar y regresar al panel de administración -->
        <div class="input-group">
          <a href="../dashboard.php?seccion=gestionar_admin" class="btn red">Cancelar</a>
        </div>
      </div>
    </form>
  </div>
</body>

</html>