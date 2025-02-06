<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';


// Procesar formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_turno = $_POST['id_turno'];
    $id_med = $_POST['id_med'];
    $id_pac = $_POST['id_pac'];
    $fecha_turno = $_POST['fecha_turno'];
    $hora_turno = $_POST['hora_turno'];

    $query = "UPDATE turnos SET ID_Med = ?, ID_Pac = ?, FechaTurno = ?, HoraTurno = ? WHERE ID_Turno = ?";
    $stmt = $pdo->prepare($query);

    if ($stmt->execute([$id_med, $id_pac, $fecha_turno, $hora_turno, $id_turno])) {
        header("Location: dashboard.php?seccion=gestionar_turnos");
        exit();
    } else {
        $error = "Error al procesar el formulario: " . $stmt->errorInfo()[2];
    }
}

// Obtener datos del turno si se está editando
$id_turno = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM turnos WHERE ID_Turno = ?");
$stmt->execute([$id_turno]);
$turno = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener listas de médicos y pacientes
$medicos = $pdo->query("SELECT ID_Med, NomMed, ApellidoMed FROM personalmedico ORDER BY NomMed")->fetchAll(PDO::FETCH_ASSOC);
$pacientes = $pdo->query("SELECT ID_Pac, Nombre, Apellido FROM pacientes ORDER BY Nombre")->fetchAll(PDO::FETCH_ASSOC);

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

        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

        <form method="POST" action="">
            <input type="hidden" name="id_turno" value="<?php echo htmlspecialchars($turno['ID_Turno']); ?>">
            <div class="form-group">
                <label for="id_med">Médico</label>
                <select id="id_med" name="id_med" required>
                    <?php foreach ($medicos as $medico): ?>
                        <option value="<?php echo htmlspecialchars($medico['ID_Med']); ?>" <?php if ($turno['ID_Med'] == $medico['ID_Med']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($medico['NomMed'] . ' ' . $medico['ApellidoMed']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_pac">Paciente</label>
                <select id="id_pac" name="id_pac" required>
                    <?php foreach ($pacientes as $paciente): ?>
                        <option value="<?php echo htmlspecialchars($paciente['ID_Pac']); ?>" <?php if ($turno['ID_Pac'] == $paciente['ID_Pac']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($paciente['Nombre'] . ' ' . $paciente['Apellido']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_turno">Fecha del Turno</label>
                <input type="date" id="fecha_turno" name="fecha_turno" value="<?php echo htmlspecialchars($turno['FechaTurno']); ?>" required>
            </div>
            <div class="form-group">
                <label for="hora_turno">Hora del Turno</label>
                <input type="time" id="hora_turno" name="hora_turno" value="<?php echo htmlspecialchars($turno['HoraTurno']); ?>" required>
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