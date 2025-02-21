<?php
// Iniciar la sesión para trabajar con variables de sesión.
// session_start() es necesario para gestionar sesiones en PHP [[1]].
session_start();

// Incluir el archivo de configuración que contiene la conexión a la base de datos.
require '../config.php';

// Verificar si el usuario ha iniciado sesión y tiene el rol de médico (rol_id = 2).
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 2) {
  // Si no está autenticado o no tiene el rol adecuado, redirigir al usuario a la página de inicio de sesión.
  header("Location: login.php");
  exit();
}

// Obtener el ID del usuario desde la sesión.
$usuario_id = $_SESSION['usuario_id'];

// Obtener el ID del médico desde la tabla personalmedico.
$query_obtener_medico = "SELECT ID_Med FROM personalmedico WHERE ID_Usuario = ?";
$stmt_obtener_medico = $pdo->prepare($query_obtener_medico);
$stmt_obtener_medico->execute([$usuario_id]);
$medico = $stmt_obtener_medico->fetch(PDO::FETCH_ASSOC);

if (!$medico) {
  // Si no se encuentra un médico asociado, detener la ejecución con un mensaje de error.
  die("El ID del médico no existe en la tabla personalmedico.");
}

// Obtener el ID del médico.
$id_medico = $medico['ID_Med'];

// Procesar el formulario si se ha enviado una solicitud POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Obtener los datos enviados en la solicitud POST.
  $id_paciente = $_POST['id_paciente'];
  $fecha = $_POST['fecha'];
  $descripcion = $_POST['descripcion'];
  $archivo = $_FILES['archivo_pdf'];

  // Verificar si se ha subido un archivo y si es un PDF.
  if ($archivo && $archivo['type'] == 'application/pdf') {
    // Asegurarse de que el directorio de destino exista.
    $upload_dir = '../uploads/';
    if (!is_dir($upload_dir)) {
      mkdir($upload_dir, 0777, true); // Crear el directorio con permisos adecuados.
    }

    // Generar un nombre único para el archivo.
    $nombre_archivo = uniqid() . '_' . basename($archivo['name']);
    $ruta_archivo = $upload_dir . $nombre_archivo;

    // Mover el archivo al directorio de destino.
    if (move_uploaded_file($archivo['tmp_name'], $ruta_archivo)) {
      // Insertar el nuevo informe en la base de datos con la ruta del archivo.
      $query = "INSERT INTO historialmedico (ID_Med, ID_Pac, Fecha, Descripcion, archivo_pdf) VALUES (?, ?, ?, ?, ?)";
      $stmt = $pdo->prepare($query);
      if ($stmt->execute([$id_medico, $id_paciente, $fecha, $descripcion, $ruta_archivo])) {
        // Respuesta de éxito si el informe se registra correctamente.
        echo json_encode(["status" => "success", "message" => "Informe registrado correctamente."]);
      } else {
        // Respuesta de error si ocurre un problema al registrar el informe.
        echo json_encode(["status" => "error", "message" => "Error al registrar el informe en la base de datos."]);
      }
    } else {
      // Respuesta de error si ocurre un problema al subir el archivo.
      echo json_encode(["status" => "error", "message" => "Error al subir el archivo."]);
    }
  } else {
    // Insertar el informe sin archivo si no se subió un archivo o no es un PDF.
    $query = "INSERT INTO historialmedico (ID_Med, ID_Pac, Fecha, Descripcion) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    if ($stmt->execute([$id_medico, $id_paciente, $fecha, $descripcion])) {
      // Respuesta de éxito si el informe se registra correctamente sin archivo.
      echo json_encode(["status" => "success", "message" => "Informe registrado correctamente sin archivo."]);
    } else {
      // Respuesta de error si ocurre un problema al registrar el informe.
      echo json_encode(["status" => "error", "message" => "Error al registrar el informe en la base de datos."]);
    }
  }
} else {
  // Respuesta de error si el método de la solicitud no es POST o los datos están incompletos.
  echo json_encode(["status" => "error", "message" => "Método no permitido o datos incompletos."]);
}
?>