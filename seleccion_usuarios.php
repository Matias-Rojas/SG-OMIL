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

$conn = new mysqli($servername, $db_username, $db_password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if (isset($_POST["seleccionados"]) && isset($_POST["oferta_capacitacion_ids"])) {
    $seleccionados = $_POST["seleccionados"];
    $oferta_capacitacion_ids = $_POST["oferta_capacitacion_ids"];
/*
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
*/
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
/*
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
            }*/
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
    <link rel="stylesheet" type="text/css" href="assets/css/estilos.css">
    <!-- BOOTSTRAP STYLES-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
     <!-- FONTAWESOME STYLES-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
     <!-- MORRIS CHART STYLES-->
    <link href="assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
    <link href="assets/css/custom.css" rel="stylesheet" />
     <!-- GOOGLE FONTS-->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <!-- TABLE STYLES-->
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
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
<div id="wrapper">
        <nav class="navbar navbar-default navbar-cls-top " role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">SG-OMIL</a> 
            </div>
    <div style="color: white; padding: 15px 50px 5px 50px; float: right; font-size: 16px;">
        <a id="cargarDatosBtn" class="btn btn-danger square-btn-adjust">Actualizar Datos Regionales</a>
        <script>
        document.getElementById('cargarDatosBtn').addEventListener('click', function() {
            // Llamada a la API utilizando JavaScript
            fetch('cargar_datos_geograficos.php')
                .then(response => response.json())
                .then(data => {
                    if (data.mensajes.length > 0) {
                        alert('Mensajes de la carga:\n' + data.mensajes.join('\n'));
                    } else {
                        alert('Datos cargados exitosamente');
                    }
                })
                .catch(error => {
                    console.error('Error al cargar datos:', error);
                });
            });
        </script>
        <a href="cambiar_contraseña.php" class="btn btn-danger square-btn-adjust">Cambiar Contraseña</a>
        <a href="logout.php" class="btn btn-danger square-btn-adjust">Cerrar Sesion</a> 
    </div>
        </nav>   
           <!-- /. NAV TOP  -->
                <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
				<li class="text-center">
                    <img src="assets/img/Omil.png" class="user-image img-responsive"/>
					</li>
				
					
                    <li>
                        <a class="active-menu"  href="index.php"><i class="fa fa-bar-chart-o fa-3x"></i> Dashboard</a>
                    </li>
                    <?php if ($rol === "administrador"): ?>
                    <li>
                        <a href="#"><i class="fa fa-desktop fa-3x"></i> Administracion<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="crear_usuario.php">Crear Usuario</a>
                            </li>
                            <li>
                                <a href="admin.php">Administrar Usuario</a>
                            </li>
                        </ul>
                      </li>
                      <?php endif; ?> 
                    <li>
                        <a href="#"><i class="fa fa-edit fa-3x"></i> Registros<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="#">Usuarios Vecinales<span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li>
                                        <a href="registro_usuario_vecinal.php">Registrar Usuario Vecinal</a>
                                    </li>
                                    <li>
                                        <a href="asociar_empleo.php">Asociar Requerimiento de Empleo</a>
                                    </li>
                                    <li>
                                        <a href="asociar_capacitacion.php">Asociar Requerimiento de Capacitacion</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">Ofertas de Capacitacion<span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li>
                                        <a href="registro_oferta.php">Crear Oferta de Capacitacion</a>
                                    </li>
                                    <li>
                                        <a href="postulacion_capacitacion.php">Postular Usuario Vecinal</a>
                                    </li>
                                    <li>
                                        <a href="seleccion_usuarios.php">Seleccionar Usuario Vecinal</a>
                                    </li>
                                    <li>
                                        <a href="matricular_usuario.php">Matricular Usuario Vecinal</a>
                                    </li>
                                    <li>
                                        <a href="registrar_capacitacion.php">Registrar Capacitacion Realizada a Usuario Vecinal</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">Ofertas de Empleo<span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li>
                                        <a href="registro_oferta_empleo.php">Crear Oferta de Empleo</a>
                                    </li>
                                    <li>
                                        <a href="postulacion_empleo.php">Postular Usuario Vecinal</a>
                                    </li>
                                    <li>
                                        <a href="seleccion_usuario_empleo.php">Seleccionar Usuario Vecinal</a>
                                    </li>
                                    <li>
                                        <a href="presentar_usuarios.php">Presentar Usuario Vecinal</a>
                                    </li>
                                    <li>
                                        <a href="registrar_ocupacion.php">Registrar Ocupacion a Usuario Vecinal</a>
                                    </li>
                                    <li>
                                        <a href="registrar_seguimiento_laboral.php">Registrar Seguimiento Laboral</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                      </li> 
                      <li>
                        <a href="#"><i class="fa fa-sitemap fa-3x"></i> Gestion<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="#">Gestion con Empresas<span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li>
                                        <a href="visita_empresa.php">Programar Visita a Empresa</a>
                                    </li>
                                    <li>
                                        <a href="reporte_visita_empresa.php">Ingresar Reporte de Visita a Empresa</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">Gestion con OTEC<span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li>
                                        <a href="visita_otec.php">Programar Visita a OTEC</a>
                                    </li>
                                    <li>
                                        <a href="reporte_visita_otec.php">Ingresar Reporte de Visita a OTEC</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">Gestion con BNE-SENCE<span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li>
                                        <a href="registrar_meta_anual.php">Metas Anuales</a>
                                    </li>
                                    <li>
                                        <a href="registrar_avance_mensual.php">Avance Mensual de Metas Anuales</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">Gestion con Unidades Municipales<span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li>
                                        <a href="derivacion_capacitacion.php">Derivar Capacitacion</a>
                                    </li>
                                    <li>
                                        <a href="derivacion_empleo.php">Derivar Oferta Laboral</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                      </li> 
                      <li> 
                      <a href="#"><i class="fa fa-table fa-3x"></i> Tablas<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="#">Datos Usuarios Vecinales<span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li>
                                        <a href="adminVecinal.php">Usuarios Vecinales</a>
                                    </li>
                                    <li>
                                        <a href="adminAsociacionEmpleo.php">Asociaciones de Empleo</a>
                                    </li>
                                    <li>
                                        <a href="adminAsociacionCapacitacion.php">Asociaciones de Capacitacion</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">Datos Ofertas de Capacitacion<span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li>
                                        <a href="adminCapacitacion.php">Ofertas de Capacitacion</a>
                                    </li>
                                    <li>
                                        <a href="postuladosCapacitacion.php">Postulaciones</a>
                                    </li>
                                    <li>
                                        <a href="seleccionadosCapacitacion.php">Seleccionados</a>
                                    </li>
                                    <li>
                                        <a href="capacitacionRealizada.php">Realizaciones de Capacitacion</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">Datos Ofertas de Empleo<span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li>
                                        <a href="adminEmpleo.php">Ofertas de Empleo</a>
                                    </li>
                                    <li>
                                        <a href="postuladosEmpleo.php">Postulaciones</a>
                                    </li>
                                    <li>
                                        <a href="seleccionadosEmpleo.php">Seleccionados</a>
                                    </li>
                                    <li>
                                        <a href="ocupacionesVecinos.php">Ocupaciones</a>
                                    </li>
                                    <li>
                                        <a href="seguimientoVecinos.php">Seguimiento Laboral</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">Gestion<span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li>
                                        <a href="visitaReporteEmpresa.php">Visitas y Reportes Empresa</a>
                                    </li>
                                    <li>
                                        <a href="visitaReporteOTEC.php">Visitas y Reportes OTEC</a>
                                    </li>
                                    <li>
                                        <a href="metaAvanceMensual.php">Metas y Avances Mensuales</a>
                                    </li>
                                    <li>
                                        <a href="derivacionesEmpleo.php">Derivacion Oferta Laboral</a>
                                    </li>
                                    <li>
                                        <a href="derivacionesCapacitacion.php">Derivacion Capacitacion</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                      </li> 			
                </ul>
            </div>
            
        </nav>  
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                </div>
    <div class="centered-form">
        <h2>Selección de Usuarios Vecinales para Capacitacion</h2><br>
        <form method="POST" action="seleccion_usuarios.php">
            <!-- Lista de usuarios vecinales postulantes (obtenidos desde la base de datos) -->
            <div class="row">
                <div class="col-md-12">
                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                             Seleccionar Postulados para Capacitacion
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                <thead>
                    <tr>
                        <th>ID de Usuarios</th>
                        <th>RUT</th>
                        <th>Nombre Completo</th>
                        <th>ID de Oferta de Capacitación</th>
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
                            echo "<td class='actions'><input type='checkbox' name='seleccionados[$index]' value='$usuario_vecinal_id'><input type='hidden' name='oferta_capacitacion_ids[$index]' value='$oferta_capacitacion_id'></td>";
                            echo "</tr>";

                            $index++; // Incrementamos el índice para la siguiente fila
                        }
                    }
                    ?>
                </tbody>
            </table>
            </div>
                        </div>
                        <!--End Advanced Tables -->
                    </div>
                </div>
            <br>
            <input type="submit" value="Seleccionar Usuarios">
        </form>
         <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
        <!-- JQUERY SCRIPTS -->
        <script src="assets/js/jquery-1.10.2.js"></script>
        <!-- BOOTSTRAP SCRIPTS -->
        <script src="assets/js/bootstrap.min.js"></script>
        <!-- METISMENU SCRIPTS -->
        <script src="assets/js/jquery.metisMenu.js"></script>
        <!-- MORRIS CHART SCRIPTS -->
        <script src="assets/js/morris/raphael-2.1.0.min.js"></script>
        <script src="assets/js/morris/morris.js"></script>
        <!-- DATA TABLE SCRIPTS -->
        <script src="assets/js/dataTables/jquery.dataTables.js"></script>
        <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
            <script>
                $(document).ready(function () {
                    $('#dataTables-example').dataTable();
                });
        </script>
        <!-- CUSTOM SCRIPTS -->
        <script src="assets/js/custom.js"></script>
    </div>
</body>
</html>
