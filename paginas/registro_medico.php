<?php
// Iniciar la sesión para trabajar con variables de sesión.
// session_start() es necesario para gestionar sesiones en PHP [[1]].
session_start();

// Incluir el archivo de configuración que contiene la conexión a la base de datos.
// Esto asegura que podamos interactuar con la base de datos.
include("../config.php");

// Establecer el encabezado de la respuesta como JSON para que el cliente sepa que el contenido devuelto es en formato JSON.
header('Content-Type: application/json');

// Verificar si el método de la solicitud es POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Obtener los datos enviados en la solicitud POST.
  $nombre = $_POST['Nombre'];
  $apellido = $_POST['Apellido'];
  $email = $_POST['EmailMed'];
  $contraseña = password_hash($_POST['Contraseña'], PASSWORD_BCRYPT); // Hashear la contraseña para mayor seguridad.
  $direccion = $_POST['DirMed'];
  $celular = $_POST['CelMed'];
  $genero = $_POST['GenMed'];
  $especialidad = $_POST['EspecialidadMed'];

  // Validar que los campos obligatorios no estén vacíos.
  if (empty($nombre) || empty($apellido) || empty($email) || empty($contraseña)) {
    echo json_encode(["status" => "error", "message" => "Por favor, completa todos los campos obligatorios."]);
    exit();
  }

  // Verificar si el correo electrónico ya existe en la base de datos.
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE Email = ?");
  $stmt->execute([$email]);
  $email_count = $stmt->fetchColumn();

  if ($email_count > 0) {
    // Si el correo ya está registrado, devolver un mensaje de error.
    echo json_encode(["status" => "error", "message" => "Error: El correo electrónico ya está registrado."]);
    exit();
  }

  try {
    // Iniciar una transacción para asegurar que las operaciones sean atómicas.
    $pdo->beginTransaction();

    // Insertar el nuevo usuario en la tabla usuarios.
    $id_rol = 2; // Rol de médico (según la tabla Roles).
    $stmt = $pdo->prepare("INSERT INTO usuarios (Email, Contraseña, ID_Rol) VALUES (?, ?, ?)");
    $stmt->execute([$email, $contraseña, $id_rol]);
    $id_usuario = $pdo->lastInsertId(); // Obtener el ID del usuario recién creado.

    // Insertar los datos del médico en la tabla personalmedico.
    $query = "INSERT INTO personalmedico (NomMed, ApellidoMed, EmailMed, DirMed, CelMed, GenMed, EspecialidadMed, ID_Usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$nombre, $apellido, $email, $direccion, $celular, $genero, $especialidad, $id_usuario]);

    // Confirmar la transacción si todo se ejecuta correctamente.
    $pdo->commit();

    // Devolver una respuesta de éxito en formato JSON.
    echo json_encode(["status" => "success"]);
  } catch (Exception $e) {
    // Revertir la transacción en caso de error.
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
  }
} else {
  // Si el método de la solicitud no es POST, devolver un mensaje de error.
  echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}
?>