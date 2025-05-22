<?php
session_start();
require 'db.php';

// Verificar si es administrador
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['is_admin'] != 1) {
    die("Acceso denegado: No eres administrador.");
}

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $usuario_id = $_POST['user_id'];

    try {
        // Eliminar archivos asociados al usuario primero (por integridad referencial)
        $conn->beginTransaction();

        // 1. Eliminar archivos compartidos que involucren al usuario
        $stmt = $conn->prepare("DELETE FROM archivos_compartidos WHERE id_usuario = ? OR id_usuario_compartido = ?");
        $stmt->execute([$usuario_id, $usuario_id]);

        // 2. Eliminar archivos del usuario
        $stmt = $conn->prepare("DELETE FROM archivos WHERE id_usuario = ?");
        $stmt->execute([$usuario_id]);

        // 3. Finalmente, eliminar al usuario
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario_id]);

        $conn->commit();
        $_SESSION['mensaje'] = "Usuario eliminado correctamente.";
    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION['error'] = "Error al eliminar usuario: " . $e->getMessage();
    }
}

header("Location: control-panel.php");
exit;
?>