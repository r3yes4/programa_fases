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
    <style>
        /* Estilo para el alert de error */
        .error {
            position: fixed;
            top: 20px;
            right: 20px;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            width: 320px;
            padding: 12px;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: start;
            background: #EF665B;
            border-radius: 8px;
            box-shadow: 0px 0px 5px -3px #111;
            z-index: 1000;
            animation: slideIn 0.3s forwards, fadeOut 0.5s forwards 3s;
        }
        
        .error__icon {
            width: 20px;
            height: 20px;
            transform: translateY(-2px);
            margin-right: 8px;
        }
        
        .error__icon path {
            fill: #fff;
        }
        
        .error__title {
            font-weight: 500;
            font-size: 14px;
            color: #fff;
        }
        
        .error__close {
            width: 20px;
            height: 20px;
            cursor: pointer;
            margin-left: auto;
        }
        
        .error__close path {
            fill: #fff;
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>
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
                    <a href="#" onclick="showLoginAlert(); return false;">Subir archivo</a>
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
    <script>
        function showLoginAlert() {
            // Crear el elemento del alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'error';
            alertDiv.innerHTML = `
                <div class="error__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" height="24" fill="none"><path fill="#fff" d="m13 13h-2v-6h2zm0 4h-2v-2h2zm-1-15c-1.3132 0-2.61358.25866-3.82683.7612-1.21326.50255-2.31565 1.23915-3.24424 2.16773-1.87536 1.87537-2.92893 4.41891-2.92893 7.07107 0 2.6522 1.05357 5.1957 2.92893 7.0711.92859.9286 2.03098 1.6651 3.24424 2.1677 1.21325.5025 2.51363.7612 3.82683.7612 2.6522 0 5.1957-1.0536 7.0711-2.9289 1.8753-1.8754 2.9289-4.4189 2.9289-7.0711 0-1.3132-.2587-2.61358-.7612-3.82683-.5026-1.21326-1.2391-2.31565-2.1677-3.24424-.9286-.92858-2.031-1.66518-3.2443-2.16773-1.2132-.50254-2.5136-.7612-3.8268-.7612z"></path></svg>
                </div>
                <div class="error__title">Para subir archivos, por favor inicie sesión</div>
                <div class="error__close" onclick="this.parentElement.remove()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" viewBox="0 0 20 20" height="20"><path fill="#fff" d="m15.8333 5.34166-1.175-1.175-4.6583 4.65834-4.65833-4.65834-1.175 1.175 4.65833 4.65834-4.65833 4.6583 1.175 1.175 4.65833-4.6583 4.6583 4.6583 1.175-1.175-4.6583-4.6583z"></path></svg>
                </div>
            `;
            
            // Añadir el alert al cuerpo del documento
            document.body.appendChild(alertDiv);
            
            // Eliminar el alert después de la animación
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 3500);
        }
    </script>
</div>
</body>
</html>