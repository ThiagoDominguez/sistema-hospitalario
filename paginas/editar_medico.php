<?php
// Incluir el archivo de configuración que contiene la conexión a la base de datos.
// require_once asegura que el archivo solo se incluya una vez, evitando errores de inclusión múltiple.
require_once '../config.php';

// Establecer el encabezado de la respuesta como JSON para que el cliente sepa que el contenido devuelto es en formato JSON.
header('Content-Type: application/json');

// Manejar solicitudes GET para obtener información de un médico específico.
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
  // Convertir el parámetro 'id' a un entero para mayor seguridad.
  $id_med = intval($_GET['id']);

  // Preparar la consulta para obtener los datos del médico.
  $stmt = $pdo->prepare("SELECT * FROM personalmedico WHERE ID_Med = ?");
  $stmt->execute([$id_med]);
  $medico = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($medico) {
    // Si se encuentra el médico, devolver los datos en formato JSON.
    echo json_encode(['status' => 'success', 'medico' => $medico]);
  } else {
    // Si no se encuentra el médico, devolver un mensaje de error.
    echo json_encode(['status' => 'error', 'message' => 'Médico no encontrado']);
  }
}
// Manejar solicitudes POST para actualizar la información de un médico.
elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
  // Obtener los datos enviados en la solicitud POST.
  $id_med = intval($_POST['id']);
  $nombre = $_POST['nombre'];
  $apellido = $_POST['apellido'];
  $direccion = $_POST['direccion'];
  $celular = $_POST['celular'];
  $email = $_POST['email'];
  $genero = $_POST['genero']; // Asegúrate de que el valor sea "M" o "F".
  $especialidad = $_POST['especialidad'];

  // Preparar la consulta para actualizar los datos del médico.
  $query = "UPDATE personalmedico SET 
                NomMed = ?, ApellidoMed = ?, DirMed = ?, CelMed = ?, 
                EmailMed = ?, GenMed = ?, EspecialidadMed = ?
              WHERE ID_Med = ?";
  $stmt = $pdo->prepare($query);

  // Ejecutar la consulta con los datos proporcionados.
  if ($stmt->execute([$nombre, $apellido, $direccion, $celular, $email, $genero, $especialidad, $id_med])) {
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