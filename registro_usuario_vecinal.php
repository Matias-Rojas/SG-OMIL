<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Realiza la conexión a la base de datos
    $servername = "localhost";
    $db_username = "HURDOX";
    $db_password = "gokudeus2023";
    $database = "sg_omil";

    $conn = new mysqli($servername, $db_username, $db_password, $database);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $nombres = $_POST["nombres"];
    $apellidos = $_POST["apellidos"];
    $rut = $_POST["rut"];
    $genero = $_POST["genero"];
    $fecha_nacimiento = $_POST["fecha_nacimiento"];
    $estado_civil = $_POST["estado_civil"];
    $direccion = $_POST["direccion"];
    $region = $_POST["region"];
    $comuna = $_POST["comuna"];
    $sector = $_POST["sector"];
    $nacionalidad = $_POST["nacionalidad"];
    $correo_electronico = $_POST["correo_electronico"];
    $telefono = $_POST["telefono"];
    $telefono_alternativo = $_POST["telefono_alternativo"];
    $nivel_educacional = $_POST["nivel_educacional"];
    $area = $_POST["area"];
    $titulo = $_POST["titulo"];
    $nombre_curso = $_POST["nombre_curso"];
    $institucion = $_POST["institucion"];
    $fecha = $_POST["fecha"];
    $motivo_consulta = $_POST["motivo_consulta"];
    $estado_motivo_consulta = $_POST["estado_motivo_consulta"];

    // Verificar si el RUT ya existe en la base de datos
    $sql_verificar_rut = "SELECT Rut FROM sg_omil_usuariosvecinales WHERE Rut = ?";
    $stmt_verificar_rut = $conn->prepare($sql_verificar_rut);
    $stmt_verificar_rut->bind_param("s", $rut);
    $stmt_verificar_rut->execute();
    $result_verificar_rut = $stmt_verificar_rut->get_result();

    if ($result_verificar_rut->num_rows > 0) {
        // El RUT ya está registrado, muestra un mensaje de error o toma alguna acción adecuada.
        echo "El RUT ya está registrado en la base de datos.";
    } else {
        // El RUT no está registrado, procede a insertar el nuevo usuario vecinal.
        $sql_insert = "INSERT INTO sg_omil_usuariosvecinales (Nombres, Apellidos, Rut, Genero, FechaNacimiento, EstadoCivil, Direccion, Region, Comuna, Sector, Nacionalidad, CorreoElectronico, Telefono, TelefonoAlternativo, NivelEducacional, Area, Titulo, NombreCurso, Institucion, Fecha, MotivoConsulta, EstadoMotivoConsulta) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssssssssssssssssssssss", $nombres, $apellidos, $rut, $genero, $fecha_nacimiento, $estado_civil, $direccion, $region, $comuna, $sector, $nacionalidad, $correo_electronico, $telefono, $telefono_alternativo, $nivel_educacional, $area, $titulo, $nombre_curso, $institucion, $fecha, $motivo_consulta, $estado_motivo_consulta);

        if ($stmt_insert->execute()) {
            // Éxito al registrar el usuario vecinal
            echo "Usuario vecinal registrado con éxito.";
        } else {
            // Error al registrar el usuario vecinal
            echo "Error al registrar el usuario vecinal.";
        }

        $stmt_insert->close();
    }

    $stmt_verificar_rut->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario Vecinal</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Registro de Usuario Vecinal</h2>
    <form method="POST">
    <label for="nombres">Nombres:</label>
        <input type="text" id="nombres" name="nombres" required>
        <br><br>

        <label for="apellidos">Apellidos:</label>
        <input type="text" id="apellidos" name="apellidos" required>
        <br><br>

        <label for="rut">RUT:</label>
        <input type="text" id="rut" name="rut" required>
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

        <label for="estado_civil">Estado Civil:</label>
        <input type="text" id="estado_civil" name="estado_civil" required>
        <br><br>

        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion">
        <br><br>

        <label for="region">Región:</label>
        <input type="text" id="region" name="region" required>
        <br><br>

        <label for="comuna">Comuna:</label>
        <input type="text" id="comuna" name="comuna" required>
        <br><br>

        <label for="sector">Sector:</label>
        <input type="text" id="sector" name="sector">
        <br><br>

        <label for="nacionalidad">Nacionalidad:</label>
        <input type="text" id="nacionalidad" name="nacionalidad">
        <br><br>

        <label for="correo_electronico">Correo Electrónico:</label>
        <input type="email" id="correo_electronico" name="correo_electronico">
        <br><br>

        <label for="telefono">Teléfono:</label>
        <input type="tel" id="telefono" name="telefono">
        <br><br>

        <label for="telefono_alternativo">Teléfono Alternativo:</label>
        <input type="tel" id="telefono_alternativo" name="telefono_alternativo">
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

        <label for="motivo_consulta">Motivo de Consulta:</label>
        <input type="text" id="motivo_consulta" name="motivo_consulta" required>
        <br><br>

        <label for="estado_motivo_consulta">Estado del Motivo de Consulta:</label>
        <input type="text" id="estado_motivo_consulta" name="estado_motivo_consulta" required>
        <br><br>

        <input type="submit" value="Registrar Usuario">
    </form>
    <a href="welcome.php">Volver al Inicio</a>
</body>
</html>

