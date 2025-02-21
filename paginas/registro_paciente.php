<?php
// Incluir el archivo de configuración que contiene la conexión a la base de datos.
// require_once asegura que el archivo solo se incluya una vez, evitando errores de inclusión múltiple.
require_once '../config.php';

// Establecer el encabezado de la respuesta como JSON para que el cliente sepa que el contenido devuelto es en formato JSON.
header('Content-Type: application/json');

// Verificar si el método de la solicitud es POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Obtener los datos enviados en la solicitud POST.
  $nombre = $_POST['Nombre'];
  $apellido = $_POST['Apellido'];
  $dni = $_POST['DNIPac'];
  $email = $_POST['EmailPac'];
  $celular = $_POST['CelPac'];
  $genero = $_POST['GenPac'];
  $fecha_nacimiento = $_POST['FechaNacimiento'];
  $direccion = $_POST['DirPac'];
  $contraseña = $_POST['Contraseña']; // Recibir la contraseña proporcionada por el usuario.

  // Validar que los campos obligatorios no estén vacíos.
  if (empty($nombre) || empty($apellido) || empty($dni) || empty($email) || empty($contraseña)) {
    echo json_encode(["status" => "error", "message" => "Por favor, completa todos los campos obligatorios."]);
    exit();
  }

  try {
    // Iniciar una transacción para asegurar que las operaciones sean atómicas.
    $pdo->beginTransaction();

    // Verificar si el correo electrónico ya existe en la tabla usuarios.
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE Email = ?");
    $stmt->execute([$email]);
    $email_count = $stmt->fetchColumn();

    // Verificar si el DNI ya existe en la tabla pacientes.
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pacientes WHERE DNIPac = ?");
    $stmt->execute([$dni]);
    $dni_count = $stmt->fetchColumn();

    // Si el correo electrónico ya está registrado, devolver un mensaje de error.
    if ($email_count > 0) {
      echo json_encode(["status" => "error", "message" => "Error: El correo electrónico ya está registrado."]);
      $pdo->rollBack(); // Revertir la transacción.
      exit();
    } elseif ($dni_count > 0) {
      // Si el DNI ya está registrado, devolver un mensaje de error.
      echo json_encode(["status" => "error", "message" => "Error: El DNI ya está registrado."]);
      $pdo->rollBack(); // Revertir la transacción.
      exit();
    } else {
      // Generar un hash seguro de la contraseña proporcionada por el usuario.
      $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

      // Insertar el nuevo usuario en la tabla usuarios.
      $id_rol = 1; // Rol de paciente (según la tabla Roles).
      $stmt = $pdo->prepare("INSERT INTO usuarios (Email, Contraseña, ID_Rol) VALUES (?, ?, ?)");
      $stmt->execute([$email, $contraseña_hash, $id_rol]);
      $id_usuario = $pdo->lastInsertId(); // Obtener el ID del usuario recién creado.

      // Insertar los datos del paciente en la tabla pacientes.
      $stmt = $pdo->prepare("INSERT INTO pacientes (Nombre, Apellido, DNIPac, EmailPac, CelPac, GenPac, FechaNacimiento, DirPac, ID_Usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
      if ($stmt->execute([$nombre, $apellido, $dni, $email, $celular, $genero, $fecha_nacimiento, $direccion, $id_usuario])) {
        // Confirmar la transacción si todo se ejecuta correctamente.
        $pdo->commit();
        echo json_encode(["status" => "success", "message" => "Paciente registrado exitosamente."]);
        exit();
      } else {
        // Revertir la transacción si ocurre un error al insertar el paciente.
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Error al registrar el paciente: " . $stmt->errorInfo()[2]]);
        exit();
      }
    }
  } catch (Exception $e) {
    // Revertir la transacción en caso de que ocurra una excepción.
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => "Excepción capturada: " . $e->getMessage()]);
    exit();
  }
} else {
  // Si el método de la solicitud no es POST, devolver un mensaje de error.
  echo json_encode(["status" => "error", "message" => "Método no permitido"]);
  exit();
}
?>