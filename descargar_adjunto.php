<?php
// Verifica si el archivo existe y es legible
if (isset($_GET['filename'])) {
    $filename = "Archivos/" . basename(urldecode($_GET['filename']));
    if (file_exists($filename) && is_readable($filename)) {
        // Establece las cabeceras para la descarga
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        // Lee el archivo y envíalo al navegador
        readfile($filename);
        exit;
    } else {
        echo "El archivo no existe o no se puede leer.";
    }
} else {
    echo "Nombre de archivo no proporcionado.";
}
?>
