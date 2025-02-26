<?php
session_start();
ob_start(); // Iniciar el buffer de salida

// Incluir el archivo de conexión a la base de datos
require 'db.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: subir-archivos.php?message=session_error");
    exit;
}

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar si se han subido archivos o carpetas
    if (
        (!empty($_FILES['file']['name'][0])) || 
        (!empty($_FILES['folder']['name'][0]))
    ) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $id_usuario = $_SESSION['usuario'];
        $uploadSuccess = false; // Cambiar a 'false' si *ningún* archivo se subió
        $atLeastOneSuccess = false; // Nueva variable para rastrear si *algún* archivo se subió

        // Si hay archivos en la carpeta, fusionarlos con los archivos normales
        if (!empty($_FILES['folder']['name'][0])) {
            $_FILES['file']['name'] = array_merge($_FILES['file']['name'] ?? [], $_FILES['folder']['name']);
            $_FILES['file']['tmp_name'] = array_merge($_FILES['file']['tmp_name'] ?? [], $_FILES['folder']['tmp_name']);
            $_FILES['file']['error'] = array_merge($_FILES['file']['error'] ?? [], $_FILES['folder']['error']);
        }

        foreach ($_FILES['file']['name'] as $i => $name) {
            if ($_FILES['file']['error'][$i] !== UPLOAD_ERR_OK) {
                error_log("Error en el archivo '$name': Código " . $_FILES['file']['error'][$i]);
                continue; // Solo saltar este archivo en caso de error
            }

            // Obtener la ruta completa de la carpeta desde el nombre del archivo
            $relativeFolderPath = dirname($_FILES['file']['name'][$i]);

            // Si la ruta es '.', asignar la raíz '/'
            if ($relativeFolderPath === '.') {
                $relativeFolderPath = '/';
            }

            // Generar nombre único para el archivo
            $fileName = uniqid() . '_' . basename($name);
            $uploadFile = $uploadDir . $fileName;

            // Mover el archivo a la carpeta de subidas
            if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $uploadFile)) {
                try {
                    // Insertar el archivo en la base de datos
                    // Guardar también la ruta de la carpeta en la base de datos
                    $stmt = $conn->prepare("INSERT INTO archivos (ruta_archivo, id_usuario, ruta_carpeta) 
                                            VALUES (:ruta, :id_usuario, :ruta_carpeta)");
                    if ($stmt->execute([':ruta' => $uploadFile, ':id_usuario' => $id_usuario, ':ruta_carpeta' => $relativeFolderPath])) {
                        $uploadSuccess = true;
                        $atLeastOneSuccess = true; // Marcar que al menos un archivo se subió correctamente
                    } else {
                        error_log("Error al insertar en la base de datos.");
                    }
                } catch (PDOException $e) {
                    error_log("Error en la base de datos: " . $e->getMessage());
                }
            } else {
                error_log("Error al mover el archivo '$name'.");
            }
        }

        // Redirigir según el resultado
        if ($atLeastOneSuccess && $uploadSuccess) {
            header("Location: subir-archivos.php?message=success");
        } elseif ($atLeastOneSuccess) {
            header("Location: subir-archivos.php?message=partial_success");
        } else {
            header("Location: subir-archivos.php?message=upload_error");
        }
        exit;
    } else {
        // No se subieron archivos
        header("Location: subir-archivos.php?message=no_files");
        exit;
    }
}
?>



