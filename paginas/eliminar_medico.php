<?php
require_once 'config.php';

// Obtener datos del médico
if (isset($_GET['id'])) {
    $id_med = intval($_GET['id']);

    // Iniciar la transacción
    $pdo->beginTransaction();

    try {
        // Obtener el ID de usuario del médico
        $query_usuario_id = "SELECT ID_Usuario FROM personalmedico WHERE ID_Med = ?";
        $stmt_usuario_id = $pdo->prepare($query_usuario_id);
        $stmt_usuario_id->execute([$id_med]);
        $usuario = $stmt_usuario_id->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $id_usuario = $usuario['ID_Usuario'];

            // Eliminar el médico de la tabla PersonalMedico
            $query_medico = "DELETE FROM personalmedico WHERE ID_Med = ?";
            $stmt_medico = $pdo->prepare($query_medico);
            $stmt_medico->execute([$id_med]);

            // Eliminar las credenciales del médico de la tabla Usuarios
            $query_usuario = "DELETE FROM usuarios WHERE ID_Usuario = ?";
            $stmt_usuario = $pdo->prepare($query_usuario);
            $stmt_usuario->execute([$id_usuario]);

            // Confirmar la transacción
            $pdo->commit();

            header("Location: dashboard.php?seccion=gestionar_medicos");
            exit();
        } else {
            throw new Exception("Médico no encontrado.");
        }
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $pdo->rollBack();
        echo "Error al eliminar: " . $e->getMessage();
    }
}

$pdo = null;
?>