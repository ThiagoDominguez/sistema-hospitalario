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

/*La línea `file_put_contents (" log.txt "," datos recibidos: ". Print_r (, true)." \ N ",
File_append); `está escribiendo los datos recibidos en un archivo de registro llamado" Log.txt ". */
file_put_contents("log.txt", "Datos recibidos: " . print_r($data, true) . "\n", FILE_APPEND);

if (!empty($data['id'])) {
  // Imprimir el ID de la notificación para depuración
  file_put_contents("log.txt", "ID recibido: " . $data['id'] . " | Usuario ID: " . $id_usuario . "\n", FILE_APPEND);

  /* La línea `=" actualizar las notificaciones SET leido = 1 WHERE id =? AND id_usuario =? ";`Es
  Preparación de una consulta SQL para actualizar un registro en la tabla "Notificaciones" en la base de datos.*/
  $query = "UPDATE notificaciones SET leido = 1 WHERE id = ? AND id_usuario = ?";
  /* La línea `= -> prepare ();` está preparando una declaración SQL para la ejecución. En este
  Caso, está preparando una declaración de actualización de SQL para actualizar un registro en la tabla "Notificaciones" en
  la base de datos. La declaración preparada permite la ejecución de la consulta con marcadores de posición para
  Parámetros que estarán atados más tarde. Esto ayuda a prevenir los ataques de inyección de SQL y mejora
  rendimiento al ejecutar la misma consulta varias veces con diferentes parámetros. */
  $stmt = $pdo->prepare($query);
  /* El bloque de código `if (->execute([['id'], ])) { echo json_encode(['status' =>
  'success']); } else { echo json_encode(['status' => 'error', 'message' => 'Error al actualizar']);
  }` es responsable de ejecutar la instrucción SQL preparada para actualizar un registro en el
  Tabla de "notificaciones" en la base de datos basada en los datos recibidos. */
  if ($stmt->execute([$data['id'], $id_usuario])) {
    echo json_encode(['status' => 'success']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar']);
  }
} else {
  echo json_encode(['status' => 'error', 'message' => 'ID de notificación no recibido']);
}
?>