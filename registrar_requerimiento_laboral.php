<?php
session_start();

// Realiza la conexión a la base de datos
$servername = "localhost";
$db_username = "HURDOX";
$db_password = "gokudeus2023";
$database = "sg_omil";

$conn = new mysqli($servername, $db_username, $db_password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_POST["usuario_id"];
    $descripcion = $_POST["descripcion"];
    $fecha_solicitud = $_POST["fecha_solicitud"];

    // Insertar el Requerimiento de Oportunidad Laboral en la base de datos
    $stmt = $conn->prepare("INSERT INTO sg_omil_requerimientos_laborales (UsuarioID, Descripcion, FechaSolicitud) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $usuario_id, $descripcion, $fecha_solicitud);

    if ($stmt->execute()) {
        echo "Requerimiento de Oportunidad Laboral registrado con éxito.";
    } else {
        echo "Error al registrar el Requerimiento de Oportunidad Laboral: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Requerimiento de Oportunidad Laboral</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Registrar Requerimiento de Oportunidad Laboral</h2>
    <form method="POST" action="registrar_requerimiento_laboral.php">
        <label for="usuario_id">Usuario Vecinal:</label>
        <select id="usuario_id" name="usuario_id" required>
            <?php
            // Genera las opciones del menú desplegable con los IDs de las ofertas de empleo
            foreach ($UsuarioID as $usuarioV) {
                echo "<option value='" . $usuarioV["UsuarioID"] . "'>" . $usuarioV["OfertaVecino"] . "</option>";
            }
            ?>
        </select><br><br>

        <label for="descripcion">Descripción del Requerimiento:</label>
        <textarea name="descripcion" id="descripcion" required></textarea>

        <label for="fecha_solicitud">Fecha de Solicitud:</label>
        <input type="date" name="fecha_solicitud" id="fecha_solicitud" required>

        <input type="submit" value="Registrar Requerimiento de Oportunidad Laboral">
    </form>
    <a href="welcome.php">Volver al Inicio</a>
</body>
</html>
