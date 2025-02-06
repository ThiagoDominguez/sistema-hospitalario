<?php
session_start();
require 'config.php';

// Verificar si el usuario ha iniciado sesión y es un paciente (rol 1)
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
  header("Location: login.php");
  exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Verificar que el ID_Pac existe y obtenerlo basado en el ID_Usuario
$query_verificar_paciente = "SELECT ID_Pac FROM pacientes WHERE ID_Usuario = ?";
$stmt_verificar_paciente = $conn->prepare($query_verificar_paciente);
$stmt_verificar_paciente->bind_param('i', $usuario_id);
$stmt_verificar_paciente->execute();
$result_verificar_paciente = $stmt_verificar_paciente->get_result();

if ($result_verificar_paciente->num_rows > 0) {
  $paciente = $result_verificar_paciente->fetch_assoc();
  $id_pac = $paciente['ID_Pac'];

  // Obtener la lista de todos los médicos
  $query_medicos = "SELECT ID_Med, NomMed, ApellidoMed FROM personalmedico ORDER BY NomMed, ApellidoMed";
  $result_medicos = $conn->query($query_medicos);

  // Procesar el formulario si se ha enviado
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_medico = $_POST['id_medico'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];

    // Insertar el nuevo turno en la base de datos
    $query = "INSERT INTO Turnos (ID_Pac, ID_Med, FechaTurno, HoraTurno) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iiss', $id_pac, $id_medico, $fecha, $hora);

    if ($stmt->execute()) {
      $message = "Turno solicitado exitosamente.";
      header("location:dashboard.php?seccion=turnos");
    } else {
      $error = "Error al solicitar el turno: " . $stmt->error;
    }
  }
} else {
  $error = "El ID del paciente no existe.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/styles.css">
  <title>Solicitar Turno</title>
</head>

<body>
  <div class="container">
    <h2 class="title">Solicitar Turno</h2>
    <?php if (isset($message))
      echo "<p style='color: green;'>$message</p>"; ?>
    <?php if (isset($error))
      echo "<p style='color: red;'>$error</p>"; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label for="id_medico">Seleccionar Médico:</label>
        <select id="id_medico" name="id_medico" required>
          <?php if ($result_medicos->num_rows > 0): ?>
            <?php while ($medico = $result_medicos->fetch_assoc()): ?>
              <option value="<?php echo $medico['ID_Med']; ?>">
                <?php echo $medico['NomMed'] . ' ' . $medico['ApellidoMed']; ?>
              </option>
            <?php endwhile; ?>
          <?php else: ?>
            <option value="">No hay médicos disponibles</option>
          <?php endif; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="fecha">Fecha del Turno:</label>
        <input type="date" id="fecha" name="fecha" required>
      </div>
      <div class="form-group">
        <label for="hora">Hora del Turno:</label>
        <input type="time" id="hora" name="hora" required>
      </div>

      <div class="both-inp">
        <div class="input-group">
          <button type="submit" class="btn blue">Guardar Cambios</button>

        </div>
        <div class="input-group">
          <a href="dashboard.php?seccion=turnos" class="btn red">Cancelar</a>

        </div>
      </div>
    </form>
  </div>
</body>

</html>

<?php
$conn->close();
?>