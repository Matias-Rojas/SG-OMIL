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

// Consulta para obtener los Ids de ofertas de capacitacion desde la base de datos
$sql = "SELECT OfertaCapacitacionID, CONCAT(' Id de Oferta: ',OfertaCapacitacionID, ' | ',' Nombre de Capacitacion: ',NombreCurso, ' | ', ' Rut de Empresa Otec: ', RutEmpresaOTEC, ' | ', 'Nombre OTEC: ', NombreOTEC) AS OfertaNombreCap FROM sg_omil_ofertascapacitacion";
$result = $conn->query($sql);

$OfertaCapacitacionID = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $OfertaCapacitacionID[] = $row;
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

// Procesar la solicitud de asociación de capacitación
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_POST["usuario_id"];
    $oferta_capacitacion_id = $_POST["oferta_capacitacion_id"];

    // Validar si el usuario ya está asociado a esta oferta de capacitación
    $sql_validar = "SELECT * FROM sg_omil_asociacionusuarioscapacitacion WHERE UsuarioID = ? AND OfertaCapacitacionID = ?";
    $stmt_validar = $conn->prepare($sql_validar);
    $stmt_validar->bind_param("ii", $usuario_id, $oferta_capacitacion_id);
    $stmt_validar->execute();

    if ($stmt_validar->fetch()) {
        echo "El usuario ya está asociado a esta oferta de capacitación.";
    } else {
        // Realizar la asociación de capacitación
        $sql_asociar = "INSERT INTO sg_omil_asociacionusuarioscapacitacion (UsuarioID, OfertaCapacitacionID) VALUES (?, ?)";
        $stmt_asociar = $conn->prepare($sql_asociar);
        $stmt_asociar->bind_param("ii", $usuario_id, $oferta_capacitacion_id);

        if ($stmt_asociar->execute()) {
            echo "Asociación de capacitación exitosa.";
        } else {
            echo "Error al asociar la oferta de capacitación.";
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
    <title>Asociar Oferta de Capacitación</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Asociar Oferta de Capacitación</h2>
    <form method="POST" action="asociar_capacitacion.php">
        <label for="usuario_id">Usuario Vecinal:</label>
        <select id="usuario_id" name="usuario_id" required>
            <?php
            // Genera las opciones del menú desplegable con los IDs de las ofertas de empleo
            foreach ($UsuarioID as $usuarioV) {
                echo "<option value='" . $usuarioV["UsuarioID"] . "'>" . $usuarioV["OfertaVecino"] . "</option>";
            }
            ?>
        </select><br><br>
        
        <label for="oferta_capacitacion_id">Oferta de Capacitacion:</label>
        <select id="oferta_capacitacion_id" name="oferta_capacitacion_id" required>
            <?php
            // Genera las opciones del menú desplegable con los IDs de las ofertas de empleo
            foreach ($OfertaCapacitacionID as $oferta) {
                echo "<option value='" . $oferta["OfertaCapacitacionID"] . "'>" . $oferta["OfertaNombreCap"] . "</option>";
            }
            ?>
        </select><br><br>

        <input type="submit" value="Asociar Oferta de Capacitación">
    </form>
    <a href="welcome.php">Volver al Inicio</a>
</body>
</html>