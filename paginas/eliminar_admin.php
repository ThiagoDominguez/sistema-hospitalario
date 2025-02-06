<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "config.php";

echo "Inicio del script<br>";

// Eliminar Administrador
if (isset($_GET['eliminar'])) {
  echo "Parámetro eliminar recibido<br>";
  $id_admin = intval($_GET['eliminar']); // Asegúrate de que sea un entero
  echo "ID del administrador a eliminar: $id_admin<br>";

  $sql = "DELETE FROM PersonalAdministrativo WHERE ID_Admin=?";
  $stmt = $conn->prepare($sql);

  if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
  }

  $stmt->bind_param("i", $id_admin);
  if ($stmt->execute()) {
    echo "Administrador eliminado con éxito.<br>";
    $stmt->close();
    $conn->close();
    header("Location: dashboard.php?seccion=gestionar_admin");
    exit(); // Asegúrate de detener la ejecución del script
  } else {
    echo "Error al eliminar: " . $stmt->error . "<br>";
  }
  $stmt->close();
} else {
  echo "No se recibió el parámetro eliminar<br>";
}

$conn->close();
?>