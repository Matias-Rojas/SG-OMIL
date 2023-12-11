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
    $sqlDelete = "DELETE FROM sg_omil_avance_mensual WHERE AvanceID = $deleteId";

    if ($conn->query($sqlDelete) === TRUE) {
        echo "Avance eliminado correctamente.";
        header("Location: metaAvanceMensual.php");
        exit();
    } else {
        echo "Error al eliminar Avance: " . $conn->error;
    }
} else {
    echo "ID de Avance no proporcionado.";
}

// Cierra la conexión
$conn->close();
?>
