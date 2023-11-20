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
$sql = "SELECT Rut
        FROM (
            SELECT uv.Rut,
                ROW_NUMBER() OVER (PARTITION BY uv.Rut ORDER BY (SELECT NULL)) AS RowNum
            FROM `sg_omil_seleccionados_empleo` AS sc
            JOIN sg_omil_usuariosvecinales AS uv ON sc.UsuarioID = uv.UsuarioID
        ) AS Ranked
        WHERE RowNum = 1;";
$result = $conn->query($sql);

$ruts_usvecinal = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ruts_usvecinal[] = $row["Rut"];
    }
}

// Obtener el rut del usuario vecinal seleccionado (puedes obtenerlo de la sesión o de algún otro lugar)
$rut_usuario = isset($_POST["rut_usuario"]) ? $_POST["rut_usuario"] : (count($ruts_usvecinal) > 0 ? $ruts_usvecinal[0] : '');

// Consulta para obtener las capacitaciones asociadas al usuario
$sql_capacitaciones = "SELECT oc.NombreVacante 
                        FROM `sg_omil_datos_postulacion_empleo` AS sc 
                        JOIN sg_omil_ofertasempleo AS oc ON sc.OfertaEmpleoID = oc.OfertaEmpleoID 
                        JOIN sg_omil_usuariosvecinales AS uv ON sc.Rut = uv.Rut 
                        WHERE uv.Rut = '$rut_usuario'";

$result_capacitaciones = $conn->query($sql_capacitaciones);

$capacitaciones = array();

if ($result_capacitaciones->num_rows > 0) {
    while ($row_capacitacion = $result_capacitaciones->fetch_assoc()) {
        $capacitaciones[] = $row_capacitacion["NombreVacante"];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_empleo = $_POST["nombre_capacitacion"];
    $seguimiento_postulacion = $_POST["seguimiento_postulacion"];

    // Insertar la capacitación en la base de datos
    $stmt = $conn->prepare("INSERT INTO sg_omil_seguimiento_laboral (Rut, PostulacionEmpleo, SeguimientoEmpleo) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $rut_usuario, $nombre_empleo, $seguimiento_postulacion);

    if ($stmt->execute()) {
        echo "Seguimiento de empleo realizado con exito.";
    } else {
        echo "Error al realizar seguimiento: " . $stmt->error;
    }

    $stmt->close(); // Mover la línea aquí
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Seguimiento Laboral</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Cuando cambia la selección del rut
            $("#rut_usuario").change(function() {
                // Obtener el valor seleccionado
                var rutSeleccionado = $(this).val();

                // Realizar la solicitud AJAX para obtener las capacitaciones asociadas al usuario
                $.ajax({
                    type: "POST",
                    url: "obtener_empleo.php", // Reemplaza esto con la ruta correcta de tu archivo PHP
                    data: { rut_usuario: rutSeleccionado },
                    success: function(data) {
                        // Actualizar el contenido del select de capacitaciones
                        $("#nombre_capacitacion").html(data);
                    }
                });
            });
        });
    </script>
</head>
<body>
    <h2>Registro de Seguimiento Laboral</h2>
    <form method="POST" action="registrar_seguimiento_laboral.php">
        <label for="rut_usuario">Rut de Usuario Vecinal Matriculado:</label>
        <select id="rut_usuario" name="rut_usuario" required>
            <?php
            // Genera las opciones del menú desplegable con los RUTs de los usuarios vecinales obtenidos desde la base de datos
            foreach ($ruts_usvecinal as $rut) {
                echo "<option value='$rut' " . ($rut == $rut_usuario ? "selected" : "") . ">$rut</option>";
            }
            ?>
        </select><br><br>

        <label for="nombre_capacitacion">Nombre del Empleo:</label>
        <select name="nombre_capacitacion" id="nombre_capacitacion" required>
            <?php
            // Llena el select con las capacitaciones asociadas al usuario seleccionado
            foreach ($capacitaciones as $capacitacion) {
                echo "<option value='$capacitacion'>$capacitacion</option>";
            }
            ?>
        </select><br><br>

        <label for="seguimiento_postulacion">Seguimiento a la postulacion:</label>
        <textarea name="seguimiento_postulacion" id = "seguimiento_postulacion" required></textarea>

        <input type="submit" value="Registrar Seguimiento Laboral">
    </form>
    <a href="welcome.php">Volver al Inicio</a>
</body>
</html>
