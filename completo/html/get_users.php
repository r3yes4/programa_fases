<?php
session_start();
require 'db.php';

$stmt = $conn->prepare("SELECT usuario FROM usuarios");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($users);
?>