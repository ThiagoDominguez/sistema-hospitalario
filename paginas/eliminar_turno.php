<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';


// Capturar ID del turno a eliminar
$id_turno = $_GET['id'];

// Eliminar el turno
$query = "DELETE FROM turnos WHERE ID_Turno = ?";
$stmt = $pdo->prepare($query);

if ($stmt->execute([$id_turno])) {
    header("Location: dashboard.php?seccion=gestionar_turnos");
    exit();
} else {
    echo "Error al eliminar el turno: " . $stmt->errorInfo()[2];
}
?>