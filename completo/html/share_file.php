<?php
session_start();
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$archivoId = $data['archivoId'];
$usuarioCompartido = $data['usuarioCompartido'];
$id_usuario = $_SESSION['usuario']; // Usuario que comparte el archivo

try {
    $stmt = $conn->prepare("INSERT INTO archivos_compartidos (id_usuario, id_archivo, id_usuario_compartido) 
                            VALUES (:id_usuario, :id_archivo, :id_usuario_compartido)");
    $stmt->execute([
        ':id_usuario' => $id_usuario,
        ':id_archivo' => $archivoId,
        ':id_usuario_compartido' => $usuarioCompartido
    ]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>