<?php
require 'db.php';

header('Content-Type: application/json');

$stmt = $conn->prepare("SELECT id_departamento, nombre FROM departamentos");
$stmt->execute();
$departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($departamentos);
?>