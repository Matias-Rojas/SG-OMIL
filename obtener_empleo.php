<?php
// Realiza la conexión a la base de datos (puedes reutilizar el código de conexión existente)
$servername = "localhost";
$db_username = "HURDOX";
$db_password = "gokudeus2023";
$database = "sg_omil";

$conn = new mysqli($servername, $db_username, $db_password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener el rut del usuario vecinal seleccionado desde la solicitud POST
$rut_usuario = isset($_POST["rut_usuario"]) ? $_POST["rut_usuario"] : '';

// Consulta para obtener las capacitaciones asociadas al usuario
$sql_capacitaciones = "SELECT oc.NombreVacante 
                        FROM `sg_omil_datos_postulacion_empleo` AS sc 
                        JOIN sg_omil_ofertasempleo AS oc ON sc.OfertaEmpleoID = oc.OfertaEmpleoID 
                        JOIN sg_omil_usuariosvecinales AS uv ON sc.Rut = uv.Rut 
                        WHERE uv.Rut = '$rut_usuario'";

$result_capacitaciones = $conn->query($sql_capacitaciones);

$capacitaciones = array();

if ($result_capacitaciones->num_rows > 0) {
    while ($row_capacitacion = $result_capacitaciones->fetch_assoc()) {
        $capacitaciones[] = $row_capacitacion["NombreVacante"];
    }
}

// Generar las opciones del select con las capacitaciones asociadas al usuario
foreach ($capacitaciones as $capacitacion) {
    echo "<option value='$capacitacion'>$capacitacion</option>";
}

$conn->close();
?>
