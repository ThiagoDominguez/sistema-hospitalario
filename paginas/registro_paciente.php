<?php
require_once '../config.php';

header('Content-Type: application/json');

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
    echo json_encode(["status" => "error", "message" => "Por favor, completa todos los campos obligatorios."]);
    exit();
  }

  try {
    // Iniciar una transacción
    $pdo->beginTransaction();

    // Verificar si el correo electrónico ya existe en la base de datos
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE Email = ?");
    $stmt->execute([$email]);
    $email_count = $stmt->fetchColumn();

    // Verificar si el DNI ya existe en la base de datos
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pacientes WHERE DNIPac = ?");
    $stmt->execute([$dni]);
    $dni_count = $stmt->fetchColumn();

    if ($email_count > 0) {
      // Si el correo ya existe, mostrar el mensaje de error
      echo json_encode(["status" => "error", "message" => "Error: El correo electrónico ya está registrado."]);
      $pdo->rollBack();
      exit();
    } elseif ($dni_count > 0) {
      // Si el DNI ya existe, mostrar el mensaje de error
      echo json_encode(["status" => "error", "message" => "Error: El DNI ya está registrado."]);
      $pdo->rollBack();
      exit();
    } else {
      // Hash de la contraseña
      $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

      // Insertar el usuario en la tabla Usuarios
      $id_rol = 1; // Rol de paciente (según la tabla Roles)
      $stmt = $pdo->prepare("INSERT INTO usuarios (Email, Contraseña, ID_Rol) VALUES (?, ?, ?)");
      $stmt->execute([$email, $contraseña_hash, $id_rol]);
      $id_usuario = $pdo->lastInsertId(); // Obtener el ID del usuario recién creado

      // Insertar el paciente en la base de datos
      $stmt = $pdo->prepare("INSERT INTO pacientes (Nombre, Apellido, DNIPac, EmailPac, CelPac, GenPac, FechaNacimiento, DirPac, ID_Usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
      if ($stmt->execute([$nombre, $apellido, $dni, $email, $celular, $genero, $fecha_nacimiento, $direccion, $id_usuario])) {
        // Confirmar la transacción
        $pdo->commit();
        echo json_encode(["status" => "success", "message" => "Paciente registrado exitosamente."]);
        exit();
      } else {
        // Revertir la transacción en caso de error
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Error al registrar el paciente: " . $stmt->errorInfo()[2]]);
        exit();
      }
    }
  } catch (Exception $e) {
    // Revertir la transacción en caso de excepción
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => "Excepción capturada: " . $e->getMessage()]);
    exit();
  }
} else {
  echo json_encode(["status" => "error", "message" => "Método no permitido"]);
  exit();
}
?>