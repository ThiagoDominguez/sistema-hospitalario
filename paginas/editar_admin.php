<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
  $id_admin = intval($_GET['id']);
  $stmt = $pdo->prepare("SELECT * FROM personaladministrativo WHERE ID_Admin = ?");
  $stmt->execute([$id_admin]);
  $admin = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($admin) {
    echo json_encode(['status' => 'success', 'admin' => $admin]);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Administrador no encontrado']);
  }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
  $id_admin = intval($_POST['id']);
  $nombre = $_POST['Nombre'];
  $apellido = $_POST['Apellido'];
  $email = $_POST['EmailAdmin'];
  $celular = $_POST['CelAdmin'];
  $direccion = $_POST['DirAdmin'];

  // Validar campos obligatorios
  if (empty($nombre) || empty($apellido) || empty($email)) {
    echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios."]);
    exit();
  }

  try {
    $stmt = $pdo->prepare("UPDATE personaladministrativo SET NomAdmin = ?, ApellidoAdmin = ?, EmailAdmin = ?, CelAdmin = ?, DirAdmin = ? WHERE ID_Admin = ?");
    if ($stmt->execute([$nombre, $apellido, $email, $celular, $direccion, $id_admin])) {
      echo json_encode(["status" => "success", "message" => "Administrador actualizado exitosamente."]);
    } else {
      echo json_encode(["status" => "error", "message" => "Error al actualizar el administrador: " . $stmt->errorInfo()[2]]);
    }
  } catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Excepción capturada: " . $e->getMessage()]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}
?>