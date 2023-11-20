<?php
// Configuración de la base de datos
$host = 'localhost'; // Cambia esto al host de tu base de datos
$usuario = 'HURDOX'; // Cambia esto a tu nombre de usuario de la base de datos
$contrasena = 'gokudeus2023'; // Cambia esto a tu contraseña de la base de datos
$base_datos = 'sg_omil'; // Cambia esto al nombre de tu base de datos

// Crear una conexión a la base de datos
$conexion = mysqli_connect($host, $usuario, $contrasena, $base_datos);

// Verificar la conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>
