<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
  $id_med = intval($_GET['id']);
  $stmt = $pdo->prepare("SELECT * FROM personalmedico WHERE ID_Med = ?");
  $stmt->execute([$id_med]);
  $medico = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($medico) {
    echo json_encode(['status' => 'success', 'medico' => $medico]);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Médico no encontrado']);
  }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
  $id_med = intval($_POST['id']);
  $nombre = $_POST['nombre'];
  $apellido = $_POST['apellido'];
  $direccion = $_POST['direccion'];
  $celular = $_POST['celular'];
  $email = $_POST['email'];
  $genero = $_POST['genero']; // Asegúrate de que el valor sea "M" o "F"
  $especialidad = $_POST['especialidad'];

  $query = "UPDATE personalmedico SET 
                NomMed = ?, ApellidoMed = ?, DirMed = ?, CelMed = ?, 
                EmailMed = ?, GenMed = ?, EspecialidadMed = ?
              WHERE ID_Med = ?";
  $stmt = $pdo->prepare($query);
  if ($stmt->execute([$nombre, $apellido, $direccion, $celular, $email, $genero, $especialidad, $id_med])) {
    echo json_encode(["status" => "success"]);
  } else {
    echo json_encode(["status" => "error", "message" => $stmt->errorInfo()[2]]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Método no permitido o datos incompletos"]);
}
?>