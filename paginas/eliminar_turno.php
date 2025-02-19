<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
  $id_turno = intval($_POST['id']);

  try {
    $stmt = $pdo->prepare("DELETE FROM turnos WHERE ID_Turno = ?");
    if ($stmt->execute([$id_turno])) {
      echo json_encode(["status" => "success", "message" => "Turno eliminado exitosamente."]);
    } else {
      echo json_encode(["status" => "error", "message" => "Error al eliminar el turno: " . $stmt->errorInfo()[2]]);
    }
  } catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Excepción capturada: " . $e->getMessage()]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}
?>