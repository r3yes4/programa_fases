<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['borrar_cuenta'])) {
        $password = $_POST['password'];
        
        // Verificar la contraseña
        $stmt = $conn->prepare("SELECT password FROM usuarios WHERE usuario = :usuario");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($password, $user_data['password'])) {
            // Borrar la cuenta
            $stmt = $conn->prepare("DELETE FROM usuarios WHERE usuario = :usuario");
            $stmt->bindParam(':usuario', $usuario);
            
            if ($stmt->execute()) {
                // Destruir la sesión y redirigir
                session_destroy();
                header("Location: login.php?mensaje=cuenta_eliminada");
                exit;
            } else {
                $mensaje = "<p style='color: red;'>Error al eliminar la cuenta</p>";
            }
        } else {
            $mensaje = "<p style='color: red;'>Contraseña incorrecta, no se pudo eliminar la cuenta</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Borrar Cuenta</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <style>
        .container {
            margin-top: 50px;  
            display: flex;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            background: #5e42a6;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        .sidebar {
            width: 30%;
            padding: 20px;
            text-align: center;
            border-right: 2px solid #444;
        }
        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
        }
        .username {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
            color: #fff;
        }
        .sidebar nav ul {
            list-style: none;
            padding: 0;
        }
        .sidebar nav ul li {
            padding: 10px;
            background: #333;
            margin: 5px 0;
            border-radius: 5px;
            text-align: left;
        }
        .sidebar nav ul li a {
            color: #fff;
            text-decoration: none;
            display: block;
        }
        .sidebar nav ul li.active {
            background: #222;
        }
        .content {
            width: 70%;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            color: #fff;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #777;
            border-radius: 5px;
            background: #222;
            color: #fff;
        }
        button {
            background: #5e42a6;
            color: white;
            padding: 10px 15px;
            border: 1px solid white;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
            margin-top: 10px;
            text-transform: uppercase;
            font-weight: bold;
        }
        button:hover {
            background: #4a3685;
        }
        .delete-button {
            background-color: #ff3333;
        }
        .delete-button:hover {
            background-color: #cc0000;
        }
        .confirmation-box {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid #ff0000;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
        .mensaje {
            margin-bottom: 15px;
        }
    </style>
</head>
<body class="is-preload">
    <header id="header">
        <a href="index.php" class="title">BLEET</a>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="subir-archivos.php">Subir archivo</a></li>
                <li><a href="mi-cuenta.php" class="active">Mi Cuenta</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <div class="sidebar">
            <img src="assets/images/perfil.jpg" alt="Foto de perfil" class="profile-pic">
            <div class="username"><?php echo htmlspecialchars($usuario); ?></div>
            <nav>
                <ul>
                    <li>
                        <a href="mi-cuenta.php">Cuenta</a>
                    </li>
                    <li>
                        <a href="cambiar-password.php">Cambiar la contraseña</a>
                    </li>
                    <li>
                        <a href="logout.php">Cerrar sesión</a>
                    </li>
                    <li class="active">
                        <a href="borrar-cuenta.php">Borrar la cuenta</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="content">
            <?php if (isset($mensaje)): ?>
                <div class="mensaje">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            
            <h2 style="color:#fff;">Borrar la cuenta</h2>
            <p style="color:#fff;">Esta acción no se puede deshacer. Se eliminarán permanentemente todos tus datos.</p>
            
            <div class="confirmation-box">
                <form method="POST">
                    <div class="form-group">
                        <label>Introduce tu contraseña para confirmar</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" name="borrar_cuenta" class="delete-button">BORRAR MI CUENTA</button>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
</body>
</html>