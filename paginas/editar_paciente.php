<?php
// Incluir el archivo de configuración que contiene la conexión a la base de datos.
// require_once asegura que el archivo solo se incluya una vez, evitando errores de inclusión múltiple.
require_once '../config.php';

// Establecer el encabezado de la respuesta como JSON para que el cliente sepa que el contenido devuelto es en formato JSON.
header('Content-Type: application/json');

// Manejar solicitudes GET para obtener información de un paciente específico.
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
  // Convertir el parámetro 'id' a un entero para mayor seguridad.
  $id_pac = intval($_GET['id']);

  // Preparar la consulta para obtener los datos del paciente.
  $stmt = $pdo->prepare("SELECT * FROM pacientes WHERE ID_Pac = ?");
  $stmt->execute([$id_pac]);
  $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($paciente) {
    // Si se encuentra el paciente, devolver los datos en formato JSON.
    echo json_encode(['status' => 'success', 'paciente' => $paciente]);
  } else {
    // Si no se encuentra el paciente, devolver un mensaje de error.
    echo json_encode(['status' => 'error', 'message' => 'Paciente no encontrado']);
  }
}
// Manejar solicitudes POST para actualizar la información de un paciente.
elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
  // Obtener los datos enviados en la solicitud POST.
  $id_pac = intval($_POST['id']);
  $nombre = $_POST['nombre'];
  $apellido = $_POST['apellido'];
  $direccion = $_POST['direccion'];
  $celular = $_POST['celular'];
  $email = $_POST['email'];
  $dni = $_POST['dni'];
  $genero = $_POST['genero'];
  $fecha_nacimiento = $_POST['fecha_nacimiento'];

  // Preparar la consulta para actualizar los datos del paciente.
  $query = "UPDATE pacientes SET 
                Nombre = ?, Apellido = ?, DirPac = ?, CelPac = ?, 
                EmailPac = ?, GenPac = ?, DNIPac = ?, FechaNacimiento = ?
              WHERE ID_Pac = ?";
  $stmt = $pdo->prepare($query);

  // Ejecutar la consulta con los datos proporcionados.
  if ($stmt->execute([$nombre, $apellido, $direccion, $celular, $email, $genero, $dni, $fecha_nacimiento, $id_pac])) {
    // Si la actualización es exitosa, devolver una respuesta de éxito.
    echo json_encode(["status" => "success"]);
  } else {
    // Si ocurre un error al ejecutar la consulta, devolver un mensaje de error.
    echo json_encode(["status" => "error", "message" => $stmt->errorInfo()[2]]);
  }
} else {
  // Si el método de la solicitud no es válido, devolver un mensaje de error.
  echo json_encode(["status" => "error", "message" => "Método no permitido o datos incompletos"]);
}
?>