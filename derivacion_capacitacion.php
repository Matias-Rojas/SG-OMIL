<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Realizar la conexión a la base de datos
    $servername = "localhost";
    $db_username = "HURDOX";
    $db_password = "gokudeus2023";
    $database = "sg_omil";

    $conn = new mysqli($servername, $db_username, $db_password, $database);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $fecha_derivacion = date("Y-m-d H:i:s"); // Fecha y hora actual
    $rut = $_POST["rut"];
    $nombres = $_POST["nombres"];
    $apellidos = $_POST["apellidos"];
    $genero = $_POST["genero"];
    $fecha_nacimiento = $_POST["fecha_nacimiento"];
    $nacionalidad = $_POST["nacionalidad"];
    $telefono = $_POST["telefono"];
    $celular_alternativo = isset($_POST["celular_alternativo"]) ? $_POST["celular_alternativo"] : null;
    $correo_electronico = $_POST["correo_electronico"];
    $region = $_POST["region"];
    $comuna = $_POST["comuna"];
    $sector = $_POST["sector"];
    $direccion = $_POST["direccion"];
    $nivel_educacional = $_POST["nivel_educacional"];
    $area = $_POST["area"];
    $titulo = $_POST["titulo"];
    $nombre_curso = $_POST["nombre_curso"];
    $institucion = $_POST["institucion"];
    $fecha = $_POST["fecha"];
    $motivo_derivacion = $_POST["motivo_derivacion"];

    $sql_insert = "INSERT INTO sg_omil_derivacion_capacitacion 
        (fecha_derivacion, rut, nombres, apellidos, genero, fecha_nacimiento, nacionalidad, telefono, celular_alternativo, 
        correo_electronico, region, comuna, sector, direccion, nivel_educacional, area, titulo, nombre_curso, institucion, 
        fecha, motivo_derivacion) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("sssssssssssssssssssss", 
        $fecha_derivacion, $rut, $nombres, $apellidos, $genero, $fecha_nacimiento, $nacionalidad, $telefono, 
        $celular_alternativo, $correo_electronico, $region, $comuna, $sector, $direccion, $nivel_educacional, 
        $area, $titulo, $nombre_curso, $institucion, $fecha, $motivo_derivacion);

    if ($stmt_insert->execute()) {
        // Éxito al registrar la derivación para capacitación
        echo "Derivación para capacitación registrada con éxito.";
    } else {
        // Error al registrar la derivación para capacitación
        echo "Error al registrar la derivación para capacitación.";
    }

    $stmt_insert->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Derivación para Capacitación</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Derivación para Capacitación</h2>
    <form method="POST">
        <label for="rut">RUT:</label>
        <input type="text" id="rut" name="rut" required>
        <br><br>

        <label for="nombres">Nombres:</label>
        <input type="text" id="nombres" name="nombres" required>
        <br><br>

        <label for="apellidos">Apellidos:</label>
        <input type="text" id="apellidos" name="apellidos" required>
        <br><br>

        <label for="genero">Género:</label>
        <select name="genero" id="genero">
            <option value="Masculino">Masculino</option>
            <option value="Femenino">Femenino</option>
            <option value="Otro">Otro</option>
        </select>
        <br><br>

        <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento">
        <br><br>

        <label for="nacionalidad">Nacionalidad:</label>
        <input type="text" id="nacionalidad" name="nacionalidad">
        <br><br>

        <label for="telefono">Teléfono:</label>
        <input type="tel" id="telefono" name="telefono">
        <br><br>

        <label for="celular_alternativo">Teléfono Alternativo:</label>
        <input type="tel" id="celular_alternativo" name="celular_alternativo">
        <br><br>

        <label for="correo_electronico">Correo Electrónico:</label>
        <input type="email" id="correo_electronico" name="correo_electronico">
        <br><br>

        <label for="region">Región:</label>
        <input type="text" id="region" name="region">
        <br><br>

        <label for="comuna">Comuna:</label>
        <input type="text" id="comuna" name="comuna">
        <br><br>

        <label for="sector">Sector:</label>
        <input type="text" id="sector" name="sector">
        <br><br>

        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion">
        <br><br>

        <label for="nivel_educacional">Nivel Educacional:</label>
        <input type="text" id="nivel_educacional" name="nivel_educacional">
        <br><br>

        <label for="area">Área:</label>
        <input type="text" id="area" name="area">
        <br><br>

        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo">
        <br><br>

        <label for="nombre_curso">Nombre del Curso:</label>
        <input type="text" id="nombre_curso" name="nombre_curso">
        <br><br>

        <label for="institucion">Institución:</label>
        <input type="text" id="institucion" name="institucion">
        <br><br>

        <label for="fecha">Fecha:</label>
        <input type="date" id="fecha" name="fecha" required>
        <br><br>

        <label for="motivo_derivacion">Motivo de Derivación:</label>
        <textarea id="motivo_derivacion" name="motivo_derivacion" required></textarea>
        <br><br>

        <input type="submit" value="Registrar Derivación">
    </form>
    <a href="welcome.php">Volver al Inicio</a>
</body>
</html>
