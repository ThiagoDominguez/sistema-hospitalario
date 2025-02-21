$(document).ready(function () {
  // **Inicializar List.js para turnos**
  // Esta lista se utiliza para gestionar los turnos, permitiendo buscar, ordenar y filtrar por las siguientes columnas:
  // - id_turno: Identificador único del turno.
  // - especialidad: Especialidad médica asociada al turno.
  // - medico: Nombre del médico asignado al turno.
  // - fecha: Fecha del turno.
  // - hora: Hora del turno.
  var options = {
    valueNames: ["id_turno", "especialidad", "medico", "fecha", "hora"],
  };
  var turnosList = new List("turnos-list", options);

  // **Inicializar List.js para resultados**
  // Esta lista se utiliza para gestionar los resultados médicos, permitiendo buscar, ordenar y filtrar por:
  // - id_historial: Identificador único del historial médico.
  // - medico: Médico que generó el resultado.
  // - descripcion: Descripción del resultado.
  // - archivo: Archivo asociado al resultado (por ejemplo, un PDF).
  // - fecha: Fecha del resultado.
  var optionsResultados = {
    valueNames: ["id_historial", "medico", "descripcion", "archivo", "fecha"],
  };
  var resultadosList = new List("resultados-list", optionsResultados);

  // **Inicializar List.js para historial**
  // Esta lista se utiliza para gestionar el historial médico, permitiendo buscar, ordenar y filtrar por:
  // - id_historial: Identificador único del historial.
  // - medico: Médico asociado al historial.
  // - fecha: Fecha del historial.
  var optionsHistorial = {
    valueNames: ["id_historial", "medico", "fecha"],
  };
  var historialList = new List("historial-list", optionsHistorial);

  // **Inicializar List.js para mis pacientes**
  // Esta lista se utiliza para gestionar los pacientes del médico, permitiendo buscar, ordenar y filtrar por:
  // - id_paciente: Identificador único del paciente.
  // - nombre: Nombre del paciente.
  // - apellido: Apellido del paciente.
  // - fecha_nacimiento: Fecha de nacimiento del paciente.
  // - genero: Género del paciente.
  var optionsPacientes = {
    valueNames: [
      "id_paciente",
      "nombre",
      "apellido",
      "fecha_nacimiento",
      "genero",
    ],
  };
  var pacientesList = new List("pacientes-list", optionsPacientes);

  // **Inicializar List.js para agenda**
  // Esta lista se utiliza para gestionar la agenda del médico, permitiendo buscar, ordenar y filtrar por:
  // - id_turno: Identificador único del turno.
  // - nombre_paciente: Nombre del paciente.
  // - apellido_paciente: Apellido del paciente.
  // - fecha_turno: Fecha del turno.
  // - hora_turno: Hora del turno.
  var optionsAgenda = {
    valueNames: [
      "id_turno",
      "nombre_paciente",
      "apellido_paciente",
      "fecha_turno",
      "hora_turno",
    ],
  };
  var agendaList = new List("agenda-list", optionsAgenda);

  // **Inicializar List.js para informes**
  // Esta lista se utiliza para gestionar los informes médicos, permitiendo buscar, ordenar y filtrar por:
  // - id_historial: Identificador único del historial médico.
  // - nombre_paciente: Nombre del paciente.
  // - apellido_paciente: Apellido del paciente.
  // - medico: Médico que generó el informe.
  // - contenido_historial: Contenido del historial médico.
  // - archivo: Archivo asociado al informe.
  // - fecha_historial: Fecha del informe.
  var optionsInformes = {
    valueNames: [
      "id_historial",
      "nombre_paciente",
      "apellido_paciente",
      "medico",
      "contenido_historial",
      "archivo",
      "fecha_historial",
    ],
  };
  var informesList = new List("informes-list", optionsInformes);

  // **Inicializar List.js para gestionar médicos**
  // Esta lista se utiliza para gestionar los médicos, permitiendo buscar, ordenar y filtrar por:
  // - id: Identificador único del médico.
  // - nombre: Nombre del médico.
  // - apellido: Apellido del médico.
  // - direccion: Dirección del médico.
  // - celular: Número de celular del médico.
  // - email: Correo electrónico del médico.
  // - genero: Género del médico.
  // - especialidad: Especialidad médica del médico.
  var optionsMedicos = {
    valueNames: [
      "id",
      "nombre",
      "apellido",
      "direccion",
      "celular",
      "email",
      "genero",
      "especialidad",
    ],
  };
  var medicosList = new List("medicos-list", optionsMedicos);

  // **Inicializar List.js para gestionar pacientes**
  // Esta lista se utiliza para gestionar los pacientes, permitiendo buscar, ordenar y filtrar por:
  // - id: Identificador único del paciente.
  // - nombre: Nombre del paciente.
  // - apellido: Apellido del paciente.
  // - direccion: Dirección del paciente.
  // - celular: Número de celular del paciente.
  // - email: Correo electrónico del paciente.
  // - genero: Género del paciente.
  // - dni: Documento Nacional de Identidad del paciente.
  // - fecha_nacimiento: Fecha de nacimiento del paciente.
  var optionsGestionPacientes = {
    valueNames: [
      "id",
      "nombre",
      "apellido",
      "direccion",
      "celular",
      "email",
      "genero",
      "dni",
      "fecha_nacimiento",
    ],
  };
  var gestionarPacientesList = new List(
    "gestionarpacientes-list",
    optionsGestionPacientes
  );

  // **Inicializar List.js para gestionar administradores**
  // Esta lista se utiliza para gestionar los administradores, permitiendo buscar, ordenar y filtrar por:
  // - id: Identificador único del administrador.
  // - nombre: Nombre del administrador.
  // - apellido: Apellido del administrador.
  // - email: Correo electrónico del administrador.
  // - celular: Número de celular del administrador.
  // - direccion: Dirección del administrador.
  var optionsAdmin = {
    valueNames: ["id", "nombre", "apellido", "email", "celular", "direccion"],
  };
  var adminList = new List("admin-list", optionsAdmin);

  // **Inicializar List.js para gestionar turnos**
  // Esta lista se utiliza para gestionar los turnos, permitiendo buscar, ordenar y filtrar por:
  // - id: Identificador único del turno.
  // - medico: Médico asignado al turno.
  // - paciente: Paciente asignado al turno.
  // - fecha: Fecha del turno.
  // - hora: Hora del turno.
  var optionsGestionTurnos = {
    valueNames: ["id", "medico", "paciente", "fecha", "hora"],
  };
  var gestionarTurnosList = new List(
    "gestionarturnos-list",
    optionsGestionTurnos
  );
});
