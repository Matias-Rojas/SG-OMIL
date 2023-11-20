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

// Consulta para obtener los Ids de ofertas de empleo desde la base de datos
$sql = "SELECT OfertaEmpleoID, CONCAT(' Id de Oferta: ',OfertaEmpleoID, ' | ',' Nombre de Oferta ',Nombre, ' | ', ' Rut de Empresa de Oferta: ', RutEmpresa) AS OfertaNombre FROM sg_omil_ofertasempleo";
$result = $conn->query($sql);

$OfertaEmpleoID = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $OfertaEmpleoID[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $oferta_empleo_id = $_POST["oferta_empleo"];
    $perfil_cargo = $_POST["perfil_cargo"];
    $experiencia = $_POST["experiencia"];
    $estudios_requeridos = $_POST["estudios_requeridos"];
    $otro = $_POST["otro"];
    $grupos_objetivos = $_POST["grupos_objetivos"];

    // Query para insertar el nuevo perfil de empleo
    $sql_insert = "INSERT INTO sg_omil_perfilempleo (OfertaEmpleoID, PerfilCargo, Experiencia, EstudiosRequeridos, Otro, GruposObjetivos) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("isssss", $oferta_empleo_id, $perfil_cargo, $experiencia, $estudios_requeridos, $otro, $grupos_objetivos);

    if ($stmt_insert->execute()) {
        // Éxito al registrar el perfil de empleo
        echo "Perfil de empleo registrado con éxito.";
    } else {
        // Error al registrar el perfil de empleo
        echo "Error al registrar el perfil de empleo.";
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
    <title>Registro de Perfil de Empleo</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Registro de Perfil de Empleo</h2>
    <form method="POST" action="registro_perfil_empleo.php">
        <label for="oferta_empleo">Oferta de Empleo:</label>
        <select id="oferta_empleo" name="oferta_empleo" required>
            <?php
            // Genera las opciones del menú desplegable con los IDs de las ofertas de empleo
            foreach ($OfertaEmpleoID as $oferta) {
                echo "<option value='" . $oferta["OfertaEmpleoID"] . "'>" . $oferta["OfertaNombre"] . "</option>";
            }
            ?>
        </select><br><br>

        <label for="perfil_cargo">Perfil de Cargo:</label>
        <input type="text" id="perfil_cargo" name="perfil_cargo" required><br><br>

        <label for="experiencia">Experiencia:</label>
        <textarea id="experiencia" name="experiencia" rows="4" required></textarea><br><br>

        <label for="estudios_requeridos">Estudios Requeridos:</label>
        <input type="text" id="estudios_requeridos" name="estudios_requeridos" required><br><br>

        <label for="otro">Otro:</label>
        <textarea id="otro" name="otro" rows="4"></textarea><br><br>

        <label for="grupos_objetivos">Grupos Objetivos:</label>
        <input type="text" id="grupos_objetivos" name="grupos_objetivos" required><br><br>

        <input type="submit" value="Registrar Perfil de Empleo">
    </form>
    <a href="welcome.php">Volver al Inicio</a>
</body>
</html>
