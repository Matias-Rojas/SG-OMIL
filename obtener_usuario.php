<?php
// obtener_usuario.php

// Conectarse a la base de datos
$servername = "localhost";
$db_username = "HURDOX";
$db_password = "gokudeus2023";
$database = "sg_omil_usuarios";

$conn = new mysqli($servername, $db_username, $db_password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener el ID del usuario de la solicitud GET
$userId = $_GET["id"];

// Consulta para obtener los datos del usuario por ID
$sql = "SELECT id, username, email, rol FROM sg_omil_usuarios WHERE id = $userId";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $userData = $result->fetch_assoc();
    // Devolver los datos del usuario en formato JSON
    echo json_encode($userData);
} else {
    echo "Usuario no encontrado";
}

// Cerrar la conexión
$conn->close();
?>
