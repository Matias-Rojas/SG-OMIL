<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Realiza la conexión a la base de datos
$servername = "localhost";
$db_username = "HURDOX";
$db_password = "gokudeus2023";
$database = "sg_omil";

// Conecta a la base de datos
$conn = new mysqli($servername, $db_username, $db_password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verifica si se ha enviado el ID para eliminar
if (isset($_GET['id'])) {
    $deleteId = $_GET['id'];

    // Realiza la eliminación en la base de datos
    $sqlDelete = "DELETE FROM sg_omil_visitas_otecs WHERE VisitaID = $deleteId";

    if ($conn->query($sqlDelete) === TRUE) {
        echo "Visita eliminada correctamente.";
        header("Location: visitaReporteOTEC.php");
        exit();
    } else {
        echo "Error al eliminar Visita: " . $conn->error;
    }
} else {
    echo "ID de Visita no proporcionado.";
}

// Cierra la conexión
$conn->close();
?>
