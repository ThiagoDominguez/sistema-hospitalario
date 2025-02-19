<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../config.php");

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

// Obtener la lista de todos los pacientes
$query_pacientes = "
  SELECT p.ID_Pac, p.Nombre, p.Apellido
  FROM pacientes p
  ORDER BY p.Nombre, p.Apellido
";
$stmt_pacientes = $pdo->query($query_pacientes);
$pacientes = $stmt_pacientes->fetchAll(PDO::FETCH_ASSOC);

// Generar opciones de pacientes para los formularios
$pacientesOptions = '';
foreach ($pacientes as $paciente_item) {
  $pacientesOptions .= "<option value=\"" . htmlspecialchars($paciente_item['ID_Pac']) . "\">" . htmlspecialchars($paciente_item['Nombre'] . ' ' . $paciente_item['Apellido']) . "</option>";
}

// Obtener la lista de todos los médicos
$query_medicos = "SELECT ID_Med, NomMed, ApellidoMed FROM personalmedico ORDER BY NomMed, ApellidoMed";
$stmt_medicos = $pdo->query($query_medicos);
$medicos = $stmt_medicos->fetchAll(PDO::FETCH_ASSOC);

// Generar opciones de médicos para el formulario
$medicosOptions = '';
foreach ($medicos as $medico_item) {
  $medicosOptions .= "<option value='" . htmlspecialchars($medico_item['ID_Med']) . "'>" . htmlspecialchars($medico_item['NomMed'] . ' ' . $medico_item['ApellidoMed']) . "</option>";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="../assets/icons/001-dashboard.png" type="image/x-icon">
  <link rel="stylesheet" href="../css/styles.css?v=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="../butterup/butterup.css">
  <script src="../butterup/butterup.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>
  <script>
    var medicosOptions = `<?php echo $medicosOptions; ?>`;
    var pacientesOptions = `<?php echo $pacientesOptions; ?>`;
  </script>

  <script src="modales.js"></script>
  <script src="busqueda.js"></script>
  <title>Panel</title>
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
    <?php
    $seccion_actual = isset($_GET['seccion']) ? $_GET['seccion'] : '';
    if ($rol_id == 1): // Paciente 
      ?>
      <h2><?php echo htmlspecialchars($paciente['Nombre'] . ' ' . $paciente['Apellido']); ?>
      </h2>
      <button id="btnVerNotificaciones" class="btn blue"><i class="fa-solid fa-bell"></i></button>
      <hr>
      <a href="dashboard.php?seccion=turnos" class="<?php echo $seccion_actual == 'turnos' ? 'active' : ''; ?>">Solicitar
        Turnos</a>
      <a href="dashboard.php?seccion=resultados"
        class="<?php echo $seccion_actual == 'resultados' ? 'active' : ''; ?>">Resultados Estudios</a>
      <a href="dashboard.php?seccion=historial"
        class=" <?php echo $seccion_actual == 'historial' ? 'active' : ''; ?>">Historial Atención</a>

    <?php elseif ($rol_id == 2): // Medico 
      ?>
      <h2><?php echo htmlspecialchars($medico['NomMed'] . ' ' . $medico['ApellidoMed']); ?>
      </h2>
      <hr>

      <a href="dashboard.php?seccion=mis_pacientes"
        class="<?php echo $seccion_actual == 'mis_pacientes' ? 'active' : ''; ?>">Mis Pacientes</a>
      <a href="dashboard.php?seccion=agenda" class="<?php echo $seccion_actual == 'agenda' ? 'active' : ''; ?>">Agenda</a>
      <a href="dashboard.php?seccion=informes"
        class="<?php echo $seccion_actual == 'informes' ? 'active' : ''; ?>">Informes Médicos</a>

    <?php elseif ($rol_id == 3): // Admin 
      ?>
      <h2><?php echo $admin['NomAdmin'] . ' ' . $admin['ApellidoAdmin']; ?>
      </h2>

      <hr>

      <a href="dashboard.php?seccion=gestionar_pacientes"
        class=" <?php echo $seccion_actual == 'gestionar_pacientes' ? 'active' : ''; ?>">Gestionar Pacientes</a>
      <a href="dashboard.php?seccion=gestionar_medicos"
        class=" <?php echo $seccion_actual == 'gestionar_medicos' ? 'active' : ''; ?>">Gestionar Médicos</a>
      <a href="dashboard.php?seccion=gestionar_turnos"
        class=" <?php echo $seccion_actual == 'gestionar_turnos' ? 'active' : ''; ?>">Gestionar Turnos</a>
      <a href="dashboard.php?seccion=gestionar_admin"
        class=" <?php echo $seccion_actual == 'gestionar_admin' ? 'active' : ''; ?>">Gestionar Administrador</a>
    <?php endif ?>
    <a href="logout.php">Cerrar Sesión</a>
  </div>

  <div class="main">
    <!-- ANCHOR PACIENTE -->
    <!-- Sección para solicitar un turno -->
    <?php if ($seccion_activa == "turnos"): ?>
      <div class="section">
        <h3>Solicitar turnos</h3>
        <button id="btnSolicitarTurno" class="btn green">Registrar Turno</button>
        <h3>Ver Turnos Solicitados</h3>
        <div id="turnos-list">
          <input class="search" placeholder="Buscar por nombre, fecha, etc." />
          <div class="table-container">
            <table class="styled-table">
              <thead>
                <tr>
                  <th class="sort" data-sort="id_turno">ID Turno <i class="icon-table fa-solid fa-sort"></i></th>
                  <th class="sort" data-sort="medico">Médico <i class="icon-table fa-solid fa-sort"></i></th>
                  <th class="sort" data-sort="especialidad">Especialidad <i class="icon-table fa-solid fa-sort"></i></th>
                  <th class="sort" data-sort="fecha">Fecha <i class="icon-table fa-solid fa-sort"></i></th>
                  <th class="sort" data-sort="hora">Hora <i class="icon-table fa-solid fa-sort"></i></th>
                  <th>Accion</th>
                </tr>
              </thead>
              <tbody class="list">
                <?php
                // Obtener la lista de turnos solicitados por el paciente
                $query_turnos = "
                  SELECT t.ID_Turno, m.NomMed AS NombreMedico, m.ApellidoMed AS ApellidoMedico,m.EspecialidadMed, t.FechaTurno, t.HoraTurno,t.estado
                  FROM turnos t
                  JOIN personalmedico m ON t.ID_Med = m.ID_Med
                  WHERE t.ID_Pac = ?
                  ORDER BY FIELD(t.estado, 'pendiente', 'completado','cancelado'), t.FechaTurno DESC, t.HoraTurno DESC
                ";
                $stmt_turnos = $pdo->prepare($query_turnos);
                $stmt_turnos->execute([$id_pac]);
                $result_turnos = $stmt_turnos->fetchAll(PDO::FETCH_ASSOC);

                // Verificar si hay resultados
                if (count($result_turnos) > 0) {
                  foreach ($result_turnos as $row) {
                    // Formatear la fecha al formato dd/mm/yyyy
                    $fecha_turno = date('d/m/Y', strtotime($row['FechaTurno']));
                    $estado = $row['estado'] === 'completado' ? 'tachado' : ($row['estado'] === 'cancelado' ? 'cancelado' : '');
                    echo "<tr>";
                    echo "<tr class='$estado'>";
                    echo "<td class='id_turno'>" . htmlspecialchars($row['ID_Turno']) . "</td>";
                    echo "<td class='medico'>" . htmlspecialchars($row['NombreMedico'] . " " . htmlspecialchars($row['ApellidoMedico'])) . "</td>";
                    echo "<td class='especialidad'>" . htmlspecialchars($row['EspecialidadMed']) . "</td>";
                    echo "<td class='fecha'>" . htmlspecialchars($fecha_turno) . "</td>";
                    echo "<td class='hora'>" . htmlspecialchars($row['HoraTurno']) . "</td>";
                    echo "<td> <button class='btn red btnCancelarTurno' data-id='" . htmlspecialchars($row['ID_Turno']) . "'><i class='fa-solid fa-times'></i></button></td";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='4' style='text-align:center;'>No hay turnos solicitados.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif ?>



    <!-- Sección para Ver Resultados de Estudios -->
    <?php if ($seccion_activa == "resultados"): ?>
      <div class="section">
        <h3>Resultados de Estudios</h3>
        <div id="resultados-list">
          <input class="search" placeholder="Buscar por nombre, fecha, etc." />
          <div class="table-container">
            <table class="styled-table">
              <thead>
                <tr>
                  <th class="sort" data-sort="id_historial">ID Historial <i class="icon-table fa-solid fa-sort"></i></th>
                  <th class="sort" data-sort="medico">Médico <i class="icon-table fa-solid fa-sort"></i></th>
                  <th class="sort" data-sort="descripcion">Descripción <i class="icon-table fa-solid fa-sort"></i></th>
                  <th class="sort" data-sort="archivo">Archivo <i class="icon-table fa-solid fa-sort"></i></th>
                  <th class="sort" data-sort="fecha">Fecha <i class="icon-table fa-solid fa-sort"></i></th>
                </tr>
              </thead>
              <tbody class="list">
                <?php
                // Obtener la lista de resultados de estudios del paciente
                $query_resultados = "
                  SELECT h.ID_Historial, h.Descripcion, h.Fecha, h.archivo_pdf, CONCAT(m.NomMed, ' ', m.ApellidoMed) AS NombreMedico
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
                    echo "<td class='id_historial'>" . htmlspecialchars($row['ID_Historial']) . "</td>";
                    echo "<td class='medico'>" . htmlspecialchars($row['NombreMedico']) . "</td>";
                    echo "<td class='descripcion'>" . htmlspecialchars($row['Descripcion']) . "</td>";
                    echo "<td class='archivo'>";
                    if (!empty($row['archivo_pdf'])) {
                      echo " <a href='" . htmlspecialchars($row['archivo_pdf']) . "' target='_blank' class='btn black'><i class='fa-solid fa-file-pdf'></i></a>";
                    }
                    echo "</td>";
                    echo "<td class='fecha'>" . htmlspecialchars($fecha_estudio) . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='5' style='text-align:center;'>No hay resultados de estudios.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>



    <!-- Sección para Ver Historial de Atención -->
    <?php if ($seccion_activa == "historial"): ?>
      <div class="section">
        <h3>Historial de Atención</h3>
        <div id="historial-list">
          <input class="search" placeholder="Buscar por nombre, fecha, etc." />
          <div class="table-container">
            <table class="styled-table">
              <thead>
                <tr>
                  <th class="sort" data-sort="id_historial">ID Historial <i class="icon-table fa-solid fa-sort"></i></th>
                  <th class="sort" data-sort="medico">Médico <i class="icon-table fa-solid fa-sort"></i></th>
                  <th class="sort" data-sort="fecha">Fecha <i class="icon-table fa-solid fa-sort"></i></th>
                </tr>
              </thead>
              <tbody class="list">
                <?php
                // Obtener la lista de historial de atención del paciente
                $query_historial = "
                  SELECT h.ID_Historial, h.Fecha, h.Descripcion, CONCAT(m.NomMed, ' ', m.ApellidoMed) AS NombreMedico
                  FROM historialmedico h
                  JOIN personalmedico m ON h.ID_Med = m.ID_Med
                  JOIN turnos t ON h.ID_Turno = t.ID_Turno
  WHERE h.ID_Pac = ? AND t.estado = 'completado'
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
                    echo "<td class='id_historial'>" . htmlspecialchars($row['ID_Historial']) . "</td>";
                    echo "<td class='medico'>" . htmlspecialchars($row['NombreMedico']) . "</td>";
                    echo "<td class='fecha'>" . htmlspecialchars($fecha_atencion) . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='3' style='text-align:center;'>No hay historial de atención.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>



    <!-- ANCHOR MEDICO -->

    <!-- Sección de Mis Pacientes -->
    <?php if ($seccion_activa == "mis_pacientes"): ?>
      <div class="section">
        <h3>Mis Pacientes</h3>
        <div id="pacientes-list">
          <input class="search" placeholder="Buscar por nombre, fecha, etc." />
          <div class="table-container">
            <table class="styled-table">
              <thead>
                <tr>
                  <th class="sort" data-sort="id_paciente">ID Paciente <i class="icon-table fa-solid fa-sort"></i></th>
                  <th class="sort" data-sort="nombre">Nombre <i class="icon-table fa-solid fa-sort"></i></th>
                  <th class="sort" data-sort="apellido">Apellido <i class="icon-table fa-solid fa-sort"></i></th>
                  <th class="sort" data-sort="fecha_nacimiento">Fecha de Nacimiento <i
                      class="icon-table fa-solid fa-sort"></i>
                  </th>
                  <th class="sort" data-sort="genero">Género <i class="icon-table fa-solid fa-sort"></i></th>
                </tr>
              </thead>
              <tbody class="list">
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
                    echo "<td class='id_paciente'>" . htmlspecialchars($row['ID_Pac']) . "</td>";
                    echo "<td class='nombre'>" . htmlspecialchars($row['Nombre']) . "</td>";
                    echo "<td class='apellido'>" . htmlspecialchars($row['Apellido']) . "</td>";
                    echo "<td class='fecha_nacimiento'>" . htmlspecialchars($fecha_nacimiento) . "</td>";
                    echo "<td class='genero'>" . htmlspecialchars($row['GenPac']) . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='5' style='text-align:center;'>No hay pacientes registrados.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>



    <!-- Sección de Agenda del Médico -->
    <?php if ($seccion_activa == "agenda"): ?>
      <div class="section">
        <h3>Agenda del Médico</h3>
        <div id="agenda-list">
          <input class="search" placeholder="Buscar por nombre, fecha, etc." />
          <div class="table-container">
            <table class="styled-table">
              <thead>
                <tr>
                  <th class="sort" data-sort="id_turno">ID Turno <i class="icon-table fa-solid fa-sort"></i></th>
                  <th class="sort" data-sort="nombre_paciente">Nombre del Paciente <i
                      class="icon-table fa-solid fa-sort"></i></th>
                  <th class="sort" data-sort="apellido_paciente">Apellido del Paciente <i
                      class="icon-table fa-solid fa-sort"></i></th>
                  <th class="sort" data-sort="fecha_turno">Fecha del Turno <i class="icon-table fa-solid fa-sort"></i>
                  </th>
                  <th class="sort" data-sort="hora_turno">Hora del Turno <i class="icon-table fa-solid fa-sort"></i></th>
                  <th>Accion</th>
                </tr>
              </thead>
              <tbody class="list">
                <?php
                // Reutilizar la consulta SQL para obtener los turnos del médico actual
                $query = "
                  SELECT t.ID_Turno, p.Nombre, p.Apellido, t.FechaTurno, t.HoraTurno, t.estado
                  FROM turnos t
                  JOIN pacientes p ON t.ID_Pac = p.ID_Pac
                  WHERE t.ID_Med = ?
                  ORDER BY FIELD(t.estado, 'pendiente', 'completado','cancelado'), t.FechaTurno, t.HoraTurno
                ";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$medico['ID_Med']]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Verificar si hay resultados
              
                if (count($result) > 0) {
                  foreach ($result as $row) {
                    // Formatear la fecha al formato dd/mm/yyyy
                    $fecha_turno = date('d/m/Y', strtotime($row['FechaTurno']));
                    $estado = $row['estado'] === 'completado' ? 'tachado' : ($row['estado'] === 'cancelado' ? 'cancelado' : '');
                    echo "<tr class='$estado'>";
                    echo "<td class='id_turno'>" . htmlspecialchars($row['ID_Turno']) . "</td>";
                    echo "<td class='nombre_paciente'>" . htmlspecialchars($row['Nombre']) . "</td>";
                    echo "<td class='apellido_paciente'>" . htmlspecialchars($row['Apellido']) . "</td>";
                    echo "<td class='fecha_turno'>" . htmlspecialchars($fecha_turno) . "</td>";
                    echo "<td class='hora_turno'>" . htmlspecialchars($row['HoraTurno']) . "</td>";
                    echo "<td>
              <button class='btn blue btnCompletarTurno' data-id='" . htmlspecialchars($row['ID_Turno']) . "'><i class='fa-solid fa-circle-check'></i></button>
              <button class='btn red btnCancelarTurno' data-id='" . htmlspecialchars($row['ID_Turno']) . "'><i class='fa-solid fa-times'></i></button>
            </td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='6' style='text-align:center;'>No hay turnos registrados.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>



    <!-- SECTION de Historial Médico -->

    <?php if ($seccion_activa == "informes"): ?>
      <div class="section">
        <h3>Historial Médico</h3>
        <button id="btnRegistrarInforme" class="btn green">Registrar nuevo
          Informe</button>
        <div id="informes-list">
          <input class="search" placeholder="Buscar por nombre, fecha, etc." />
          <div class="table-container">
            <h4>Listado de Informes</h4>
            <table class="styled-table">
              <thead>
                <tr>
                  <th class="sort" data-sort="id_historial">
                    <div class="th-content">
                      <span>ID Historial</span>
                      <i class="icon-table fa-solid fa-sort"></i>
                    </div>
                  </th>
                  <th class="sort" data-sort="nombre_paciente">
                    <div class="th-content">
                      <span>Nombre del Paciente</span>
                      <i class="icon-table fa-solid fa-sort"></i>
                    </div>
                  </th>
                  <th class="sort" data-sort="apellido_paciente">
                    <div class="th-content">
                      span>Apellido del
                      Paciente</span> <i class="icon-table fa-solid fa-sort"></i>
                    </div>
                  </th>
                  <th class="sort" data-sort="fecha_historial">
                    <div class="th-content">
                      <span>Fecha del Historial</span>
                      <i class="icon-table fa-solid fa-sort"></i>
                    </div>
                  </th>
                  <th class="sort" data-sort="contenido_historial">
                    <div class="th-content">
                      <span>Contenido del
                        Historial</span> <i class="icon-table fa-solid fa-sort"></i>
                    </div>
                  </th>
                  <th class="sort" data-sort="archivo">
                    <div class="th-content">
                      <span>Archivo</span> <i class="icon-table fa-solid fa-sort"></i>
                    </div>
                  </th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody class="list">
                <?php
                // Reutilizar la consulta SQL para obtener el historial médico del médico actual
                $query = "
                  SELECT h.ID_Historial, p.Nombre, p.Apellido, h.Fecha, h.Descripcion, h.archivo_pdf
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
                    echo "<td class='id_historial'>" . htmlspecialchars($row['ID_Historial']) . "</td>";
                    echo "<td class='nombre_paciente'>" . htmlspecialchars($row['Nombre']) . "</td>";
                    echo "<td class='apellido_paciente'>" . htmlspecialchars($row['Apellido']) . "</td>";
                    echo "<td class='fecha_historial'>" . htmlspecialchars($fecha_historial) . "</td>";
                    echo "<td class='contenido_historial'>" . htmlspecialchars($row['Descripcion']) . "</td>";
                    echo "<td class='archivo'>";
                    if (!empty($row['archivo_pdf'])) {
                      echo " <a href='" . htmlspecialchars($row['archivo_pdf']) . "' target='_blank' class='btn black'><i class='fa-solid fa-file-pdf'></i></a>";
                    }
                    echo "</td>";
                    echo "<td><button class='btnEditarInforme btn blue' data-id='" . htmlspecialchars($row['ID_Historial']) . "'><i class='fa-solid fa-edit'></i></button></td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='7' style='text-align:center;'>No hay registros de historial médico.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <!-- !SECTION -->

    <!--ANCHOR ADMINISTRADOR   -->


    <!-- Sección de Gestión de Médicos -->
    <?php if ($seccion_activa == "gestionar_medicos"): ?>
      <div class="section">
        <h3>Gestión de Médicos</h3>
        <button id="btnRegistrarMedico" class="btn green">Registrar
          Médico</button>
        <h4>Listado de Médicos Registrados</h4>
        <div id="medicos-list">
          <input class="search" placeholder="Buscar por nombre, especialidad, etc." />
          <div class="table-container">
            <table class="styled-table">
              <thead>
                <tr>
                  <div class="th-content"></div>
                  <th class="sort" data-sort="id">
                    <div class="th-content">
                      <span>ID</span>
                      <i class="icon-table fa-solid fa-sort"></i>

                    </div>
                  </th>
                  <th class="sort" data-sort="nombre">
                    <div class="th-content">
                      <span>Nombre</span>
                      <i class="icon-table fa-solid fa-sort"></i>
                    </div>
                  </th>
                  <th class="sort" data-sort="apellido">
                    <div class="th-content">
                      <span>Apellido</span>
                      <i class="icon-table fa-solid fa-sort"></i>
                    </div>
                  </th>
                  <th class="sort" data-sort="direccion">
                    <div class="th-content">
                      <span>Dirección</span>
                      <i class="icon-table fa-solid fa-sort"></i>

                    </div>
                  </th>
                  <th class="sort" data-sort="celular">
                    <div class="th-content">
                      <span>Celular</span>
                      <i class="icon-table fa-solid fa-sort"></i>

                    </div>
                  </th>
                  <th class="sort" data-sort="email">
                    <div class="th-content">
                      <span>Email</span>
                      <i class="icon-table fa-solid fa-sort"></i>

                    </div>
                  </th>
                  <th class="sort" data-sort="genero">
                    <div class="th-content">
                      <span>Género

                      </span>
                      <i class="icon-table fa-solid fa-sort"></i>

                    </div>
                  </th>
                  <th class="sort" data-sort="especialidad">
                    <div class="th-content">
                      <span>Especialidad

                      </span>
                      <i class="icon-table fa-solid fa-sort"></i>

                    </div>
                  </th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody class="list">
                <?php
                $query = "SELECT * FROM personalmedico ORDER BY ID_Med ASC";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($result) > 0) {
                  foreach ($result as $medico) {
                    echo "<tr>
                            <td class='id'>" . htmlspecialchars($medico['ID_Med']) . "</td>
                            <td class='nombre'>" . htmlspecialchars($medico['NomMed']) . "</td>
                            <td class='apellido'>" . htmlspecialchars($medico['ApellidoMed']) . "</td>
                            <td class='direccion'>" . htmlspecialchars($medico['DirMed']) . "</td>
                            <td class='celular'>" . htmlspecialchars($medico['CelMed']) . "</td>
                            <td class='email'>" . htmlspecialchars($medico['EmailMed']) . "</td>
                            <td class='genero'>" . htmlspecialchars($medico['GenMed']) . "</td>
                            <td class='especialidad'>" . htmlspecialchars($medico['EspecialidadMed']) . "</td>
                            <td> <button class='btn blue btnEditarMedico' data-id='" . htmlspecialchars($medico['ID_Med']) . "'><i class='fa-solid fa-edit' ></i></button>
                                <button class='btn red btnEliminarMedico' data-id='" . htmlspecialchars($medico['ID_Med']) . "'><i class='fa-solid fa-trash' ></i></button>
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
        </div>
      </div>
    <?php endif; ?>





    <!-- Sección de Gestión de Pacientes -->
    <?php if ($seccion_activa == "gestionar_pacientes"): ?>
      <div class="section">
        <h3>Gestión de Pacientes</h3>
        <button id="btnRegistrarPaciente" class="btn green">Registrar Paciente</button>
        <h4>Listado de Pacientes Registrados</h4>
        <div id="gestionarpacientes-list">
          <input class="search" placeholder="Buscar por nombre, especialidad, etc." />
          <div class="table-container">
            <table class="styled-table">
              <thead>
                <tr>
                  <th class="sort" data-sort="id">
                    <div class="th-content">
                      <span>
                        ID

                      </span>
                      <i class="icon-table fa-solid fa-sort"></i>
                    </div>
                  </th>
                  <th class="sort" data-sort="nombre">
                    <div class="th-content">
                      <span>
                        Nombre

                      </span>
                      <i class="icon-table fa-solid fa-sort"></i>
                    </div>
                  </th>
                  <th class="sort" data-sort="apellido">
                    <div class="th-content">
                      <span>
                        Apellido

                      </span>
                      <i class="icon-table fa-solid fa-sort"></i>
                    </div>
                  </th>
                  <th class="sort" data-sort="direccion">
                    <div class="th-content">
                      <span>
                        Dirección

                      </span>
                      <i class="icon-table fa-solid fa-sort"></i>
                    </div>
                  </th>
                  <th class="sort" data-sort="celular">
                    <div class="th-content">
                      <span>
                        Celular

                      </span>
                      <i class="icon-table fa-solid fa-sort"></i>
                    </div>
                  </th>
                  <th class="sort" data-sort="email">
                    <div class="th-content">
                      <span>
                        Email

                      </span>
                      <i class="icon-table fa-solid fa-sort"></i>
                    </div>
                  </th>
                  <th class="sort" data-sort="genero">
                    <div class="th-content">
                      <span>
                        Género

                      </span>
                      <i class="icon-table fa-solid fa-sort"></i>
                    </div>
                  </th>
                  <th class="sort" data-sort="dni">
                    <div class="th-content">
                      <span>
                        DNI

                      </span>
                      <i class="icon-table fa-solid fa-sort"></i>
                    </div>
                  </th>
                  <th class="sort" data-sort="fecha_nacimiento">
                    <div class="th-content">
                      <span>
                        Fecha de Nacimiento

                      </span>
                      <i class="icon-table fa-solid fa-sort"></i>
                    </div>
                  </th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody class="list">
                <?php
                $query = "SELECT * FROM pacientes ORDER BY ID_Pac ASC";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($result) > 0) {
                  foreach ($result as $paciente) {
                    echo "<tr>
                              <td class='id'>" . htmlspecialchars($paciente['ID_Pac']) . "</td>
                              <td class='nombre'>" . htmlspecialchars($paciente['Nombre']) . "</td>
                              <td class='apellido'>" . htmlspecialchars($paciente['Apellido']) . "</td>
                              <td class='direccion'>" . htmlspecialchars($paciente['DirPac']) . "</td>
                              <td class='celular'>" . htmlspecialchars($paciente['CelPac']) . "</td>
                              <td class='email'>" . htmlspecialchars($paciente['EmailPac']) . "</td>
                              <td class='genero'>" . htmlspecialchars($paciente['GenPac']) . "</td>
                              <td class='dni'>" . htmlspecialchars($paciente['DNIPac']) . "</td>
                              <td class='fecha_nacimiento'>" . htmlspecialchars($paciente['FechaNacimiento']) . "</td>
                              <td>
                                  <button class='btn blue btnEditarPaciente' data-id='" . htmlspecialchars($paciente['ID_Pac']) . "'><i class='fa-solid fa-edit' ></i></button>
                                  <button class='btn red btnEliminarPaciente' data-id='" . htmlspecialchars($paciente['ID_Pac']) . "'><i class='fa-solid fa-trash'></i></button>
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
        </div>
      </div>
    <?php endif; ?>



    <!-- Sección de Gestión de Administradores -->
    <?php if ($seccion_activa == "gestionar_admin"): ?>
      <div class="section">
        <h3>Gestión de Administradores</h3>
        <button id="btnRegistrarAdmin" class="btn green">Registrar
          Administrador</button>
        <h4>Listado de Administradores</h4>
        <div id="admin-list">
          <input class="search" placeholder="Buscar por nombre, email, etc." />
          <div class="table-container">
            <table class="styled-table">
              <thead>
                <tr>
                  <th class="sort" data-sort="id">ID <i class="icon-table fa-solid fa-sort"></i>
                  </th>
                  <th class="sort" data-sort="nombre">
                    Nombre <i class="icon-table fa-solid fa-sort"></i>
                  </th>
                  <th class="sort" data-sort="apellido">
                    Apellido <i class="icon-table fa-solid fa-sort"></i>
                  </th>
                  <th class="sort" data-sort="email">Email
                    <i class="icon-table fa-solid fa-sort"></i>
                  </th>
                  <th class="sort" data-sort="celular">
                    Celular <i class="icon-table fa-solid fa-sort"></i>
                  </th>
                  <th class="sort" data-sort="direccion">
                    Dirección <i class="icon-table fa-solid fa-sort"></i>
                  </th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody class="list">
                <?php
                // Consultar la base de datos para obtener los administradores
                $query = "SELECT * FROM personaladministrativo ORDER BY NomAdmin";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Verificar si hay resultados
                if (count($result) > 0) {
                  foreach ($result as $admin) {
                    echo "<tr>
                      <td class='id'>" . htmlspecialchars($admin['ID_Admin']) . "</td>
                      <td class='nombre'>" . htmlspecialchars($admin['NomAdmin']) . "</td>
                      <td class='apellido'>" . htmlspecialchars($admin['ApellidoAdmin']) . "</td>
                      <td class='email'>" . htmlspecialchars($admin['EmailAdmin']) . "</td>
                      <td class='celular'>" . htmlspecialchars($admin['CelAdmin']) . "</td>
                      <td class='direccion'>" . htmlspecialchars($admin['DirAdmin']) . "</td>
                      <td>
                      <button class='btn blue btnEditarAdmin' data-id='" . htmlspecialchars($admin['ID_Admin']) . "'><i class='fa-solid fa-edit'></i></button>
                      <button class='btn red btnEliminarAdmin' data-id='" . htmlspecialchars($admin['ID_Admin']) . "'><i class='fa-solid fa-trash'></i></button>
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
        </div>
      </div>
    <?php endif; ?>





    <!-- Sección de Gestión de Turnos -->
    <!-- Sección de Gestión de Turnos -->
    <?php if ($seccion_activa == "gestionar_turnos"): ?>
      <div class="section">
        <h3>Gestión de Turnos</h3>
        <button id="btnRegistrarTurno" class="btn green">Registrar
          Turno</button>
        <h4>Listado de Turnos</h4>
        <div id="gestionarturnos-list">
          <input class="search" placeholder="Buscar por nombre, fecha, etc." />
          <div class="table-container">
            <table class="styled-table">
              <thead>
                <tr>
                  <th class="sort" data-sort="id">
                    ID <i class="icon-table fa-solid fa-sort"></i>
                  </th>
                  <th class="sort" data-sort="medico">Médico <i class="icon-table fa-solid fa-sort"></i>
                  </th>
                  <th class="sort" data-sort="paciente">Paciente
                    <i class="icon-table fa-solid fa-sort"></i>
                  </th>
                  <th class="sort" data-sort="fecha">Fecha <i class="icon-table fa-solid fa-sort"></i>
                  </th>
                  <th class="sort" data-sort="hora">Hora <i class="icon-table fa-solid fa-sort"></i>
                  </th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody class="list">
                <?php
                // Consultar la base de datos para obtener los turnos
                $query = "
                  SELECT t.ID_Turno, t.FechaTurno, t.HoraTurno,t.estado, m.NomMed, m.ApellidoMed, p.Nombre, p.Apellido 
                  FROM turnos t
                  JOIN personalmedico m ON t.ID_Med = m.ID_Med
                  JOIN pacientes p ON t.ID_Pac = p.ID_Pac
                  ORDER BY FIELD(t.estado, 'pendiente', 'completado','cancelado'), t.FechaTurno DESC, t.HoraTurno DESC
                ";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Verificar si hay resultados
                if (count($result) > 0) {
                  foreach ($result as $turno) {
                    $estado = $turno['estado'] === 'completado' ? 'tachado' : ($turno['estado'] === 'cancelado' ? 'cancelado' : '');
                    echo "<tr>";
                    echo " <tr class='$estado'>
                      <td class='id'>" . htmlspecialchars($turno['ID_Turno']) . "</td>
                      <td class='medico'>" . htmlspecialchars($turno['NomMed'] . ' ' . $turno['ApellidoMed']) . "</td>
                      <td class='paciente'>" . htmlspecialchars($turno['Nombre'] . ' ' . $turno['Apellido']) . "</td>
                      <td class='fecha'>" . htmlspecialchars($turno['FechaTurno']) . "</td>
                      <td class='hora'>" . htmlspecialchars($turno['HoraTurno']) . "</td>
                      <td>
                        <button class='btn blue btnEditarTurno' data-id='" . htmlspecialchars($turno['ID_Turno']) . "'><i class='fa-solid fa-edit'></i></button>
                        <button class='btn red btnEliminarTurno' data-id='" . htmlspecialchars($turno['ID_Turno']) . "'><i class='fa-solid fa-trash'></i></button>
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
        </div>
      </div>
    <?php endif; ?>
  </div>



</body>

</html>