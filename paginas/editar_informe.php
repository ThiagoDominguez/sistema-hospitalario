<?php
session_start();
require_once 'config.php';

// Verificar si el usuario ha iniciado sesión y es un médico (rol 2)
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 2) {
    header("Location: login.php");
    exit();
}

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
    $query = "UPDATE historialmedico SET ID_Pac = ?, Fecha = ?, Descripcion = ? WHERE ID_Historial = ?";
    $stmt = $pdo->prepare($query);
    if ($stmt->execute([$id_paciente, $fecha, $descripcion, $id_historial])) {
        header("Location: dashboard.php?seccion=informes");
        exit();
    } else {
        $error = "Error al actualizar el historial médico. Por favor, inténtelo de nuevo.";
    }
}

// Obtener los detalles del historial médico actual
$query = "SELECT h.ID_Historial, h.ID_Pac, h.Fecha, h.Descripcion, p.Nombre, p.Apellido
          FROM historialmedico h
          JOIN pacientes p ON h.ID_Pac = p.ID_Pac
          WHERE h.ID_Historial = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id_historial]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $id_paciente = $row['ID_Pac'];
    $fecha = $row['Fecha'];
    $descripcion = $row['Descripcion'];
} else {
    $error = "No se encontró el historial médico.";
}

// Obtener la lista de pacientes
$query_pacientes = "SELECT ID_Pac, Nombre, Apellido FROM pacientes";
$result_pacientes = $pdo->query($query_pacientes);
$pacientes = $result_pacientes->fetchAll(PDO::FETCH_ASSOC);
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
                    <?php if (count($pacientes) > 0): ?>
                        <?php foreach ($pacientes as $paciente): ?>
                            <option value="<?php echo htmlspecialchars($paciente['ID_Pac']); ?>" <?php echo ($paciente['ID_Pac'] == $id_paciente) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($paciente['Nombre'] . ' ' . $paciente['Apellido']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No hay pacientes disponibles</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha">Fecha del Informe</label>
                <input type="date" id="fecha" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción del Informe</label>
                <textarea id="descripcion" name="descripcion" rows="5" required><?php echo htmlspecialchars($descripcion); ?></textarea>
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