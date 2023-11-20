<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_email = $_POST["username_email"];
    $password = $_POST["password"];

    // Establecer la conexión a la base de datos
    $servername = "localhost";
    $db_username = "HURDOX";
    $db_password = "gokudeus2023";
    $database = "sg_omil_usuarios";

    $conn = new mysqli($servername, $db_username, $db_password, $database);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Consulta preparada para verificar las credenciales con nombre de usuario o correo electrónico
    $stmt = $conn->prepare("SELECT * FROM sg_omil_usuarios WHERE (username = ? OR email = ?) AND password = ?");
    $stmt->bind_param("sss", $username_email, $username_email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Las credenciales son válidas
        $row = $result->fetch_assoc();
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $row["username"];
        $_SESSION["email"] = $row["email"];
        $_SESSION["rol"] = $row["rol"];
        header("location: welcome.php");
    } else {
        // Las credenciales son inválidas
        echo "Credenciales incorrectas.";
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
}
?>

