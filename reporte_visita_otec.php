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

// Consulta para obtener los datos de las OTECs que tienen visitas programadas
$sql = "SELECT vo.RutEmpresaOTEC, CONCAT(' Rut de OTEC: ', otec.RutEmpresaOTEC, ' | ',' Nombre de OTEC: ', otec.NombreOTEC, ' | ', ' Sector: ', otec.Sector, ' | ', 'Propósito: ', vo.Proposito) AS DatosOtec FROM sg_omil_visitas_otecs AS vo JOIN sg_omil_ofertascapacitacion AS otec ON vo.RutEmpresaOTEC = otec.RutEmpresaOTEC";
$result = $conn->query($sql);

$datos_otec = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $datos_otec[] = $row; // Almacena toda la fila en el array
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $RutEmpresaOTEC = $_POST["RutEmpresaOTEC"];
    $fecha_informe = $_POST["fecha_informe"];
    $detalles_informe = $_POST["detalles_informe"];
    $acuerdos_tomados = $_POST["acuerdos_tomados"];
    $fecha_proxima_visita = $_POST["fecha_proxima_visita"];

    // Insertar el informe en la base de datos
    $stmt = $conn->prepare("INSERT INTO sg_omil_reportes_visita_otecs (RutEmpresaOTEC, FechaInforme, DetallesInforme, AcuerdosTomados, FechaProximaVisita) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $RutEmpresaOTEC, $fecha_informe, $detalles_informe, $acuerdos_tomados, $fecha_proxima_visita);

    if ($stmt->execute()) {
        // Informe ingresado con éxito
        echo "Informe ingresado con éxito.";

        // Obtener la dirección de correo de la OTEC
        $result = $conn->query("SELECT CorreoContacto FROM sg_omil_ofertascapacitacion WHERE RutEmpresaOTEC = $RutEmpresaOTEC");

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $correo_otec = $row["CorreoContacto"];

            // Enviar correo de notificación
            require 'PHPMailer-master/src/PHPMailer.php';
            require 'PHPMailer-master/src/SMTP.php';
            require 'PHPMailer-master/src/Exception.php';

            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->IsSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            $mail->SMTPSecure = 'tls';
            $mail->SMTPAuth = true;
            $mail->Username = 'hurdoxloly@gmail.com'; // Tu dirección de correo electrónico
            $mail->Password = 'ojcbwovhufwltjtg'; // Tu contraseña de correo electrónico

            $subject = 'Informe de Visita a OTEC';
            $message = "Hemos ingresado un informe relacionado con su OTEC. La fecha del informe es: $fecha_informe. Detalles del informe: $detalles_informe";
            $headers = 'From: hurdoxloly@gmail.com';

            $mail->SetFrom('hurdoxloly@gmail.com', 'Matias Rojas'); // Cambia a tu dirección de correo y nombre
            $mail->AddAddress($correo_otec);

            $mail->Subject = $subject;
            $mail->Body = $message;

            if ($mail->Send()) {
                echo "Correo de notificación enviado a la OTEC ($correo_otec).";
            } else {
                echo "Error al enviar el correo de notificación.";
            }
        }
    } else {
        // Error al ingresar el informe
        echo "Error al ingresar el informe: " . $stmt->error;
    }

    $stmt->close(); // Cierra la declaración preparada
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar Informe de Visita a OTEC</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Ingresar Informe de Visita a OTEC</h2>
    <form method="POST" action="reporte_visita_otec.php">
        <label for="RutEmpresaOTEC">Rut de OTEC:</label>
        <select id="RutEmpresaOTEC" name="RutEmpresaOTEC" required>
            <?php
            // Genera las opciones del menú desplegable con los datos de las OTECs que tienen visitas programadas
            foreach ($datos_otec as $otec) {
                echo "<option value='" . $otec["RutEmpresaOTEC"] . "'>" . $otec["DatosOtec"] . "</option>";
            }
            ?>
        </select><br><br>

        <label for="fecha_informe">Fecha del Informe:</label>
        <input type="date" name="fecha_informe" id="fecha_informe" required>

        <label for="detalles_informe">Detalles del Informe:</label>
        <textarea name="detalles_informe" id="detalles_informe" required></textarea>

        <label for="acuerdos_tomados">Acuerdos Tomados:</label>
        <textarea name="acuerdos_tomados" id="acuerdos_tomados" required></textarea>
        
        <label for="fecha_proxima_visita">Fecha Proxima Visita:</label>
        <input type="date" name="fecha_proxima_visita" id="fecha_proxima_visita" required>

        <input type="submit" value="Ingresar Informe">
    </form>
    <a href="welcome.php">Volver al Inicio</a>
</body>
</html>
