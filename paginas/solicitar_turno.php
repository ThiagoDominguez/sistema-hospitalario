<?php
session_start();
// Mostrar errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
include("config.php");

// Verificar si el usuario ha iniciado sesión y es un paciente (rol 1)
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Verificar que el ID_Pac existe y obtenerlo basado en el ID_Usuario
$query_verificar_paciente = "SELECT ID_Pac FROM pacientes WHERE ID_Usuario = ?";
$stmt_verificar_paciente = $pdo->prepare($query_verificar_paciente);
$stmt_verificar_paciente->execute([$usuario_id]);
$paciente = $stmt_verificar_paciente->fetch(PDO::FETCH_ASSOC);

if ($paciente) {
    $id_pac = $paciente['ID_Pac'];

    // Obtener la lista de todos los médicos
    $query_medicos = "SELECT ID_Med, NomMed, ApellidoMed FROM personalmedico ORDER BY NomMed, ApellidoMed";
    $stmt_medicos = $pdo->query($query_medicos);
    $medicos = $stmt_medicos->fetchAll(PDO::FETCH_ASSOC);

    // Procesar el formulario si se ha enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_medico = $_POST['id_medico'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];

        // Insertar el nuevo turno en la base de datos
        $query = "INSERT INTO turnos (ID_Pac, ID_Med, FechaTurno, HoraTurno) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        if ($stmt->execute([$id_pac, $id_medico, $fecha, $hora])) {
            $message = "Turno solicitado exitosamente.";
            header("Location: dashboard.php?seccion=turnos");
            exit();
        } else {
            $error = "Error al solicitar el turno: " . $stmt->errorInfo()[2];
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
        <?php if (isset($message)) echo "<p style='color: green;'>$message</p>"; ?>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="id_medico">Seleccionar Médico:</label>
                <select id="id_medico" name="id_medico" required>
                    <?php if (count($medicos) > 0): ?>
                        <?php foreach ($medicos as $medico): ?>
                            <option value="<?php echo $medico['ID_Med']; ?>">
                                <?php echo htmlspecialchars($medico['NomMed'] . ' ' . $medico['ApellidoMed']); ?>
                            </option>
                        <?php endforeach; ?>
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