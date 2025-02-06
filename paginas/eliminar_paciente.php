<?php
$conn = new mysqli("localhost", "root", "", "sghsantarosa");

if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
  $id_pac = intval($_GET['id']);

  // Iniciar la transacción
  $conn->begin_transaction();

  try {
    // Obtener el ID de usuario del paciente
    $query_usuario_id = "SELECT ID_Usuario FROM Pacientes WHERE ID_Pac = ?";
    $stmt_usuario_id = $conn->prepare($query_usuario_id);
    $stmt_usuario_id->bind_param('i', $id_pac);
    $stmt_usuario_id->execute();
    $result_usuario_id = $stmt_usuario_id->get_result();

    if ($result_usuario_id->num_rows > 0) {
      $row = $result_usuario_id->fetch_assoc();
      $id_usuario = $row['ID_Usuario'];

      // Eliminar el paciente de la tabla Pacientes
      $query_paciente = "DELETE FROM Pacientes WHERE ID_Pac = ?";
      $stmt_paciente = $conn->prepare($query_paciente);
      $stmt_paciente->bind_param('i', $id_pac);
      $stmt_paciente->execute();

      // Eliminar las credenciales del paciente de la tabla Usuarios
      $query_usuario = "DELETE FROM Usuarios WHERE ID_Usuario = ?";
      $stmt_usuario = $conn->prepare($query_usuario);
      $stmt_usuario->bind_param('i', $id_usuario);
      $stmt_usuario->execute();

      // Confirmar la transacción
      $conn->commit();

      header("Location: dashboard.php?seccion=gestionar_pacientes");
      exit();
    } else {
      throw new Exception("Paciente no encontrado.");
    }
  } catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    echo "Error al eliminar: " . $e->getMessage();
  }
}

$conn->close();
?>