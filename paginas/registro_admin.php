<?php
require_once '../config.php';

header('Content-Type: application/json'); // Establece el tipo de contenido como JSON

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
    echo json_encode(['status' => 'error', 'message' => 'Por favor, completa todos los campos obligatorios.']);
    exit();
  }

  // Verificar si el correo electrónico ya existe en la base de datos
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE Email = ?");
  $stmt->execute([$email]);
  $email_count = $stmt->fetchColumn();

  if ($email_count > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Error: El correo electrónico ya está registrado.']);
    exit();
  }

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
    echo json_encode(['status' => 'success', 'message' => 'Administrador registrado exitosamente.']);
    exit();
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Error al registrar el administrador: ' . $stmt->errorInfo()[2]]);
    exit();
  }
}
?>