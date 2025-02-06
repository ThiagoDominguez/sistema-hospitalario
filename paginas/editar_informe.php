<?php
// Incluir archivo de configuración de la base de datos
require_once 'config.php';

// Obtener el ID del historial médico desde la URL
$id_historial = isset($_GET['id']) ? $_GET['id'] : die('ERROR: ID no encontrado.');

// Inicializar variables para almacenar los datos del historial
$id_paciente = $fecha = $descripcion = $error = $success = '';

// Procesar el formulario cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar y sanitizar los datos del formulario
    $id_paciente = htmlspecialchars(strip_tags($_POST['id_paciente']));
    $fecha = htmlspecialchars(strip_tags($_POST['fecha']));
    $descripcion = htmlspecialchars(strip_tags($_POST['descripcion']));

    // Actualizar el historial médico en la base de datos
    $query = "UPDATE HistorialMedico SET ID_Pac = ?, Fecha = ?, Descripcion = ? WHERE ID_Historial = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('issi', $id_paciente, $fecha, $descripcion, $id_historial);

        if ($stmt->execute()) {
            $success = "Historial médico actualizado con éxito.";
        } else {
            $error = "Error al actualizar el historial médico. Por favor, inténtelo de nuevo.";
        }
    } else {
        $error = "Error en la preparación de la consulta. Por favor, inténtelo de nuevo.";
    }
}

// Obtener los detalles del historial médico actual
$query = "SELECT h.ID_Historial, h.ID_Pac, h.Fecha, h.Descripcion, p.Nombre, p.Apellido
          FROM HistorialMedico h
          JOIN Pacientes p ON h.ID_Pac = p.ID_Pac
          WHERE h.ID_Historial = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('i', $id_historial);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $id_paciente = $row['ID_Pac'];
        $fecha = $row['Fecha'];
        $descripcion = $row['Descripcion'];
    } else {
        $error = "No se encontró el historial médico.";
    }
} else {
    $error = "Error en la preparación de la consulta.";
}

// Obtener la lista de pacientes
$query_pacientes = "SELECT ID_Pac, Nombre, Apellido FROM Pacientes";
$result_pacientes = $conn->query($query_pacientes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Historial Médico</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container_both-inp">
        <h2 class="title">Editar Historial Médico</h2>
        <form method="POST" action="" class="form_both-inp">
            <!-- Mostrar mensajes de éxito o error -->
            <div class="message">
                <?php if ($success) echo "<p class='success'>$success</p>"; ?>
                <?php if ($error) echo "<p class='error'>$error</p>"; ?>
            </div>
            <div class="form-group">
                <label for="id_paciente">Paciente</label>
                <select id="id_paciente" name="id_paciente" required>
                    <?php if ($result_pacientes->num_rows > 0): ?>
                        <?php while ($paciente = $result_pacientes->fetch_assoc()): ?>
                            <option value="<?php echo $paciente['ID_Pac']; ?>" <?php echo ($paciente['ID_Pac'] == $id_paciente) ? 'selected' : ''; ?>>
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
                <input type="date" id="fecha" name="fecha" value="<?php echo $fecha; ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción del Informe</label>
                <textarea id="descripcion" name="descripcion" rows="5" required><?php echo $descripcion; ?></textarea>
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