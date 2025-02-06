<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

echo "Inicio del script<br>";

// Mostrar todos los parámetros GET
echo "Parámetros GET recibidos:<br>";
print_r($_GET);
echo "<br>";

// Verificar si el parámetro id está presente en la URL
if (isset($_GET['id'])) {
    echo "Parámetro id recibido<br>";
    $id_admin = intval($_GET['id']); // Asegúrate de que sea un entero
    echo "ID del administrador a eliminar: $id_admin<br>";

    if ($id_admin > 0) {
        try {
            $pdo = new PDO("mysql:host=sql102.infinityfree.com;dbname=if0_38254567_sgh", "if0_38254567", "6q9gejZs8Roq");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "DELETE FROM personaladministrativo WHERE ID_Admin = ?";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute([$id_admin])) {
                echo "Administrador eliminado con éxito.<br>";
                header("Location: dashboard.php?seccion=gestionar_admin");
                exit(); // Asegúrate de detener la ejecución del script
            } else {
                echo "Error al eliminar: " . $stmt->errorInfo()[2] . "<br>";
            }
        } catch (PDOException $e) {
            die("Error en la conexión a la base de datos: " . $e->getMessage());
        }
    } else {
        echo "ID de administrador inválido<br>";
    }
} else {
    echo "No se recibió el parámetro id<br>";
}

?>