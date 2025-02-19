<?php
/**
 * Script para la cancelación de turnos médicos y envío de notificaciones
 * 
 * Este script maneja la cancelación de turnos y envía notificaciones automáticas
 * a los pacientes afectados. Requiere una conexión PDO establecida.
 * 
 * @requires ../config.php
 * @version 1.0
 */

// Incluye el archivo de configuración que contiene la conexión PDO a la base de datos
require_once '../config.php';

// Establece el tipo de contenido de la respuesta como JSON
header('Content-Type: application/json');

// Inicia la sesión (aunque no se utiliza explícitamente en este script)
session_start();

try {
  /**
   * Verifica que la solicitud sea de tipo POST y que incluya el parámetro 'id'
   */
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    // Convierte el ID del turno a un entero para evitar problemas de seguridad
    $id_turno = intval($_POST['id']);

    /**
     * Actualiza el estado del turno a 'cancelado' en la base de datos
     */
    $query = "UPDATE turnos SET estado = 'cancelado' WHERE ID_Turno = ?";
    $stmt = $pdo->prepare($query);

    // Ejecuta la consulta con el ID del turno como parámetro
    if ($stmt->execute([$id_turno])) {
      /**
       * Obtiene información detallada del turno y del paciente asociado
       */
      $query_info = "
                SELECT t.ID_Turno, t.FechaTurno, t.HoraTurno, p.ID_Usuario, p.Nombre, p.Apellido
                FROM turnos t
                JOIN pacientes p ON t.ID_Pac = p.ID_Pac
                WHERE t.ID_Turno = ?
            ";
      $stmt_info = $pdo->prepare($query_info);
      $stmt_info->execute([$id_turno]);
      $turno_info = $stmt_info->fetch(PDO::FETCH_ASSOC);

      // Verifica si se encontró información del turno
      if ($turno_info) {
        /**
         * Genera un mensaje personalizado de notificación para el paciente
         */
        $mensaje = "Estimado/a " . htmlspecialchars($turno_info['Nombre']) . " " .
          htmlspecialchars($turno_info['Apellido']) . ", su turno programado para el " .
          date('d/m/Y', strtotime($turno_info['FechaTurno'])) . " a las " .
          $turno_info['HoraTurno'] . " ha sido cancelado.";

        /**
         * Almacena la notificación en la base de datos
         */
        $insertQuery = "INSERT INTO notificaciones (id_usuario, mensaje) VALUES (?, ?)";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->execute([$turno_info['ID_Usuario'], $mensaje]);

        // Devuelve una respuesta JSON indicando que la operación fue exitosa
        echo json_encode(["status" => "success"]);
      } else {
        // Devuelve un mensaje de error si no se encuentra información del turno
        echo json_encode(["status" => "error", "message" => "Información del turno no encontrada"]);
      }
    } else {
      // Devuelve un mensaje de error si la actualización del turno falla
      echo json_encode(["status" => "error", "message" => $stmt->errorInfo()[2]]);
    }
  } else {
    // Devuelve un mensaje de error si la solicitud no es válida
    echo json_encode(["status" => "error", "message" => "Método no permitido o datos incompletos"]);
  }
} catch (Exception $e) {
  /**
   * Maneja cualquier excepción que ocurra durante la ejecución del script
   */
  echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>