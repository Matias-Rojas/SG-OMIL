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

// Consulta para obtener los RUTs de las Empresas desde la base de datos
$sql = "SELECT RutEmpresaOTEC, CONCAT(' Rut de OTEC: ', RutEmpresaOTEC, ' | ',' Nombre de OTEC: ', NombreOTEC, ' | ', ' Sector: ', Sector) AS DatosOtec FROM sg_omil_ofertascapacitacion";
$result = $conn->query($sql);

$datos_otec = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $datos_otec[] = $row; // Almacena toda la fila en el array
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $RutEmpresaOTEC = $_POST["RutEmpresaOTEC"];
    $fecha_visita = $_POST["fecha_visita"];
    $hora_inicio = $_POST["hora_inicio"];
    $proposito = $_POST["proposito"];
    $duracion_estimada = $_POST["duracion_estimada"];

    // Insertar la visita programada en la base de datos
    $stmt = $conn->prepare("INSERT INTO sg_omil_visitas_otecs (RutEmpresaOTEC, FechaVisita, HoraInicio, Proposito, DuracionEstimada) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $RutEmpresaOTEC, $fecha_visita, $hora_inicio, $proposito, $duracion_estimada);

    if ($stmt->execute()) {
        // Visita programada con éxito
        echo "Visita programada con éxito.";

        // Obtener la dirección de correo de la empresa
        $result = $conn->query("SELECT CorreoContacto FROM sg_omil_ofertascapacitacion WHERE RutEmpresaOTEC = $RutEmpresaOTEC");

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $correo_empresa = $row["CorreoContacto"];

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

            $subject = 'Visita Programada';
            $message = "Hemos programado una visita a su OTEC para el $fecha_visita a la hora estimada de $hora_inicio. El propósito de la visita es: $proposito. La duración estimada es de $duracion_estimada horas.";
            $headers = 'From: hurdoxloly@gmail.com';

            $mail->SetFrom('hurdoxloly@gmail.com', 'Matias Rojas'); // Cambia a tu dirección de correo y nombre
            $mail->AddAddress($correo_empresa);

            $mail->Subject = $subject;
            $mail->Body = $message;

            if ($mail->Send()) {
                echo "Correo de notificación enviado a la empresa ($correo_empresa).";
            } else {
                echo "Error al enviar el correo de notificación.";
            }
        }
    } else {
        // Error al programar la visita
        echo "Error al programar la visita: " . $stmt->error;
    }

    $stmt->close(); // Cierra la declaración preparada
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Visita a Otec</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Registrar Visita a Otec</h2>
    <form method="POST" action="visita_otec.php">
        <label for="RutEmpresaOTEC">Rut de Otec:</label>
        <select id="RutEmpresaOTEC" name="RutEmpresaOTEC" required>
            <?php
            // Genera las opciones del menú desplegable con los RUTs de las OTECs obtenidos desde la base de datos
            foreach ($datos_otec as $otec) {
                echo "<option value='" . $otec["RutEmpresaOTEC"] . "'>" . $otec["DatosOtec"] . "</option>";
            }
            ?>
        </select><br><br>

        <label for="fecha_visita">Fecha de Visita:</label>
        <input type="date" name="fecha_visita" id="fecha_visita" required>

        <label for="hora_inicio">Hora de Inicio:</label>
        <input type="time" name="hora_inicio" id="hora_inicio" required>

        <label for="proposito">Propósito de la Visita:</label>
        <input type="text" name="proposito" id="proposito" required>

        <label for="duracion_estimada">Duración Estimada (horas):</label>
        <input type="number" name="duracion_estimada" id="duracion_estimada" required>

        <input type="submit" value="Registrar Visita">
    </form>
    <a href="welcome.php">Volver al Inicio</a>
</body>
</html>
