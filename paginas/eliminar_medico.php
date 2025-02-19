<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
  $id_med = intval($_POST['id']);

  // Iniciar una transacción
  $pdo->beginTransaction();

  try {
    // Obtener el ID del usuario vinculado al médico
    $stmt = $pdo->prepare("SELECT ID_Usuario FROM personalmedico WHERE ID_Med = ?");
    $stmt->execute([$id_med]);
    $id_usuario = $stmt->fetchColumn();

    if ($id_usuario) {
      // Eliminar al médico
      $stmt = $pdo->prepare("DELETE FROM personalmedico WHERE ID_Med = ?");
      $stmt->execute([$id_med]);

      // Eliminar al usuario vinculado
      $stmt = $pdo->prepare("DELETE FROM usuarios WHERE ID_Usuario = ?");
      $stmt->execute([$id_usuario]);

      // Confirmar la transacción
      $pdo->commit();

      echo json_encode(["status" => "success"]);
    } else {
      // Revertir la transacción si no se encuentra el usuario vinculado
      $pdo->rollBack();
      echo json_encode(["status" => "error", "message" => "Usuario vinculado no encontrado"]);
    }
  } catch (Exception $e) {
    // Revertir la transacción en caso de error
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Método no permitido o datos incompletos"]);
}
?>