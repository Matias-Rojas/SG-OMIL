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

// Incluir el archivo de la clase PHPMailer
require 'PHPMailer-master\src\PHPMailer.php';
require 'PHPMailer-master\src\SMTP.php';
require 'PHPMailer-master\src\Exception.php';

// Función para enviar correo
function enviarCorreo($destinatario) {
    // Configuración de PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->IsSMTP();
    //$mail->SMTPDebug = 2; // Habilitar logs

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->Port       = 587;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'hurdoxloly@gmail.com';
        $mail->Password   = 'nelsiwyqnvkmlrjf';

        // Configuración del correo
        $mail->setFrom('hurdoxloly@gmail.com', 'Remitente');
        $mail->addAddress($destinatario);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Información Importante';

        $body = '<p>Estimado/a usuario/a,</p>';
        $body .= '<p>Este es un correo de ejemplo con información importante.</p>';
        $body .= '<p>Puedes personalizar este contenido según tus necesidades.</p>';
        $body .= '<p>Atentamente,<br>Remitente</p>';
        
        $mail->Body = $body;

        $mail->send();
        return 'Correo enviado con éxito';
    } catch (Exception $e) {
        return "Error al enviar el correo: {$mail->ErrorInfo}";
    }
}

// Verificar si se ha enviado la solicitud POST para enviar el correo
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["correo"])) {
        $correoDestino = $_POST["correo"];
        $resultadoEnvio = enviarCorreo($correoDestino);
        echo $resultadoEnvio;
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presentar Usuario</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
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
    <!-- Agregar la tabla de seleccionados -->
    <h2>Presentar Usuarios</h2>
    <table id="tabla_seleccionados">
        <!-- Cabeceras de la tabla -->
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
                <th>Correo Contacto Empresa</th>
                <th>Acción</th>
            </tr>
        </thead>
        <!-- Cuerpo de la tabla -->
        <tbody>
            <?php
            // Obtener los datos de los usuarios seleccionados
            $sql_seleccionados = "SELECT uv.UsuarioID, uv.Rut, uv.Nombres, uv.Apellidos, oc.OfertaEmpleoID, oe.NombreVacante, oe.NombreEmpresa, oe.RutEmpresa, oe.RubroOferta, oe.CorreoContacto FROM sg_omil_usuariosvecinales AS uv JOIN sg_omil_seleccionados_empleo AS oc ON uv.UsuarioID = oc.UsuarioID JOIN sg_omil_ofertasempleo AS oe ON oc.OfertaEmpleoID = oe.OfertaEmpleoID";
            $result_seleccionados = $conn->query($sql_seleccionados);

            if ($result_seleccionados->num_rows > 0) {
                while ($row_seleccionados = $result_seleccionados->fetch_assoc()) {
                    // Mostrar los datos de los usuarios seleccionados
                    echo "<tr>";
                    echo "<td>{$row_seleccionados['UsuarioID']}</td>";
                    echo "<td>{$row_seleccionados['Rut']}</td>";
                    echo "<td>{$row_seleccionados['Nombres']} {$row_seleccionados['Apellidos']}</td>";
                    echo "<td>{$row_seleccionados['OfertaEmpleoID']}</td>";
                    echo "<td>{$row_seleccionados['NombreVacante']}</td>";
                    echo "<td>{$row_seleccionados['RubroOferta']}</td>";
                    echo "<td>{$row_seleccionados['RutEmpresa']}</td>";
                    echo "<td>{$row_seleccionados['NombreEmpresa']}</td>";
                    echo "<td>{$row_seleccionados['CorreoContacto']}</td>";
                    // Agregar un botón para enviar correo
                    echo "<td><form method='post'><input type='hidden' name='correo' value='{$row_seleccionados['CorreoContacto']}'><input type='submit' value='Enviar Correo'></form></td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
        <!-- Botón para descargar la tabla -->
        <input type="button" id="descargarTabla" value="Descargar Tabla"></input><br><br>

        <!-- Script para descargar la tabla como XLSX -->
        <script>
            document.getElementById('descargarTabla').addEventListener('click', function () {
                var table = document.getElementById('tabla_seleccionados'); // Selecciona la tabla por su id
                descargarTablaComoXLSX(table, 'tabla_seleccionados.xlsx');
            });

            function descargarTablaComoXLSX(table, filename) {
                var rows = table.querySelectorAll('tr');
                var data = [];

                for (var i = 0; i < rows.length; i++) {
                    var row = [], cols = rows[i].querySelectorAll('td, th');
                    for (var j = 0; j < cols.length; j++) {
                        row.push(cols[j].innerText);
                    }
                    data.push(row);
                }

                var wb = XLSX.utils.book_new();
                var ws = XLSX.utils.aoa_to_sheet(data);
                XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');

                XLSX.writeFile(wb, filename);
            }
        </script>

        <a href="welcome.php">Volver al Inicio</a>
    </div>
</body>
</html>