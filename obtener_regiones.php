<?php
// Conexión a la base de datos (reemplaza los valores con los tuyos)
$servername = "localhost";
$username = "HURDOX";
$password = "gokudeus2023";
$dbname = "sg_omil";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para obtener las regiones
$sql = "SELECT nombre, id FROM sg_omil_regiones";
$result = $conn->query($sql);

// Construir las opciones de las regiones
$options = "";
while ($row = $result->fetch_assoc()) {
    $options .= "<option value='" . $row['id'] . "'>" . $row['nombre'] . "</option>";
}

// Cerrar la conexión
$conn->close();

echo $options;
?>
