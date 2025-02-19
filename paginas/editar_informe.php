<?php
session_start();
require '../config.php';

header('Content-Type: application/json');

// Verificar si el usuario ha iniciado sesión y es un médico (rol 2)
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 2) {
  echo json_encode(["status" => "error", "message" => "Acceso no autorizado."]);
  exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener el ID del médico desde la tabla personalmedico
$query_obtener_medico = "SELECT ID_Med FROM personalmedico WHERE ID_Usuario = ?";
$stmt_obtener_medico = $pdo->prepare($query_obtener_medico);
$stmt_obtener_medico->execute([$usuario_id]);
$medico = $stmt_obtener_medico->fetch(PDO::FETCH_ASSOC);

if (!$medico) {
  echo json_encode(["status" => "error", "message" => "El ID del médico no existe en la tabla personalmedico."]);
  exit();
}

$id_medico = $medico['ID_Med'];

// Procesar el formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_historial'])) {
  $id_historial = intval($_POST['id_historial']);
  $id_paciente = $_POST['id_paciente'];
  $fecha = $_POST['fecha'];
  $descripcion = $_POST['descripcion'];
  $archivo = $_FILES['archivo_pdf'];

  // Iniciar una transacción
  $pdo->beginTransaction();

  try {
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
        // Actualizar el informe con la nueva ruta del archivo
        $query = "UPDATE historialmedico SET ID_Pac = ?, Fecha = ?, Descripcion = ?, archivo_pdf = ? WHERE ID_Historial = ?";
        $stmt = $pdo->prepare($query);
        if ($stmt->execute([$id_paciente, $fecha, $descripcion, $ruta_archivo, $id_historial])) {
          $pdo->commit();
          echo json_encode(["status" => "success", "message" => "Informe actualizado correctamente."]);
        } else {
          throw new Exception("Error al actualizar el informe en la base de datos.");
        }
      } else {
        throw new Exception("Error al subir el archivo.");
      }
    } else {
      // Actualizar el informe sin cambiar el archivo
      $query = "UPDATE historialmedico SET ID_Pac = ?, Fecha = ?, Descripcion = ? WHERE ID_Historial = ?";
      $stmt = $pdo->prepare($query);
      if ($stmt->execute([$id_paciente, $fecha, $descripcion, $id_historial])) {
        $pdo->commit();
        echo json_encode(["status" => "success", "message" => "Informe actualizado correctamente sin cambiar el archivo."]);
      } else {
        throw new Exception("Error al actualizar el informe en la base de datos.");
      }
    }
  } catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
  }
} else {
  // Obtener datos del informe para edición
  if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id_historial'])) {
    $id_historial = intval($_GET['id_historial']);
    $stmt = $pdo->prepare("SELECT * FROM historialmedico WHERE ID_Historial = ?");
    $stmt->execute([$id_historial]);
    $informe = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($informe) {
      echo json_encode(['status' => 'success', 'informe' => $informe]);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Informe no encontrado']);
    }
  } else {
    echo json_encode(["status" => "error", "message" => "Método no permitido o datos incompletos."]);
  }
}
?>