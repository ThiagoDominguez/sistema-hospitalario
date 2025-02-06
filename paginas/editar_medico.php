<?php
$conn = new mysqli("localhost", "root", "", "sghsantarosa");

if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del médico
if (isset($_GET['id'])) {
  $id_med = intval($_GET['id']);
  $result = $conn->query("SELECT * FROM personalmedico WHERE ID_Med = $id_med");
  $medico = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nombre = $_POST['nombre'];
  $apellido = $_POST['apellido'];
  $direccion = $_POST['direccion'];
  $celular = $_POST['celular'];
  $email = $_POST['email'];
  $genero = ($_POST['genero'] == "Masculino") ? "M" : "F";
  $especialidad = $_POST['especialidad'];

  $query = "UPDATE personalmedico SET 
              NomMed='$nombre', ApellidoMed='$apellido', DirMed='$direccion', CelMed='$celular', 
              EmailMed='$email', GenMed='$genero', EspecialidadMed='$especialidad'
              WHERE ID_Med=$id_med";

  if ($conn->query($query)) {
    header("Location: dashboard.php?seccion=gestionar_medicos");
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
  <title>Editar Médico</title>
</head>

<body>
  <div class="container_both-inp">
    <h2 class="title">Editar Médico</h2>
    <form method="POST" action="" class="form_both-inp">
      <!-- Mostrar mensajes de éxito o error -->
      <div class="message">
        <?php if (isset($success))
          echo "<p class='success'>$success</p>"; ?>
        <?php if (isset($error))
          echo "<p class='error'>$error</p>"; ?>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <label for="nombre">Nombre</label>
          <input type="text" id="nombre" name="nombre" value="<?php echo $medico['NomMed']; ?>" required>
        </div>
        <div class="input-group">
          <label for="apellido">Apellido</label>
          <input type="text" id="apellido" name="apellido" value="<?php echo $medico['ApellidoMed']; ?>" required>
        </div>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <label for="direccion">Dirección</label>
          <input type="text" id="direccion" name="direccion" value="<?php echo $medico['DirMed']; ?>" required>
        </div>
        <div class="input-group">
          <label for="celular">Celular</label>
          <input type="text" id="celular" name="celular" value="<?php echo $medico['CelMed']; ?>" required>
        </div>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <label for="email">Correo Electrónico</label>
          <input type="email" id="email" name="email" value="<?php echo $medico['EmailMed']; ?>" required>
        </div>
        <div class="input-group">
          <label for="genero">Género</label>
          <select id="genero" name="genero">
            <option value="M" <?= $medico['GenMed'] == 'M' ? 'selected' : '' ?>>Masculino</option>
            <option value="F" <?= $medico['GenMed'] == 'F' ? 'selected' : '' ?>>Femenino</option>
          </select>
        </div>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <label for="especialidad">Especialidad</label>
          <input type="text" id="especialidad" name="especialidad" value="<?php echo $medico['EspecialidadMed']; ?>"
            required>
        </div>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <button type="submit" class="btn blue">Guardar Cambios</button>

        </div>
        <div class="input-group">
          <a href="dashboard.php?seccion=gestionar_medicos" class="btn red">Cancelar</a>

        </div>
      </div>
    </form>
  </div>
</body>

</html>