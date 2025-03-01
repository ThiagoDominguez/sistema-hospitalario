<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config.php';

header('Content-Type: application/json'); // Establece el tipo de contenido como JSON

// Verificar si el parámetro id está presente en la solicitud POST
if (isset($_POST['id'])) {
  $id_admin = intval($_POST['id']); // Asegúrate de que sea un entero

  if ($id_admin > 0) {
    try {
      // Eliminar el administrador de la base de datos
      $sql = "DELETE FROM personaladministrativo WHERE ID_Admin = ?";
      $stmt = $pdo->prepare($sql);

      if ($stmt->execute([$id_admin])) {
        echo json_encode(['status' => 'success', 'message' => 'Administrador eliminado con éxito.']);
        exit();
      } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar: ' . $stmt->errorInfo()[2]]);
        exit();
      }
    } catch (PDOException $e) {
      echo json_encode(['status' => 'error', 'message' => 'Error en la conexión a la base de datos: ' . $e->getMessage()]);
      exit();
    }
  } else {
    echo json_encode(['status' => 'error', 'message' => 'ID de administrador inválido.']);
    exit();
  }
} else {
  echo json_encode(['status' => 'error', 'message' => 'No se recibió el parámetro id.']);
  exit();
}
?>