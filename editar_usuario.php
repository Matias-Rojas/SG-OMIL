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
