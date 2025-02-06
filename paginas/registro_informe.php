<?php
session_start();
require 'config.php';

// Verificar si el usuario ha iniciado sesión y es un médico (rol 2)
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 2) {
  header("Location: login.php");
  exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener el ID del médico desde la tabla personalmedico
$query_obtener_medico = "SELECT ID_Med FROM personalmedico WHERE ID_Usuario = ?";
$stmt_obtener_medico = $conn->prepare($query_obtener_medico);
$stmt_obtener_medico->bind_param('i', $usuario_id);
$stmt_obtener_medico->execute();
$result_obtener_medico = $stmt_obtener_medico->get_result();

if ($result_obtener_medico->num_rows == 0) {
  die("El ID del médico no existe en la tabla personalmedico.");
}

$medico = $result_obtener_medico->fetch_assoc();
$id_medico = $medico['ID_Med'];


// Obtener la lista de todos los pacientes
$query_pacientes = "
  SELECT p.ID_Pac, p.Nombre, p.Apellido
  FROM Pacientes p
  ORDER BY p.Nombre, p.Apellido
";
$result_pacientes = $conn->query($query_pacientes);

// Procesar el formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_paciente = $_POST['id_paciente'];
  $fecha = $_POST['fecha'];
  $descripcion = $_POST['descripcion'];

  // Insertar el nuevo informe en la base de datos
  $query = "INSERT INTO HistorialMedico (ID_Med, ID_Pac, Fecha, Descripcion) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('iiss', $id_medico, $id_paciente, $fecha, $descripcion);

  if ($stmt->execute()) {
    header("Location: dashboard.php?seccion=informes");
    exit();
  } else {
    $error = "Error al registrar el informe: " . $stmt->error;
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/styles.css">
  <title>Registrar nuevo Informe</title>

</head>

<body>
  <div class="container">
    <h2 class="title">Registrar nuevo informe</h2>
    <?php if (isset($error))
      echo "<p style='color: red;'>$error</p>"; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label for="id_paciente">Paciente</label>
        <select id="id_paciente" name="id_paciente" required>
          <?php if ($result_pacientes->num_rows > 0): ?>
            <?php while ($paciente = $result_pacientes->fetch_assoc()): ?>
              <option value="<?php echo $paciente['ID_Pac']; ?>">
                <?php echo $paciente['Nombre'] . ' ' . $paciente['Apellido']; ?>
              </option>
            <?php endwhile; ?>
          <?php else: ?>
            <option value="">No hay pacientes disponibles</option>
          <?php endif; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="fecha">Fecha del Informe</label>
        <input type="date" id="fecha" name="fecha" required>
      </div>
      <div class="form-group">
        <label for="descripcion">Descripción del Informe</label>
        <textarea id="descripcion" name="descripcion" rows="5" required></textarea>
      </div>
      <div class="both-inp">
        <div class="input-group">
          <button type="submit" class="btn blue">Guardar Cambios</button>

        </div>
        <div class="input-group">
          <a href="dashboard.php?seccion=informes" class="btn red">Cancelar</a>

        </div>
      </div>
    </form>
  </div>
</body>

</html>

<?php
$conn->close();
?>