<?php
require 'db.php'; // Asegúrate de que este archivo contiene la conexión a la base de datos

$result = null; // Variable para almacenar el resultado de la ejecución

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dep_name = trim($_POST['dep_name']); // Obtener el nombre del departamento

    if (!empty($dep_name)) {
        // Preparar y ejecutar la consulta
        $stmt = $conn->prepare("INSERT INTO departamentos (nombre) VALUES (:nombre)");
        $stmt->bindParam(':nombre', $dep_name);
        $result = $stmt->execute();
    }

    // Redirigir para evitar doble envío
    header("Location: agregar-departamento.php?success=" . ($result ? "1" : "0"));
    exit;
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Agregar Departamento</title>
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
                <h1 class="major">Agregar Departamento</h1>

                <!-- Mostrar mensajes de éxito o error -->
                <?php if (isset($_GET['success'])): ?>
                    <?php if ($_GET['success'] == "1"): ?>
                        <p style="color: green;">✅ Departamento creado exitosamente.</p>
                    <?php else: ?>
                        <p style="color: red;">❌ Error al crear el departamento. Por favor, inténtalo de nuevo.</p>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Formulario de creación de departamento -->
                <form action="" method="post">
                    <div class="row gtr-uniform">
                        <div class="col-6 col-12-xsmall">
                            <label for="dep_name">Nombre del Departamento</label>
                            <input type="text" name="dep_name" id="dep_name" value="" placeholder="Nombre del departamento" required />
                        </div>
                        <div class="col-12">
                            <ul class="actions">
                                <li><input type="submit" value="Agregar Departamento" class="primary" /></li>
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
