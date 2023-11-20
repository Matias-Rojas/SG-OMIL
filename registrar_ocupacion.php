<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.html");
    exit;
}

// Mensaje de éxito o error
$successMessage = '';

// Realiza la conexión a la base de datos
$servername = "localhost";
$db_username = "HURDOX";
$db_password = "gokudeus2023";
$database = "sg_omil";

$conn = new mysqli($servername, $db_username, $db_password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verifica si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera los datos del formulario
    $rutUsuarioVecinal = $_POST["rut"];
    $estadoUsuario = $_POST["estado"];

    // Manejo del archivo adjunto (puedes ajustar según tus necesidades)
    $adjunto = null;
    if (isset($_FILES["adjunto"]) && $_FILES["adjunto"]["error"] == UPLOAD_ERR_OK) {
        $adjunto = $_FILES["adjunto"]["name"];
        $rutaGuardado = "Archivos/" . $adjunto;
        move_uploaded_file($_FILES["adjunto"]["tmp_name"], $rutaGuardado);
    }

    // Inserta los datos en la tabla sg_omil_ocupacion_usuario_vecinal
    $sql = "INSERT INTO sg_omil_ocupacion_usuario_vecinal (RutUsuarioVecinal, AdjuntoDocumento, EstadoUsuario) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $rutUsuarioVecinal, $adjunto, $estadoUsuario);

    if ($stmt->execute()) {
        $successMessage = "Ocupación registrada con éxito.";
    } else {
        // Muestra mensajes de error de MySQL
        $successMessage = "Error al registrar la ocupación. " . $conn->error;
    }

    // Cierra la conexión y el statement
    $stmt->close();
}

// Consulta para obtener los RUTs de los usuarios vecinales desde la base de datos
$sql = "SELECT Rut FROM sg_omil_usuariosvecinales";
$result = $conn->query($sql);

$ruts_usvecinal = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ruts_usvecinal[] = $row["Rut"];
    }
}

// Consulta para obtener los datos registrados en la tabla sg_omil_ocupacion_usuario_vecinal
$sql = "SELECT RutUsuarioVecinal, AdjuntoDocumento, EstadoUsuario FROM sg_omil_ocupacion_usuario_vecinal";
$result = $conn->query($sql);

$registros = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $registros[] = $row;
    }
}

// Cierra la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Ocupación Usuario Vecinal</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Registrar Ocupación Usuario Vecinal</h2>
    <!-- Muestra el mensaje de éxito o error -->
    <?php if (!empty($successMessage)): ?>
        <p style="color: <?php echo strpos($successMessage, 'Error') !== false ? 'red' : 'green'; ?>"><?php echo $successMessage; ?></p>
    <?php endif; ?>
    <form action="registrar_ocupacion.php" method="post" enctype="multipart/form-data">
        <label for="rut">Rut:</label>
        <!-- Utiliza un elemento select para mostrar los RUTs -->
        <select name="rut" required>
            <?php foreach ($ruts_usvecinal as $rut): ?>
                <option value="<?php echo $rut; ?>"><?php echo $rut; ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="adjunto">Adjuntar Documento de Respaldo:</label>
        <input type="file" name="adjunto" accept=".pdf, .doc, .docx"><br>
        
        <label for="estado">Estado del Usuario:</label>
        <select name="estado" id="estado">
            <option value="Colocado">Colocado / Trabajando</option>
            <option value="Sin Trabajo">Sin Trabajo</option>
        </select>

        <input type="submit" value="Registrar">
    </form>

    <h2>Registros</h2>
    <table border="1">
        <tr>
            <th>Rut Usuario Vecinal</th>
            <th>Adjunto Documento</th>
            <th>Estado Usuario</th>
            <th>Descargar Adjunto</th>
        </tr>
        <?php foreach ($registros as $registro): ?>
            <tr>
                <td><?php echo $registro["RutUsuarioVecinal"]; ?></td>
                <td><?php echo $registro["AdjuntoDocumento"]; ?></td>
                <td><?php echo $registro["EstadoUsuario"]; ?></td>
                <td><a href="descargar_adjunto.php?filename=<?php echo urlencode($registro["AdjuntoDocumento"]); ?>">Descargar</a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <a href="welcome.php">Volver al Inicio</a>
</body>
</html>
