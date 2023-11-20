<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.html");
    exit;
}

// Verificar si el formulario se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar los datos del formulario
    $razon_social = $_POST["razon_social"];
    $giro = $_POST["giro"];
    $rut = $_POST["rut"];
    $direccion = $_POST["direccion"];
    $sector = $_POST["sector"];
    $tamanio_empresa = $_POST["tamanio_empresa"];
    $accesibilidad_sucursal = $_POST["accesibilidad_sucursal"];
    $contacto_casa_matriz = $_POST["contacto_casa_matriz"];
    $telefono_casa_matriz = $_POST["telefono_casa_matriz"];
    $correo_casa_matriz = $_POST["correo_casa_matriz"];
    $nombre_contacto = $_POST["nombre_contacto"];
    $cargo_contacto = $_POST["cargo_contacto"];
    $telefono_contacto = $_POST["telefono_contacto"];
    $correo_contacto = $_POST["correo_contacto"];

    // Conectar a la base de datos
    $servername = "localhost";
    $username = "HURDOX";
    $password = "gokudeus2023";
    $database = "sg_omil";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Consulta SQL para insertar una nueva empresa
    $sql = "INSERT INTO sg_omil_nuevasempresas (RazonSocial, Giro, Rut, Direccion, Sector, TamañoEmpresa, AccesibilidadSucursal, ContactoCasaMatriz, TelefonoCasaMatriz, CorreoCasaMatriz, NombreContacto, CargoContacto, TelefonoContacto, CorreoContacto)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Vincular los parámetros
    $stmt->bind_param("ssssssssssssss", $razon_social, $giro, $rut, $direccion, $sector, $tamanio_empresa, $accesibilidad_sucursal, $contacto_casa_matriz, $telefono_casa_matriz, $correo_casa_matriz, $nombre_contacto, $cargo_contacto, $telefono_contacto, $correo_contacto);

    if ($stmt->execute()) {
        // Inserción exitosa
        echo "Empresa registrada con éxito.";
    } else {
        // Error en la inserción
        echo "Error al registrar la empresa: " . $stmt->error;
    }

    // Cerrar la conexión y liberar recursos
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Registrar Nueva Empresa</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Registrar Nueva Empresa</h2>
    <form method="POST" action="registro_empresa.php">
        <label for="razon_social">Razón Social:</label>
        <input type="text" id="razon_social" name="razon_social" required><br><br>

        <label for="giro">Giro:</label>
        <input type="text" id="giro" name="giro"><br><br>

        <label for="rut">RUT:</label>
        <input type="text" id="rut" name="rut" required><br><br>

        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion"><br><br>

        <label for="sector">Sector:</label>
        <input type="text" id="sector" name="sector"><br><br>

        <label for="tamanio_empresa">Tamaño Empresa:</label>
        <input type="text" id="tamanio_empresa" name="tamanio_empresa"><br><br>

        <label for="accesibilidad_sucursal">Accesibilidad Sucursal:</label>
        <input type="text" id="accesibilidad_sucursal" name="accesibilidad_sucursal"><br><br>

        <label for="contacto_casa_matriz">Contacto Casa Matriz:</label>
        <input type="text" id="contacto_casa_matriz" name="contacto_casa_matriz"><br><br>

        <label for="telefono_casa_matriz">Teléfono Casa Matriz:</label>
        <input type="text" id="telefono_casa_matriz" name="telefono_casa_matriz"><br><br>

        <label for="correo_casa_matriz">Correo Casa Matriz:</label>
        <input type="text" id="correo_casa_matriz" name="correo_casa_matriz"><br><br>

        <label for="nombre_contacto">Nombre de Contacto:</label>
        <input type="text" id="nombre_contacto" name="nombre_contacto"><br><br>

        <label for="cargo_contacto">Cargo de Contacto:</label>
        <input type="text" id="cargo_contacto" name="cargo_contacto"><br><br>

        <label for="telefono_contacto">Teléfono de Contacto:</label>
        <input type="text" id="telefono_contacto" name="telefono_contacto"><br><br>

        <label for="correo_contacto">Correo de Contacto:</label>
        <input type="text" id="correo_contacto" name="correo_contacto"><br><br>

        <input type="submit" value="Registrar">
    </form>
    <a href="welcome.php">Volver a la página de administración</a>
</body>
</html>
