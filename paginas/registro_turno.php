<?php
// Iniciar la sesión para acceder a las variables de sesión del usuario.
// session_start() es necesario para trabajar con sesiones en PHP [[1]].
session_start();

// Verificar si el usuario ha iniciado sesión.
// Si no está autenticado, redirigir al usuario a la página de inicio de sesión.
if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php");
  exit();
}

// Incluir el archivo de configuración que contiene la conexión a la base de datos.
require_once '../config.php';

// Establecer el encabezado de la respuesta como JSON.
// Esto indica que el contenido devuelto será en formato JSON.
header('Content-Type: application/json');

// Verificar si la solicitud es de tipo POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Obtener los datos enviados en la solicitud POST.
  $id_medico = $_POST['ID_Med'];
  $id_paciente = $_POST['ID_Pac'];
  $fecha_turno = $_POST['FechaTurno'];
  $hora_turno = $_POST['HoraTurno'];

  // Validar que todos los campos obligatorios estén presentes.
  if (empty($id_medico) || empty($id_paciente) || empty($fecha_turno) || empty($hora_turno)) {
    echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios."]);
    exit();
  }

  try {
    // Verificar si ya existe un turno activo para el mismo médico en la misma fecha y hora.
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM turnos 
                              WHERE ID_Med = ? 
                              AND FechaTurno = ? 
                              AND HoraTurno = ? 
                              AND Estado != 'Cancelado'");
    $stmt->execute([$id_medico, $fecha_turno, $hora_turno]);
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
                              AND Estado != 'Cancelado'");
    $stmt->execute([$id_medico, $fecha_turno]);
    $turnos_dia = $stmt->fetchColumn();

    if ($turnos_dia >= 10) {
      // Si el médico ya tiene 10 turnos activos, devolver un mensaje de error.
      echo json_encode(["status" => "error", "message" => "El médico ya tiene 10 turnos activos para este día."]);
      exit();
    }

    // Insertar el nuevo turno en la base de datos.
    $stmt = $pdo->prepare("INSERT INTO turnos (ID_Med, ID_Pac, FechaTurno, HoraTurno, Estado) VALUES (?, ?, ?, ?, 'Pendiente')");
    if ($stmt->execute([$id_medico, $id_paciente, $fecha_turno, $hora_turno])) {
      // Si la inserción es exitosa, devolver un mensaje de éxito.
      echo json_encode(["status" => "success", "message" => "Turno registrado exitosamente."]);
    } else {
      // Si ocurre un error en la inserción, devolver un mensaje de error con detalles.
      echo json_encode(["status" => "error", "message" => "Error al registrar el turno: " . $stmt->errorInfo()[2]]);
    }
  } catch (Exception $e) {
    // Capturar y manejar cualquier excepción que ocurra durante el proceso.
    echo json_encode(["status" => "error", "message" => "Excepción capturada: " . $e->getMessage()]);
  }
} else {
  // Si la solicitud no es POST, obtener listas de especialidades, médicos y pacientes.
  try {
    // Obtener la lista de especialidades únicas de los médicos.
    $especialidades = $pdo->query("SELECT DISTINCT EspecialidadMed FROM personalmedico ORDER BY EspecialidadMed")->fetchAll(PDO::FETCH_ASSOC);

    // Obtener la lista de médicos con sus nombres, apellidos y especialidades.
    $medicos = $pdo->query("SELECT ID_Med, NomMed, ApellidoMed, EspecialidadMed FROM personalmedico ORDER BY NomMed")->fetchAll(PDO::FETCH_ASSOC);

    // Obtener la lista de pacientes con sus nombres y apellidos.
    $pacientes = $pdo->query("SELECT ID_Pac, Nombre, Apellido FROM pacientes ORDER BY Nombre")->fetchAll(PDO::FETCH_ASSOC);

    // Devolver las listas en formato JSON.
    echo json_encode(['status' => 'success', 'especialidades' => $especialidades, 'medicos' => $medicos, 'pacientes' => $pacientes]);
  } catch (Exception $e) {
    // Capturar y manejar cualquier excepción que ocurra durante el proceso.
    echo json_encode(['status' => 'error', 'message' => 'Error al obtener los datos de especialidades, médicos y pacientes: ' . $e->getMessage()]);
  }
}
?>