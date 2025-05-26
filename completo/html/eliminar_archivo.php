<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['archivo_id'])) {
    require 'db.php'; // Conexión a la BD MySQL
    require 'vendor/autoload.php'; // Asegúrate de tener Composer autoload (MongoDB)

    try {
        // Codificar la contraseña para evitar problemas con los caracteres especiales
        $usuario = 'mongoadmin';
        $contraseña = 'mongop@ssw0rd';
        $contraseñaCodificada = urlencode($contraseña);
    
        
        $mongoClient = new MongoDB\Client("mongodb://{$usuario}:{$contraseñaCodificada}@mongo:27017/admin");
        $mongoDb = $mongoClient->file_uploads; 
        $logCollection = $mongoDb->eliminados; 
        
    } catch (Exception $e) {
        echo "Error de conexión a MongoDB: " . $e->getMessage() . "<br>";
        exit;
    }

    

    $archivo_id = $_GET['archivo_id'];

    // Obtener la ruta del archivo antes de eliminarlo
    $stmt = $conn->prepare("SELECT ruta_archivo FROM archivos WHERE id = ?");
    $stmt->execute([$archivo_id]);
    $archivo = $stmt->fetch();

    if ($archivo) {
        $rutaArchivo = $archivo['ruta_archivo'];

        // Guardar el registro de eliminación en MongoDB antes de eliminar el archivo
        $logData = [
            'archivo_id' => $archivo_id,
            'ruta_archivo' => $rutaArchivo,
            'fecha_eliminacion' => new MongoDB\BSON\UTCDateTime(), // Fecha y hora actual
            'usuario_id' => $_SESSION['usuario'], // Suponiendo que el ID del usuario está en la sesión
        ];
        
        // Insertar el registro en MongoDB
        $logCollection->insertOne($logData);

        // Eliminar el archivo del servidor
        if (file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
        }

        // Eliminar el registro de la base de datos MySQL
        $stmt = $conn->prepare("DELETE FROM archivos WHERE id = ?");
        $stmt->execute([$archivo_id]);

        // Redirigir con mensaje de éxito
        header("Location: archivos.php?msg=success");
        exit;
    } else {
        // Si no se encuentra el archivo
        header("Location: index.php?msg=error");
        exit;
    }
} else {
    // Si la solicitud no es válida
    header("Location: index.php");
    exit;
}
?>


