<?php
require_once '../config.php';


header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
  $id_pac = intval($_GET['id']);
  $stmt = $pdo->prepare("SELECT * FROM pacientes WHERE ID_Pac = ?");
  $stmt->execute([$id_pac]);
  $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($paciente) {
    echo json_encode(['status' => 'success', 'paciente' => $paciente]);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Paciente no encontrado']);
  }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
  $id_pac = intval($_POST['id']);
  $nombre = $_POST['nombre'];
  $apellido = $_POST['apellido'];
  $direccion = $_POST['direccion'];
  $celular = $_POST['celular'];
  $email = $_POST['email'];
  $dni = $_POST['dni'];
  $genero = $_POST['genero'];
  $fecha_nacimiento = $_POST['fecha_nacimiento'];

  $query = "UPDATE pacientes SET 
                Nombre = ?, Apellido = ?, DirPac = ?, CelPac = ?, 
                EmailPac = ?, GenPac = ?, DNIPac = ?, FechaNacimiento = ?
              WHERE ID_Pac = ?";
  $stmt = $pdo->prepare($query);
  if ($stmt->execute([$nombre, $apellido, $direccion, $celular, $email, $genero, $dni, $fecha_nacimiento, $id_pac])) {
    echo json_encode(["status" => "success"]);
  } else {
    echo json_encode(["status" => "error", "message" => $stmt->errorInfo()[2]]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Método no permitido o datos incompletos"]);
}
?>