<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../config.php';

// Verificar si el usuario ha iniciado sesión y es un paciente (rol 1)
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
  echo json_encode(["status" => "error", "message" => "Acceso no autorizado."]);
  exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Verificar que el ID_Pac existe y obtenerlo basado en el ID_Usuario
$query_verificar_paciente = "SELECT ID_Pac FROM pacientes WHERE ID_Usuario = ?";
$stmt_verificar_paciente = $pdo->prepare($query_verificar_paciente);
$stmt_verificar_paciente->execute([$usuario_id]);
$paciente = $stmt_verificar_paciente->fetch(PDO::FETCH_ASSOC);

if (!$paciente) {
  echo json_encode(["status" => "error", "message" => "El ID del paciente no existe."]);
  exit();
}

$id_pac = $paciente['ID_Pac'];

// Procesar el formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_medico = $_POST['id_medico'];
  $fecha = $_POST['fecha'];
  $hora = $_POST['hora'];

  // Validar campos obligatorios
  if (empty($id_medico) || empty($fecha) || empty($hora)) {
    echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios."]);
    exit();
  }

  try {
    // Verificar si ya existe un turno activo (no cancelado) para el mismo médico en la misma fecha y hora
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM turnos 
                              WHERE ID_Med = ? 
                              AND FechaTurno = ? 
                              AND HoraTurno = ? 
                              AND (Estado IS NULL OR Estado != 'Cancelado')");
    $stmt->execute([$id_medico, $fecha, $hora]);
    $turnos_existentes = $stmt->fetchColumn();

    if ($turnos_existentes > 0) {
      echo json_encode(["status" => "error", "message" => "Ya existe un turno activo para este médico en la misma fecha y hora."]);
      exit();
    }

    // Verificar si el médico ya tiene 10 turnos activos en el mismo día
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM turnos 
                              WHERE ID_Med = ? 
                              AND FechaTurno = ? 
                              AND (Estado IS NULL OR Estado != 'Cancelado')");
    $stmt->execute([$id_medico, $fecha]);
    $turnos_dia = $stmt->fetchColumn();

    if ($turnos_dia >= 10) {
      echo json_encode(["status" => "error", "message" => "El médico ya tiene 10 turnos activos para este día."]);
      exit();
    }

    // Insertar el nuevo turno
    $query = "INSERT INTO turnos (ID_Pac, ID_Med, FechaTurno, HoraTurno, Estado) VALUES (?, ?, ?, ?, 'Pendiente')";
    $stmt = $pdo->prepare($query);
    if ($stmt->execute([$id_pac, $id_medico, $fecha, $hora])) {
      echo json_encode(["status" => "success", "message" => "Turno solicitado exitosamente."]);
    } else {
      echo json_encode(["status" => "error", "message" => "Error al solicitar el turno: " . $stmt->errorInfo()[2]]);
    }
  } catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error al procesar la solicitud: " . $e->getMessage()]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
?>