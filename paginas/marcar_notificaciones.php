<?php
require_once '../config.php';

header('Content-Type: application/json');

session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'Usuario no autenticado']);
  exit;
}

$id_usuario = $_SESSION['usuario_id'];

// Obtener datos enviados por AJAX
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
  $data = $_POST;  // Intenta obtener los datos desde POST si json_decode falla
}

file_put_contents("log.txt", "Datos recibidos: " . print_r($data, true) . "\n", FILE_APPEND);


file_put_contents("log.txt", "Datos recibidos: " . print_r($data, true) . "\n", FILE_APPEND); // Registrar datos recibidos

if (!empty($data['id'])) {
  // Imprimir el ID de la notificación para depuración
  file_put_contents("log.txt", "ID recibido: " . $data['id'] . " | Usuario ID: " . $id_usuario . "\n", FILE_APPEND);

  $query = "UPDATE notificaciones SET leido = 1 WHERE id = ? AND id_usuario = ?";
  $stmt = $pdo->prepare($query);
  if ($stmt->execute([$data['id'], $id_usuario])) {
    echo json_encode(['status' => 'success']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar']);
  }
} else {
  echo json_encode(['status' => 'error', 'message' => 'ID de notificación no recibido']);
}
?>