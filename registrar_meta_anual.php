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
    $nombre_meta = $_POST["nombre_meta"];
    $anio = $_POST["anio"];
    $meta = $_POST["meta"];

    // Insertar la Meta Anual en la base de datos
    $stmt = $conn->prepare("INSERT INTO sg_omil_metas_anuales (NombreMeta, Anio, Meta) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $nombre_meta, $anio, $meta);

    if ($stmt->execute()) {
        echo "Meta Anual registrada con éxito.";
    } else {
        echo "Error al registrar la Meta Anual: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Meta Anual</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Registrar Meta Anual por BNE-SENCE</h2>
    <form method="POST" action="registrar_meta_anual.php">
        <label for="nombre_meta">Nombre de Meta:</label>
        <input type="text" name="nombre_meta" id="nombre_meta" required>

        <label for="anio">Año:</label>
        <input type="number" name="anio" id="anio" required>

        <label for="meta">Meta Anual:</label>
        <input type="text" name="meta" id="meta" required>

        <input type="submit" value="Registrar Meta Anual">
    </form>
    <a href="welcome.php">Volver al Inicio</a>
</body>
</html>
