<?php
require_once 'config.php';

try {
    // Obtener el ID del paciente desde la URL
    if (isset($_GET['id'])) {
        $id_pac = intval($_GET['id']);

        // Iniciar la transacción
        $pdo->beginTransaction();

        // Obtener el ID de usuario del paciente
        $query_usuario_id = "SELECT ID_Usuario FROM pacientes WHERE ID_Pac = ?";
        $stmt_usuario_id = $pdo->prepare($query_usuario_id);
        $stmt_usuario_id->execute([$id_pac]);
        $usuario = $stmt_usuario_id->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $id_usuario = $usuario['ID_Usuario'];

            // Eliminar el paciente de la tabla Pacientes
            $query_paciente = "DELETE FROM pacientes WHERE ID_Pac = ?";
            $stmt_paciente = $pdo->prepare($query_paciente);
            $stmt_paciente->execute([$id_pac]);

            // Eliminar las credenciales del paciente de la tabla Usuarios
            $query_usuario = "DELETE FROM usuarios WHERE ID_Usuario = ?";
            $stmt_usuario = $pdo->prepare($query_usuario);
            $stmt_usuario->execute([$id_usuario]);

            // Confirmar la transacción
            $pdo->commit();

            header("Location: dashboard.php?seccion=gestionar_pacientes");
            exit();
        } else {
            throw new Exception("Paciente no encontrado.");
        }
    } else {
        throw new Exception("ID del paciente no proporcionado.");
    }
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $pdo->rollBack();
    echo "Error al eliminar: " . $e->getMessage();
}

$pdo = null;
?>