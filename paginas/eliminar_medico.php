<?php
// Incluir el archivo de configuración que contiene la conexión a la base de datos.
// require_once asegura que el archivo solo se incluya una vez, evitando errores de inclusión múltiple.
require_once '../config.php';

// Establecer el encabezado de la respuesta como JSON para que el cliente sepa que el contenido devuelto es en formato JSON.
header('Content-Type: application/json');

// Verificar si el método de la solicitud es POST y si se ha proporcionado el parámetro 'id'.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
  // Convertir el parámetro 'id' a un entero para mayor seguridad.
  $id_med = intval($_POST['id']);

  // Iniciar una transacción para asegurar que las operaciones sean atómicas.
  $pdo->beginTransaction();

  try {
    // Obtener el ID del usuario vinculado al médico desde la tabla personalmedico.
    $stmt = $pdo->prepare("SELECT ID_Usuario FROM personalmedico WHERE ID_Med = ?");
    $stmt->execute([$id_med]);
    $id_usuario = $stmt->fetchColumn();

    if ($id_usuario) {
      // Si se encuentra el usuario vinculado, proceder a eliminar al médico.
      $stmt = $pdo->prepare("DELETE FROM personalmedico WHERE ID_Med = ?");
      $stmt->execute([$id_med]);

      // Eliminar también al usuario vinculado desde la tabla usuarios.
      $stmt = $pdo->prepare("DELETE FROM usuarios WHERE ID_Usuario = ?");
      $stmt->execute([$id_usuario]);

      // Confirmar la transacción si ambas eliminaciones son exitosas.
      $pdo->commit();

      // Devolver una respuesta de éxito en formato JSON.
      echo json_encode(["status" => "success"]);
    } else {
      // Si no se encuentra el usuario vinculado, revertir la transacción.
      $pdo->rollBack();
      echo json_encode(["status" => "error", "message" => "Usuario vinculado no encontrado"]);
    }
  } catch (Exception $e) {
    // Revertir la transacción en caso de error o excepción.
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
  }
} else {
  // Si el método de la solicitud no es POST o no se proporciona el parámetro 'id', devolver un mensaje de error.
  echo json_encode(["status" => "error", "message" => "Método no permitido o datos incompletos"]);
}
?>