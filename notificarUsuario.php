<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$rol = $_SESSION["rol"];
$username = $_SESSION["username"];
$correo = $_SESSION["email"];

// Realiza la conexión a la base de datos
$servername = "localhost";
$db_username = "HURDOX";
$db_password = "gokudeus2023";
$database = "sg_omil";

// Crear la conexión
$conn = new mysqli($servername, $db_username, $db_password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Incluir el archivo de la clase PHPMailer
require 'PHPMailer-master\src\PHPMailer.php';
require 'PHPMailer-master\src\SMTP.php';
require 'PHPMailer-master\src\Exception.php';

$userId = $_GET['usuarioId'];
$ofertaId = $_GET['ofertaId'];

// Consulta SQL para obtener la información del usuario y su oferta de capacitación asociada
$sqlUsuario = "SELECT uv.*, sc.OfertaCapacitacionID 
               FROM sg_omil_usuariosvecinales uv
               JOIN sg_omil_seleccionados_capacitacion sc ON uv.UsuarioID = sc.UsuarioID
               WHERE uv.UsuarioID = $userId AND sc.OfertaCapacitacionID = $ofertaId";

$resultUsuario = $conn->query($sqlUsuario);

if ($resultUsuario->num_rows > 0) {
    $usuario = $resultUsuario->fetch_assoc();

    // Consulta SQL para obtener los detalles de la oferta de capacitación
    $ofertaId = $usuario['OfertaCapacitacionID'];
    $sqlOferta = "SELECT * FROM sg_omil_ofertascapacitacion WHERE OfertaCapacitacionID = $ofertaId";
    $resultOferta = $conn->query($sqlOferta);

    if ($resultOferta !== false && $resultOferta->num_rows > 0) {
        $oferta = $resultOferta->fetch_assoc();

        // Configuración de PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->Port       = 587;
            $mail->SMTPSecure = 'tls';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'hurdoxloly@gmail.com';
            $mail->Password   = 'nelsiwyqnvkmlrjf';

            // Configura el remitente y destinatario
            $mail->setFrom('hurdoxloly@gmail.com', 'SG-OMIL');
            $mail->addAddress($usuario['CorreoElectronico'], $usuario['Nombres'] . ' ' . $usuario['Apellidos']);

            // Configura el contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Notificacion de Seleccion para Capacitacion';
            $mail->Body = "Felicidades {$usuario['Nombres']} {$usuario['Apellidos']}, has sido seleccionado para una oferta de capacitacion. Detalles de la oferta:\n\n " .
                          "Nombre de la oferta: {$oferta['NombreCurso']}\n | " .
                          "OTEC: {$oferta['NombreOTEC']}\n | " .
                          "Para mas informacion pongase en contacto con: {$oferta['NombreContacto']}\n | " .
                          "Teléfono de contacto: {$oferta['TelefonoContacto']}\n | " .
                          "Correo de contacto: {$oferta['CorreoContacto']} | ";

            // Envía el correo
            $mail->send();

            echo 'Correo enviado correctamente';
            header("Location: seleccionadosCapacitacion.php");
            exit;
        } catch (Exception $e) {
            echo 'Error al enviar el correo: ' . $mail->ErrorInfo;
        }
    } else {
        echo 'Detalles de la oferta no encontrados';
    }
} else {
    echo 'Usuario no encontrado';
}
?>
