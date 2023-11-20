<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.html");
    exit;
}

// Conectar a la base de datos
$servername = "localhost";
$db_username = "HURDOX";
$db_password = "gokudeus2023";
$database = "sg_omil";

$conn = new mysqli($servername, $db_username, $db_password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta para obtener los Ids de ofertas de empleo desde la base de datos
$sql = "SELECT OfertaEmpleoID, CONCAT(' Id de Oferta: ',OfertaEmpleoID, ' | ',' Nombre de Oferta: ',Nombre, ' | ', ' Rut de Empresa de Oferta: ', RutEmpresa) AS OfertaNombre FROM sg_omil_ofertasempleo";
$result = $conn->query($sql);

$OfertaEmpleoID = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $OfertaEmpleoID[] = $row;
    }
}

// Consulta para obtener los Ids de los usuarios vecinales desde la base de datos
$sql = "SELECT UsuarioID, CONCAT('Id de Usuario Vecinal: ', UsuarioID, ' | ', 'Nombre Completo de Usuario Vecinal: ', CONCAT(Nombres, ' ', Apellidos), ' | ', 'Rut de Usuario Vecinal: ', Rut) AS OfertaVecino FROM sg_omil_usuariosvecinales";
$result = $conn->query($sql);

$UsuarioID = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $UsuarioID[] = $row;
    }
}

// Procesar la solicitud de asociación de empleo
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_POST["usuario_id"];
    $oferta_empleo_id = $_POST["oferta_empleo_id"];

    // Validar si el usuario ya está asociado a esta oferta de empleo
    $sql_validar = "SELECT * FROM sg_omil_asociacionusuariosempleo WHERE UsuarioID = ? AND OfertaEmpleoID = ?";
    $stmt_validar = $conn->prepare($sql_validar);
    $stmt_validar->bind_param("ii", $usuario_id, $oferta_empleo_id);
    $stmt_validar->execute();

    if ($stmt_validar->fetch()) {
        echo "El usuario ya está asociado a esta oferta de empleo.";
    } else {
        // Realizar la asociación de empleo
        $sql_asociar = "INSERT INTO sg_omil_asociacionusuariosempleo (UsuarioID, OfertaEmpleoID) VALUES (?, ?)";
        $stmt_asociar = $conn->prepare($sql_asociar);
        $stmt_asociar->bind_param("ii", $usuario_id, $oferta_empleo_id);

        if ($stmt_asociar->execute()) {
            echo "Asociación de empleo exitosa.";
        } else {
            echo "Error al asociar la oferta de empleo.";
        }
    }

    $stmt_validar->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asociar Oferta de Empleo</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Asociar Oferta de Empleo</h2>
    <form method="POST" action="asociar_empleo.php">
        <label for="usuario_id">Usuario Vecinal:</label>
        <select id="usuario_id" name="usuario_id" required>
            <?php
            // Genera las opciones del menú desplegable con los IDs de las ofertas de empleo
            foreach ($UsuarioID as $usuarioV) {
                echo "<option value='" . $usuarioV["UsuarioID"] . "'>" . $usuarioV["OfertaVecino"] . "</option>";
            }
            ?>
        </select><br><br>
        
        <label for="oferta_empleo_id">Oferta de Empleo:</label>
        <select id="oferta_empleo_id" name="oferta_empleo_id" required>
            <?php
            // Genera las opciones del menú desplegable con los IDs de las ofertas de empleo
            foreach ($OfertaEmpleoID as $oferta) {
                echo "<option value='" . $oferta["OfertaEmpleoID"] . "'>" . $oferta["OfertaNombre"] . "</option>";
            }
            ?>
        </select><br><br>

        <input type="submit" value="Asociar Oferta de Empleo">
    </form>
    <a href="welcome.php">Volver al Inicio</a>
</body>
</html>
