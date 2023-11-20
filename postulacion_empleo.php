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
$sql = "SELECT OfertaEmpleoID, CONCAT(' Id de Oferta: ',OfertaEmpleoID, ' | ', ' Nombre de Oferta: ', NombreVacante, ' | ', 'Rubro Oferta: ',RubroOferta, ' | ', ' Rut de Empresa: ', RutEmpresa,' | ', 'Nombre Empresa: ', NombreEmpresa) AS OfertaNombreCap FROM sg_omil_ofertasempleo";
$result = $conn->query($sql);

$OfertaEmpleoID = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $OfertaEmpleoID[] = $row;
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
    $verificar_datos = isset($_POST["verificar_datos"]) ? 1 : 0;
    $rango_etario = $_POST["rango_etario"];
    $oferta_empleo = $_POST["oferta_empleo"];

    // Realiza la inserción en la tabla de datos de postulación
    $sql_insert = "INSERT INTO sg_omil_datos_postulacion_empleo (Rut, VerificarDatosUsuario, RangoEtario, OfertaEmpleoID) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);

    $stmt_insert->bind_param("sssi", $rut, $verificar_datos, $rango_etario, $oferta_empleo);

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
    <title>Postular Usuario Vecinal a Oferta de Empleo</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Postular Usuario Vecinal a Oferta de Empleo</h2>
    <form method="POST" action="postulacion_empleo.php">
        <label for="rut">RUT de Usuario Vecinal:</label>
        <select id="rut" name="rut" required>
            <?php
            // Genera las opciones del menú desplegable con los RUTs de los usuarios vecinales obtenidos desde la base de datos
            foreach ($ruts_usvecinal as $rut) {
                echo "<option value='$rut'>$rut</option>";
            }
            ?>
        </select><br><br>

        <!-- Verificar datos del usuario -->
        <label for="verificar_datos">Verificar datos del usuario:</label>
        <input type="checkbox" name="verificar_datos">

        <!-- Verificar rango etario de la oferta -->
        <label for="rango_etario">Verificar rango etario de la oferta:</label>
        <select name="rango_etario" id="rango_etario" required>
            <option value="Infancia">Infancia</option>
            <option value="Adolecencia">Adolecencia</option>
            <option value="Adultez">Adultez</option>
            <option value="Vejez">Vejez</option>
        </select><br><br>

        <label for="oferta_empleo">Oferta de Empleo:</label>
        <select id="oferta_empleo" name="oferta_empleo" required>
            <?php
            // Genera las opciones del menú desplegable con los IDs de las ofertas de capacitación
            foreach ($OfertaEmpleoID as $oferta) {
                echo "<option value='" . $oferta["OfertaEmpleoID"] . "'>" . $oferta["OfertaNombreCap"] . "</option>";
            }
            ?>
        </select><br><br>

        <input type="submit" value="Postular">
    </form>
    <a href="welcome.php">Volver al Inicio</a>
</body>
</html>
