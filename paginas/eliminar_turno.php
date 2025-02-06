<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php");
  exit();
}

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "sghsantarosa");
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

// Capturar ID del turno a eliminar
$id_turno = $_GET['id'];

// Eliminar el turno
$query = "DELETE FROM Turnos WHERE ID_Turno = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id_turno);

if ($stmt->execute()) {
  header("Location: dashboard.php?seccion=gestionar_turnos");
  exit();
} else {
  echo "Error al eliminar el turno: " . $stmt->error;
}
?>