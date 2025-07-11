<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];

// Obtener los datos del usuario desde la base de datos
$stmt = $conn->prepare("SELECT nombre, apellidos, email, admin FROM usuarios WHERE usuario = :usuario");
$stmt->bindParam(':usuario', $usuario);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$_SESSION['is_admin'] = isset($user['admin']) && $user['admin'] == 1 ? 1 : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['actualizar_cuenta'])) {
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $email = $_POST['email'];

        // Actualizar datos
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = :nombre, apellidos = :apellidos, email = :email WHERE usuario = :usuario");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':usuario', $usuario);
        
        if ($stmt->execute()) {
            $mensaje = "<p style='color: green;'>Cuenta actualizada correctamente</p>";
        } else {
            $mensaje = "<p style='color: red;'>Error al actualizar la cuenta</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mi Cuenta</title>
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
                <li><a href="archivos.php">Mis Archivos</a></li>
                <li><a href="subir-archivos.php">Subir archivo</a></li>
                <?php if ($_SESSION['is_admin'] == 1): ?>
                    <li>
                        <a href="control-panel.php">Panel de administración</a>
                    </li>
                <?php endif; ?>
                <li><a href="mi_cuenta.php" class="active">Mi Cuenta</a></li>
            </ul>
        </nav>
    </header>
    <div class="container" style="margin-bottom: 50px;">
        <div class="sidebar">
            <div class="username"><?php echo htmlspecialchars($usuario); ?></div>
            <nav>
                <ul>
                    <li class="active">
                        <a href="mi_cuenta.php">Cuenta</a>
                    </li>
                    <li>
                        <a href="cambiar-password.php">Cambiar la contraseña</a>
                    </li>
                    <li>
                        <a href="logout.php">Cerrar sesión</a>
                    </li>
                    <li>
                        <a href="borrar_cuenta.php">Borrar la cuenta</a>
                    </li>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                    <li>
                        <a href="control-panel.php">Panel de administración</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <div class="content">
            <?php if (isset($mensaje)): ?>
                <div class="mensaje">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            <h2 style="color:#fff;">Cuenta</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Nombre de usuario</label>
                    <input type="text" value="<?= htmlspecialchars($usuario) ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($user['nombre'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Apellidos</label>
                    <input type="text" name="apellidos" value="<?= htmlspecialchars($user['apellidos'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Dirección de correo electrónico</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                </div>
                <button type="submit" name="actualizar_cuenta">ACTUALIZAR CUENTA</button>
            </form>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
    <footer id="footer" class="wrapper alt">
        <div class="inner">
            <ul class="menu">
                <li>&copy; Untitled. All rights reserved.</li><li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
            </ul>
        </div>
    </footer>
</body>
</html>