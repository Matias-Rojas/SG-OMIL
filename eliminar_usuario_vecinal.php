<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.php");
    exit;
}

$rol = $_SESSION["rol"];
$username = $_SESSION["username"];
$correo = $_SESSION["email"];

// Verifica si se ha enviado un ID de usuario para eliminar
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $usuarioID = $_GET['id'];

    // Conexión a la base de datos
    $servername = "localhost";
    $db_username = "HURDOX";
    $db_password = "gokudeus2023";
    $database = "sg_omil";

    $conn = new mysqli($servername, $db_username, $db_password, $database);

    // Verifica la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Sentencia SQL para eliminar el usuario
    $sql = "DELETE FROM sg_omil_usuariosvecinales WHERE UsuarioID = $usuarioID";

    if ($conn->query($sql) === TRUE) {
        // Éxito al eliminar el usuario
        echo "Usuario eliminado exitosamente.";
    } else {
        // Error al eliminar el usuario
        echo "Error al eliminar el usuario: " . $conn->error;
    }

    // Cierra la conexión a la base de datos
    $conn->close();

    // Redirecciona de nuevo a la página de administración de usuarios
    header("Location: adminVecinal.php");
    exit();
} else {
    // Si no se proporcionó un ID válido, redirecciona a la página principal
    echo "ID de usuario no válido.";
    exit();
}
?>
