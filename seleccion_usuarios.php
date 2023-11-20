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

if (isset($_POST["seleccionados"]) && isset($_POST["oferta_capacitacion_ids"])) {
    $seleccionados = $_POST["seleccionados"];
    $oferta_capacitacion_ids = $_POST["oferta_capacitacion_ids"];

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
    $stmt_insert = $conn->prepare("INSERT INTO sg_omil_seleccionados_capacitacion (UsuarioID, OfertaCapacitacionID) VALUES (?, ?)");

    foreach ($seleccionados as $index => $usuario_vecinal_id) {
        $oferta_capacitacion_id = $oferta_capacitacion_ids[$index];
        $stmt_insert->bind_param("ii", $usuario_vecinal_id, $oferta_capacitacion_id);
        $stmt_insert->execute();

        // Resto del código no modificado para enviar el correo
        $result1 = $conn->query("SELECT uv.Nombres, uv.Apellidos, uv.CorreoElectronico, oc.NombreCurso, oc.CorreoContacto, oc.TelefonoContacto FROM sg_omil_usuariosvecinales AS uv JOIN sg_omil_datos_postulacion_capacitacion AS pc ON uv.Rut=pc.Rut JOIN sg_omil_ofertascapacitacion AS oc ON pc.OfertaCapacitacionID = oc.OfertaCapacitacionID WHERE uv.UsuarioID = $usuario_vecinal_id");

        if ($result1->num_rows == 1) {
            $row = $result1->fetch_assoc();
            $nombre = $row["Nombres"];
            $apellido = $row["Apellidos"];
            $correo = $row["CorreoElectronico"];
            $nombre_capacitacion = $row["NombreCurso"];
            $correo_capacitacion = $row["CorreoContacto"];
            $telefono_capacitacion = $row["TelefonoContacto"];

            // Crear el cuerpo del mensaje del correo
            $subject = 'Has sido seleccionado para una capacitacion';
            $message = "¡Felicidades $nombre $apellido! Has sido seleccionado para participar en nuestra capacitacion llamada $nombre_capacitacion.\n\n";
            $message .= "Para mayor información, ponte en contacto con la oferta en el siguiente correo: $correo_capacitacion o al número de contacto: $telefono_capacitacion.";
            $headers = 'From: hurdoxloly@gmail.com'; // Cambia tucorreo@gmail.com al correo desde el que deseas enviar

            // Configura los destinatarios y el contenido del correo
            $mail->SetFrom('hurdoxloly@gmail.com', 'Matias Rojas'); // Cambia tucorreo@gmail.com y Tu Nombre
            $mail->AddAddress($correo, $nombre);

            $mail->Subject = $subject;
            $mail->Body = $message;

            if ($mail->Send()) {
                echo "Correo enviado correctamente a $nombre $apellido ($correo).";
            } else {
                echo "Error al enviar el correo a $nombre $apellido ($correo).";
            }
        }
    }

    $stmt_insert->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selección de Usuarios Vecinales para Capacitacion</title>
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
        <h2>Selección de Usuarios Vecinales para Capacitacion</h2>
        <form method="POST" action="seleccion_usuarios.php">
            <!-- Lista de usuarios vecinales postulantes (obtenidos desde la base de datos) -->
            <table>
                <thead>
                    <tr>
                        <th>Id de Usuarios</th>
                        <th>RUT</th>
                        <th>Nombre Completo</th>
                        <th>Id de Oferta de Capacitación</th>
                        <th>Nombre de Oferta</th>
                        <th>Nombre de OTEC</th>
                        <th>Rut de OTEC</th>
                        <th>Seleccionar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT uv.UsuarioID, uv.Rut, uv.Nombres, uv.Apellidos, oc.OfertaCapacitacionID, oa.NombreCurso, oa.NombreOTEC, oa.RutEmpresaOTEC FROM sg_omil_usuariosvecinales AS uv JOIN sg_omil_datos_postulacion_capacitacion AS oc ON uv.Rut = oc.Rut JOIN sg_omil_ofertascapacitacion AS oa ON oc.OfertaCapacitacionID = oa.OfertaCapacitacionID";
                    $result = $conn->query($sql);

                    if (!$result) {
                        die("Error en la consulta: " . $conn->error);
                    }

                    if ($result->num_rows > 0) {
                        $index = 0; // Agregamos un índice para mantener un seguimiento de las filas
                        while ($row = $result->fetch_assoc()) {
                            $usuario_vecinal_id = $row["UsuarioID"];
                            $rut = $row["Rut"];
                            $nombre = $row["Nombres"];
                            $apellido = $row["Apellidos"];
                            $oferta_capacitacion_id = $row["OfertaCapacitacionID"];
                            $nombre_capacitacion = $row["NombreCurso"];
                            $NombreOTEC = $row["NombreOTEC"];
                            $RutEmpresaOTEC = $row["RutEmpresaOTEC"];

                            echo "<tr>";
                            echo "<td>$usuario_vecinal_id</td>";
                            echo "<td>$rut</td>";
                            echo "<td>$nombre $apellido</td>";
                            echo "<td>$oferta_capacitacion_id</td>";
                            echo "<td>$nombre_capacitacion</td>";
                            echo "<td>$NombreOTEC</td>";
                            echo "<td>$RutEmpresaOTEC</td>";
                            echo "<td><input type='checkbox' name='seleccionados[$index]' value='$usuario_vecinal_id'><input type='hidden' name='oferta_capacitacion_ids[$index]' value='$oferta_capacitacion_id'></td>";
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
        <h2>Seleccionados</h2>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
        <table id="tabla_seleccionados">
                <thead>
                    <tr>
                        <th>Id Usuario</th>
                        <th>RUT</th>
                        <th>Nombre Completo</th>
                        <th>Id de Oferta de Capacitación</th>
                        <th>Nombre de Oferta</th>
                        <th>Rut de OTEC</th>
                        <th>Nombre de OTEC</th>
                        <th>Nombre de Contacto</th>
                        <th>Correo de Contacto</th>
                        <th>Numero de Contacto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT uv.UsuarioID, uv.Rut, uv.Nombres, uv.Apellidos, oc.OfertaCapacitacionID, oa.NombreCurso, oa.RutEmpresaOTEC, oa.CorreoContacto, oa.TelefonoContacto, oa.NombreOTEC, oa.NombreContacto FROM sg_omil_usuariosvecinales AS uv JOIN sg_omil_seleccionados_capacitacion AS oc ON uv.UsuarioID = oc.UsuarioID JOIN sg_omil_ofertascapacitacion AS oa ON oc.OfertaCapacitacionID = oa.OfertaCapacitacionID";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $usuario_vecinal_id = $row["UsuarioID"];
                            $rut = $row["Rut"];
                            $nombre = $row["Nombres"];
                            $apellido = $row["Apellidos"];
                            $oferta_capacitacion_id = $row["OfertaCapacitacionID"];
                            $nombre_capacitacion = $row["NombreCurso"];
                            $NombreOTEC = $row["NombreOTEC"];
                            $rut_otec = $row["RutEmpresaOTEC"];
                            $NombreContacto = $row["NombreContacto"];
                            $correo_contacto = $row["CorreoContacto"];
                            $telefono_contacto = $row["TelefonoContacto"];

                            echo "<tr>";
                            echo "<td>$usuario_vecinal_id</td>";
                            echo "<td>$rut</td>";
                            echo "<td>$nombre $apellido</td>";
                            echo "<td>$oferta_capacitacion_id</td>";
                            echo "<td>$nombre_capacitacion</td>";
                            echo "<td>$rut_otec</td>";
                            echo "<td>$NombreOTEC</td>";
                            echo "<td>$NombreContacto</td>";
                            echo "<td>$correo_contacto</td>";
                            echo "<td>$telefono_contacto</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
            <input type="submit" id="descargarTabla" value="Descargar Tabla"></input><br><br>
            <script>
                document.getElementById('descargarTabla').addEventListener('click', function () {
                var table = document.getElementById('tabla_seleccionados'); // Selecciona la tabla por su id
                descargarTablaComoXLSX(table, 'tabla_seleccionados.xlsx');
            });

            function descargarTablaComoXLSX(table, filename) {
                var rows = table.querySelectorAll('tr');
                var data = [];

                for (var i = 0; i < rows.length; i++) {
                    var cols = rows[i].querySelectorAll('td, th');
                    var rowData = [];

                    for (var j = 0; j < cols.length; j++) {
                        rowData.push({ v: cols[j].textContent });
                    }

                    data.push(rowData);
                }

                var ws = XLSX.utils.aoa_to_sheet(data);

                // Definir estilos con bordes para todas las celdas
                ws['!cols'] = [{ wch: 15 }, { wch: 15 }, { wch: 20 }, { wch: 25 }, { wch: 20 }, { wch: 15 }, { wch: 30 }, { wch: 20 }];

                var wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Hoja1');
                XLSX.writeFile(wb, filename);
            }
            </script>
        <a href="welcome.php">Volver al Inicio</a>
    </div>
</body>
</html>
