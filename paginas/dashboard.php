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

// Obtener datos del usuario autenticado
$id_usuario = $_SESSION['usuario_id'];
$rol_id = $_SESSION['rol_id'];

// Consulta preparada para obtener datos del usuario
$stmt = $conn->prepare("SELECT * FROM Usuarios WHERE ID_Usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Inicializar variables
$paciente = null;
$medico = null;
$admin = null;

// Verificar el rol y obtener datos adicionales según corresponda
if ($rol_id == 1) { // Paciente
  $stmt = $conn->prepare("SELECT * FROM Pacientes WHERE ID_Usuario = ?");
  $stmt->bind_param("i", $id_usuario);
  $stmt->execute();
  $paciente = $stmt->get_result()->fetch_assoc();
  $stmt->close();
  $id_pac = $paciente['ID_Pac']; // Obtener el ID_Pac del paciente
} elseif ($rol_id == 2) { // Médico
  $stmt = $conn->prepare("SELECT * FROM PersonalMedico WHERE ID_Usuario = ?");
  $stmt->bind_param("i", $id_usuario);
  $stmt->execute();
  $medico = $stmt->get_result()->fetch_assoc();
  $stmt->close();
} elseif ($rol_id == 3) { // Administrativo
  $stmt = $conn->prepare("SELECT * FROM PersonalAdministrativo WHERE ID_Usuario = ?");
  $stmt->bind_param("i", $id_usuario);
  $stmt->execute();
  $admin = $stmt->get_result()->fetch_assoc();
  $stmt->close();
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
              FROM Turnos t
              JOIN personalmedico m ON t.ID_Med = m.ID_Med
              WHERE t.ID_Pac = ?
              ORDER BY t.FechaTurno DESC, t.HoraTurno DESC
            ";
            $stmt_turnos = $conn->prepare($query_turnos);
            $stmt_turnos->bind_param('i', $id_pac);
            $stmt_turnos->execute();
            $result_turnos = $stmt_turnos->get_result();

            // Verificar si hay resultados
            if ($result_turnos->num_rows > 0) {
              while ($row = $result_turnos->fetch_assoc()) {
                // Formatear la fecha al formato dd/mm/yyyy
                $fecha_turno = date('d/m/Y', strtotime($row['FechaTurno']));
                echo "<tr>";
                echo "<td>" . $row['ID_Turno'] . "</td>";
                echo "<td>" . $row['NombreMedico'] . " " . $row['ApellidoMedico'] . "</td>";
                echo "<td>" . $fecha_turno . "</td>";
                echo "<td>" . $row['HoraTurno'] . "</td>";
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
            $stmt_resultados = $conn->prepare($query_resultados);
            $stmt_resultados->bind_param('i', $id_pac);
            $stmt_resultados->execute();
            $result_resultados = $stmt_resultados->get_result();

            // Verificar si hay resultados
            if ($result_resultados->num_rows > 0) {
              while ($row = $result_resultados->fetch_assoc()) {
                // Formatear la fecha al formato dd/mm/yyyy
                $fecha_estudio = date('d/m/Y', strtotime($row['Fecha']));
                echo "<tr>";
                echo "<td>" . $row['ID_Historial'] . "</td>";
                echo "<td class='content'>" . $row['Descripcion'] . "</td>";
                echo "<td>" . $fecha_estudio . "</td>";
                echo "<td>" . $row['NombreMedico'] . "</td>";
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
            $stmt_historial = $conn->prepare($query_historial);
            $stmt_historial->bind_param('i', $id_pac);
            $stmt_historial->execute();
            $result_historial = $stmt_historial->get_result();

            // Verificar si hay resultados
            if ($result_historial->num_rows > 0) {
              while ($row = $result_historial->fetch_assoc()) {
                // Formatear la fecha al formato dd/mm/yyyy
                $fecha_atencion = date('d/m/Y', strtotime($row['Fecha']));
                echo "<tr>";
                echo "<td>" . $row['ID_Historial'] . "</td>";
                echo "<td>" . $fecha_atencion . "</td>";
                echo "<td class='content'>" . $row['Descripcion'] . "</td>";
                echo "<td>" . $row['NombreMedico'] . "</td>";

              }
            } else {
              echo "<tr><td colspan='5' style='text-align:center;'>No hay historial de atención.</td></tr>";
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
              FROM Turnos t
              JOIN Pacientes p ON t.ID_Pac = p.ID_Pac
              WHERE t.ID_Med = ?
              ORDER BY p.Nombre, p.Apellido
            ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $medico['ID_Med']);
            $stmt->execute();
            $result = $stmt->get_result();

            // Verificar si hay resultados
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                // Formatear la fecha de nacimiento al formato dd/mm/yyyy
                $fecha_nacimiento = date('d/m/Y', strtotime($row['FechaNacimiento']));
                echo "<tr>";
                echo "<td>" . $row['ID_Pac'] . "</td>";
                echo "<td>" . $row['Nombre'] . "</td>";
                echo "<td>" . $row['Apellido'] . "</td>";
                echo "<td>" . $fecha_nacimiento . "</td>";
                echo "<td>" . $row['GenPac'] . "</td>";
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
          FROM Turnos t
          JOIN Pacientes p ON t.ID_Pac = p.ID_Pac
          WHERE t.ID_Med = ?
          ORDER BY t.FechaTurno, t.HoraTurno
        ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $medico['ID_Med']);
            $stmt->execute();
            $result = $stmt->get_result();

            // Verificar si hay resultados
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                // Formatear la fecha al formato dd/mm/yyyy
                $fecha_turno = date('d/m/Y', strtotime($row['FechaTurno']));
                echo "<tr>";
                echo "<td>{$row['ID_Turno']}</td>";
                echo "<td>{$row['Nombre']}</td>";
                echo "<td>{$row['Apellido']}</td>";
                echo "<td>{$fecha_turno}</td>";
                echo "<td>{$row['HoraTurno']}</td>";
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
          FROM HistorialMedico h
          JOIN Pacientes p ON h.ID_Pac = p.ID_Pac
          WHERE h.ID_Med = ?
          ORDER BY h.Fecha DESC
        ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $medico['ID_Med']);
            $stmt->execute();
            $result = $stmt->get_result();

            // Verificar si hay resultados
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                // Formatear la fecha al formato dd/mm/yyyy
                $fecha_historial = date('d/m/Y', strtotime($row['Fecha']));
                echo "<tr>";
                echo "<td>{$row['ID_Historial']}</td>";
                echo "<td>{$row['Nombre']}</td>";
                echo "<td>{$row['Apellido']}</td>";
                echo "<td>{$fecha_historial}</td>";
                echo "<td class='content'>{$row['Descripcion']}</td>";
                echo "<td><a href='editar_informe.php?id={$row['ID_Historial']}' class='btn blue '>Editar</a></td>";
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
            $result = $conn->query($query);

            // Verificar si hay resultados
            if ($result->num_rows > 0) {
              while ($medico = $result->fetch_assoc()) {
                echo "<tr>
                                <td>{$medico['ID_Med']}</td>
                                <td>{$medico['NomMed']}</td>
                                <td>{$medico['ApellidoMed']}</td>
                                <td>{$medico['DirMed']}</td>
                                <td>{$medico['CelMed']}</td>
                                <td>{$medico['EmailMed']}</td>
                                <td>{$medico['GenMed']}</td>
                                <td>{$medico['EspecialidadMed']}</td>
                                <td>
                                    <a href='editar_medico.php?id={$medico['ID_Med']}' class='btn blue'>Editar</a>
                                    <a href='eliminar_medico.php?id={$medico['ID_Med']}' class='btn red' onclick='return confirm(\"¿Estás seguro de eliminar este médico?\")'>Eliminar</a>
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
          <t body>
            <?php
            // Consultar la base de datos para obtener los pacientes
            $query = "SELECT * FROM Pacientes ORDER BY ID_Pac ASC";
            $result = $conn->query($query);

            // Verificar si hay resultados
            if ($result->num_rows > 0) {
              while ($paciente = $result->fetch_assoc()) {
                echo "<tr>
                          <td>{$paciente['ID_Pac']}</td>
                          <td>{$paciente['Nombre']}</td>
                          <td>{$paciente['Apellido']}</td>
                          <td>{$paciente['DirPac']}</td>
                          <td>{$paciente['CelPac']}</td>
                          <td>{$paciente['EmailPac']}</td>
                          <td>{$paciente['GenPac']}</td>
                          <td>{$paciente['DNIPac']}</td>
                          <td>{$paciente['FechaNacimiento']}</td>
                          <td>
                              <a href='editar_paciente.php?id={$paciente['ID_Pac']}' class='btn blue'>Editar</a>
                              <a href='eliminar_paciente.php?id={$paciente['ID_Pac']}' class='btn red' onclick='return confirm(\"¿Estás seguro de eliminar este paciente?\")'>Eliminar</a>
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
            $query = "SELECT * FROM PersonalAdministrativo ORDER BY ID_Admin ASC";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
              while ($admin = $result->fetch_assoc()) {
                echo "<tr>
                <td>" . htmlspecialchars($admin['ID_Admin']) . "</td>
                <td>" . htmlspecialchars($admin['NomAdmin']) . "</td>
                <td>" . htmlspecialchars($admin['ApellidoAdmin']) . "</td>
                <td>" . htmlspecialchars($admin['DirAdmin']) . "</td>
                <td>" . htmlspecialchars($admin['CelAdmin']) . "</td>
                <td>" . htmlspecialchars($admin['EmailAdmin']) . "</td>
                <td>
                    <a href='editar_admin.php?id=" . htmlspecialchars($admin['ID_Admin']) . "' class='btn blue'>Editar</a>
                    <a href='eliminar_admin.php?eliminar=" . htmlspecialchars($admin['ID_Admin']) . "' class='btn red' onclick='return confirm(\"¿Estás seguro de eliminar este administrador?\")'>Eliminar</a>
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
                      FROM Turnos t
                      JOIN PersonalMedico m ON t.ID_Med = m.ID_Med
                      JOIN Pacientes p ON t.ID_Pac = p.ID_Pac
                      ORDER BY t.FechaTurno DESC, t.HoraTurno DESC";
            $result = $conn->query($query);

            // Verificar si hay resultados
            if ($result->num_rows > 0) {
              while ($turno = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$turno['ID_Turno']}</td>
                        <td>{$turno['NomMed']} {$turno['ApellidoMed']}</td>
                        <td>{$turno['Nombre']} {$turno['Apellido']}</td>
                        <td>{$turno['FechaTurno']}</td>
                        <td>{$turno['HoraTurno']}</td>
                        <td>
                          <a href='editar_turno.php?id={$turno['ID_Turno']}' class='btn blue'>Editar</a>
                          <a href='eliminar_turno.php?id={$turno['ID_Turno']}' class='btn red' onclick='return confirm(\"¿Estás seguro de eliminar este turno?\")'>Eliminar</a>
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