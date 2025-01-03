<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $uploadFile = $uploadDir . basename($_FILES['file']['name']);
    
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            // Ejecutar el script de Python para verificar el archivo
            $output = shell_exec("python3 check_virus.py");
            if ($output === null) {
                echo "Error al ejecutar el script de verificación.";
                unlink($uploadFile);
                exit;
            }
            $result = trim($output); // Eliminar espacios en blanco alrededor de la salida

            // Verificar el resultado del script de Python
            if ($result === '0') {
                header("Location: subir-archivos.php?message=success");
            } elseif ($result === '1') {
                header("Location: subir-archivos.php?message=virus");

            } else {
                header("Location: subir-archivos.php?message=error_verification");
            }
        } else {
            header("Location: subir-archivos.php?message=move_error");
        }
    } else {
        header("Location: subir-archivos.php?message=upload_error");
    }
    exit;
}
?>