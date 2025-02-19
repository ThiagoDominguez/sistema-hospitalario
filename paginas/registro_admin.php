<?php
require_once '../config.php';

// Procesar el formulario al enviarlo
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nombre = $_POST['Nombre'];
  $apellido = $_POST['Apellido'];
  $email = $_POST['EmailAdmin'];
  $celular = $_POST['CelAdmin'];
  $direccion = $_POST['DirAdmin'];
  $contraseña = $_POST['Contraseña']; // Recibir la contraseña proporcionada por el usuario

  // Validar campos obligatorios
  if (empty($nombre) || empty($apellido) || empty($email) || empty($contraseña)) {
    $error = "Por favor, completa todos los campos obligatorios.";
  } else {
    // Verificar si el correo electrónico ya existe en la base de datos
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE Email = ?");
    $stmt->execute([$email]);
    $email_count = $stmt->fetchColumn();

    if ($email_count > 0) {
      $error = "Error: El correo electrónico ya está registrado.";
    } else {
      // Hash de la contraseña
      $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

      // Insertar el usuario en la tabla Usuarios
      $id_rol = 3; // Rol de administrador (según la tabla Roles)
      $stmt = $pdo->prepare("INSERT INTO usuarios (Email, Contraseña, ID_Rol) VALUES (?, ?, ?)");
      $stmt->execute([$email, $contraseña_hash, $id_rol]);
      $id_usuario = $pdo->lastInsertId();

      // Insertar el administrador en la base de datos
      $stmt = $pdo->prepare("INSERT INTO personaladministrativo (NomAdmin, ApellidoAdmin, EmailAdmin, CelAdmin, DirAdmin, ID_Usuario) VALUES (?, ?, ?, ?, ?, ?)");
      if ($stmt->execute([$nombre, $apellido, $email, $celular, $direccion, $id_usuario])) {
        $success = "Administrador registrado exitosamente.";
        header("Location: ../dashboard.php?seccion=gestionar_admin");
        exit();
      } else {
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
    <form method="POST" action="" class="form_both-inp">
      <div class="both-inp">
        <div class="input-group">
          <input type="text" id="nombre" name="Nombre" required placeholder="Nombre">
        </div>
        <div class="input-group">
          <input type="text" id="apellido" name="Apellido" required placeholder="Apellido">
        </div>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <input type="email" id="email" name="EmailAdmin" required placeholder="Email">
        </div>
        <div class="input-group">
          <input type="password" id="contraseña" name="Contraseña" required placeholder="Contraseña">
        </div>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <input type="text" id="dir" name="DirAdmin" required placeholder="Dirección">
        </div>
        <div class="input-group">
          <input type="text" id="celular" name="CelAdmin" placeholder="Celular">
        </div>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <button type="submit" class="btn blue">Guardar Cambios</button>
        </div>
        <div class="input-group">
          <a href="../dashboard.php?seccion=gestionar_admin" class="btn red">Cancelar</a>
        </div>
      </div>
    </form>
  </div>
</body>

</html>