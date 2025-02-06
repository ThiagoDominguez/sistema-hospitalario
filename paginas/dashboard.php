<?php
session_start();
// Mostrar errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
include("config.php");

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener datos del usuario autenticado
$id_usuario = $_SESSION['usuario_id'];
$rol_id = $_SESSION['rol_id'];

// Consulta preparada para obtener datos del usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE ID_Usuario = ?");
$stmt->bindParam(1, $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Inicializar variables
$paciente = null;
$medico = null;
$admin = null;

// Verificar el rol y obtener datos adicionales según corresponda
if ($rol_id == 1) { // Paciente
    $stmt = $pdo->prepare("SELECT * FROM pacientes WHERE ID_Usuario = ?");
    $stmt->bindParam(1, $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
    $id_pac = $paciente['ID_Pac']; // Obtener el ID_Pac del paciente
} elseif ($rol_id == 2) { // Médico
    $stmt = $pdo->prepare("SELECT * FROM personalmedico WHERE ID_Usuario = ?");
    $stmt->bindParam(1, $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $medico = $stmt->fetch(PDO::FETCH_ASSOC);
} elseif ($rol_id == 3) { // Administrativo
    $stmt = $pdo->prepare("SELECT * FROM personaladministrativo WHERE ID_Usuario = ?");
    $stmt->bindParam(1, $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Capturar qué sección se debe mostrar
$seccion_activa = isset($_GET['seccion']) ? $_GET['seccion'] : null;

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/styles.css">
  <title>Dashboard</title>
  <style>
    .section {
      display:
        <?= $seccion_activa ? 'block' : 'none' ?>
      ;
      margin-top: 15px;
      padding: 15px;
      border: 1px solid #ddd;
      background: #f9f9f9;
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <h2>Gestión Hospitalaria</h2>
    <hr>
    <?php if ($rol_id == 1): // Paciente ?>
      <h3><?php echo $paciente['Nombre'] . ' ' . $paciente['Apellido']; ?></h3>
    <?php elseif ($rol_id == 2): // Medico ?>
      <h3><?php echo $medico['NomMed'] . ' ' . $medico['ApellidoMed']; ?></h3>
    <?php elseif ($rol_id == 3): // Admin ?>
      <h3><?php echo $admin['NomAdmin'] . ' ' . $admin['ApellidoAdmin']; ?></h3>
    <?php endif ?>
    <a href="dashboard.php">Inicio</a>

    <a href="logout.php">Cerrar Sesión</a>


  </div>

  <div class="main">
    <?php if ($rol_id == 1): // Paciente ?>
      <h2>Bienvenido, <?php echo $paciente['Nombre'] . ' ' . $paciente['Apellido']; ?></h2>
    <?php elseif ($rol_id == 2): // Medico ?>
      <h2>Bienvenido, <?php echo $medico['NomMed'] . ' ' . $medico['ApellidoMed']; ?></h2>
    <?php elseif ($rol_id == 3): // Admin ?>
      <h2>Bienvenido, <?php echo $admin['NomAdmin'] . ' ' . $admin['ApellidoAdmin']; ?></h2>
    <?php endif ?>

    <div class="card-container">
      <?php if ($rol_id == 1): // Paciente ?>
        <a href="dashboard.php?seccion=turnos" class="card blue">Solicitar Turnos</a>
        <a href="dashboard.php?seccion=proximos" class="card green">Próximos Turnos</a>
        <a href="dashboard.php?seccion=resultados" class="card orange">Resultados Estudios</a>
        <a href="dashboard.php?seccion=historial" class="card red">Historial Atención</a>
      <?php elseif ($rol_id == 2): // Médico ?>
        <a href="dashboard.php?seccion=mis_pacientes" class="card blue">Mis Pacientes</a>
        <a href="dashboard.php?seccion=agenda" class="card green">Agenda</a>
        <a href="dashboard.php?seccion=informes" class="card orange">Informes Médicos</a>
      <?php elseif ($rol_id == 3): // Administrativo ?>
        <a href="dashboard.php?seccion=gestionar_pacientes" class="card blue">Gestionar Pacientes</a>
        <a href="dashboard.php?seccion=gestionar_medicos" class="card blue">Gestionar Médicos</a>
        <a href="dashboard.php?seccion=gestionar_turnos" class="card green">Gestionar Turnos</a>
        <a href="dashboard.php?seccion=gestionar_admin" class="card orange">Gestionar Administrador</a>
      <?php endif; ?>
    </div>

    <!-- ANCHOR PACIENTE -->
   <!-- Sección para solicitar un turno -->
<?php if ($seccion_activa == "turnos"): ?>
  <div class="section">
    <h3>Solicitar turnos</h3>
    <a href="solicitar_turno.php" class="btn green">Solicitar turno</a>
  </div>
<?php endif ?>

<!-- Sección para Ver Turnos Solicitados -->
<?php if ($seccion_activa == "proximos"): ?>
  <div class="section">
    <h3>Ver Turnos Solicitados</h3>
    <table class="styled-table">
      <thead>
        <tr>
          <th>ID Turno</th>
          <th>Médico</th>
          <th>Fecha</th>
          <th>Hora</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Obtener la lista de turnos solicitados por el paciente
        $query_turnos = "
          SELECT t.ID_Turno, m.NomMed AS NombreMedico, m.ApellidoMed AS ApellidoMedico, t.FechaTurno, t.HoraTurno
          FROM turnos t
          JOIN personalmedico m ON t.ID_Med = m.ID_Med
          WHERE t.ID_Pac = ?
          ORDER BY t.FechaTurno DESC, t.HoraTurno DESC
        ";
        $stmt_turnos = $pdo->prepare($query_turnos);
        $stmt_turnos->execute([$id_pac]);
        $result_turnos = $stmt_turnos->fetchAll(PDO::FETCH_ASSOC);

        // Verificar si hay resultados
        if (count($result_turnos) > 0) {
          foreach ($result_turnos as $row) {
            // Formatear la fecha al formato dd/mm/yyyy
            $fecha_turno = date('d/m/Y', strtotime($row['FechaTurno']));
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['ID_Turno']) . "</td>";
            echo "<td>" . htmlspecialchars($row['NombreMedico'] . " " . htmlspecialchars($row['ApellidoMedico'])) . "</td>";
            echo "<td>" . htmlspecialchars($fecha_turno) . "</td>";
            echo "<td>" . htmlspecialchars($row['HoraTurno']) . "</td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='4' style='text-align:center;'>No hay turnos solicitados.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<!-- Sección para Ver Resultados de Estudios -->
<?php if ($seccion_activa == "resultados"): ?>
  <div class="section">
    <h3>Resultados de Estudios</h3>
    <table class="styled-table">
      <thead>
        <tr>
          <th>ID Historial</th>
          <th>Descripción</th>
          <th>Fecha</th>
          <th>Médico</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Obtener la lista de resultados de estudios del paciente
        $query_resultados = "
          SELECT h.ID_Historial, h.Descripcion, h.Fecha, CONCAT(m.NomMed, ' ', m.ApellidoMed) AS NombreMedico
          FROM historialmedico h
          JOIN personalmedico m ON h.ID_Med = m.ID_Med
          WHERE h.ID_Pac = ?
          ORDER BY h.Fecha DESC
        ";
        $stmt_resultados = $pdo->prepare($query_resultados);
        $stmt_resultados->execute([$id_pac]);
        $result_resultados = $stmt_resultados->fetchAll(PDO::FETCH_ASSOC);

        // Verificar si hay resultados
        if (count($result_resultados) > 0) {
          foreach ($result_resultados as $row) {
            // Formatear la fecha al formato dd/mm/yyyy
            $fecha_estudio = date('d/m/Y', strtotime($row['Fecha']));
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['ID_Historial']) . "</td>";
            echo "<td class='content'>" . htmlspecialchars($row['Descripcion']) . "</td>";
            echo "<td>" . htmlspecialchars($fecha_estudio) . "</td>";
            echo "<td>" . htmlspecialchars($row['NombreMedico']) . "</td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='4' style='text-align:center;'>No hay resultados de estudios.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<!-- Sección para Ver Historial de Atención -->
<?php if ($seccion_activa == "historial"): ?>
  <div class="section">
    <h3>Historial de Atención</h3>
    <table class="styled-table">
      <thead>
        <tr>
          <th>ID Historial</th>
          <th>Fecha</th>
          <th>Descripción</th>
          <th>Médico</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Obtener la lista de historial de atención del paciente
        $query_historial = "
          SELECT h.ID_Historial, h.Fecha, h.Descripcion, CONCAT(m.NomMed, ' ', m.ApellidoMed) AS NombreMedico
          FROM historialmedico h
          JOIN personalmedico m ON h.ID_Med = m.ID_Med
          WHERE h.ID_Pac = ?
          ORDER BY h.Fecha DESC
        ";
        $stmt_historial = $pdo->prepare($query_historial);
        $stmt_historial->execute([$id_pac]);
        $result_historial = $stmt_historial->fetchAll(PDO::FETCH_ASSOC);

        // Verificar si hay resultados
        if (count($result_historial) > 0) {
          foreach ($result_historial as $row) {
            // Formatear la fecha al formato dd/mm/yyyy
            $fecha_atencion = date('d/m/Y', strtotime($row['Fecha']));
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['ID_Historial']) . "</td>";
            echo "<td>" . htmlspecialchars($fecha_atencion) . "</td>";
            echo "<td class='content'>" . htmlspecialchars($row['Descripcion']) . "</td>";
            echo "<td>" . htmlspecialchars($row['NombreMedico']) . "</td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='4' style='text-align:center;'>No hay historial de atención.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

    <!-- ANCHOR MEDICO -->

<!-- Sección de Mis Pacientes -->
<?php if ($seccion_activa == "mis_pacientes"): ?>
  <div class="section">
    <h3>Mis Pacientes</h3>
    <table class="styled-table">
      <thead>
        <tr>
          <th>ID Paciente</th>
          <th>Nombre</th>
          <th>Apellido</th>
          <th>Fecha de Nacimiento</th>
          <th>Género</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Consultar la base de datos para obtener los pacientes del médico actual
        $query = "
          SELECT DISTINCT p.ID_Pac, p.Nombre, p.Apellido, p.FechaNacimiento, p.GenPac
          FROM turnos t
          JOIN pacientes p ON t.ID_Pac = p.ID_Pac
          WHERE t.ID_Med = ?
          ORDER BY p.Nombre, p.Apellido
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$medico['ID_Med']]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Verificar si hay resultados
        if (count($result) > 0) {
          foreach ($result as $row) {
            // Formatear la fecha de nacimiento al formato dd/mm/yyyy
            $fecha_nacimiento = date('d/m/Y', strtotime($row['FechaNacimiento']));
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['ID_Pac']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Apellido']) . "</td>";
            echo "<td>" . htmlspecialchars($fecha_nacimiento) . "</td>";
            echo "<td>" . htmlspecialchars($row['GenPac']) . "</td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='5' style='text-align:center;'>No hay pacientes registrados.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<!-- Sección de Agenda del Médico -->
<?php if ($seccion_activa == "agenda"): ?>
  <div class="section">
    <h3>Agenda del Médico</h3>
    <table class="styled-table">
      <thead>
        <tr>
          <th>ID Turno</th>
          <th>Nombre del Paciente</th>
          <th>Apellido del Paciente</th>
          <th>Fecha del Turno</th>
          <th>Hora del Turno</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Reutilizar la consulta SQL para obtener los turnos del médico actual
        $query = "
          SELECT t.ID_Turno, p.Nombre, p.Apellido, t.FechaTurno, t.HoraTurno
          FROM turnos t
          JOIN pacientes p ON t.ID_Pac = p.ID_Pac
          WHERE t.ID_Med = ?
          ORDER BY t.FechaTurno, t.HoraTurno
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$medico['ID_Med']]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Verificar si hay resultados
        if (count($result) > 0) {
          foreach ($result as $row) {
            // Formatear la fecha al formato dd/mm/yyyy
            $fecha_turno = date('d/m/Y', strtotime($row['FechaTurno']));
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['ID_Turno']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Apellido']) . "</td>";
            echo "<td>" . htmlspecialchars($fecha_turno) . "</td>";
            echo "<td>" . htmlspecialchars($row['HoraTurno']) . "</td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='5' style='text-align:center;'>No hay turnos registrados.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<!-- Sección de Historial Médico -->
<?php if ($seccion_activa == "informes"): ?>
  <div class="section">
    <h3>Historial Médico</h3>
    <a href="registro_informe.php" class="btn green">Registrar nuevo Informe</a>
    <table class="styled-table">
      <thead>
        <tr>
          <th>ID Historial</th>
          <th>Nombre del Paciente</th>
          <th>Apellido del Paciente</th>
          <th>Fecha del Historial</th>
          <th>Contenido del Historial</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Reutilizar la consulta SQL para obtener el historial médico del médico actual
        $query = "
          SELECT h.ID_Historial, p.Nombre, p.Apellido, h.Fecha, h.Descripcion
          FROM historialmedico h
          JOIN pacientes p ON h.ID_Pac = p.ID_Pac
          WHERE h.ID_Med = ?
          ORDER BY h.Fecha DESC
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$medico['ID_Med']]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Verificar si hay resultados
        if (count($result) > 0) {
          foreach ($result as $row) {
            // Formatear la fecha al formato dd/mm/yyyy
            $fecha_historial = date('d/m/Y', strtotime($row['Fecha']));
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['ID_Historial']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Apellido']) . "</td>";
            echo "<td>" . htmlspecialchars($fecha_historial) . "</td>";
            echo "<td class='content'>" . htmlspecialchars($row['Descripcion']) . "</td>";
            echo "<td><a href='editar_informe.php?id=" . htmlspecialchars($row['ID_Historial']) . "' class='btn blue'>Editar</a></td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='6' style='text-align:center;'>No hay registros de historial médico.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

    <!--ANCHOR ADMINISTRADOR   -->

    <!-- Sección de Gestión de Médicos -->
<?php if ($seccion_activa == "gestionar_medicos"): ?>
  <div class="section">
    <h3>Gestión de Médicos</h3>
    <a href="registro_medico.php" class="btn green">Registrar Médico</a>
    <h4>Listado de Médicos Registrados</h4>
    <table class="styled-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Apellido</th>
          <th>Dirección</th>
          <th>Celular</th>
          <th>Email</th>
          <th>Género</th>
          <th>Especialidad</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Consultar la base de datos para obtener los médicos
        $query = "SELECT * FROM personalmedico ORDER BY ID_Med ASC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Verificar si hay resultados
        if (count($result) > 0) {
          foreach ($result as $medico) {
            echo "<tr>
                    <td>" . htmlspecialchars($medico['ID_Med']) . "</td>
                    <td>" . htmlspecialchars($medico['NomMed']) . "</td>
                    <td>" . htmlspecialchars($medico['ApellidoMed']) . "</td>
                    <td>" . htmlspecialchars($medico['DirMed']) . "</td>
                    <td>" . htmlspecialchars($medico['CelMed']) . "</td>
                    <td>" . htmlspecialchars($medico['EmailMed']) . "</td>
                    <td>" . htmlspecialchars($medico['GenMed']) . "</td>
                    <td>" . htmlspecialchars($medico['EspecialidadMed']) . "</td>
                    <td>
                        <a href='editar_medico.php?id=" . htmlspecialchars($medico['ID_Med']) . "' class='btn blue'>Editar</a>
                        <a href='eliminar_medico.php?id=" . htmlspecialchars($medico['ID_Med']) . "' class='btn red' onclick='return confirm(\"¿Estás seguro de eliminar este médico?\")'>Eliminar</a>
                    </td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='9' style='text-align:center;'>No hay médicos registrados.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>


   <!-- Sección de Gestión de Pacientes -->
<?php if ($seccion_activa == "gestionar_pacientes"): ?>
    <div class="section">
        <h3>Gestión de Pacientes</h3>
        <!-- Botón de Registrar Paciente -->
        <a href="registro_paciente.php" class="btn green">Registrar Paciente</a>

        <h4>Listado de Pacientes Registrados</h4>
        <!-- Tabla de Pacientes -->
        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Dirección</th>
                    <th>Celular</th>
                    <th>Email</th>
                    <th>Género</th>
                    <th>DNI</th>
                    <th>Fecha de Nacimiento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consultar la base de datos para obtener los pacientes
                $query = "SELECT * FROM pacientes ORDER BY ID_Pac ASC";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Verificar si hay resultados
                if (count($result) > 0) {
                    foreach ($result as $paciente) {
                        echo "<tr>
                                <td>" . htmlspecialchars($paciente['ID_Pac']) . "</td>
                                <td>" . htmlspecialchars($paciente['Nombre']) . "</td>
                                <td>" . htmlspecialchars($paciente['Apellido']) . "</td>
                                <td>" . htmlspecialchars($paciente['DirPac']) . "</td>
                                <td>" . htmlspecialchars($paciente['CelPac']) . "</td>
                                <td>" . htmlspecialchars($paciente['EmailPac']) . "</td>
                                <td>" . htmlspecialchars($paciente['GenPac']) . "</td>
                                <td>" . htmlspecialchars($paciente['DNIPac']) . "</td>
                                <td>" . htmlspecialchars($paciente['FechaNacimiento']) . "</td>
                                <td>
                                    <a href='editar_paciente.php?id=" . htmlspecialchars($paciente['ID_Pac']) . "' class='btn blue'>Editar</a>
                                    <a href='eliminar_paciente.php?id=" . htmlspecialchars($paciente['ID_Pac']) . "' class='btn red' onclick='return confirm(\"¿Estás seguro de eliminar este paciente?\")'>Eliminar</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' style='text-align:center;'>No hay pacientes registrados.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
      <!-- Sección de Gestión de Administradores -->
<?php elseif ($seccion_activa == "gestionar_admin"): ?>
    <div class="section">
        <h3>Gestión de Administradores</h3>
        <a href="registro_admin.php" class="btn green">Registrar Administrador</a>

        <h4>Listado de Administradores Registrados</h4>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Dirección</th>
                    <th>Celular</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consultar la base de datos para obtener los administradores
                $query = "SELECT * FROM personaladministrativo ORDER BY ID_Admin ASC";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($result) > 0) {
                    foreach ($result as $admin) {
                        echo "<tr>
                            <td>" . htmlspecialchars($admin['ID_Admin']) . "</td>
                            <td>" . htmlspecialchars($admin['NomAdmin']) . "</td>
                            <td>" . htmlspecialchars($admin['ApellidoAdmin']) . "</td>
                            <td>" . htmlspecialchars($admin['DirAdmin']) . "</td>
                            <td>" . htmlspecialchars($admin['CelAdmin']) . "</td>
                            <td>" . htmlspecialchars($admin['EmailAdmin']) . "</td>
                            <td>
                                <a href='editar_admin.php?id=" . htmlspecialchars($admin['ID_Admin']) . "' class='btn blue'>Editar</a>
                                <a href='eliminar_admin.php?id=" . htmlspecialchars($admin['ID_Admin']) . "' class='btn red' onclick='return confirm(\"¿Estás seguro de eliminar este administrador?\")'>Eliminar</a>
                            </td>
                          </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center;'>No hay administradores registrados.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>


     <!-- Sección de Gestión de Turnos -->
<?php elseif ($seccion_activa == "gestionar_turnos"): ?>
  <div class="section">
    <h3>Gestión de Turnos</h3>
    <a href="registro_turno.php" class="btn green">Registrar Turno</a>

    <h4>Listado de Turnos</h4>
    <table class="styled-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Médico</th>
          <th>Paciente</th>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Consultar la base de datos para obtener los turnos
        $query = "SELECT t.ID_Turno, t.FechaTurno, t.HoraTurno, m.NomMed, m.ApellidoMed, p.Nombre, p.Apellido 
                  FROM turnos t
                  JOIN personalmedico m ON t.ID_Med = m.ID_Med
                  JOIN pacientes p ON t.ID_Pac = p.ID_Pac
                  ORDER BY t.FechaTurno DESC, t.HoraTurno DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Verificar si hay resultados
        if (count($result) > 0) {
          foreach ($result as $turno) {
            echo "<tr>
                    <td>" . htmlspecialchars($turno['ID_Turno']) . "</td>
                    <td>" . htmlspecialchars($turno['NomMed'] . ' ' . $turno['ApellidoMed']) . "</td>
                    <td>" . htmlspecialchars($turno['Nombre'] . ' ' . $turno['Apellido']) . "</td>
                    <td>" . htmlspecialchars($turno['FechaTurno']) . "</td>
                    <td>" . htmlspecialchars($turno['HoraTurno']) . "</td>
                    <td>
                      <a href='editar_turno.php?id=" . htmlspecialchars($turno['ID_Turno']) . "' class='btn blue'>Editar</a>
                      <a href='eliminar_turno.php?id=" . htmlspecialchars($turno['ID_Turno']) . "' class='btn red' onclick='return confirm(\"¿Estás seguro de eliminar este turno?\")'>Eliminar</a>
                    </td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='6' style='text-align:center;'>No hay turnos registrados.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
  </div>
</body>

</html>