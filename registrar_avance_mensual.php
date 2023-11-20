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

// Obtener la lista de Metas Anuales para mostrar en el formulario
$sql = "SELECT MetaID, CONCAT('Nombre de Meta: ',NombreMeta, ' | ', 'Año de Meta: ', Anio) AS Meta FROM sg_omil_metas_anuales";
$result = $conn->query($sql);

$metas_anuales = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $metas_anuales[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $meta_id = $_POST["meta_id"];
    $mes = $_POST["mes"];
    $cumplimiento = $_POST["cumplimiento"];

    // Insertar el Avance Mensual en la base de datos
    $stmt = $conn->prepare("INSERT INTO sg_omil_avance_mensual (MetaID, Mes, Cumplimiento) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $meta_id, $mes, $cumplimiento);

    if ($stmt->execute()) {
        echo "Avance mensual de cumplimiento registrado con éxito.";
    } else {
        echo "Error al registrar el Avance Mensual: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Avance Mensual de Cumplimiento</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Registrar Avance Mensual de Cumplimiento por BNE-SENCE</h2>
    <form method="POST" action="registrar_avance_mensual.php">
        <label for="meta_id">Meta Anual:</label>
        <select id="meta_id" name="meta_id" required>
            <?php
            foreach ($metas_anuales as $meta) {
                echo "<option value='" . $meta["MetaID"] . "'>" . $meta["Meta"] . "</option>";
            }
            ?>
        </select>

        <label for="mes">Mes:</label>
        <input type="number" name="mes" id="mes" required>

        <label for="cumplimiento">Cumplimiento:</label>
        <input type="text" name="cumplimiento" id="cumplimiento" required>

        <input type="submit" value="Registrar Avance Mensual">
    </form>
    <a href="welcome.php">Volver al Inicio</a>
</body>
</html>
