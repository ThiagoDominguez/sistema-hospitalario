<?php
// Se incluye el archivo de configuración que contiene la conexión a la base de datos y otras configuraciones necesarias.
require_once '../config.php';

// Se establece el tipo de contenido de la respuesta como JSON.
header('Content-Type: application/json');

// Se inicia la sesión para poder acceder a las variables de sesión.
session_start();

// Se verifica si el usuario está autenticado comprobando si existe 'usuario_id' en la sesión.
if (!isset($_SESSION['usuario_id'])) {
  // Si el usuario no está autenticado, se devuelve un mensaje de error en formato JSON y se termina la ejecución.
  echo json_encode(['status' => 'error', 'message' => 'Usuario no autenticado']);
  exit; // Se sale del script para evitar que se ejecute el resto del código.
}

// Se obtiene el ID del usuario autenticado desde la sesión.
$id_usuario = $_SESSION['usuario_id'];

// Se prepara la consulta SQL para obtener las notificaciones del usuario, ordenadas por fecha de creación de forma descendente.
$query = "SELECT id, mensaje, leido FROM notificaciones WHERE id_usuario = ? ORDER BY created_at DESC";

// Se prepara la declaración SQL para evitar inyecciones SQL.
$stmt = $pdo->prepare($query);

// Se ejecuta la consulta pasando el ID del usuario como parámetro.
$stmt->execute([$id_usuario]);

// Se obtienen todas las notificaciones en formato de arreglo asociativo.
$notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Se devuelve el resultado en formato JSON.
echo json_encode($notificaciones);
?>