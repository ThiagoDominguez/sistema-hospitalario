<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php");
  exit();
}

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "sghsantarosa");
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

// Procesar formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_turno = $_POST['id_turno'];
  $id_med = $_POST['id_med'];
  $id_pac = $_POST['id_pac'];
  $fecha_turno = $_POST['fecha_turno'];
  $hora_turno = $_POST['hora_turno'];

  $query = "UPDATE Turnos SET ID_Med = ?, ID_Pac = ?, FechaTurno = ?, HoraTurno = ? WHERE ID_Turno = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('iissi', $id_med, $id_pac, $fecha_turno, $hora_turno, $id_turno);

  if ($stmt->execute()) {
    header("Location: dashboard.php?seccion=gestionar_turnos");
    exit();
  } else {
    $error = "Error al procesar el formulario: " . $stmt->error;
  }
}

// Obtener datos del turno si se está editando
$id_turno = $_GET['id'];
$result = $conn->query("SELECT * FROM Turnos WHERE ID_Turno = $id_turno");
$turno = $result->fetch_assoc();

// Obtener listas de médicos y pacientes
$medicos = $conn->query("SELECT ID_Med, NomMed, ApellidoMed FROM PersonalMedico ORDER BY NomMed");
$pacientes = $conn->query("SELECT ID_Pac, Nombre, Apellido FROM Pacientes ORDER BY Nombre");

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/styles.css">
  <title>Editar Turno</title>

</head>

<body>
  <div class="container">
    <h1 class="title">Editar Turno</h1>

    <?php if (isset($error))
      echo "<p style='color: red;'>$error</p>"; ?>

    <form method="POST" action="">
      <input type="hidden" name="id_turno" value="<?php echo $turno['ID_Turno']; ?>">
      <div class="form-group">
        <label for="id_med">Médico</label>
        <select id="id_med" name="id_med" required>
          <?php while ($medico = $medicos->fetch_assoc()): ?>
            <option value="<?php echo $medico['ID_Med']; ?>" <?php if ($turno['ID_Med'] == $medico['ID_Med'])
                 echo 'selected'; ?>>
              <?php echo $medico['NomMed'] . ' ' . $medico['ApellidoMed']; ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="id_pac">Paciente</label>
        <select id="id_pac" name="id_pac" required>
          <?php while ($paciente = $pacientes->fetch_assoc()): ?>
            <option value="<?php echo $paciente['ID_Pac']; ?>" <?php if ($turno['ID_Pac'] == $paciente['ID_Pac'])
                 echo 'selected'; ?>>
              <?php echo $paciente['Nombre'] . ' ' . $paciente['Apellido']; ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="fecha_turno">Fecha del Turno</label>
        <input type="date" id="fecha_turno" name="fecha_turno" value="<?php echo $turno['FechaTurno']; ?>" required>
      </div>
      <div class="form-group">
        <label for="hora_turno">Hora del Turno</label>
        <input type="time" id="hora_turno" name="hora_turno" value="<?php echo $turno['HoraTurno']; ?>" required>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <button type="submit" class="btn blue">Guardar Cambios</button>

        </div>
        <div class="input-group">
          <a href="dashboard.php?seccion=gestionar_turnos" class="btn red">Cancelar</a>

        </div>
      </div>
    </form>
  </div>
</body>

</html>