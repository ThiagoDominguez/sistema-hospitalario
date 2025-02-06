<?php
$conn = new mysqli("localhost", "root", "", "sghsantarosa");

if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del personal administrativo
if (isset($_GET['id'])) {
  $id_admin = intval($_GET['id']);
  $result = $conn->query("SELECT * FROM PersonalAdministrativo WHERE ID_Admin = $id_admin");
  $admin = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nombre = $_POST['nombre'];
  $apellido = $_POST['apellido'];
  $direccion = $_POST['direccion'];
  $celular = $_POST['celular'];
  $email = $_POST['email'];

  $query = "UPDATE PersonalAdministrativo SET 
              NomAdmin='$nombre', ApellidoAdmin='$apellido', DirAdmin='$direccion', CelAdmin='$celular', 
              EmailAdmin='$email'
              WHERE ID_Admin=$id_admin";

  if ($conn->query($query)) {
    header("Location: dashboard.php?seccion=gestionar_admin");
    exit();
  } else {
    echo "Error al actualizar: " . $conn->error;
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/styles.css">
  <title>Editar Personal Administrativo</title>
</head>

<body>
  <div class="container_both-inp">

    <h2 class="title">Editar Personal Administrativo</h2>
    <form method="POST" class="form_both-inp">
      <div class="both-inp">
        <div class="input-group">
          <label>Nombre</label>
          <input type="text" name="nombre" value="<?php echo $admin['NomAdmin']; ?>" required><br>

        </div>
        <div class="input-group">
          <label>Apellido</label>
          <input type="text" name="apellido" value="<?php echo $admin['ApellidoAdmin']; ?>" required><br>

        </div>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <label>Dirección</label>
          <input type="text" name="direccion" value="<?php echo $admin['DirAdmin']; ?>" required><br>

        </div>
        <div class="input-group">
          <label>Celular</label>
          <input type="text" name="celular" value="<?php echo $admin['CelAdmin']; ?>" required><br>

        </div>
      </div>



      <label>Email</label>
      <input type="email" name="email" value="<?php echo $admin['EmailAdmin']; ?>" required><br>
      <div class="both-inp">
        <div class="input-group">
          <button type="submit" class="btn blue">Guardar Cambios</button>

        </div>
        <div class="input-group">
          <a href="dashboard.php?seccion=gestionar_admin" class="btn red">Cancelar</a>

        </div>
      </div>
    </form>
  </div>
</body>

</html>