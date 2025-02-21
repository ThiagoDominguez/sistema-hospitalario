<?php
// Iniciar la sesión para acceder a las variables de sesión del usuario.
// session_start() es necesario para trabajar con sesiones en PHP [[1]].
session_start();

// Configuración para mostrar errores en pantalla durante el desarrollo.
// Esto es útil para depurar problemas, pero debe deshabilitarse en producción.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir el archivo de configuración que contiene la conexión a la base de datos.
require '../config.php';

// Verificar si el usuario ha iniciado sesión y si tiene el rol de paciente (rol_id = 1).
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
  // Si no está autenticado o no tiene el rol adecuado, devolver un mensaje de error en formato JSON.
  echo json_encode(["status" => "error", "message" => "Acceso no autorizado."]);
  exit();
}

// Obtener el ID del usuario desde la sesión.
$usuario_id = $_SESSION['usuario_id'];

// Verificar que el usuario esté asociado a un paciente en la base de datos.
$query_verificar_paciente = "SELECT ID_Pac FROM pacientes WHERE ID_Usuario = ?";
$stmt_verificar_paciente = $pdo->prepare($query_verificar_paciente);
$stmt_verificar_paciente->execute([$usuario_id]);
$paciente = $stmt_verificar_paciente->fetch(PDO::FETCH_ASSOC);

if (!$paciente) {
  // Si no se encuentra un paciente asociado, devolver un mensaje de error.
  echo json_encode(["status" => "error", "message" => "El ID del paciente no existe."]);
  exit();
}

// Obtener el ID del paciente.
$id_pac = $paciente['ID_Pac'];

// Procesar el formulario si se ha enviado una solicitud POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Obtener los datos enviados en la solicitud POST.
  $id_medico = $_POST['id_medico'];
  $fecha = $_POST['fecha'];
  $hora = $_POST['hora'];

  // Validar que todos los campos obligatorios estén presentes.
  if (empty($id_medico) || empty($fecha) || empty($hora)) {
    echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios."]);
    exit();
  }

  try {
    // Verificar si ya existe un turno activo (no cancelado) para el mismo médico, fecha y hora.
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM turnos 
                               WHERE ID_Med = ? 
                               AND FechaTurno = ? 
                               AND HoraTurno = ? 
                               AND (Estado IS NULL OR Estado != 'Cancelado')");
    $stmt->execute([$id_medico, $fecha, $hora]);
    $turnos_existentes = $stmt->fetchColumn();

    if ($turnos_existentes > 0) {
      // Si ya existe un turno activo, devolver un mensaje de error.
      echo json_encode(["status" => "error", "message" => "Ya existe un turno activo para este médico en la misma fecha y hora."]);
      exit();
    }

    // Verificar si el médico ya tiene 10 turnos activos en el mismo día.
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM turnos 
                               WHERE ID_Med = ? 
                               AND FechaTurno = ? 
                               AND (Estado IS NULL OR Estado != 'Cancelado')");
    $stmt->execute([$id_medico, $fecha]);
    $turnos_dia = $stmt->fetchColumn();

    if ($turnos_dia >= 10) {
      // Si el médico ya tiene 10 turnos activos, devolver un mensaje de error.
      echo json_encode(["status" => "error", "message" => "El médico ya tiene 10 turnos activos para este día."]);
      exit();
    }

    // Insertar el nuevo turno en la base de datos.
    $query = "INSERT INTO turnos (ID_Pac, ID_Med, FechaTurno, HoraTurno, Estado) VALUES (?, ?, ?, ?, 'Pendiente')";
    $stmt = $pdo->prepare($query);
    if ($stmt->execute([$id_pac, $id_medico, $fecha, $hora])) {
      // Si la inserción es exitosa, devolver un mensaje de éxito.
      echo json_encode(["status" => "success", "message" => "Turno solicitado exitosamente."]);
    } else {
      // Si ocurre un error en la inserción, devolver un mensaje de error con detalles.
      echo json_encode(["status" => "error", "message" => "Error al solicitar el turno: " . $stmt->errorInfo()[2]]);
    }
  } catch (Exception $e) {
    // Capturar y manejar cualquier excepción que ocurra durante el proceso.
    echo json_encode(["status" => "error", "message" => "Error al procesar la solicitud: " . $e->getMessage()]);
  }
} else {
  // Si el método de la solicitud no es POST, devolver un mensaje de error.
  echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
