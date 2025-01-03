<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'db.php';

$stmt = $conn->query("SELECT usuario, password FROM usuarios");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $hashed_password = password_hash($row['password'], PASSWORD_DEFAULT);
    $update_stmt = $conn->prepare("UPDATE usuarios SET password = :password WHERE usuario = :usuario");
    $update_stmt->bindParam(':password', $hashed_password);
    $update_stmt->bindParam(':usuario', $row['usuario']);
    $update_stmt->execute();
}

echo "Passwords updated successfully.";
?>