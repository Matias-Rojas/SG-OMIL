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
        $_SESSION["password"] = $row["password"];  // Agrega esta línea para almacenar la contraseña
        $_SESSION["rol"] = $row["rol"];
        header("location: index.php");
    } else {
        // Las credenciales son inválidas
        echo "Credenciales incorrectas.";
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
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
            <h1 style="font-family: Arial, sans-serif;">Iniciar Sesion</h1>
        
            <div class="login-form">
              <form action="login.php" method="post">
                <input type="text" id="username_email" name="username_email" placeholder="Nombre de Usuario o Correo Electronico" required>
                <input type="password" id="password" name="password" placeholder="Contraseña" required>
    
                <button type="submit">Iniciar Sesion</button>
    
              </form>
            </div>
        
          </div>
          </div>
      </div>
</body>
</html>
