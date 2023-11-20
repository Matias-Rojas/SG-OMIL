<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.html");
    exit;
}


// Procesar el formulario si se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recopilar datos del formulario
    $rutEmpresaOTEC = $_POST["rutEmpresaOTEC"];
    $razonSocial = $_POST["razonSocial"];
    $direccion = $_POST["direccion"];
    $accesibilidadSucursal = $_POST["accesibilidadSucursal"];
    $telefonoCasaMatriz = $_POST["telefonoCasaMatriz"];
    $correoCasaMatriz = $_POST["correoCasaMatriz"];
    $nombreContacto = $_POST["nombreContacto"];
    $cargoContacto = $_POST["cargoContacto"];
    $telefonoContacto = $_POST["telefonoContacto"];
    $correoContacto = $_POST["correoContacto"];

    // Realizar la conexión a la base de datos
    $servername = "localhost";
    $username = "HURDOX";
    $password = "gokudeus2023";
    $database = "sg_omil";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Consulta preparada para insertar un nuevo registro en la tabla sg_omil_otecs
    $stmt = $conn->prepare("INSERT INTO sg_omil_otecs (RutEmpresaOTEC, RazonSocial, Direccion, AccesibilidadSucursal, TelefonoCasaMatriz, CorreoCasaMatriz, NombreContacto, CargoContacto, TelefonoContacto, CorreoContacto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssssssssss", $rutEmpresaOTEC, $razonSocial, $direccion, $accesibilidadSucursal, $telefonoCasaMatriz, $correoCasaMatriz, $nombreContacto, $cargoContacto, $telefonoContacto, $correoContacto);

    if ($stmt->execute()) {
        echo "Registro exitoso.";
    } else {
        echo "Error al registrar: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registro de OTEC</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Registro de OTEC</h2>
    <form method="POST" action="registro_otecs.php">
        <label for="rutEmpresaOTEC">RUT Empresa OTEC:</label>
        <input type="text" id="rutEmpresaOTEC" name="rutEmpresaOTEC" required><br><br>

        <label for="razonSocial">Razón Social:</label>
        <input type="text" id="razonSocial" name="razonSocial" required><br><br>

        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion"><br><br>

        <label for="accesibilidadSucursal">Accesibilidad de Sucursal:</label>
        <input type="text" id="accesibilidadSucursal" name="accesibilidadSucursal"><br><br>

        <label for="telefonoCasaMatriz">Teléfono Casa Matriz:</label>
        <input type="text" id="telefonoCasaMatriz" name="telefonoCasaMatriz"><br><br>

        <label for="correoCasaMatriz">Correo Casa Matriz:</label>
        <input type="text" id="correoCasaMatriz" name="correoCasaMatriz"><br><br>

        <label for="nombreContacto">Nombre de Contacto:</label>
        <input type="text" id="nombreContacto" name="nombreContacto"><br><br>

        <label for="cargoContacto">Cargo de Contacto:</label>
        <input type="text" id="cargoContacto" name="cargoContacto"><br><br>

        <label for="telefonoContacto">Teléfono de Contacto:</label>
        <input type="text" id="telefonoContacto" name="telefonoContacto"><br><br>

        <label for="correoContacto">Correo de Contacto:</label>
        <input type="text" id="correoContacto" name="correoContacto"><br><br>

        <input type="submit" value="Registrar">
    </form>
    <a href="welcome.php">Volver al Inicio</a>
</body>
</html>
