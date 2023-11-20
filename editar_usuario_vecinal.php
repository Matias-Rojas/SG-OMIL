<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Procesar la edición del usuario y actualizar la base de datos
    $servername = "localhost";
    $db_username = "HURDOX";
    $db_password = "gokudeus2023";
    $database = "sg_omil";

    $conn = new mysqli($servername, $db_username, $db_password, $database);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $user_id = $_POST["user_id"];
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

    // Actualizar la información del usuario
    $sql = "UPDATE sg_omil_usuariosvecinales
            SET Nombres = ?, Apellidos = ?, Rut = ?, Genero = ?, FechaNacimiento = ?, EstadoCivil = ?, Direccion = ?, Region = ?, Comuna = ?, Sector = ?, Nacionalidad = ?, CorreoElectronico = ?, Telefono = ?, TelefonoAlternativo = ?, NivelEducacional = ?, Area = ?, Titulo = ?, NombreCurso = ?, Institucion = ?, Fecha = ?, MotivoConsulta = ?, EstadoMotivoConsulta = ?
            WHERE UsuarioID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssssssssssss", $nombres, $apellidos, $rut, $genero, $fecha_nacimiento, $estado_civil, $direccion, $region, $comuna, $sector, $nacionalidad, $correo_electronico, $telefono, $telefono_alternativo, $nivel_educacional, $area, $titulo, $nombre_curso, $institucion, $fecha, $motivo_consulta, $estado_motivo_consulta, $user_id);


    if ($stmt->execute()) {
        // Éxito al actualizar el usuario
        header("location: adminVecinal.php");
        exit;
    } else {
        // Error al actualizar el usuario
        echo "Error al actualizar el usuario: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
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

// Obtener el ID del usuario a editar desde la URL
if (isset($_GET["id"])) {
    $user_id = $_GET["id"];

    // Consulta para obtener los detalles del usuario
    $sql = "SELECT * FROM sg_omil_usuariosvecinales WHERE UsuarioID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
} else {
    // Redireccionar si no se proporciona un ID de usuario válido
    header("location: adminVecinal.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Editar Usuario</h2>
    <a href="adminVecinal.php">Volver a la lista de usuarios</a>

    <form method="POST">
        <input type="hidden" name="user_id" value="<?php echo $user["UsuarioID"]; ?>">
        <label for="nombres">Nombres:</label>
        <input type="text" id="nombres" name="nombres" value="<?php echo $user["Nombres"]; ?>" required>
        <br><br>

        <label for="apellidos">Apellidos:</label>
        <input type="text" id="apellidos" name="apellidos" value="<?php echo $user["Apellidos"]; ?>" required>
        <br><br>

        <label for="rut">RUT:</label>
        <input type="text" id="rut" name="rut" value="<?php echo $user["Rut"]; ?>" required>
        <br><br>

        <label for="genero">Género:</label>
        <select name="genero" id="genero">
            <option value="Masculino">Masculino</option>
            <option value="Femenino">Femenino</option>
            <option value="Otro">Otro</option>
        </select>
        <br><br>

        <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo $user["FechaNacimiento"]; ?>">
        <br><br>

        <label for="estado_civil">Estado Civil:</label>
        <input type="text" id="estado_civil" name="estado_civil" value="<?php echo $user["EstadoCivil"]; ?>">
        <br><br>

        <label for="direccion">Domicilio:</label>
        <input type="text" id="direccion" name="direccion" value="<?php echo $user["Direccion"]; ?>">
        <br><br>

        <label for="region">Región:</label>
        <input type="text" id="region" name="region" value="<?php echo $user["Region"]; ?>">
        <br><br>

        <label for="comuna">Comuna:</label>
        <input type="text" id="comuna" name="comuna" value="<?php echo $user["Comuna"]; ?>">
        <br><br>

        <label for="sector">Sector:</label>
        <input type="text" id="sector" name="sector" value="<?php echo $user["Sector"]; ?>">
        <br><br>

        <label for="nacionalidad">Nacionalidad:</label>
        <input type="text" id="nacionalidad" name="nacionalidad" value="<?php echo $user["Nacionalidad"]; ?>">
        <br><br>

        <label for="correo_electronico">Correo Electrónico:</label>
        <input type="email" id="correo_electronico" name="correo_electronico" value="<?php echo $user["CorreoElectronico"]; ?>">
        <br><br>

        <label for="telefono">Teléfono:</label>
        <input type="tel" id="telefono" name="telefono" value="<?php echo $user["Telefono"]; ?>">
        <br><br>

        <label for="telefono_alternativo">Teléfono Alternativo:</label>
        <input type="text" id="telefono_alternativo" name="telefono_alternativo" value="<?php echo $user["TelefonoAlternativo"]; ?>">
        <br><br>

        <label for="nivel_educacional">Nivel Educacional:</label>
        <input type="text" id="nivel_educacional" name="nivel_educacional" value="<?php echo $user["NivelEducacional"]; ?>">
        <br><br>

        <label for="area">Área:</label>
        <input type="text" id="area" name="area" value="<?php echo $user["Area"]; ?>">
        <br><br>

        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" value="<?php echo $user["Titulo"]; ?>">
        <br><br>

        <label for="nombre_curso">Nombre del Curso:</label>
        <input type="text" id="nombre_curso" name="nombre_curso" value="<?php echo $user["NombreCurso"]; ?>">
        <br><br>

        <label for="institucion">Institución:</label>
        <input type="text" id="institucion" name="institucion" value="<?php echo $user["Institucion"]; ?>">
        <br><br>

        <label for="fecha">Fecha:</label>
        <input type="date" id="fecha" name="fecha" value="<?php echo $user["Fecha"]; ?>">
        <br><br>

        <label for="motivo_consulta">Motivo de Consulta:</label>
        <input type="text" id="motivo_consulta" name="motivo_consulta" value="<?php echo $user["MotivoConsulta"]; ?>">
        <br><br>

        <label for="estado_motivo_consulta">Estado del Motivo de Consulta:</label>
        <input type="text" id="estado_motivo_consulta" name="estado_motivo_consulta" value="<?php echo $user["EstadoMotivoConsulta"]; ?>">
        <br><br>

        <input type="submit" value="Guardar Cambios">
    </form>
</body>
</html>
