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
    if (isset($_POST['cambiar_password'])) {
        $password_actual = $_POST['password_actual'];
        $nuevo_password = $_POST['nuevo_password'];
        $confirmar_password = $_POST['confirmar_password'];
        
        // Verificar que las contraseñas nuevas coincidan
        if ($nuevo_password !== $confirmar_password) {
            $mensaje = "<p style='color: red;'>Las contraseñas nuevas no coinciden</p>";
        } else {
            // Verificar la contraseña actual
            $stmt = $conn->prepare("SELECT password FROM usuarios WHERE usuario = :usuario");
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password_actual, $user_data['password'])) {
                // Actualizar la contraseña
                $password_hash = password_hash($nuevo_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE usuarios SET password = :password WHERE usuario = :usuario");
                $stmt->bindParam(':password', $password_hash);
                $stmt->bindParam(':usuario', $usuario);
                
                if ($stmt->execute()) {
                    $mensaje = "<p style='color: green;'>Contraseña actualizada correctamente</p>";
                } else {
                    $mensaje = "<p style='color: red;'>Error al actualizar la contraseña</p>";
                }
            } else {
                $mensaje = "<p style='color: red;'>La contraseña actual es incorrecta</p>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cambiar Contraseña</title>
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
                    <li class="active">
                        <a href="cambiar-password.php">Cambiar la contraseña</a>
                    </li>
                    <li>
                        <a href="logout.php">Cerrar sesión</a>
                    </li>
                    <li>
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
            
            <h2 style="color:#fff;">Cambiar la contraseña</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Contraseña actual</label>
                    <input type="password" name="password_actual" required>
                </div>
                <div class="form-group">
                    <label>Nueva contraseña</label>
                    <input type="password" name="nuevo_password" required>
                </div>
                <div class="form-group">
                    <label>Confirmar nueva contraseña</label>
                    <input type="password" name="confirmar_password" required>
                </div>
                <button type="submit" name="cambiar_password">CAMBIAR CONTRASEÑA</button>
            </form>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
</body>
</html>