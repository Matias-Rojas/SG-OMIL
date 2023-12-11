<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.php");
    exit;
}

// Verifica si se ha enviado el ID para eliminar
if (isset($_GET['id'])) {
    $eliminarId = $_GET['id'];

    // Realiza la conexión a la base de datos (puedes reutilizar el código de conexión existente)
    $servername = "localhost";
    $db_username = "HURDOX";
    $db_password = "gokudeus2023";
    $database = "sg_omil";
    $conn = new mysqli($servername, $db_username, $db_password, $database);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Verifica si el ID es numérico
    if (is_numeric($eliminarId)) {
        // Ejecuta la consulta para eliminar la oferta de empleo
        $sqlEliminar = "DELETE FROM sg_omil_ofertasempleo WHERE OfertaEmpleoID = $eliminarId";

        if ($conn->query($sqlEliminar) === TRUE) {
            echo "Oferta de empleo eliminada correctamente.";
            // Puedes redirigir a la página principal o realizar otras acciones después de la eliminación
            header("Location: adminEmpleo.php");
            exit();
        } else {
            echo "Error al eliminar la oferta de empleo: " . $conn->error;
        }
    } else {
        echo "ID de oferta de empleo no válido.";
    }

    // Cierra la conexión
    $conn->close();
} else {
    echo "ID de oferta de empleo no proporcionado.";
}
?>
