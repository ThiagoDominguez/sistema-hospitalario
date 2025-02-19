<?php
require_once '../config.php';

header('Content-Type: application/json');

session_start();
$id_usuario = $_SESSION['usuario_id'];

$query = "SELECT id, mensaje FROM notificaciones WHERE id_usuario = ? AND leido = 0 ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$id_usuario]);
$notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($notificaciones);
?>