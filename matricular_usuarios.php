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
$sql = "SELECT sc.UsuarioID, CONCAT('Id de Usuario Vecinal: ', sc.UsuarioID, ' | ', 'Nombre Completo de Usuario Vecinal: ', CONCAT(us.Nombres, ' ', us.Apellidos), ' | ', 'Rut de Usuario Vecinal: ', us.Rut, ' | ', 'Oferta de Capacitacion Seleccionado: ', sc.OfertaCapacitacionID) AS OfertaVecino FROM sg_omil_usuariosvecinales AS us JOIN sg_omil_seleccionados_capacitacion AS sc ON us.UsuarioID = sc.UsuarioID";
$result = $conn->query($sql);

$UsuarioID = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $UsuarioID[] = $row;
    }
}

// Consulta para obtener los Ids de ofertas de capacitacion desde la base de datos
$sql = "SELECT sc.OfertaCapacitacionID, CONCAT(' Id de Oferta: ',sc.OfertaCapacitacionID, ' | ',' Nombre de Capacitacion: ',oc.Nombre, ' | ', ' Rut de Empresa Otec: ', oc.RutEmpresaOTEC, ' | ', 'Usuario Seleccionado: ', sc.UsuarioID) AS OfertaNombreCap FROM sg_omil_ofertascapacitacion AS oc JOIN sg_omil_seleccionados_capacitacion AS sc ON oc.OfertaCapacitacionID = sc.OfertaCapacitacionID";
$result = $conn->query($sql);

$OfertaCapacitacionID = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $OfertaCapacitacionID[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_POST["usuario_id"];
    $oferta_id = $_POST["oferta_id"];
    $fecha_matriculacion = $_POST["fecha_matriculacion"];

    // Insertar la matriculación en la base de datos
    $stmt = $conn->prepare("INSERT INTO sg_omil_matriculacion (UsuarioID, OfertaCapacitacionID, FechaMatriculacion) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $usuario_id, $oferta_id, $fecha_matriculacion);

    if ($stmt->execute()) {
        echo "Matriculación registrada con éxito.";
    } else {
        echo "Error al registrar la matriculación: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matricular Usuarios Vecinales</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Matricular Usuarios Vecinales</h2>
    <form method="POST" action="matricular_usuarios.php">
        <label for="usuario_id">Usuario Vecinal:</label>
        <select id="usuario_id" name="usuario_id" required>
            <?php
            // Genera las opciones del menú desplegable con los IDs de las ofertas de empleo
            foreach ($UsuarioID as $usuarioV) {
                echo "<option value='" . $usuarioV["UsuarioID"] . "'>" . $usuarioV["OfertaVecino"] . "</option>";
            }
            ?>
        </select><br><br>

        <label for="oferta_id">Oferta de Capacitacion:</label>
        <select id="oferta_id" name="oferta_id" required>
            <?php
            // Genera las opciones del menú desplegable con los IDs de las ofertas de empleo
            foreach ($OfertaCapacitacionID as $oferta) {
                echo "<option value='" . $oferta["OfertaCapacitacionID"] . "'>" . $oferta["OfertaNombreCap"] . "</option>";
            }
            ?>
        </select><br><br>

        <label for="fecha_matriculacion">Fecha de Matriculación:</label>
        <input type="date" name="fecha_matriculacion" id="fecha_matriculacion" required>

        <input type="submit" value="Matricular Usuario Vecinal">
    </form>
</body>
</html>
