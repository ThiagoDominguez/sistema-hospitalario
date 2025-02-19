<?php
require_once '../config.php';

// Establece el tipo de contenido de la respuesta como JSON
header('Content-Type: application/json');

/**
 * Verifica si la solicitud es de tipo POST y si se ha enviado el ID del paciente.
 * 
 * @return bool Retorna true si la solicitud es de tipo POST y contiene el ID del paciente, de lo contrario false.
 */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
  // Convierte el ID del paciente a un entero
  $id_pac = intval($_POST['id']);

  /**
   * Inicia una nueva transacción en la base de datos.
   * 
   * Deshabilita el modo autocommit y comienza una nueva transacción que puede ser
   * confirmada o revertida posteriormente.
   * 
   * @throws PDOException Si ocurre un error al iniciar la transacción
   * @return bool Retorna true si la transacción se inicia correctamente
   */
  $pdo->beginTransaction();

  try {
    /**
     * Obtiene el ID del usuario vinculado al paciente.
     * 
     * Prepara y ejecuta una consulta SQL para obtener el ID del usuario vinculado al paciente
     * utilizando el ID del paciente proporcionado.
     * 
     * @param int $id_pac El ID del paciente
     * @return mixed Retorna el ID del usuario si se encuentra, de lo contrario false
     */
    $stmt = $pdo->prepare("SELECT ID_Usuario FROM pacientes WHERE ID_Pac = ?");
    $stmt->execute([$id_pac]);
    $id_usuario = $stmt->fetchColumn();

    if ($id_usuario) {
      /**
       * Elimina al paciente de la base de datos.
       * 
       * Prepara y ejecuta una consulta SQL para eliminar al paciente utilizando el ID del paciente.
       * 
       * @param int $id_pac El ID del paciente
       * @return bool Retorna true si la eliminación es exitosa
       */
      $stmt = $pdo->prepare("DELETE FROM pacientes WHERE ID_Pac = ?");
      $stmt->execute([$id_pac]);

      /**
       * Elimina al usuario vinculado de la base de datos.
       * 
       * Prepara y ejecuta una consulta SQL para eliminar al usuario utilizando el ID del usuario.
       * 
       * @param int $id_usuario El ID del usuario
       * @return bool Retorna true si la eliminación es exitosa
       */
      $stmt = $pdo->prepare("DELETE FROM usuarios WHERE ID_Usuario = ?");
      $stmt->execute([$id_usuario]);

      /**
       * Confirma la transacción en la base de datos.
       * 
       * Finaliza la transacción actual confirmando todos los cambios realizados durante la transacción.
       * 
       * @throws PDOException Si ocurre un error al confirmar la transacción
       * @return bool Retorna true si la transacción se confirma correctamente
       */
      $pdo->commit();

      // Responder con un mensaje de éxito
      echo json_encode(["status" => "success"]);
    } else {
      /**
       * Revertir la transacción si no se encuentra el usuario vinculado.
       * 
       * Deshace todos los cambios realizados durante la transacción actual.
       * 
       * @throws PDOException Si ocurre un error al revertir la transacción
       * @return bool Retorna true si la transacción se revierte correctamente
       */
      $pdo->rollBack();
      // Responder con un mensaje de error
      echo json_encode(["status" => "error", "message" => "Usuario vinculado no encontrado"]);
    }
  } catch (Exception $e) {
    /**
     * Revertir la transacción en caso de error.
     * 
     * Deshace todos los cambios realizados durante la transacción actual en caso de que ocurra una excepción.
     * 
     * @param Exception $e La excepción capturada
     * @throws PDOException Si ocurre un error al revertir la transacción
     * @return bool Retorna true si la transacción se revierte correctamente
     */
    $pdo->rollBack();
    // Responder con un mensaje de error y el mensaje de la excepción
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
  }
} else {
  /**
   * Responder con un mensaje de error si el método no es POST o los datos están incompletos.
   * 
   * @return void
   */
  echo json_encode(["status" => "error", "message" => "Método no permitido o datos incompletos"]);
}
?>