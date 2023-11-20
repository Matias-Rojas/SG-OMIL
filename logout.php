<?php
// Incluye el archivo de configuración de la base de datos
include 'config.php';

// Inicia o reanuda la sesión
session_start();

// Cierra la sesión
session_destroy();

// Desconecta la conexión a la base de datos
mysqli_close($conexion);

// Redirige a la página de inicio de sesión o a donde lo desees
header('Location: login.html');
exit;
?>
