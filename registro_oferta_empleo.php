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
    $rut_empresa = $_POST["RutEmpresa"];
    $nombre = $_POST["nombre"];
    $sector = $_POST["sector"];
    $nombre_contacto = $_POST["nombre_contacto"];
    $correo_contacto = $_POST["correo_contacto"];
    $telefono_contacto = $_POST["telefono_contacto"];
    $rubro_oferta = $_POST["rubro_oferta"];
    $cupos = $_POST["cupos"];
    $estudios_requeridos = $_POST["estudios_requeridos"];
    $documentacion_requerida = $_POST["documentacion_requerida"];
    $lugar_trabajo = $_POST["lugar_trabajo"];
    $renta_liquida = $_POST["renta_liquida"];
    $grupos_objetivos = $_POST["grupos_objetivos"];
    $horarios = $_POST["horarios"];
    $tipo_contrato = $_POST["tipo_contrato"];
    $nombre_vacante = $_POST["nombre_vacante"];

    // Query para insertar la nueva oferta de empleo con la fecha de creación
    $sql_insert = "INSERT INTO sg_omil_ofertasempleo (FechaCreacion, RutEmpresa, NombreEmpresa, Sector, NombreContacto, CorreoContacto, TelefonoContacto, NombreVacante, RubroOferta, Cupos, LugarTrabajo, RentaLiquida, EstudiosRequeridos, Horarios, DocumentacionRequerida, GruposObjetivos, TipoContrato) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ssssssssssssssss", $rut_empresa, $nombre, $sector, $nombre_contacto, $correo_contacto, $telefono_contacto, $nombre_vacante, $rubro_oferta, $cupos, $lugar_trabajo, $renta_liquida, $estudios_requeridos, $horarios, $documentacion_requerida, $grupos_objetivos, $tipo_contrato);

    if ($stmt_insert->execute()) {
        // Éxito al registrar la oferta de empleo
        echo "Oferta de empleo registrada con éxito.";
    } else {
        // Error al registrar la oferta de empleo
        echo "Error al registrar la oferta de empleo: " . $stmt_insert->error;
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
    <title>Registro de Oferta de Empleo</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Registro de Oferta de Empleo</h2>
    <form method="POST" action="registro_oferta_empleo.php">
        <label for="RutEmpresa">RUT de la Empresa:</label>
        <input type="text" id="RutEmpresa" name="RutEmpresa" required><br><br>

        <label for="nombre">Nombre de la Empresa:</label>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="sector">Sector:</label>
        <input type="text" id="sector" name="sector" required><br><br>

        <label for="nombre_contacto">Nombre de Contacto:</label>
        <input type="text" id="nombre_contacto" name="nombre_contacto" required><br><br>

        <label for="correo_contacto">Correo de Contacto:</label>
        <input type="email" id="correo_contacto" name="correo_contacto" required><br><br>

        <label for="telefono_contacto">Teléfono de Contacto:</label>
        <input type="text" id="telefono_contacto" name="telefono_contacto" required><br><br>

        <label for="nombre_vacante">Nombre de la Vacante:</label>
        <input type="text" id="nombre_vacante" name="nombre_vacante" required><br><br>

        <label for="rubro_oferta">Rubro de Oferta:</label>
        <input type="text" id="rubro_oferta" name="rubro_oferta" required></input><br><br>

        <label for="cupos">Cupos:</label>
        <input type="number" id="cupos" name="cupos" required><br><br>

        <label for="estudios_requeridos">Estudios Requeridos:</label>
        <input type="text" id="estudios_requeridos" name="estudios_requeridos" required><br><br>

        <label for="documentacion_requerida">Documentacion Requerida:</label>
        <input type="text" id="documentacion_requerida" name="documentacion_requerida" required></input><br><br>

        <label for="lugar_trabajo">Lugar de Trabajo:</label>
        <input type="text" id="lugar_trabajo" name="lugar_trabajo" required><br><br>

        <label for="renta_liquida">Renta Líquida:</label>
        <input type="text" id="renta_liquida" name="renta_liquida" required><br><br>

        <label for="grupos_objetivos">Grupos Objetivos:</label>
        <input type="text" id="grupos_objetivos" name="grupos_objetivos" required></input><br><br>

        <label for="tipo_contrato">Tipo de Contrato:</label>
        <input type="text" id="tipo_contrato" name="tipo_contrato" required><br><br>

        <label for="horarios">Horarios:</label>
        <input type="text" id="horarios" name="horarios" required><br><br>

        <input type="submit" value="Registrar Oferta de Empleo">
    </form>
    <a href="welcome.php">Volver al Inicio</a>
</body>
</html>