<?php
function generate_aes_key($password) {
    return substr(hash("sha512", $password, true), 0, 32);
}

function decrypt_and_download($input_file, $password) {
    $key = generate_aes_key($password);

    if (!file_exists($input_file)) {
        http_response_code(404);
        echo "Archivo no encontrado.";
        exit;
    }

    $data = file_get_contents($input_file);
    $iv = substr($data, 0, 16);
    $ciphertext = substr($data, 16);

    $decrypted = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

    if ($decrypted === false) {
        http_response_code(500);
        echo "Error al descifrar el archivo.";
        exit;
    }

    $filename = basename($input_file, '.aes');

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Content-Length: ' . strlen($decrypted));
    echo $decrypted;
    exit;
}

// Uso
$password = "hola123456789hola";
$input_file = $_GET['file'] ?? null;

if ($input_file) {
    decrypt_and_download($input_file, $password);
} else {
    echo "Archivo no especificado.";
}
?>
