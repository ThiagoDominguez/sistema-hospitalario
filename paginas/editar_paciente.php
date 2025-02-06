<?php
include "config.php";

// Recuperar los datos del paciente desde la base de datos
if (isset($_GET['id'])) {
  $id_paciente = $_GET['id'];
  $query = "SELECT * FROM Pacientes WHERE ID_Pac = $id_paciente";
  $result = $conn->query($query);
  $paciente = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nombre = $_POST['nombre'];
  $apellido = $_POST['apellido'];
  $direccion = $_POST['direccion'];
  $celular = $_POST['celular'];
  $email = $_POST['email'];
  $genero = $_POST['genero'];
  $dni = $_POST['dni'];
  $fecha_nacimiento = $_POST['fecha_nacimiento'];

  // Actualizar los datos del paciente
  $query = "UPDATE Pacientes SET Nombre='$nombre', Apellido='$apellido', DirPac='$direccion', 
              CelPac='$celular', EmailPac='$email', GenPac='$genero', DNIPac='$dni', FechaNacimiento='$fecha_nacimiento' 
              WHERE ID_Pac = $id_paciente";
  if ($conn->query($query) === TRUE) {
    header("Location: dashboard.php?seccion=gestionar_pacientes");
    exit();
  } else {
    echo "Error: " . $conn->error;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/styles.css">
  <title>Editar paciente</title>
</head>

<body>
  <div class="container_both-inp">
    <h2 class="title">Editar paciente</h2>
    <form action="editar_paciente.php?id=<?php echo $paciente['ID_Pac']; ?>" method="POST" class="form_both-inp">
      <div class="both-inp">
        <div class="input-group">
          <label for="nombre">Nombre</label>
          <input type="text" id="nombre" name="nombre" value="<?php echo $paciente['Nombre']; ?>" required>
        </div>
        <div class="input-group">
          <label for="apellido">Apellido</label>
          <input type="text" id="apellido" name="apellido" value="<?php echo $paciente['Apellido']; ?>" required>
        </div>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <label for="direccion">Dirección</label>
          <input type="text" id="direccion" name="direccion" value="<?php echo $paciente['DirPac']; ?>">
        </div>
        <div class="input-group">
          <label for="celular">Celular</label>
          <input type="text" id="celular" name="celular" value="<?php echo $paciente['CelPac']; ?>">
        </div>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <label for="email">Correo Electrónico</label>
          <input type="email" id="email" name="email" value="<?php echo $paciente['EmailPac']; ?>">
        </div>
        <div class="input-group">
          <label for="genero">Género</label>
          <select id="genero" name="genero">
            <option value="M" <?php echo $paciente['GenPac'] == 'M' ? 'selected' : ''; ?>>Masculino</option>
            <option value="F" <?php echo $paciente['GenPac'] == 'F' ? 'selected' : ''; ?>>Femenino</option>
          </select>
        </div>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <label for="dni">DNI</label>
          <input type="text" id="dni" name="dni" value="<?php echo $paciente['DNIPac']; ?>" required>
        </div>
        <div class="input-group">
          <label for="fecha_nacimiento">Fecha de Nacimiento</label>
          <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
            value="<?php echo $paciente['FechaNacimiento']; ?>" required>
        </div>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <button type="submit" class="btn blue">Guardar Cambios</button>

        </div>
        <div class="input-group">
          <a href="dashboard.php?seccion=gestionar_pacientes" class="btn red">Cancelar</a>

        </div>
      </div>
    </form>
  </div>

</body>

</html>