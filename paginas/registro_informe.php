<?php
session_start();
require '../config.php';

// Verificar si el usuario ha iniciado sesión y tiene el rol de médico (rol_id = 2).
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 2) {
  echo json_encode(["status" => "error", "message" => "Acceso no autorizado."]);
  exit();
}

// Obtener el ID del médico desde la tabla personalmedico.
$usuario_id = $_SESSION['usuario_id'];
$query_obtener_medico = "SELECT ID_Med FROM personalmedico WHERE ID_Usuario = ?";
$stmt_obtener_medico = $pdo->prepare($query_obtener_medico);
$stmt_obtener_medico->execute([$usuario_id]);
$medico = $stmt_obtener_medico->fetch(PDO::FETCH_ASSOC);

if (!$medico) {
  echo json_encode(["status" => "error", "message" => "El ID del médico no existe en la tabla personalmedico."]);
  exit();
}

$id_medico = $medico['ID_Med'];

// Procesar el formulario si se ha enviado una solicitud POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Depuración: Verificar los datos recibidos.
  error_log("Datos recibidos: " . print_r($_POST, true));
  error_log("Archivo recibido: " . print_r($_FILES, true));

  // Validar datos obligatorios.
  if (empty($_POST['id_paciente']) || empty($_POST['fecha']) || empty($_POST['descripcion'])) {
    echo json_encode(["status" => "error", "message" => "Todos los campos obligatorios deben ser completados."]);
    exit();
  }

  $id_paciente = $_POST['id_paciente'];
  $fecha = $_POST['fecha'];
  $descripcion = $_POST['descripcion'];
  $archivo = $_FILES['archivo_pdf'];

  // Verificar si se ha subido un archivo.
  if ($archivo && $archivo['error'] === UPLOAD_ERR_OK) {
    // Validar que el archivo sea un PDF.
    $file_extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    if (strtolower($file_extension) !== 'pdf') {
      echo json_encode(["status" => "error", "message" => "El archivo debe ser un PDF."]);
      exit();
    }

    // Mover el archivo al directorio de destino.
    $upload_dir = '../uploads/';
    if (!is_dir($upload_dir)) {
      mkdir($upload_dir, 0777, true);
    }

    $nombre_archivo = uniqid() . '_' . basename($archivo['name']);
    $ruta_archivo = $upload_dir . $nombre_archivo;

    if (move_uploaded_file($archivo['tmp_name'], $ruta_archivo)) {
      // Insertar el informe con archivo.
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
    // Insertar el informe sin archivo.
    $query = "INSERT INTO historialmedico (ID_Med, ID_Pac, Fecha, Descripcion) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    if ($stmt->execute([$id_medico, $id_paciente, $fecha, $descripcion])) {
      echo json_encode(["status" => "success", "message" => "Informe registrado correctamente sin archivo."]);
    } else {
      echo json_encode(["status" => "error", "message" => "Error al registrar el informe en la base de datos."]);
    }
  }
} else {
  echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
?>