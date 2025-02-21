<?php
// Configuración para mostrar errores en pantalla durante el desarrollo.
// ini_set permite modificar configuraciones de PHP en tiempo de ejecución.
// display_errors muestra errores en pantalla, mientras que error_reporting define el nivel de errores a mostrar.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Se incluye el archivo de configuración que contiene la conexión a la base de datos y otras configuraciones necesarias.
require_once '../config.php';

// Mensaje para indicar el inicio del script.
echo "Inicio del script<br>";

// Mostrar todos los parámetros GET recibidos para depuración.
echo "Parámetros GET recibidos:<br>";
print_r($_GET);
echo "<br>";

// Verificar si el parámetro 'id' está presente en la URL.
if (isset($_GET['id'])) {
  echo "Parámetro id recibido<br>";

  // Convertir el parámetro 'id' a un entero para mayor seguridad.
  $id_admin = intval($_GET['id']);
  echo "ID del administrador a eliminar: $id_admin<br>";

  // Verificar que el ID sea válido (mayor a 0).
  if ($id_admin > 0) {
    try {
      // Crear una nueva conexión PDO a la base de datos.
      // Se especifican el host, nombre de la base de datos, usuario y contraseña.
      $pdo = new PDO("mysql:host=sql102.infinityfree.com;dbname=if0_38254567_sgh", "if0_38254567", "6q9gejZs8Roq");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Configurar PDO para lanzar excepciones en caso de error.

      // Consulta SQL para eliminar un administrador por su ID.
      $sql = "DELETE FROM personaladministrativo WHERE ID_Admin = ?";
      $stmt = $pdo->prepare($sql); // Preparar la consulta para evitar inyecciones SQL.

      // Ejecutar la consulta con el ID del administrador como parámetro.
      if ($stmt->execute([$id_admin])) {
        echo "Administrador eliminado con éxito.<br>";

        // Redirigir al usuario a la página de gestión de administradores después de eliminar.
        header("Location: dashboard.php?seccion=gestionar_admin");
        exit(); // Detener la ejecución del script después de la redirección.
      } else {
        // Mostrar un mensaje de error si la eliminación falla.
        echo "Error al eliminar: " . $stmt->errorInfo()[2] . "<br>";
      }
    } catch (PDOException $e) {
      // Capturar y mostrar cualquier error relacionado con la conexión a la base de datos.
      die("Error en la conexión a la base de datos: " . $e->getMessage());
    }
  } else {
    // Mostrar un mensaje si el ID proporcionado no es válido.
    echo "ID de administrador inválido<br>";
  }
} else {
  // Mostrar un mensaje si no se recibió el parámetro 'id'.
  echo "No se recibió el parámetro id<br>";
}
?>