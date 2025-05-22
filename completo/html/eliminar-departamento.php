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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dep_id'])) {
    $departamento_id = $_POST['dep_id'];

    try {
        // Verificar si hay usuarios asociados al departamento
        $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE id_departamento = ?");
        $stmt->execute([$departamento_id]);
        $tiene_usuarios = $stmt->fetchColumn();

        if ($tiene_usuarios > 0) {
            $_SESSION['error'] = "No se puede eliminar: Hay usuarios asignados a este departamento.";
        } else {
            $stmt = $conn->prepare("DELETE FROM departamentos WHERE id_departamento = ?");
            $stmt->execute([$departamento_id]);
            $_SESSION['mensaje'] = "Departamento eliminado correctamente.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al eliminar departamento: " . $e->getMessage();
    }
}

header("Location: control-panel.php");
exit;
?>