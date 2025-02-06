<?php
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "sghsantarosa";

// $conn = new mysqli($servername, $username, $password, $dbname);

// // Verificar la conexión
// if ($conn->connect_error) {
//   die("Error de conexión: " . $conn->connect_error);
// }



$host = 'sql102.infinityfree.com';  
$db   = 'if0_38254567_sgh';
$user = 'if0_38254567';
$pass = '6q9gejZs8Roq';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}


?>