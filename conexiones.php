<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_email = $_POST["username_email"];
    $password = $_POST["password"];

    // Establecer la conexión a la base de datos
    $servername = "localhost";
    $db_username = "HURDOX";
    $db_password = "gokudeus2023";
    $database = "sg_omil";

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

<?php
// editar_usuario.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $userId = $_POST["id"];
    $newUsername = $_POST["username"];
    $newMail = $_POST["email"];
    $newRol = $_POST["rol"];
    $newPassword = $_POST["password"]; // Nueva contraseña sin cifrar

    // Conectarse a la base de datos
    $servername = "localhost";
    $db_username = "HURDOX";
    $db_password = "gokudeus2023";
    $database = "sg_omil";

    $conn = new mysqli($servername, $db_username, $db_password, $database);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Verificar si el nuevo nombre de usuario o correo ya existen en otros registros
    $checkQuery = "SELECT id FROM sg_omil_usuarios WHERE (username = ? OR email = ?) AND id <> ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ssi", $newUsername, $newMail, $userId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Ya existe un usuario con el mismo nombre de usuario o correo
        echo "Ya existe un usuario con el mismo nombre de usuario o correo.";
    } else {
        // Consulta preparada para actualizar los datos del usuario excluyendo la contraseña
        if (!empty($newPassword)) {
            $updateStmt = $conn->prepare("UPDATE sg_omil_usuarios SET username=?, email=?, password=?, rol=? WHERE id=?");
            $updateStmt->bind_param("ssssi", $newUsername, $newMail, $newPassword, $newRol, $userId);
        } else {
            $updateStmt = $conn->prepare("UPDATE sg_omil_usuarios SET username=?, email=?, rol=? WHERE id=?");
            $updateStmt->bind_param("sssi", $newUsername, $newMail, $newRol, $userId);
        }

        if ($updateStmt->execute()) {
            // Actualización exitosa
            echo "Usuario actualizado con éxito.";
        } else {
            // Error en la actualización
            echo "Error al actualizar el usuario.";
        }

        // Cerrar la conexión
        $updateStmt->close();
    }

    // Redirigir a la página de administrador
    header("Location: admin.php");

    // Cerrar la conexión
    $checkStmt->close();
    $conn->close();
}
?>

<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"]; // Obtener el ID del usuario a eliminar

    // Establecer la conexión a la base de datos
    $servername = "localhost";
    $db_username = "HURDOX";
    $db_password = "gokudeus2023";
    $database = "sg_omil";

    $conn = new mysqli($servername, $db_username, $db_password, $database);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Consulta para eliminar el usuario
    $stmt = $conn->prepare("DELETE FROM sg_omil_usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Eliminación exitosa
        header("location: admin.php"); // Redirigir de nuevo a la página de administración
    } else {
        echo "Error al eliminar el usuario.";
    }

    $stmt->close();
    $conn->close();
}
?>

<?php
// obtener_usuario.php

// Conectarse a la base de datos
$servername = "localhost";
$db_username = "HURDOX";
$db_password = "gokudeus2023";
$database = "sg_omil";

$conn = new mysqli($servername, $db_username, $db_password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener el ID del usuario de la solicitud GET
$userId = $_GET["id"];

// Consulta para obtener los datos del usuario por ID
$sql = "SELECT id, username, email, rol FROM sg_omil_usuarios WHERE id = $userId";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $userData = $result->fetch_assoc();
    // Devolver los datos del usuario en formato JSON
    echo json_encode($userData);
} else {
    echo "Usuario no encontrado";
}

// Cerrar la conexión
$conn->close();
?>
