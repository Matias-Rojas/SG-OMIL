<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.html");
    exit;
}

// Realiza la conexión a la base de datos
$servername = "localhost";
$db_username = "HURDOX";
$db_password = "gokudeus2023";
$database = "sg_omil";

$conn = new mysqli($servername, $db_username, $db_password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if (isset($_POST["seleccionados"]) && isset($_POST["oferta_empleo_ids"])) {
    $seleccionados = $_POST["seleccionados"];
    $oferta_empleo_ids = $_POST["oferta_empleo_ids"];

    // Incluir el archivo de la clase PHPMailer
    require 'PHPMailer-master\src\PHPMailer.php';
    require 'PHPMailer-master\src\SMTP.php';
    require 'PHPMailer-master\src\Exception.php';

    // Crea una nueva instancia de PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->IsSMTP(); // Configura PHPMailer para usar SMTP

    // Configuración del servidor SMTP (en este ejemplo, se utiliza Gmail)
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;
    $mail->Username = 'hurdoxloly@gmail.com'; // Tu dirección de correo electrónico
    $mail->Password = 'ojcbwovhufwltjtg'; // Tu contraseña de correo electrónico

    // Prepara la consulta para insertar participantes seleccionados
    $stmt_insert = $conn->prepare("INSERT INTO sg_omil_seleccionados_empleo (UsuarioID, OfertaEmpleoID) VALUES (?, ?)");

    foreach ($seleccionados as $index => $usuario_vecinal_id) {
        $oferta_empleo_id = $oferta_empleo_ids[$index];
        $stmt_insert->bind_param("ii", $usuario_vecinal_id, $oferta_empleo_id);
        $stmt_insert->execute();

        // Resto del código no modificado para enviar el correo
        
    }

    $stmt_insert->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selección de Usuarios Vecinales</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        body {
            height: 100vh;
            margin: 0;
        }
        .centered-form {
            text-align: center;
        }
        table {
            margin: 0 auto;
            border-collapse: collapse;
            width: 80%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="centered-form">
        <h2>Selección de Usuarios Vecinales para Oferta de Empleo</h2>
        <form method="POST" action="seleccion_usuario_empleo.php">
            <!-- Lista de usuarios vecinales postulantes (obtenidos desde la base de datos) -->
            <table>
                <thead>
                    <tr>
                        <th>Id Usuario</th>
                        <th>RUT</th>
                        <th>Nombre Completo</th>
                        <th>Id de Oferta de Empleo</th>
                        <th>Nombre de Oferta</th>
                        <th>Rubro de Oferta</th>
                        <th>Rut de Empresa</th>
                        <th>Nombre de Empresa</th>
                        <th>Correo Contacto</th>
                        <th>Seleccionar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT uv.UsuarioID, uv.Rut, uv.Nombres, uv.Apellidos, oc.OfertaEmpleoID, oe.NombreVacante, oe.NombreEmpresa, oe.RutEmpresa, oe.RubroOferta, oe.CorreoContacto FROM sg_omil_usuariosvecinales AS uv JOIN sg_omil_datos_postulacion_empleo AS oc ON uv.Rut = oc.Rut JOIN sg_omil_ofertasempleo AS oe ON oc.OfertaEmpleoID = oe.OfertaEmpleoID";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $index = 0; // Agregamos un índice para mantener un seguimiento de las filas
                        while ($row = $result->fetch_assoc()) {
                            $usuario_vecinal_id = $row["UsuarioID"];
                            $rut = $row["Rut"];
                            $nombre = $row["Nombres"];
                            $apellido = $row["Apellidos"];
                            $oferta_empleo_id = $row["OfertaEmpleoID"];
                            $nombre_oferta = $row["NombreVacante"];
                            $RubroOferta = $row["RubroOferta"];
                            $RutEmpresa = $row["RutEmpresa"];
                            $NombreEmpresa = $row["NombreEmpresa"];
                            $CorreoContacto = $row["CorreoContacto"];

                            echo "<tr>";
                            echo "<td>$usuario_vecinal_id</td>";
                            echo "<td>$rut</td>";
                            echo "<td>$nombre $apellido</td>";
                            echo "<td>$oferta_empleo_id</td>";
                            echo "<td>$nombre_oferta</td>";
                            echo "<td>$RubroOferta</td>";
                            echo "<td>$RutEmpresa</td>";
                            echo "<td>$NombreEmpresa</td>";
                            echo "<td>$CorreoContacto</td>";
                            echo "<td><input type='checkbox' name='seleccionados[$index]' value='$usuario_vecinal_id'><input type='hidden' name='oferta_empleo_ids[$index]' value='$oferta_empleo_id'></td>";
                            echo "</tr>";

                            $index++; // Incrementamos el índice para la siguiente fila
                        }
                    }
                    ?>
                </tbody>
            </table>
            <br>
            <input type="submit" value="Seleccionar Usuarios">
        </form>
        <a href="welcome.php">Volver al Inicio</a>
    </div>
</body>
</html>
