<?php
// Se incluye el archivo de configuración que contiene la conexión a la base de datos y otras configuraciones necesarias.
// require_once asegura que el archivo solo se incluya una vez, evitando errores de inclusión múltiple.
require_once '../config.php';

// Se establece el encabezado de la respuesta HTTP como JSON.
// Esto indica al cliente que el contenido devuelto será en formato JSON.
header('Content-Type: application/json');

// Verificamos si el método de la solicitud es GET y si se ha proporcionado el parámetro 'id'.
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
  // Convertimos el parámetro 'id' a un entero para mayor seguridad.
  $id_admin = intval($_GET['id']);

  // Preparamos una consulta SQL para obtener los datos del administrador con el ID proporcionado.
  $stmt = $pdo->prepare("SELECT * FROM personaladministrativo WHERE ID_Admin = ?");
  $stmt->execute([$id_admin]); // Ejecutamos la consulta con el ID como parámetro.
  $admin = $stmt->fetch(PDO::FETCH_ASSOC); // Obtenemos el resultado como un arreglo asociativo.

  // Si se encuentra un administrador con el ID proporcionado, devolvemos los datos en formato JSON.
  if ($admin) {
    echo json_encode(['status' => 'success', 'admin' => $admin]);
  } else {
    // Si no se encuentra, devolvemos un mensaje de error.
    echo json_encode(['status' => 'error', 'message' => 'Administrador no encontrado']);
  }
}
// Verificamos si el método de la solicitud es POST y si se ha proporcionado el parámetro 'id'.
elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
  // Obtenemos los datos enviados en la solicitud POST.
  $id_admin = intval($_POST['id']); // Convertimos el ID a un entero.
  $nombre = $_POST['Nombre']; // Nombre del administrador.
  $apellido = $_POST['Apellido']; // Apellido del administrador.
  $email = $_POST['EmailAdmin']; // Email del administrador.
  $celular = $_POST['CelAdmin']; // Número de celular del administrador.
  $direccion = $_POST['DirAdmin']; // Dirección del administrador.

  // Validamos que los campos obligatorios no estén vacíos.
  if (empty($nombre) || empty($apellido) || empty($email)) {
    echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios."]);
    exit(); // Detenemos la ejecución si faltan campos obligatorios.
  }

  try {
    // Preparamos una consulta SQL para actualizar los datos del administrador.
    $stmt = $pdo->prepare("UPDATE personaladministrativo SET NomAdmin = ?, ApellidoAdmin = ?, EmailAdmin = ?, CelAdmin = ?, DirAdmin = ? WHERE ID_Admin = ?");

    // Ejecutamos la consulta con los datos proporcionados.
    if ($stmt->execute([$nombre, $apellido, $email, $celular, $direccion, $id_admin])) {
      // Si la actualización es exitosa, devolvemos un mensaje de éxito.
      echo json_encode(["status" => "success", "message" => "Administrador actualizado exitosamente."]);
    } else {
      // Si ocurre un error en la ejecución, devolvemos un mensaje de error con información adicional.
      echo json_encode(["status" => "error", "message" => "Error al actualizar el administrador: " . $stmt->errorInfo()[2]]);
    }
  } catch (Exception $e) {
    // Capturamos cualquier excepción y devolvemos un mensaje de error con los detalles.
    echo json_encode(["status" => "error", "message" => "Excepción capturada: " . $e->getMessage()]);
  }
} else {
  // Si el método de la solicitud no es GET ni POST, devolvemos un mensaje de error indicando que el método no está permitido.
  echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}
?>