<?php
session_start();

// Verificar si el usuario está autenticado
if (isset($_SESSION['usuario'])) {
    header("Location: mi_cuenta.php");
    exit;
}

require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['is_admin'] = $user['admin'];
        if ($user['admin'] == 1) {
            header("Location: control-panel.php");
        } else {
            header("Location: subir-archivos.php");
        }
        exit;
    } else {
        $error = "Nombre de usuario o contraseña incorrectos.";
    }
}

// Verificar si viene del intento de subir archivo sin sesión
$showAlert = isset($_GET['from_upload']) && $_GET['from_upload'] == 'true';
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Login</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
    <script>
        <?php if ($showAlert): ?>
        window.onload = function() {
            alert("Debes iniciar sesión para poder subir y compartir archivos.");
        };
        <?php endif; ?>
    </script>
</head>
<body class="is-preload">

<!-- Header -->
<header id="header">
    <a href="index.php" class="title">BLEET</a>
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li>
                <?php if(isset($_SESSION['usuario'])): ?>
                    <a href="subir-archivos.php">Subir archivo</a>
                <?php else: ?>
                    <a href="#" onclick="alert('Para hacer esto inicie sesión'); return false;">Subir archivo</a>
                <?php endif; ?>
            </li>
            <li><a href="login.php" class="active">Iniciar sesión</a></li>
        </ul>
    </nav>
</header>

<!-- Wrapper -->
<div id="wrapper">

    <!-- Main -->
    <section id="main" class="wrapper">
        <div class="inner">
            <h1 class="major"></h1>

            <!-- Form -->
            <section>
                <h2>Login</h2>
                <?php if (!empty($error)): ?>
                    <p style="color: red;"><?php echo $error; ?></p>
                <?php endif; ?>
                <form method="post" action="">
                    <div class="row gtr-uniform">
                        <div class="col-6 col-12-xsmall">
                            <input type="text" name="usuario" id="usuario" value="" placeholder="Nombre de usuario" />
                        </div>
                        <div class="col-6 col-12-xsmall">
                            <input type="password" name="password" id="password" value="" placeholder="Contraseña" />
                        </div>
                        <div class="col-12">
                            <ul class="actions">
                                <li><input type="submit" value="Iniciar sesión" class="primary" /></li>
                            </ul>
                        </div>
                    </div>
                </form>
            </section>

        </div>
    </section>
</div>
</body>
</html>