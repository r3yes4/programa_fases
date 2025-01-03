<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $admin = $_POST['admin'];

    // Hashear la contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, password, admin) VALUES (:usuario, :password, :admin)");
    $stmt->bindParam(':usuario', $username);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':admin', $admin);

    if ($stmt->execute()) {
        echo "Usuario agregado exitosamente.";
    } else {
        echo "Error al agregar el usuario.";
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Agregar Usuario</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
</head>
<body class="is-preload">
    <header id="header">
        <a href="index.php" class="title">BLEET</a>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="subir-archivos.php">Subir archivo</a></li>
                <li><a href="login.php">Iniciar sesión</a></li>
                <li><a href="control-panel.php">Panel de administración</a></li>
            </ul>
        </nav>
    </header>

    <div id="wrapper">
        <section id="main" class="wrapper">
            <div class="inner">
                <h1 class="major">Agregar Usuario</h1>
                <?php if (isset($stmt) && $stmt->execute()): ?>
                    <p style="color: green;">Usuario agregado exitosamente.</p>
                <?php elseif (isset($stmt)): ?>
                    <p style="color: red;">Error al agregar el usuario.</p>
                <?php endif; ?>
                <a href="control-panel.php" class="button">Volver al Panel de Administración</a>
            </div>
        </section>
    </div>
</body>
</html>