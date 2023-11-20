<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.html");
    exit;
}

// Realiza la conexión a la base de datos
$servername = "localhost";
$db_username = "HURDOX";
$db_password = "gokudeus2023";
$database = "sg_omil";

$conn = new mysqli($servername, $db_username, $db_password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta para obtener las ofertas de capacitación disponibles
$sql = "SELECT OfertaCapacitacionID, CONCAT(' Id de Oferta: ',OfertaCapacitacionID, ' | ', ' Nombre de Capacitación: ', NombreCurso, ' | ', ' Rut de Empresa Otec: ', RutEmpresaOTEC, ' | ', ' Nombre de OTEC: ', NombreOTEC) AS OfertaNombreCap FROM sg_omil_ofertascapacitacion";
$result = $conn->query($sql);

$OfertaCapacitacionID = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $OfertaCapacitacionID[] = $row;
    }
}

// Consulta para obtener los RUTs de los usuarios vecinales desde la base de datos
$sql = "SELECT Rut FROM sg_omil_usuariosvecinales";
$result = $conn->query($sql);
        
$ruts_usvecinal = array();
        
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ruts_usvecinal[] = $row["Rut"];
    }
}

// Comprueba si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtiene los datos del formulario
    $rut = $_POST["rut"];
    $porcentaje_rsh = $_POST["porcentaje_rsh"];
    $nivel_estudios = $_POST["nivel_estudios"];
    $jefa_hogar = isset($_POST["jefa_hogar"]) ? 1 : 0;
    $discapacidad = isset($_POST["discapacidad"]) ? 1 : 0;
    $rango_etario = $_POST["rango_etario"];
    $puntaje_entrevista = $_POST["puntaje_entrevista"];
    $oferta_capacitacion = $_POST["oferta_capacitacion"];

    // Realiza la inserción en la tabla de datos de postulación
    $sql_insert = "INSERT INTO sg_omil_datos_postulacion_capacitacion (Rut, PorcentajeRSH, NivelEstudios, EsJefaHogar, Discapacidad, RangoEtario, PuntajeEntrevista, OfertaCapacitacionID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);

    $stmt_insert->bind_param("sdsiiisi", $rut, $porcentaje_rsh, $nivel_estudios, $jefa_hogar, $discapacidad, $rango_etario, $puntaje_entrevista, $oferta_capacitacion);

    if ($stmt_insert->execute()) {
        // Éxito al postular
        echo "Postulación exitosa.";
    } else {
        // Error al postular
        echo "Error al postular.";
    }

    $stmt_insert->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postular Usuario Vecinal a Capacitación</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Postular Usuario Vecinal a Capacitación</h2>
    <form method="POST" action="postulacion_capacitacion.php">
        <label for="rut">RUT de Usuario Vecinal:</label>
        <select id="rut" name="rut" required>
            <?php
            // Genera las opciones del menú desplegable con los RUTs de los usuarios vecinales obtenidos desde la base de datos
            foreach ($ruts_usvecinal as $rut) {
                echo "<option value='$rut'>$rut</option>";
            }
            ?>
        </select><br><br>

        <label for="porcentaje_rsh">Porcentaje RSH:</label>
        <input type="text" id="porcentaje_rsh" name="porcentaje_rsh" required><br><br>

        <label for="nivel_estudios">Nivel de Estudios:</label>
        <input type="text" id="nivel_estudios" name="nivel_estudios" required><br><br>

        <label for="jefa_hogar">¿Es Jefa de Hogar?</label>
        <input type="checkbox" id="jefa_hogar" name="jefa_hogar"><br><br>

        <label for="discapacidad">¿Tiene Discapacidad?</label>
        <input type="checkbox" id="discapacidad" name="discapacidad"><br><br>

        <label for="rango_etario">Rango Etario:</label>
        <input type="text" id="rango_etario" name="rango_etario" required><br><br>

        <label for="puntaje_entrevista">Puntaje Total de la Entrevista:</label>
        <input type="text" id="puntaje_entrevista" name="puntaje_entrevista" required><br><br>

        <label for="oferta_capacitacion">Oferta de Capacitación:</label>
        <select id="oferta_capacitacion" name="oferta_capacitacion" required>
            <?php
            // Genera las opciones del menú desplegable con los IDs de las ofertas de capacitación
            foreach ($OfertaCapacitacionID as $oferta) {
                echo "<option value='" . $oferta["OfertaCapacitacionID"] . "'>" . $oferta["OfertaNombreCap"] . "</option>";
            }
            ?>
        </select><br><br>

        <input type="submit" value="Postular">
    </form>
    <a href="welcome.php">Volver al Inicio</a>
</body>
</html>
