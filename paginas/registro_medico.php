<?php
session_start();
include("../config.php");

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nombre = $_POST['Nombre'];
  $apellido = $_POST['Apellido'];
  $email = $_POST['EmailMed'];
  $contraseña = password_hash($_POST['Contraseña'], PASSWORD_BCRYPT);
  $direccion = $_POST['DirMed'];
  $celular = $_POST['CelMed'];
  $genero = $_POST['GenMed'];
  $especialidad = $_POST['EspecialidadMed'];

  // Validar campos obligatorios
  if (empty($nombre) || empty($apellido) || empty($email) || empty($contraseña)) {
    echo json_encode(["status" => "error", "message" => "Por favor, completa todos los campos obligatorios."]);
    exit();
  }

  // Verificar si el correo electrónico ya existe en la base de datos
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE Email = ?");
  $stmt->execute([$email]);
  $email_count = $stmt->fetchColumn();

  if ($email_count > 0) {
    echo json_encode(["status" => "error", "message" => "Error: El correo electrónico ya está registrado."]);
    exit();
  }

  try {
    // Iniciar una transacción
    $pdo->beginTransaction();

    // Insertar el usuario en la tabla Usuarios
    $id_rol = 2; // Rol de médico (según la tabla Roles)
    $stmt = $pdo->prepare("INSERT INTO usuarios (Email, Contraseña, ID_Rol) VALUES (?, ?, ?)");
    $stmt->execute([$email, $contraseña, $id_rol]);
    $id_usuario = $pdo->lastInsertId(); // Obtener el ID del usuario recién creado

    // Insertar el médico en la tabla PersonalMedico
    $query = "INSERT INTO personalmedico (NomMed, ApellidoMed, EmailMed, DirMed, CelMed, GenMed, EspecialidadMed, ID_Usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$nombre, $apellido, $email, $direccion, $celular, $genero, $especialidad, $id_usuario]);

    // Confirmar la transacción
    $pdo->commit();

    echo json_encode(["status" => "success"]);
  } catch (Exception $e) {
    // Revertir la transacción en caso de error
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}
?>