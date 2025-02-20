<?php
/**
 * Script para marcar un turno médico como completado y registrar el historial de atención
 * 
 * Este script actualiza el estado de un turno a "completado" y registra la atención en el historial médico.
 * Requiere una conexión PDO establecida.
 * 
 * @requires ../config.php
 * @version 1.0
 */

// Incluye el archivo de configuración que contiene la conexión PDO a la base de datos
require_once '../config.php';

// Establece el tipo de contenido de la respuesta como JSON
header('Content-Type: application/json');

/**
 * Verifica que la solicitud sea de tipo POST y que incluya el parámetro 'id'
 */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
  // Convierte el ID del turno a un entero para evitar problemas de seguridad
  $id_turno = intval($_POST['id']);

  /**
   * Actualiza el estado del turno a 'completado' en la base de datos
   */
  $query = "UPDATE turnos SET estado = 'completado' WHERE ID_Turno = ?";
  $stmt = $pdo->prepare($query);

  // Ejecuta la consulta con el ID del turno como parámetro
  if ($stmt->execute([$id_turno])) {
    /**
     * Registra el historial de atención en la tabla 'historialmedico'
     */
    $query_historial = "
            INSERT INTO historialmedico (ID_Turno, ID_Pac, ID_Med, Fecha, Descripcion) 
            SELECT t.ID_Turno, t.ID_Pac, t.ID_Med, t.FechaTurno, 'Atención completada'
            FROM turnos t
            WHERE t.ID_Turno = ?
        ";
    $stmt_historial = $pdo->prepare($query_historial);
    $stmt_historial->execute([$id_turno]);

    // Devuelve una respuesta JSON indicando que la operación fue exitosa
    echo json_encode(["status" => "success"]);
  } else {
    // Devuelve un mensaje de error si la actualización del turno falla
    echo json_encode(["status" => "error", "message" => $stmt->errorInfo()[2]]);
  }
} else {
  // Devuelve un mensaje de error si la solicitud no es válida
  echo json_encode(["status" => "error", "message" => "Método no permitido o datos incompletos"]);
}
?>