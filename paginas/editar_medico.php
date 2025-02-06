<?php
require_once 'config.php';



// Obtener datos del médico
if (isset($_GET['id'])) {
    $id_med = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM personalmedico WHERE ID_Med = ?");
    $stmt->execute([$id_med]);
    $medico = $stmt->fetch(PDO::FETCH_ASSOC);
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
                NomMed = ?, ApellidoMed = ?, DirMed = ?, CelMed = ?, 
                EmailMed = ?, GenMed = ?, EspecialidadMed = ?
              WHERE ID_Med = ?";
    $stmt = $pdo->prepare($query);
    if ($stmt->execute([$nombre, $apellido, $direccion, $celular, $email, $genero, $especialidad, $id_med])) {
        header("Location: dashboard.php?seccion=gestionar_medicos");
        exit();
    } else {
        $error = "Error al actualizar: " . $stmt->errorInfo()[2];
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
                <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
                <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            </div>
            <div class="both-inp">
                <div class="input-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($medico['NomMed']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($medico['ApellidoMed']); ?>" required>
                </div>
            </div>
            <div class="both-inp">
                <div class="input-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($medico['DirMed']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="celular">Celular</label>
                    <input type="text" id="celular" name="celular" value="<?php echo htmlspecialchars($medico['CelMed']); ?>" required>
                </div>
            </div>
            <div class="both-inp">
                <div class="input-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($medico['EmailMed']); ?>" required>
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
                    <input type="text" id="especialidad" name="especialidad" value="<?php echo htmlspecialchars($medico['EspecialidadMed']); ?>" required>
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