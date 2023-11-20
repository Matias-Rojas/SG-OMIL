<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $newUsername = $_POST["username"];
    $newCorreo = $_POST["email"];
    $newPassword = $_POST["password"];
    $newRol = $_POST["rol"];

    // Establecer la conexión a la base de datos
    $servername = "localhost";
    $db_username = "HURDOX";
    $db_password = "gokudeus2023";
    $database = "sg_omil";

    $conn = new mysqli($servername, $db_username, $db_password, $database);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Consulta preparada para verificar si el nombre de usuario o correo ya existen
    $stmt_check = $conn->prepare("SELECT * FROM sg_omil_usuarios WHERE username = ? OR email = ?");
    $stmt_check->bind_param("ss", $newUsername, $newCorreo);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) {
        // No existe un usuario con el mismo nombre de usuario o correo

        // Consulta preparada para insertar un nuevo usuario con correo electrónico
        $stmt = $conn->prepare("INSERT INTO sg_omil_usuarios (username, password, email, rol) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $newUsername, $newPassword, $newCorreo, $newRol);

        if ($stmt->execute()) {
            // Inserción exitosa
            echo "Usuario creado con éxito.";
        } else {
            // Error en la inserción
            echo "Error al crear el usuario.";
        }

        // Cerrar la conexión
        $stmt->close();
    } else {
        // Ya existe un usuario con el mismo nombre de usuario o correo
        echo "Ya existe un usuario con el mismo nombre de usuario o correo electrónico.";
    }

    // Cerrar la conexión
    $stmt_check->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Crear Nuevo Usuario</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Crear Nuevo Usuario</h2>
    <form method="POST" action="crear_usuario.php">
        <label for="username">Nombre de usuario:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="email">Correo Electrónico:</label>
        <input type="text" id="email" name="email" required><br><br>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="rol">Rol:</label>
        <select id="rol" name="rol" required>
            <option value="usuario">Usuario</option>
            <option value="administrador">Administrador</option>
        </select><br><br>

        <input type="submit" value="Crear Usuario">
    </form>
    <a href="welcome.php">Volver a la página de bienvenida</a>
</body>
</html>
