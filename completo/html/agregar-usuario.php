<?php
require 'db.php';

$result = null; // Variable para almacenar el resultado de la ejecución

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $admin = $_POST['admin'];
    $email = $_POST['email']; // Obtener el email desde el formulario
    $departamento = $_POST['departamento'];

    // Hashear la contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, password, email, admin) VALUES (:usuario, :password, :email, :admin)");
    $stmt->bindParam(':usuario', $username);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':email', $email); // Vincular el email
    $stmt->bindParam(':admin', $admin);
    $stmt->bindParam(':departamento', $departamento, PDO::PARAM_INT);
    
    $result = $stmt->execute();

    // Redirigir para evitar doble envío
    header("Location: agregar-usuario.php?success=" . ($result ? "1" : "0"));
    exit;
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
                <!-- Mostrar mensajes de éxito o error -->
                <?php if (isset($_GET['success'])): ?>
                    <?php if ($_GET['success'] == "1"): ?>
                        <p style="color: green;">✅ Usuario creado exitosamente.</p>
                    <?php else: ?>
                        <p style="color: red;">❌ Error al crear el usuario. Por favor, inténtalo de nuevo.</p>
                    <?php endif; ?>
                <?php endif; ?>
                <!-- Formulario de creación de usuario -->
                <form action="" method="post">
                    <div class="row gtr-uniform">
                        <div class="col-6 col-12-xsmall">
                            <label for="username">Usuario</label>
                            <input type="text" name="username" id="username" value="" placeholder="Usuario" required />
                        </div>
                        <div class="col-6 col-12-xsmall">
                            <label for="password">Contraseña</label>
                            <input type="password" name="password" id="password" value="" placeholder="Contraseña" required />
                        </div>
                        <div class="col-6 col-12-xsmall">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" name="email" id="email" value="" placeholder="Correo Electrónico" required />
                        </div>
                        <div class="col-12">
                            <label for="admin">Administrador</label>
                            <select name="admin" id="admin">
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <ul class="actions">
                                <li><input type="submit" value="Agregar Usuario" class="primary" /></li>
                                <li><input type="reset" value="Limpiar" /></li>
                            </ul>
                        </div>
                    </div>
                </form>
                <a href="control-panel.php" class="button">Volver al Panel de Administración</a>
            </div>
        </section>
    </div>
</body>
</html>
