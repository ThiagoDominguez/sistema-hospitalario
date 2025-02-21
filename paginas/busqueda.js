$(document).ready(function () {
  // Inicializar List.js para turnos
  var options = {
    valueNames: ["id_turno", "especialidad", "medico", "fecha", "hora"],
  };
  var turnosList = new List("turnos-list", options);

  // Inicializar List.js para resultados
  var optionsResultados = {
    valueNames: ["id_historial", "medico", "descripcion", "archivo", "fecha"],
  };
  var resultadosList = new List("resultados-list", optionsResultados);

  // Inicializar List.js para historial
  var optionsHistorial = {
    valueNames: ["id_historial", "medico", "fecha"],
  };
  var historialList = new List("historial-list", optionsHistorial);

  // Inicializar List.js para mis pacientes
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

  // Inicializar List.js para agenda
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

  // Inicializar List.js para resultados
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

  // Inicializar List.js para gestionar médicos
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

  // Inicializar List.js para gestionar pacientes
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

  // Inicializar List.js para gestionar administradores
  var optionsAdmin = {
    valueNames: ["id", "nombre", "apellido", "email", "celular", "direccion"],
  };
  var adminList = new List("admin-list", optionsAdmin);

  // Inicializar List.js para gestionar turnos
  var optionsGestionTurnos = {
    valueNames: ["id", "medico", "paciente", "fecha", "hora"],
  };
  var gestionarTurnosList = new List(
    "gestionarturnos-list",
    optionsGestionTurnos
  );

 
});

