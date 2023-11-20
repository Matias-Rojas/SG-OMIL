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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rutEmpresaOTEC = $_POST["RutEmpresaOTEC"];
    $nombre = $_POST["nombre"];
    $sector = $_POST["sector"];
    $nombreContacto = $_POST["nombre_contacto"];
    $correoContacto = $_POST["correo_contacto"];
    $telefonoContacto = $_POST["telefono_contacto"];
    $tematica = $_POST["tematica"];
    $cupos = $_POST["cupos"];
    $horarios = $_POST["horarios"];
    $lugar = $_POST["lugar"];
    $costo = $_POST["costo"];
    $estudiosRequeridos = $_POST["estudios_requeridos"];
    $documentacionRequerida = $_POST["documentacion_requerida"];
    $gruposObjetivos = $_POST["grupos_objetivos"];

    // Verificar si ya existe una oferta de capacitación para esta OTEC con la misma temática
    $sql_verificar = "SELECT * FROM sg_omil_ofertascapacitacion WHERE RutEmpresaOTEC = ? AND NombreCurso = ?";
    $stmt_verificar = $conn->prepare($sql_verificar);
    $stmt_verificar->bind_param("ss", $rutEmpresaOTEC, $tematica);
    $stmt_verificar->execute();
    $result_verificar = $stmt_verificar->get_result();

    if ($result_verificar->num_rows > 0) {
        // Ya existe una oferta con la misma temática para esta OTEC
        echo "Ya existe una oferta de capacitación con la misma temática para esta OTEC.";
    } else {
        // Query para insertar la nueva oferta de capacitación con la fecha de creación
        $sql_insert = "INSERT INTO sg_omil_ofertascapacitacion (FechaCreacion, RutEmpresaOTEC, NombreOTEC, Sector, NombreContacto, CorreoContacto, TelefonoContacto, NombreCurso, Cupos, Horarios, Lugar, Costo, EstudiosRequeridos, DocumentacionRequerida, GruposObjetivos) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("sssssssiisdsss", $rutEmpresaOTEC, $nombre, $sector, $nombreContacto, $correoContacto, $telefonoContacto, $tematica, $cupos, $horarios, $lugar, $costo, $estudiosRequeridos, $documentacionRequerida, $gruposObjetivos);


        if ($stmt_insert->execute()) {
            // Éxito al registrar la oferta de capacitación
            echo "Oferta de capacitación registrada con éxito.";
        } else {
            // Error al registrar la oferta de capacitación
            echo "Error al registrar la oferta de capacitación.";
        }

        $stmt_insert->close();
    }

    $stmt_verificar->close();
}

$conn->close();
?>



<!DOCTYPE html>
<html>
<head>
    <title>Registro de Oferta de Capacitación</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Registro de Oferta de Capacitación</h2>
    <form method="POST" action="registro_oferta.php">
        <label for="RutEmpresaOTEC">RUT de la OTEC:</label>
        <input type="text" id="RutEmpresaOTEC" name="RutEmpresaOTEC" required><br><br>

        <label for="nombre">Nombre de OTEC:</label>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="sector">Sector:</label>
        <input type="text" id="sector" name="sector"><br><br>

        <label for="nombre_contacto">Nombre de Contacto:</label>
        <input type="text" id="nombre_contacto" name="nombre_contacto"><br><br>

        <label for="correo_contacto">Correo de Contacto:</label>
        <input type="email" id="correo_contacto" name="correo_contacto"><br><br>

        <label for="telefono_contacto">Teléfono de Contacto:</label>
        <input type="tel" id="telefono_contacto" name="telefono_contacto"><br><br>

        <label for="tematica">Nombre del Curso:</label>
        <input type="text" id="tematica" name="tematica"><br><br>

        <label for="cupos">Cupos:</label>
        <input type="number" id="cupos" name="cupos"><br><br>

        <label for="horarios">Horarios:</label>
        <input type="text" id="horarios" name="horarios"><br><br>

        <label for="lugar">Lugar:</label>
        <input type="text" id="lugar" name="lugar"><br><br>

        <label for="costo">Costo:</label>
        <input type="number" id="costo" name="costo"><br><br>

        <label for="estudios_requeridos">Estudios Requeridos:</label>
        <input type="text" id="estudios_requeridos" name="estudios_requeridos"><br><br>

        <label for="documentacion_requerida">Documentación Requerida:</label>
        <textarea id="documentacion_requerida" name="documentacion_requerida"></textarea><br><br>

        <label for="grupos_objetivos">Grupos Objetivos:</label>
        <input type="text" id="grupos_objetivos" name="grupos_objetivos"><br><br>

        <input type="submit" value="Registrar Oferta">
    </form>
    <a href="welcome.php">Volver al Inicio</a>
</body>
</html>
