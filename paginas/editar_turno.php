<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php");
  exit();
}

require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
  $id_turno = intval($_GET['id']);
  try {
    $stmt = $pdo->prepare("SELECT * FROM turnos WHERE ID_Turno = ?");
    $stmt->execute([$id_turno]);
    $turno = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($turno) {
      $especialidades = $pdo->query("SELECT DISTINCT EspecialidadMed FROM personalmedico ORDER BY EspecialidadMed")->fetchAll(PDO::FETCH_ASSOC);
      $medicos = $pdo->query("SELECT ID_Med, NomMed, ApellidoMed, EspecialidadMed FROM personalmedico ORDER BY NomMed")->fetchAll(PDO::FETCH_ASSOC);
      $pacientes = $pdo->query("SELECT ID_Pac, Nombre, Apellido FROM pacientes ORDER BY Nombre")->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode(['status' => 'success', 'turno' => $turno, 'especialidades' => $especialidades, 'medicos' => $medicos, 'pacientes' => $pacientes]);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Turno no encontrado']);
    }
  } catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error al obtener los datos del turno: ' . $e->getMessage()]);
  }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
  $id_turno = intval($_POST['id']);
  $id_medico = $_POST['ID_Med'];
  $id_paciente = $_POST['ID_Pac'];
  $fecha_turno = $_POST['FechaTurno'];
  $hora_turno = $_POST['HoraTurno'];

  // Validar campos obligatorios
  if (empty($id_medico) || empty($id_paciente) || empty($fecha_turno) || empty($hora_turno)) {
    echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios."]);
    exit();
  }

  try {
    // Verificar si ya existe un turno para el mismo médico en la misma fecha y hora exacta
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM turnos WHERE ID_Med = ? AND FechaTurno = ? AND HoraTurno = ? AND ID_Turno != ?");
    $stmt->execute([$id_medico, $fecha_turno, $hora_turno, $id_turno]);
    $turnos_existentes = $stmt->fetchColumn();

    if ($turnos_existentes > 0) {
      echo json_encode(["status" => "error", "message" => "Ya existe un turno registrado para este médico en la misma fecha y hora."]);
      exit();
    }

    // Verificar si ya existe un turno para el mismo médico en la misma fecha y dentro de la misma hora
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM turnos WHERE ID_Med = ? AND FechaTurno = ? AND ABS(TIMESTAMPDIFF(MINUTE, HoraTurno, ?)) < 60 AND ID_Turno != ?");
    $stmt->execute([$id_medico, $fecha_turno, $hora_turno, $id_turno]);
    $turnos_existentes = $stmt->fetchColumn();

    if ($turnos_existentes > 0) {
      echo json_encode(["status" => "error", "message" => "Ya existe un turno registrado para este médico en la misma fecha y dentro de la misma hora."]);
      exit();
    }

    // Verificar si el médico ya tiene 10 turnos en el mismo día
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM turnos WHERE ID_Med = ? AND FechaTurno = ?");
    $stmt->execute([$id_medico, $fecha_turno]);
    $turnos_dia = $stmt->fetchColumn();

    if ($turnos_dia >= 10) {
      echo json_encode(["status" => "error", "message" => "El médico ya tiene 10 turnos registrados para este día."]);
      exit();
    }

    // Actualizar el turno
    $stmt = $pdo->prepare("UPDATE turnos SET ID_Med = ?, ID_Pac = ?, FechaTurno = ?, HoraTurno = ? WHERE ID_Turno = ?");
    if ($stmt->execute([$id_medico, $id_paciente, $fecha_turno, $hora_turno, $id_turno])) {
      echo json_encode(["status" => "success", "message" => "Turno actualizado exitosamente."]);
    } else {
      echo json_encode(["status" => "error", "message" => "Error al actualizar el turno: " . $stmt->errorInfo()[2]]);
    }
  } catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Excepción capturada: " . $e->getMessage()]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}
?>