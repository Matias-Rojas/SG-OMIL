<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$error = $message = "";

// Verificar si se ha enviado el formulario de cambio de contraseña
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_password"])) {
    $old_password = $_POST["old_password"];
    $new_password = $_POST["new_password"];
    $confirm_new_password = $_POST["confirm_new_password"];

    // Validar la contraseña actual
    if ($old_password == $_SESSION["password"]) {
        // Validar que las nuevas contraseñas coincidan
        if ($new_password == $confirm_new_password) {
            // Actualizar la contraseña en la base de datos
            $servername = "localhost";
            $db_username = "HURDOX";
            $db_password = "gokudeus2023";
            $database = "sg_omil_usuarios";

            $conn = new mysqli($servername, $db_username, $db_password, $database);

            if ($conn->connect_error) {
                die("Error de conexión: " . $conn->connect_error);
            }

            // Usar consultas preparadas para evitar SQL injection
            $username = $_SESSION["username"];
            $update_query = "UPDATE sg_omil_usuarios SET password = '$new_password' WHERE username = '$username'";
            
            if ($conn->query($update_query) === TRUE) {
                $message = "Contraseña cambiada exitosamente.";
                // Actualizar la variable de sesión con la nueva contraseña
                $_SESSION["password"] = $new_password;
            } else {
                $error = "Error al actualizar la contraseña: " . $conn->error;
            }

            // Cerrar la conexión
            $conn->close();
            // Cierra la sesión
            session_destroy();
        } else {
            $error = "Las nuevas contraseñas no coinciden.";
        }
    } else {
        $error = "Contraseña actual incorrecta.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <link rel="stylesheet" href="assets\css\login.css">
</head>
<body>
    <div class="parent clearfix">
        <div class="bg-illustration">
          <img src="assets\img\logotipo_municipalidad_de_melipi.png" alt="logo">
    
          <div class="burger-btn">
            <span></span>
            <span></span>
            <span></span>
          </div>
    
        </div>
        
        <div class="login">
          <div class="container">
            <h1 style="font-family: Arial, sans-serif;">Cambiar Contraseña</h1>

            <div class="login-form">
                <div  class="change-password-form">
              <form action="cambiar_contraseña.php" method="post">
                <input type="password" id="old_password" name="old_password" placeholder="Contraseña Antigua" required>
                <input type="password" id="new_password" name="new_password" placeholder="Contraseña Nueva" required>
                <input type="password" id="confirm_new_password" name="confirm_new_password" placeholder="Confirmar Contraseña Nueva" required>

                <?php if ($error): ?>
                    <p style="color: red;"><?php echo $error; ?></p>
                <?php endif; ?>
                <?php if ($message): ?>
                    <p style="color: green;"><?php echo $message; ?></p>
                <?php endif; ?>

                <button type="submit" name="change_password">Cambiar Contraseña</button>
                <a href="index.php">Volver al inicio</a>
                
              </form>
            </div>
            </div>
        
          </div>
          </div>
      </div>
</body>
</html>
