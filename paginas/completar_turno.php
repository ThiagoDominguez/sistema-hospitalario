<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
  $id_turno = intval($_POST['id']);
  $query = "UPDATE turnos SET estado = 'completado' WHERE ID_Turno = ?";
  $stmt = $pdo->prepare($query);
  if ($stmt->execute([$id_turno])) {
    // Registrar el historial de atención
    $query_historial = "INSERT INTO historialmedico (ID_Turno, ID_Pac, ID_Med, Fecha, Descripcion) 
                        SELECT t.ID_Turno, t.ID_Pac, t.ID_Med, t.FechaTurno, 'Atención completada'
                        FROM turnos t
                        WHERE t.ID_Turno = ?";
    $stmt_historial = $pdo->prepare($query_historial);
    $stmt_historial->execute([$id_turno]);

    echo json_encode(["status" => "success"]);
  } else {
    echo json_encode(["status" => "error", "message" => $stmt->errorInfo()[2]]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Método no permitido o datos incompletos"]);
}
?>