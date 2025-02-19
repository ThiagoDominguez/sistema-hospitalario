// ANCHOR MEDICOS
// Registrar
$(document).ready(function () {
  // Registrar Médico
  $("#btnRegistrarMedico").on("click", function () {
    Swal.fire({
      grow: "row",
      position: "top",
      background: "transparent",
      html: `
        <div class="container_both-inp">
          <form id="registerForm" method="POST" class="form_both-inp">
            <div class="both-inp">
              <div class="input-group">
                <input type="text" id="nombre" name="Nombre" placeholder="Nombre" required>
              </div>
              <div class="input-group">
                <input type="text" id="apellido" name="Apellido" placeholder="Apellido" required>
              </div>
            </div>
            <div class="both-inp">
              <div class="input-group">
                <input type="email" id="email" name="EmailMed" placeholder="Email" required>
              </div>
              <div class="input-group">
                <input type="password" id="contraseña" name="Contraseña" placeholder="Contraseña" required>
              </div>
            </div>
            <div class="both-inp">
              <div class="input-group">
                <input type="text" id="dir" name="DirMed" placeholder="Dirección" required>
              </div>
              <div class="input-group">
                <input type="text" id="celular" name="CelMed" placeholder="Celular">
              </div>
            </div>
            <div class="both-inp">
              <div class="input-group">
                <select id="genero" name="GenMed">
                  <option value="M">Masculino</option>
                  <option value="F">Femenino</option>
                </select>
              </div>
              <div class="input-group">
                <input type="text" id="especialidad" name="EspecialidadMed" placeholder="Especialidad" required>
              </div>
            </div>
            <div class="both-inp">
              <button type="submit" class="btn blue">Guardar Cambios</button>
              <button type="button" class="btn red" id="cancelButton">Cancelar</button>
            </div>
          </form>
        </div>
      `,
      showConfirmButton: false,
      didOpen: () => {
        $("#cancelButton").on("click", function () {
          Swal.close();
        });

        $("#registerForm").on("submit", function (e) {
          e.preventDefault();
          $.ajax({
            type: "POST",
            url: "registro_medico.php",
            data: $("#registerForm").serialize(),
            success: function (response) {
              if (response.status === "success") {
                Swal.fire({
                  icon: "success",
                  title: "Registro exitoso",
                  text: "El médico ha sido registrado correctamente.",
                }).then(() => {
                  window.location.reload();
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: response.message,
                });
              }
            },
            error: function () {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "Hubo un error al registrar el médico.",
              });
            },
          });
        });
      },
    });
  });

  // Editar Médico
  $(document).on("click", ".btnEditarMedico", function () {
    const idMed = $(this).data("id");

    $.ajax({
      url: "editar_medico.php",
      type: "GET",
      data: { id: idMed },
      success: function (response) {
        const medico = response.medico;

        Swal.fire({
          grow: "row",
          position: "top",
          background: "transparent",
          html: `
            <div class="container_both-inp">
              <form id="editForm" method="POST" class="form_both-inp">
                <div class="both-inp">
                  <div class="input-group">
                    <input type="text" id="nombre" name="nombre" value="${
                      medico.NomMed
                    }" required>
                  </div>
                  <div class="input-group">
                    <input type="text" id="apellido" name="apellido" value="${
                      medico.ApellidoMed
                    }" required>
                  </div>
                </div>
                <div class="both-inp">
                  <div class="input-group">
                    <input type="text" id="direccion" name="direccion" value="${
                      medico.DirMed
                    }" required>
                  </div>
                  <div class="input-group">
                    <input type="text" id="celular" name="celular" value="${
                      medico.CelMed
                    }" required>
                  </div>
                </div>
                <div class="both-inp">
                  <div class="input-group">
                    <input type="email" id="email" name="email" value="${
                      medico.EmailMed
                    }" required>
                  </div>
                  <div class="input-group">
                    <select id="genero" name="genero">
                      <option value="M" ${
                        medico.GenMed === "M" ? "selected" : ""
                      }>Masculino</option>
                      <option value="F" ${
                        medico.GenMed === "F" ? "selected" : ""
                      }>Femenino</option>
                    </select>
                  </div>
                </div>
                <div class="both-inp">
                  <div class="input-group">
                    <input type="text" id="especialidad" name="especialidad" value="${
                      medico.EspecialidadMed
                    }" required>
                  </div>
                </div>
                <div class="both-inp">
                  <button type="submit" class="btn blue">Guardar Cambios</button>
                  <button type="button" class="btn red" id="cancelButton">Cancelar</button>
                </div>
              </form>
            </div>
          `,
          showConfirmButton: false,
          didOpen: () => {
            $("#cancelButton").on("click", function () {
              Swal.close();
            });

            $("#editForm").on("submit", function (e) {
              e.preventDefault();
              $.ajax({
                type: "POST",
                url: "editar_medico.php",
                data: $("#editForm").serialize() + "&id=" + idMed,
                success: function (response) {
                  Swal.fire({
                    icon: "success",
                    title: "Actualización exitosa",
                    text: "El médico ha sido actualizado correctamente.",
                  }).then(() => {
                    window.location.reload();
                  });
                },
                error: function () {
                  Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Hubo un error al actualizar el médico.",
                  });
                },
              });
            });
          },
        });
      },
      error: function () {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Hubo un error al obtener los datos del médico.",
        });
      },
    });
  });

  // Eliminar Médico
  $(document).on("click", ".btnEliminarMedico", function () {
    const idMed = $(this).data("id");

    Swal.fire({
      title: "¿Estás seguro de eliminar este médico?",
      text: "¡Esta acción no se puede deshacer!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Sí, eliminarlo",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "POST",
          url: "eliminar_medico.php",
          data: { id: idMed, action: "delete" },
          success: function (response) {
            if (response.status === "success") {
              Swal.fire({
                icon: "success",
                title: "Eliminado",
                text: "El médico ha sido eliminado correctamente.",
              }).then(() => {
                window.location.reload();
              });
            } else {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "Hubo un error al eliminar el médico.",
              });
            }
          },
          error: function () {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Hubo un error al eliminar el médico.",
            });
          },
        });
      }
    });
  });
});

// ANCHOR PACIENTES
// Registro
$(document).ready(function () {
  $("#btnRegistrarPaciente").on("click", function () {
    Swal.fire({
      grow: "row",
      position: "top",
      background: "transparent",
      html: `
        <div class="container_both-inp">
          <form id="registerForm" method="POST" class="form_both-inp">
            <div class="both-inp">
              <div class="input-group">
                
                <input type="text" id="nombre" name="Nombre" required placeholder="Nombre">
              </div>
              <div class="input-group">
                
                <input type="text" id="apellido" name="Apellido" required placeholder="Apellido">
              </div>
            </div>
            <div class="both-inp">
              <div class="input-group">
                <input type="email" id="email" name="EmailPac" required placeholder="Email">
              </div>
              <div class="input-group">
                <input type="text" id="dni" name="DNIPac" required placeholder="DNI">
              </div>
            </div>
            <div class="both-inp">
              <div class="input-group">
                <input type="password" id="contraseña" name="Contraseña" required placeholder="Contraseña">
              </div>
              <div class="input-group">
                <input type="text" id="dir" name="DirPac" required placeholder="Direccion">
              </div>
            </div>
            <div class="both-inp">
              <div class="input-group">
                <input type="text" id="celular" name="CelPac" placeholder="Celular">
              </div>
              <div class="input-group">
                <select id="genero" name="GenPac">
                  <option value="M">Masculino</option>
                  <option value="F">Femenino</option>
                  <option value="O">Otro</option>
                </select>
              </div>
            </div>
            <div class="both-inp">
              <div class="input-group">
                <input type="date" id="fecha_nacimiento" name="FechaNacimiento" placeholder="Fecha de nacimiento">
              </div>
            </div>
            <div class="both-inp">
              <button type="submit" class="btn blue">Guardar Cambios</button>
              <button type="button" class="btn red" id="cancelButton">Cancelar</button>
            </div>
          </form>
        </div>
      `,
      showConfirmButton: false,
      didOpen: () => {
        $("#cancelButton").on("click", function () {
          Swal.close();
        });

        $("#registerForm").on("submit", function (e) {
          e.preventDefault();
          $.ajax({
            type: "POST",
            url: "registro_paciente.php",
            data: $("#registerForm").serialize(),
            success: function (response) {
              if (response.status === "success") {
                Swal.fire({
                  icon: "success",
                  title: "Registro exitoso",
                  text: response.message,
                }).then(() => {
                  window.location.reload();
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: response.message,
                });
              }
            },
            error: function () {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "Hubo un error al registrar el paciente.",
              });
            },
          });
        });
      },
    });
  });

  // Edicion
  $(".btnEditarPaciente").on("click", function () {
    const idPac = $(this).data("id");

    $.ajax({
      url: "editar_paciente.php",
      type: "GET",
      data: { id: idPac },
      success: function (response) {
        const paciente = response.paciente;

        Swal.fire({
          grow: "row",
          position: "top",
          background: "transparent",
          html: `
            <div class="container_both-inp">
              <form id="editForm" method="POST" class="form_both-inp">
                <div class="both-inp">
                  <div class="input-group">
                    <input type="text" id="nombre" name="nombre" value="${
                      paciente.Nombre
                    }" required placeholder="Nombre">
                  </div>
                  <div class="input-group">
                    <input type="text" id="apellido" name="apellido" value="${
                      paciente.Apellido
                    }" required placeholder="Apellido">
                  </div>
                </div>
                <div class="both-inp">
                  <div class="input-group">
                    <input type="text" id="direccion" name="direccion" value="${
                      paciente.DirPac
                    }" required placeholder="Direccion">
                  </div>
                  <div class="input-group">
                    <input type="text" id="celular" name="celular" value="${
                      paciente.CelPac
                    }" required placeholder="Celular">
                  </div>
                </div>
                <div class="both-inp">
                  <div class="input-group">
                    <input type="email" id="email" name="email" value="${
                      paciente.EmailPac
                    }" required placeholder="Email">
                  </div>
                  <div class="input-group">
                    <input type="text" id="dni" name="dni" value="${
                      paciente.DNIPac
                    }" required placeholder="DNI">
                  </div>
                </div>
                <div class="both-inp">
                  <div class="input-group">
                    <select id="genero" name="genero">
                      <option value="M" ${
                        paciente.GenPac == "M" ? "selected" : ""
                      }>Masculino</option>
                      <option value="F" ${
                        paciente.GenPac == "F" ? "selected" : ""
                      }>Femenino</option>
                    </select>
                  </div>
                  <div class="input-group">
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="${
                      paciente.FechaNacimiento
                    }" required placeholder="Fecha de nacimiento">
                  </div>
                </div>
                <div class="both-inp">
                  <button type="submit" class="btn blue">Guardar Cambios</button>
                  <button type="button" class="btn red" id="cancelButton">Cancelar</button>
                </div>
              </form>
            </div>
          `,
          showConfirmButton: false,
          didOpen: () => {
            $("#cancelButton").on("click", function () {
              Swal.close();
            });

            $("#editForm").on("submit", function (e) {
              e.preventDefault();
              $.ajax({
                type: "POST",
                url: "editar_paciente.php",
                data: $("#editForm").serialize() + "&id=" + idPac,
                success: function (response) {
                  if (response.status === "success") {
                    Swal.fire({
                      icon: "success",
                      title: "Actualización exitosa",
                      text: "El paciente ha sido actualizado correctamente.",
                    }).then(() => {
                      window.location.reload();
                    });
                  } else {
                    Swal.fire({
                      icon: "error",
                      title: "Error",
                      text: response.message,
                    });
                  }
                },
                error: function () {
                  Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Hubo un error al actualizar el paciente.",
                  });
                },
              });
            });
          },
        });
      },
      error: function () {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Hubo un error al obtener los datos del paciente.",
        });
      },
    });
  });

  // Eliminar
  $(".btnEliminarPaciente").on("click", function () {
    const idPac = $(this).data("id");

    Swal.fire({
      title: "¿Estás seguro de eliminar este paciente?",
      text: "¡Esta acción no se puede deshacer!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Sí, eliminarlo",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "POST",
          url: "eliminar_paciente.php",
          data: { id: idPac },
          success: function (response) {
            if (response.status === "success") {
              Swal.fire({
                icon: "success",
                title: "Eliminado",
                text: "El paciente ha sido eliminado correctamente.",
              }).then(() => {
                window.location.reload();
              });
            } else {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: response.message,
              });
            }
          },
          error: function () {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Hubo un error al eliminar el paciente.",
            });
          },
        });
      }
    });
  });
});

// ANCHOR TURNOS
// Registro de Turnos
$(document).ready(function () {
  $("#btnRegistrarTurno").on("click", function () {
    $.ajax({
      url: "registro_turno.php",
      type: "GET",
      success: function (response) {
        if (response.status === "success") {
          const especialidades = response.especialidades;
          const medicos = response.medicos;
          const pacientes = response.pacientes;
          let especialidadesOptions = "";
          let pacientesOptions = "";

          especialidades.forEach((especialidad) => {
            especialidadesOptions += `<option value="${especialidad.EspecialidadMed}">${especialidad.EspecialidadMed}</option>`;
          });

          pacientes.forEach((paciente) => {
            pacientesOptions += `<option value="${paciente.ID_Pac}">${paciente.Nombre} ${paciente.Apellido}</option>`;
          });

          Swal.fire({
            grow: "row",
            position: "top",
            background: "transparent",
            html: `
              <div class="container_both-inp">
                <form id="registerForm" method="POST" class="form_both-inp">
                  <div class="both-inp">
                    <div class="input-group">
                      <label for="especialidad_medico">Especialidad</label>
                      <select id="especialidad_medico" name="EspecialidadMed" required>
                        <option value="">Seleccione una especialidad</option>
                        ${especialidadesOptions}
                      </select>
                    </div>
                    <div class="input-group">
                      <label for="id_medico">Médico</label>
                      <select id="id_medico" name="ID_Med" required>
                        <option value="">Seleccione un médico</option>
                      </select>
                    </div>
                  </div>
                  <div class="both-inp">
                    <div class="input-group">
                      <label for="id_paciente">Paciente</label>
                      <select id="id_paciente" name="ID_Pac" required>
                        ${pacientesOptions}
                      </select>
                    </div>
                  </div>
                  <div class="both-inp">
                    <div class="input-group">
                      <label for="fecha_turno">Fecha</label>
                      <input type="date" id="fecha_turno" name="FechaTurno" required>
                    </div>
                    <div class="input-group">
                      <label for="hora_turno">Hora</label>
                      <input type="time" id="hora_turno" name="HoraTurno" required>
                    </div>
                  </div>
                  <div class="both-inp">
                    <button type="submit" class="btn blue">Guardar Cambios</button>
                    <button type="button" class="btn red" id="cancelButton">Cancelar</button>
                  </div>
                </form>
              </div>
            `,
            showConfirmButton: false,
            didOpen: () => {
              $("#cancelButton").on("click", function () {
                Swal.close();
              });

              // Actualizar la lista de médicos según la especialidad seleccionada
              $("#especialidad_medico").on("change", function () {
                const especialidadSeleccionada = $(this).val();
                let medicosOptions =
                  '<option value="">Seleccione un médico</option>';

                medicos.forEach((medico) => {
                  if (medico.EspecialidadMed === especialidadSeleccionada) {
                    medicosOptions += `<option value="${medico.ID_Med}">${medico.NomMed} ${medico.ApellidoMed}</option>`;
                  }
                });

                $("#id_medico").html(medicosOptions);
              });

              $("#registerForm").on("submit", function (e) {
                e.preventDefault();
                $.ajax({
                  type: "POST",
                  url: "registro_turno.php",
                  data: $("#registerForm").serialize(),
                  success: function (response) {
                    if (response.status === "success") {
                      Swal.fire({
                        icon: "success",
                        title: "Registro exitoso",
                        text: response.message,
                      }).then(() => {
                        window.location.reload();
                      });
                    } else {
                      Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.message,
                      });
                    }
                  },
                  error: function (xhr) {
                    var response = JSON.parse(xhr.responseText);
                    Swal.fire({
                      icon: "error",
                      title: "Error",
                      text: response.message,
                    });
                  },
                });
              });
            },
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: response.message,
          });
        }
      },
      error: function () {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Hubo un error al obtener los datos de especialidades, médicos y pacientes.",
        });
      },
    });
  });

  // Editar
  $(".btnEditarTurno").on("click", function () {
    const idTurno = $(this).data("id");

    $.ajax({
      url: "editar_turno.php",
      type: "GET",
      data: { id: idTurno },
      success: function (response) {
        if (response.status === "success") {
          const turno = response.turno;
          const especialidades = response.especialidades;
          const medicos = response.medicos;
          const pacientes = response.pacientes;
          let especialidadesOptions = "";
          let medicosOptions = "";
          let pacientesOptions = "";

          especialidades.forEach((especialidad) => {
            especialidadesOptions += `<option value="${
              especialidad.EspecialidadMed
            }" ${
              especialidad.EspecialidadMed === turno.EspecialidadMed
                ? "selected"
                : ""
            }>${especialidad.EspecialidadMed}</option>`;
          });

          medicos.forEach((medico) => {
            if (medico.EspecialidadMed === turno.EspecialidadMed) {
              medicosOptions += `<option value="${medico.ID_Med}" ${
                medico.ID_Med == turno.ID_Med ? "selected" : ""
              }>${medico.NomMed} ${medico.ApellidoMed}</option>`;
            }
          });

          pacientes.forEach((paciente) => {
            pacientesOptions += `<option value="${paciente.ID_Pac}" ${
              paciente.ID_Pac == turno.ID_Pac ? "selected" : ""
            }>${paciente.Nombre} ${paciente.Apellido}</option>`;
          });

          Swal.fire({
            grow: "row",
            position: "top",
            background: "transparent",
            html: `
              <div class="container_both-inp">
                <form id="editForm" method="POST" class="form_both-inp">
                  <div class="both-inp">
                    <div class="input-group">
                      <label for="especialidad_medico">Especialidad</label>
                      <select id="especialidad_medico" name="EspecialidadMed" required>
                        <option value="">Seleccione una especialidad</option>

                        ${especialidadesOptions}
                      </select>
                    </div>
                    <div class="input-group">
                      <label for="id_medico">Médico</label>
                      <select id="id_medico" name="ID_Med" required>
                        ${medicosOptions}
                      </select>
                    </div>
                  </div>
                  <div class="both-inp">
                    <div class="input-group">
                      <label for="id_paciente">Paciente</label>
                      <select id="id_paciente" name="ID_Pac" required>
                        ${pacientesOptions}
                      </select>
                    </div>
                  </div>
                  <div class="both-inp">
                    <div class="input-group">
                      <label for="fecha_turno">Fecha</label>
                      <input type="date" id="fecha_turno" name="FechaTurno" value="${turno.FechaTurno}" required>
                    </div>
                    <div class="input-group">
                      <label for="hora_turno">Hora</label>
                      <input type="time" id="hora_turno" name="HoraTurno" value="${turno.HoraTurno}" required>
                    </div>
                  </div>
                  <div class="both-inp">
                    <button type="submit" class="btn blue">Guardar Cambios</button>
                    <button type="button" class="btn red" id="cancelButton">Cancelar</button>
                  </div>
                </form>
              </div>
            `,
            showConfirmButton: false,
            didOpen: () => {
              $("#cancelButton").on("click", function () {
                Swal.close();
              });

              // Actualizar la lista de médicos según la especialidad seleccionada
              $("#especialidad_medico").on("change", function () {
                const especialidadSeleccionada = $(this).val();
                let medicosOptions =
                  '<option value="">Seleccione un médico</option>';

                medicos.forEach((medico) => {
                  if (medico.EspecialidadMed === especialidadSeleccionada) {
                    medicosOptions += `<option value="${medico.ID_Med}">${medico.NomMed} ${medico.ApellidoMed}</option>`;
                  }
                });

                $("#id_medico").html(medicosOptions);
              });

              $("#editForm").on("submit", function (e) {
                e.preventDefault();
                $.ajax({
                  type: "POST",
                  url: "editar_turno.php",
                  data: $("#editForm").serialize() + "&id=" + idTurno,
                  success: function (response) {
                    if (response.status === "success") {
                      Swal.fire({
                        icon: "success",
                        title: "Actualización exitosa",
                        text: "El turno ha sido actualizado correctamente.",
                      }).then(() => {
                        window.location.reload();
                      });
                    } else {
                      Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.message,
                      });
                    }
                  },
                  error: function () {
                    Swal.fire({
                      icon: "error",
                      title: "Error",
                      text: "Hubo un error al actualizar el turno.",
                    });
                  },
                });
              });
            },
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: response.message,
          });
        }
      },
      error: function () {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Hubo un error al obtener los datos del turno.",
        });
      },
    });
  });

  // Eliminar
  $(".btnEliminarTurno").on("click", function () {
    const idTurno = $(this).data("id");

    Swal.fire({
      title: "¿Estás seguro de eliminar este turno?",
      text: "¡Esta acción no se puede deshacer!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Sí, eliminarlo",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "POST",
          url: "eliminar_turno.php",
          data: { id: idTurno },
          success: function (response) {
            if (response.status === "success") {
              Swal.fire({
                icon: "success",
                title: "Eliminado",
                text: "El turno ha sido eliminado correctamente.",
              }).then(() => {
                window.location.reload();
              });
            } else {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: response.message,
              });
            }
          },
          error: function () {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Hubo un error al eliminar el turno.",
            });
          },
        });
      }
    });
  });
});

$(document).ready(function () {
  let notificacionesMostradas = false;
  let ultimaNotificacionId = null;

  function obtenerNotificaciones() {
    return $.ajax({
      url: "obtener_notificaciones.php",
      method: "GET",
    });
  }

  // Función para marcar la notificación como leída
  function marcarComoLeida(id) {
    console.log("Marcando como leída la notificación con ID:", id);
    $.ajax({
      url: "marcar_notificaciones.php",
      method: "POST",
      contentType: "application/json",
      data: JSON.stringify({ id: id }),
      success: function (response) {
        console.log("Notificación marcada como leída:", response);
      },
      error: function (xhr, status, error) {
        console.error("Error AJAX:", error);
        console.error("Respuesta del servidor:", xhr.responseText);
        console.error("Estado:", status);
      },
    });
  }

  // Función para mostrar notificaciones
  function mostrarNotificaciones() {
    if (!notificacionesMostradas) {
      obtenerNotificaciones().done(function (data) {
        if (data.length > 0) {
          const ultimaNotificacion = data.find((notificacion) =>
            notificacion.mensaje.includes("cancelado")
          );
          if (ultimaNotificacion) {
            ultimaNotificacionId = ultimaNotificacion.id;

            butterup.options.toastLife = 30000;
            // Usamos Butterup para mostrar la notificación
            butterup.toast({
              message: ultimaNotificacion.mensaje,
              location: "top-right",
              dismissable: true,
              type: "info",
              primaryButton: {
                text: "Marcar como leído",

                onClick: function () {
                  console.log("La notificación se cerró automáticamente.");
                  console.log("Última notificación:", ultimaNotificacion);

                  if (ultimaNotificacionId) {
                    console.log(
                      "Intentando marcar como leída la ID:",
                      ultimaNotificacionId
                    );
                    marcarComoLeida(ultimaNotificacionId);
                    ultimaNotificacionId = null;

                    // Cerrar la notificación manualmente
                    // Aquí debes agregar el `id` del toast.
                    const toastId = document.querySelector(".butteruptoast").id; // Obtener el id del primer toast
                    butterup.despawnToast(toastId); // Cerrar la notificación
                  } else {
                    console.warn(
                      "No hay ID de notificación para marcar como leída."
                    );
                  }
                },
              },
            });

            notificacionesMostradas = true;
          }
        }
      });
    }
  }

  mostrarNotificaciones();
});

function obtenerTodasLasNotificaciones() {
  return $.ajax({
    url: "obtener_todas_notificaciones.php", // Nueva URL para obtener todas
    method: "GET",
  });
}
// Abrir la ventana modal de notificaciones
$(document).ready(function () {
  $("#btnVerNotificaciones").on("click", function () {
    obtenerTodasLasNotificaciones()
      .done(function (data) {
        var notificacionesHTML =
          '<div style="max-height: 300px; overflow-y: auto;">';
        notificacionesHTML +=
          "<ul style='list-style: none; padding: 0; margin: 0;'>";

        data.forEach(function (notificacion) {
          let estilo =
            notificacion.leido == 0
              ? "color:black; font-weight:bold;" // Notificación no leída
              : ""; // Notificación leída
          console.log(notificacion.leido);
          notificacionesHTML += `<li class"" style="${estilo};padding: 8px; border-bottom: 1px solid #ccc;">${notificacion.mensaje}</li>`;
        });

        notificacionesHTML += "</ul></div>";

        Swal.fire({
          title: "Notificaciones",
          html: notificacionesHTML,
          icon: "info",
          showCloseButton: true,
          confirmButtonText: "Cerrar",
          width: "400px",
        });
      })
      .fail(function () {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Hubo un error al obtener las notificaciones.",
        });
      });
  });
});

// Completar Turno
$(document).on("click", ".btnCompletarTurno", function () {
  const idTurno = $(this).data("id");

  Swal.fire({
    title: "¿Estás seguro de marcar este turno como completado?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, completarlo",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: "POST",
        url: "completar_turno.php",
        data: { id: idTurno },
        success: function (response) {
          if (response.status === "success") {
            Swal.fire({
              icon: "success",
              title: "Completado",
              text: "El turno ha sido marcado como completado.",
            }).then(() => {
              window.location.reload();
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Hubo un error al marcar el turno como completado.",
            });
          }
        },
        error: function () {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Hubo un error al marcar el turno como completado.",
          });
        },
      });
    }
  });
});

// Completar Turno
$(document).on("click", ".btnCompletarTurno", function () {
  const idTurno = $(this).data("id");

  Swal.fire({
    title: "¿Estás seguro de marcar este turno como completado?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, completarlo",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: "POST",
        url: "completar_turno.php",
        data: { id: idTurno },
        success: function (response) {
          if (response.status === "success") {
            Swal.fire({
              icon: "success",
              title: "Completado",
              text: "El turno ha sido marcado como completado.",
            }).then(() => {
              window.location.reload();
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Hubo un error al marcar el turno como completado.",
            });
          }
        },
        error: function () {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Hubo un error al marcar el turno como completado.",
          });
        },
      });
    }
  });
});

// Cancelar Turno
$(document).on("click", ".btnCancelarTurno", function () {
  const idTurno = $(this).data("id");

  Swal.fire({
    title: "¿Estás seguro de cancelar este turno?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, cancelarlo",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: "POST",
        url: "cancelar_turno.php",
        data: { id: idTurno },
        success: function (response) {
          if (response.status === "success") {
            Swal.fire({
              icon: "success",
              title: "Cancelado",
              text: "El turno ha sido cancelado.",
            }).then(() => {
              window.location.reload();
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: response.message,
            });
          }
        },
        error: function (xhr) {
          var response = JSON.parse(xhr.responseText);
          Swal.fire({
            icon: "error",
            title: "Error",
            text: response.message,
          });
        },
      });
    }
  });
});

// ANCHOR ADMIN
// Registro
$(document).ready(function () {
  $("#btnRegistrarAdmin").on("click", function () {
    Swal.fire({
      grow: "row",
      position: "top",
      background: "transparent",
      html: `
        <div class="container_both-inp">
          <form id="registerForm" method="POST" class="form_both-inp">
            <div class="both-inp">
              <div class="input-group">
                <input type="text" id="nombre" name="Nombre" required placeholder="Nombre">
              </div>
              <div class="input-group">
                <input type="text" id="apellido" name="Apellido" required placeholder="Apellido">
              </div>
            </div>
            <div class="both-inp">
              <div class="input-group">
                <input type="email" id="email" name="EmailAdmin" required placeholder="Email">
              </div>
              <div class="input-group">
                <input type="password" id="contraseña" name="Contraseña" required placeholder="Contraseña">
              </div>
            </div>
            <div class="both-inp">
              <div class="input-group">
                <input type="text" id="direccion" name="DirAdmin" required placeholder="Direccion">
              </div>
              <div class="input-group">
                <input type="text" id="celular" name="CelAdmin" placeholder="Celular">
              </div>
            </div>
            <div class="both-inp">
              <button type="submit" class="btn blue">Guardar Cambios</button>
              <button type="button" class="btn red" id="cancelButton">Cancelar</button>
            </div>
          </form>
        </div>
      `,
      showConfirmButton: false,
      didOpen: () => {
        $("#cancelButton").on("click", function () {
          Swal.close();
        });

        $("#registerForm").on("submit", function (e) {
          e.preventDefault();
          $.ajax({
            type: "POST",
            url: "registro_admin.php",
            data: $("#registerForm").serialize(),
            success: function (response) {
              if (response.status === "success") {
                Swal.fire({
                  icon: "success",
                  title: "Registro exitoso",
                  text: response.message,
                }).then(() => {
                  window.location.reload();
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: response.message,
                });
              }
            },
            error: function () {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "Hubo un error al registrar el administrador.",
              });
            },
          });
        });
      },
    });
  });

  // Editar

  $(".btnEditarAdmin").on("click", function () {
    const idAdmin = $(this).data("id");

    $.ajax({
      url: "editar_admin.php",
      type: "GET",
      data: { id: idAdmin },
      success: function (response) {
        if (response.status === "success") {
          const admin = response.admin;

          Swal.fire({
            grow: "row",
            position: "top",
            background: "transparent",
            html: `
              <div class="container_both-inp">
                <form id="editForm" method="POST" class="form_both-inp">
                  <div class="both-inp">
                    <div class="input-group">
                      <input type="text" id="nombre" name="Nombre" value="${admin.NomAdmin}" required placeholder="Nombre">
                    </div>
                    <div class="input-group">
                      <input type="text" id="apellido" name="Apellido" value="${admin.ApellidoAdmin}" required placeholder="Apellido">
                    </div>
                  </div>
                  <div class="both-inp">
                    <div class="input-group">
                      <input type="email" id="email" name="EmailAdmin" value="${admin.EmailAdmin}" required placeholder="Email">
                    </div>
                    <div class="input-group">
                      <input type="text" id="direccion" name="DirAdmin" value="${admin.DirAdmin}" required placeholder="Direccion">
                    </div>
                  </div>
                  <div class="both-inp">
                    <div class="input-group">
                      <input type="text" id="celular" name="CelAdmin" value="${admin.CelAdmin}" placeholder="Celular">
                    </div>
                  </div>
                  <div class="both-inp">
                    <button type="submit" class="btn blue">Guardar Cambios</button>
                    <button type="button" class="btn red" id="cancelButton">Cancelar</button>
                  </div>
                </form>
              </div>
            `,
            showConfirmButton: false,
            didOpen: () => {
              $("#cancelButton").on("click", function () {
                Swal.close();
              });

              $("#editForm").on("submit", function (e) {
                e.preventDefault();
                $.ajax({
                  type: "POST",
                  url: "editar_admin.php",
                  data: $("#editForm").serialize() + "&id=" + idAdmin,
                  success: function (response) {
                    if (response.status === "success") {
                      Swal.fire({
                        icon: "success",
                        title: "Actualización exitosa",
                        text: "El administrador ha sido actualizado correctamente.",
                      }).then(() => {
                        window.location.reload();
                      });
                    } else {
                      Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.message,
                      });
                    }
                  },
                  error: function () {
                    Swal.fire({
                      icon: "error",
                      title: "Error",
                      text: "Hubo un error al actualizar el administrador.",
                    });
                  },
                });
              });
            },
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: response.message,
          });
        }
      },
      error: function () {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Hubo un error al obtener los datos del administrador.",
        });
      },
    });
  });

  // Eliminar
  $(".btnEliminarAdmin").on("click", function () {
    const idAdmin = $(this).data("id");

    Swal.fire({
      title: "¿Estás seguro de eliminar este administrador?",
      text: "¡Esta acción no se puede deshacer!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Sí, eliminarlo",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "POST",
          url: "eliminar_admin.php",
          data: { id: idAdmin },
          success: function (response) {
            if (response.status === "success") {
              Swal.fire({
                icon: "success",
                title: "Eliminado",
                text: "El administrador ha sido eliminado correctamente.",
              }).then(() => {
                window.location.reload();
              });
            } else {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: response.message,
              });
            }
          },
          error: function () {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Hubo un error al eliminar el administrador.",
            });
          },
        });
      }
    });
  });
});

// ANCHOR INFORMES
// Registro
$(document).ready(function () {
  $("#btnRegistrarInforme").on("click", function () {
    Swal.fire({
      grow: "row",
      position: "top",
      background: "transparent",
      html: `
        <div class="container_both-inp">
          <form id="registroForm" enctype="multipart/form-data" class="form_both-inp">
            <div class="both-inp">
              <div class="input-group">
                <label for="id_paciente">Paciente</label>
                <select id="id_paciente" name="id_paciente" required>
                  ${pacientesOptions}
                </select>
              </div>
              <div class="input-group">
                <label for="fecha">Fecha del Informe</label>
                <input type="date" id="fecha" name="fecha" required>
              </div>
            </div>
            <div class="both-inp">
              <div class="input-group">
                <label for="descripcion">Descripción del Informe</label>
                <textarea id="descripcion" name="descripcion" rows="5" required></textarea>
              </div>
            </div>
            <div class="both-inp">
              <div class="input-group">
                <label for="archivo_pdf">Seleccionar archivo PDF</label>
                <input type="file" id="archivo_pdf" name="archivo_pdf" accept="application/pdf">
              </div>
            </div>
            <div class="both-inp">
              <button type="submit" class="btn blue">Guardar Cambios</button>
              <button type="button" class="btn red" id="cancelButton">Cancelar</button>
            </div>
          </form>
        </div>
      `,
      showConfirmButton: false,
      didOpen: () => {
        $("#cancelButton").on("click", function () {
          Swal.close();
        });

        $("#registroForm").on("submit", function (e) {
          e.preventDefault();
          var formData = new FormData(this);
          $.ajax({
            type: "POST",
            url: "registro_informe.php",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
              if (response.status === "success") {
                Swal.fire({
                  icon: "success",
                  title: "Registro exitoso",
                  text: response.message,
                }).then(() => {
                  window.location.reload();
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: response.message,
                });
              }
            },
            error: function (xhr) {
              var response = JSON.parse(xhr.responseText);
              Swal.fire({
                icon: "error",
                title: "Error",
                text: response.message,
              });
            },
          });
        });
      },
    });
  });

  // Editar Informe
  $(".btnEditarInforme").on("click", function () {
    const idHistorial = $(this).data("id");

    $.ajax({
      url: "editar_informe.php",
      type: "GET",
      data: { id_historial: idHistorial },
      success: function (response) {
        if (response.status === "success") {
          const informe = response.informe;

          Swal.fire({
            grow: "row",
            position: "top",
            background: "transparent",
            html: `
              <div class="container_both-inp">
                <form id="editarForm" enctype="multipart/form-data" class="form_both-inp">
                  <input type="hidden" name="id_historial" value="${informe.ID_Historial}">
                  <div class="both-inp">
                    <div class="input-group">
                      <label for="id_paciente">Paciente</label>
                      <select id="id_paciente" name="id_paciente" required>
                        ${pacientesOptions}
                      </select>
                    </div>
                    <div class="input-group">
                      <label for="fecha">Fecha del Informe</label>
                      <input type="date" id="fecha" name="fecha" value="${informe.Fecha}" required>
                    </div>
                  </div>
                  <div class="both-inp">
                    <div class="input-group">
                      <label for="descripcion">Descripción del Informe</label>
                      <textarea id="descripcion" name="descripcion" rows="5" required>${informe.Descripcion}</textarea>
                    </div>
                  </div>
                  <div class="both-inp">
                    <div class="input-group">
                      <label for="archivo_pdf">Seleccionar archivo PDF</label>
                      <input type="file" id="archivo_pdf" name="archivo_pdf" accept="application/pdf">
                    </div>
                  </div>
                  <div class="both-inp">
                    <button type="submit" class="btn blue">Guardar Cambios</button>
                    <button type="button" class="btn red" id="cancelButton">Cancelar</button>
                  </div>
                </form>
              </div>
            `,
            showConfirmButton: false,
            didOpen: () => {
              $("#cancelButton").on("click", function () {
                Swal.close();
              });

              $("#editarForm").on("submit", function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                  type: "POST",
                  url: "editar_informe.php",
                  data: formData,
                  contentType: false,
                  processData: false,
                  success: function (response) {
                    if (response.status === "success") {
                      Swal.fire({
                        icon: "success",
                        title: "Actualización exitosa",
                        text: "El informe ha sido actualizado correctamente.",
                      }).then(() => {
                        window.location.reload();
                      });
                    } else {
                      Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.message,
                      });
                    }
                  },
                  error: function (xhr) {
                    var response = JSON.parse(xhr.responseText);
                    Swal.fire({
                      icon: "error",
                      title: "Error",
                      text: response.message,
                    });
                  },
                });
              });
            },
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: response.message,
          });
        }
      },
      error: function (xhr) {
        var response = JSON.parse(xhr.responseText);
        Swal.fire({
          icon: "error",
          title: "Error",
          text: response.message,
        });
      },
    });
  });
});

// ANCHOR SOLICITAR TURNO

$(document).ready(function () {
  $("#btnSolicitarTurno").on("click", function () {
    // Primero, obtener la lista de médicos y especialidades
    $.ajax({
      url: "registro_turno.php",
      type: "GET",
      success: function (response) {
        if (response.status === "success") {
          const especialidades = response.especialidades;
          const medicos = response.medicos;

          let especialidadesOptions =
            '<option value="">Seleccione una especialidad</option>';
          especialidades.forEach((especialidad) => {
            especialidadesOptions += `<option value="${especialidad.EspecialidadMed}">${especialidad.EspecialidadMed}</option>`;
          });

          Swal.fire({
            grow: "row",
            position: "top",
            background: "transparent",
            html: `
              <div class="container_both-inp">
                <form id="solicitarTurnoForm" method="POST" class="form_both-inp">
                  <div class="both-inp">
                    <div class="input-group">
                      <label for="especialidad_medico">Especialidad:</label>
                      <select id="especialidad_medico" name="especialidad" required>
                        ${especialidadesOptions}
                      </select>
                    </div>
                    <div class="input-group">
                      <label for="id_medico">Médico:</label>
                      <select id="id_medico" name="id_medico" required>
                        <option value="">Primero seleccione una especialidad</option>
                      </select>
                    </div>
                  </div>
                  <div class="both-inp">
                    <div class="input-group">
                      <label for="fecha">Fecha del Turno:</label>
                      <input type="date" id="fecha" name="fecha" required min="${
                        new Date().toISOString().split("T")[0]
                      }">
                    </div>
                    <div class="input-group">
                      <label for="hora">Hora del Turno:</label>
                      <input type="time" id="hora" name="hora" required>
                    </div>
                  </div>
                  <div class="both-inp">
                    <button type="submit" class="btn blue">Solicitar Turno</button>
                    <button type="button" class="btn red" id="cancelButton">Cancelar</button>
                  </div>
                </form>
              </div>
            `,
            showConfirmButton: false,
            didOpen: () => {
              // Manejar el cambio de especialidad
              $("#especialidad_medico").on("change", function () {
                const especialidadSeleccionada = $(this).val();
                let medicosOptions =
                  '<option value="">Seleccione un médico</option>';

                medicos.forEach((medico) => {
                  if (medico.EspecialidadMed === especialidadSeleccionada) {
                    medicosOptions += `<option value="${medico.ID_Med}">${medico.NomMed} ${medico.ApellidoMed}</option>`;
                  }
                });

                $("#id_medico").html(medicosOptions);
              });

              // Manejar el botón cancelar
              $("#cancelButton").on("click", function () {
                Swal.close();
              });

              // Manejar el envío del formulario
              $("#solicitarTurnoForm").on("submit", function (e) {
                e.preventDefault();
                $.ajax({
                  type: "POST",
                  url: "solicitar_turno.php",
                  data: $(this).serialize(),
                  dataType: "json",
                  success: function (response) {
                    if (response.status === "success") {
                      Swal.fire({
                        icon: "success",
                        title: "Éxito",
                        text: response.message,
                      }).then(() => {
                        window.location.reload();
                      });
                    } else {
                      Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.message,
                      });
                    }
                  },
                  error: function (xhr) {
                    let errorMessage =
                      "Hubo un error al procesar la solicitud.";
                    try {
                      const response = JSON.parse(xhr.responseText);
                      errorMessage = response.message || errorMessage;
                    } catch (e) {
                      console.error("Error parsing response:", e);
                    }
                    Swal.fire({
                      icon: "error",
                      title: "Error",
                      text: errorMessage,
                    });
                  },
                });
              });
            },
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudieron cargar los datos necesarios.",
          });
        }
      },
      error: function () {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Error al cargar los datos iniciales.",
        });
      },
    });
  });
});
