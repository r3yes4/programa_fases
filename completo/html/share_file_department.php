<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$usuario = $_SESSION['usuario'];  // El identificador del usuario es 'usuario'
$archivoId = $data['archivoId'] ?? null;
$departamentoCompartido = $data['departamentoCompartido'] ?? null;

if (!$archivoId || !$departamentoCompartido) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

// Verificar que el archivo pertenece al usuario que lo comparte
$stmt = $conn->prepare("SELECT id FROM archivos WHERE id = :archivoId AND id_usuario = :usuario");
$stmt->execute([':archivoId' => $archivoId, ':usuario' => $usuario]);

if ($stmt->rowCount() === 0) {
    echo json_encode(['success' => false, 'message' => 'No tienes permiso para compartir este archivo']);
    exit;
}

// Obtener los usuarios del departamento
$stmt = $conn->prepare("SELECT u.usuario FROM usuarios u
                        INNER JOIN departamentos d ON u.id_departamento = d.id_departamento
                        WHERE d.nombre = :departamento");
$stmt->execute([':departamento' => $departamentoCompartido]);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$usuarios) {
    echo json_encode(['success' => false, 'message' => 'No hay usuarios en este departamento']);
    exit;
}

// Compartir el archivo con los usuarios del departamento
foreach ($usuarios as $usuarioComp) {
    // Verificar si el archivo ya está compartido con el usuario
    $stmt = $conn->prepare("SELECT 1 FROM archivos_compartidos 
                            WHERE id_archivo = :archivoId AND id_usuario_compartido = :usuarioComp");
    $stmt->execute([':archivoId' => $archivoId, ':usuarioComp' => $usuarioComp['usuario']]);

    // Si no está compartido, insertamos el archivo en la tabla 'archivos_compartidos'
    if ($stmt->rowCount() === 0) {
        $stmt = $conn->prepare("INSERT INTO archivos_compartidos (id_archivo, id_usuario, id_usuario_compartido) 
                                VALUES (:archivoId, :usuario, :usuarioComp)");
        $stmt->execute([':archivoId' => $archivoId, ':usuario' => $usuario, ':usuarioComp' => $usuarioComp['usuario']]);
    }
}

echo json_encode(['success' => true, 'message' => 'Archivo compartido con el departamento correctamente']);
?>

