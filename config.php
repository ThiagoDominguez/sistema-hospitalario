<?php
// Cargar variables de entorno desde un archivo .env
require_once 'vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Obtener las credenciales de la base de datos desde las variables de entorno
$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$dbname = $_ENV['DB_NAME'];

try {
  $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];

  $pdo = new PDO($dsn, $username, $password, $options);
  // echo "Conexión exitosa";  // Opcional: para verificar conexión
} catch (PDOException $e) {
  die("Error de conexión: " . $e->getMessage());
}
?>