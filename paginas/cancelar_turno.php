<?php
require_once '../config.php';

header('Content-Type: application/json');

session_start();

try {
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id_turno = intval($_POST['id']);
    $query = "UPDATE turnos SET estado = 'cancelado' WHERE ID_Turno = ?";
    $stmt = $pdo->prepare($query);
    if ($stmt->execute([$id_turno])) {
      // Obtener información del turno y del paciente
      $query_info = "
                SELECT t.ID_Turno, t.FechaTurno, t.HoraTurno, p.ID_Usuario, p.Nombre, p.Apellido
                FROM turnos t
                JOIN pacientes p ON t.ID_Pac = p.ID_Pac
                WHERE t.ID_Turno = ?
            ";
      $stmt_info = $pdo->prepare($query_info);
      $stmt_info->execute([$id_turno]);
      $turno_info = $stmt_info->fetch(PDO::FETCH_ASSOC);

      if ($turno_info) {
        $mensaje = "Estimado/a " . htmlspecialchars($turno_info['Nombre']) . " " . htmlspecialchars($turno_info['Apellido']) . ", su turno programado para el " . date('d/m/Y', strtotime($turno_info['FechaTurno'])) . " a las " . $turno_info['HoraTurno'] . " ha sido cancelado.";

        // Insertar notificación en la base de datos
        $insertQuery = "INSERT INTO notificaciones (id_usuario, mensaje) VALUES (?, ?)";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->execute([$turno_info['ID_Usuario'], $mensaje]);

        echo json_encode(["status" => "success"]);
      } else {
        echo json_encode(["status" => "error", "message" => "Información del turno no encontrada"]);
      }
    } else {
      echo json_encode(["status" => "error", "message" => $stmt->errorInfo()[2]]);
    }
  } else {
    echo json_encode(["status" => "error", "message" => "Método no permitido o datos incompletos"]);
  }
} catch (Exception $e) {
  echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>