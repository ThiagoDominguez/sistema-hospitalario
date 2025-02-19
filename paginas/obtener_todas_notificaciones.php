<?php
require_once '../config.php';

header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['usuario_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'Usuario no autenticado']);
  exit;
}

$id_usuario = $_SESSION['usuario_id'];

$query = "SELECT id, mensaje, leido FROM notificaciones WHERE id_usuario = ? ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$id_usuario]);
$notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($notificaciones);
?>
