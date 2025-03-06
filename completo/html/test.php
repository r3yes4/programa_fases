<?php
// Asegúrate de tener el autoload de Composer
require 'vendor/autoload.php'; // Si usas Composer para cargar la biblioteca de MongoDB

// Conexión a MongoDB

try {
    // Codificar la contraseña para evitar problemas con los caracteres especiales
    $usuario = 'mongoadmin';
    $contraseña = 'mongop@ssw0rd';
    $contraseñaCodificada = urlencode($contraseña);

    // Conexión a MongoDB con la contraseña codificada
    $mongoClient = new MongoDB\Client("mongodb://{$usuario}:{$contraseñaCodificada}@mongo:27017/admin");
    echo "Conexión exitosa a MongoDB<br>";
    
} catch (Exception $e) {
    echo "Error de conexión a MongoDB: " . $e->getMessage() . "<br>";
    exit;
}
// Selección de la base de datos y colección
$mongoDb = $mongoClient->file_uploads; // Reemplaza 'test_database' con tu base de datos
$collection = $mongoDb->eliminados; // Reemplaza 'test_collection' con tu colección

// Datos a insertar
$data = [
    'nombre' => 'Juan Pérez',
    'email' => 'juan.perez@example.com',
    'fecha_creacion' => new MongoDB\BSON\UTCDateTime(), // Fecha y hora actual
];

// Intentar insertar el documento
try {
    $insertResult = $collection->insertOne($data);
    echo "Registro insertado con éxito. ID: " . $insertResult->getInsertedId() . "<br>";
} catch (Exception $e) {
    echo "Error al insertar el registro: " . $e->getMessage() . "<br>";
}
?>
