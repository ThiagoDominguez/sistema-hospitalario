<?php
session_start();
require '../config.php';

// Verificar si el usuario ha iniciado sesión y es un médico (rol 2)
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 2) {
  header("Location: login.php");
  exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener el ID del médico desde la tabla personalmedico
$query_obtener_medico = "SELECT ID_Med FROM personalmedico WHERE ID_Usuario = ?";
$stmt_obtener_medico = $pdo->prepare($query_obtener_medico);
$stmt_obtener_medico->execute([$usuario_id]);
$medico = $stmt_obtener_medico->fetch(PDO::FETCH_ASSOC);

if (!$medico) {
  die("El ID del médico no existe en la tabla personalmedico.");
}

$id_medico = $medico['ID_Med'];

// Procesar el formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_paciente = $_POST['id_paciente'];
  $fecha = $_POST['fecha'];
  $descripcion = $_POST['descripcion'];
  $archivo = $_FILES['archivo_pdf'];

  // Verificar si se ha subido un archivo y si es un PDF
  if ($archivo && $archivo['type'] == 'application/pdf') {
    // Asegurarse de que el directorio de destino exista
    $upload_dir = '../uploads/';
    if (!is_dir($upload_dir)) {
      mkdir($upload_dir, 0777, true);
    }

    // Generar un nombre único para el archivo
    $nombre_archivo = uniqid() . '_' . basename($archivo['name']);
    $ruta_archivo = $upload_dir . $nombre_archivo;

    // Mover el archivo al directorio de destino
    if (move_uploaded_file($archivo['tmp_name'], $ruta_archivo)) {
      // Insertar el nuevo informe en la base de datos con la ruta del archivo
      $query = "INSERT INTO historialmedico (ID_Med, ID_Pac, Fecha, Descripcion, archivo_pdf) VALUES (?, ?, ?, ?, ?)";
      $stmt = $pdo->prepare($query);
      if ($stmt->execute([$id_medico, $id_paciente, $fecha, $descripcion, $ruta_archivo])) {
        echo json_encode(["status" => "success", "message" => "Informe registrado correctamente."]);
      } else {
        echo json_encode(["status" => "error", "message" => "Error al registrar el informe en la base de datos."]);
      }
    } else {
      echo json_encode(["status" => "error", "message" => "Error al subir el archivo."]);
    }
  } else {
    // Insertar el informe sin archivo
    $query = "INSERT INTO historialmedico (ID_Med, ID_Pac, Fecha, Descripcion) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    if ($stmt->execute([$id_medico, $id_paciente, $fecha, $descripcion])) {
      echo json_encode(["status" => "success", "message" => "Informe registrado correctamente sin archivo."]);
    } else {
      echo json_encode(["status" => "error", "message" => "Error al registrar el informe en la base de datos."]);
    }
  }
} else {
  echo json_encode(["status" => "error", "message" => "Método no permitido o datos incompletos."]);
}
?>