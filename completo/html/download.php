<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['usuario'])) {
    http_response_code(403);
    die("Acceso denegado.");
}

if ($_SERVER['HTTP_REFERER'] !== 'http://*/download') {
    http_response_code(403);
    die("Acceso denegado.");
}

$usuario = $_SESSION['usuario'];
$password = "hola123456789hola";
$key = substr(hash("sha512", $password, true), 0, 32);

if (!isset($_GET['file'])) {
    http_response_code(400);
    die("Archivo no especificado.");
}

$nombreArchivo = basename($_GET['file']);
$rutaArchivo = __DIR__ . "/uploads/limpios/" . $nombreArchivo;

// Verificación de ruta
$realPath = realpath($rutaArchivo);
$allowedPath = realpath(__DIR__ . "/uploads/limpios");
if (strpos($realPath, $allowedPath) !== 0 || !file_exists($realPath)) {
    http_response_code(404);
    die("Archivo no encontrado o acceso no autorizado.");
}

// Conexión segura con PDO
try {
    $pdo = new PDO("mysql:host=db;dbname=bleet", "root", "rootp@ssw0rd");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    die("Error de conexión: " . $e->getMessage());
}

// Verificación de acceso: dueño o compartido
$sql = "
    SELECT a.id 
    FROM archivos a
    LEFT JOIN archivos_compartidos ac ON a.id = ac.id_archivo
    WHERE a.ruta_archivo LIKE CONCAT('%', :nombreArchivo)
      AND (a.id_usuario = :usuario OR ac.id_usuario_compartido = :usuario)
    LIMIT 1
";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':nombreArchivo' => $nombreArchivo,
    ':usuario' => $usuario
]);

if ($stmt->rowCount() === 0) {
    http_response_code(403);
    die("No tienes permiso para descargar este archivo.");
}

// Leer y descifrar archivo
$handle = fopen($realPath, "rb");
$iv = fread($handle, 16);
$ciphertext = stream_get_contents($handle);
fclose($handle);

$plaintext = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
if ($plaintext === false) {
    http_response_code(500);
    die("Error al descifrar el archivo.");
}

// Nombre sin ID ni extensión .aes
$nombreOriginal = preg_replace('/^\d+_/', '', basename($nombreArchivo, '.aes'));

// Descargar
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $nombreOriginal . '"');
header('Content-Length: ' . strlen($plaintext));
echo $plaintext;
exit;
?>

