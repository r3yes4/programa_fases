<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];
$cuenta_borrada = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_borrado'])) {
    try {
        // Iniciar una transacción para asegurar la integridad de los datos
        $conn->beginTransaction();
        
        // 1. Primero borrar todos los archivos asociados al usuario
        $stmt = $conn->prepare("DELETE FROM archivos WHERE id_usuario = :usuario");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        
        // 2. Luego borrar el usuario
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE usuario = :usuario");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        
        // Confirmar la transacción si todo salió bien
        $conn->commit();
        
        session_unset();
        session_destroy();
        $cuenta_borrada = true;
    } catch (PDOException $e) {
        // Si hay algún error, revertir la transacción
        $conn->rollBack();
        $error = "Error al borrar la cuenta: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Borrar cuenta</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <style>
        .container {
            max-width: 600px;
            margin: 80px auto;
            background: #5e42a6;
            padding: 30px;
            border-radius: 10px;
            color: #fff;
            text-align: center;
        }
        button {
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        .danger {
            background-color: #c0392b;
            color: #fff;
        }
        .cancel {
            background-color: #777;
            color: #fff;
        }
    </style>
    <?php if ($cuenta_borrada): ?>
        <meta http-equiv="refresh" content="5;url=index.php">
    <?php endif; ?>
</head>
<body>
    <div class="container">
        <?php if ($cuenta_borrada): ?>
            <h2>Cuenta eliminada correctamente</h2>
            <p>Serás redirigido al inicio en unos segundos...</p>
        <?php else: ?>
            <h2>¿Estás seguro de que deseas borrar tu cuenta?</h2>
            <p>Esta acción es <strong>irreversible</strong> y eliminará todos tus datos asociados, incluyendo tus archivos.</p>

            <?php if (isset($error)): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST">
                <button type="submit" name="confirmar_borrado" class="danger">Sí, borrar mi cuenta</button>
                <a href="mi_cuenta.php" class="cancel">Cancelar</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>