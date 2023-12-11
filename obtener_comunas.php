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

// Obtener el id de la región seleccionada desde la URL
$idRegion = $_GET['id_region'];

// Consulta para obtener las comunas según la región seleccionada
$sql = "SELECT nombre, id FROM sg_omil_comunas WHERE id_region = $idRegion";
$result = $conn->query($sql);

// Construir las opciones de las comunas
$options = "";
while ($row = $result->fetch_assoc()) {
    $options .= "<option value='" . $row['nombre'] . "'>" . $row['nombre'] . "</option>";
}


// Cerrar la conexión
$conn->close();

echo $options;
?>
