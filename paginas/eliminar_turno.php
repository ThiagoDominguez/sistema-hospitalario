<?php
// Incluir el archivo de configuración que contiene la conexión a la base de datos.
// require_once asegura que el archivo solo se incluya una vez, evitando errores de inclusión múltiple.
require_once '../config.php';

// Establecer el encabezado de la respuesta como JSON para que el cliente sepa que el contenido devuelto es en formato JSON.
header('Content-Type: application/json');

// Verificar si el método de la solicitud es POST y si se ha proporcionado el parámetro 'id'.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
  // Convertir el parámetro 'id' a un entero para mayor seguridad.
  $id_turno = intval($_POST['id']);

  try {
    // Preparar la consulta SQL para eliminar el turno con el ID proporcionado.
    $stmt = $pdo->prepare("DELETE FROM turnos WHERE ID_Turno = ?");

    // Ejecutar la consulta con el ID del turno como parámetro.
    if ($stmt->execute([$id_turno])) {
      // Si la eliminación es exitosa, devolver una respuesta de éxito en formato JSON.
      echo json_encode(["status" => "success", "message" => "Turno eliminado exitosamente."]);
    } else {
      // Si ocurre un error al ejecutar la consulta, devolver un mensaje de error.
      echo json_encode(["status" => "error", "message" => "Error al eliminar el turno: " . $stmt->errorInfo()[2]]);
    }
  } catch (Exception $e) {
    // Capturar y manejar cualquier excepción que ocurra durante el proceso.
    echo json_encode(["status" => "error", "message" => "Excepción capturada: " . $e->getMessage()]);
  }
} else {
  // Si el método de la solicitud no es POST o no se proporciona el parámetro 'id', devolver un mensaje de error.
  echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}
?>