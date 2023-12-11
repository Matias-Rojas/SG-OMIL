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

// Incluir el archivo de la clase PHPMailer
require 'PHPMailer-master\src\PHPMailer.php';
require 'PHPMailer-master\src\SMTP.php';
require 'PHPMailer-master\src\Exception.php';

// Función para enviar correo
function enviarCorreo($destinatario, $nombre, $apellidos, $nombreVacante, $rubroOferta, $nombreEmpresa, $correoUsuario, $telefonoUsuario) {
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
        $mail->setFrom('hurdoxloly@gmail.com', 'SG-OMIL MELIPILLA');
        $mail->addAddress($destinatario);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Presentacion de Usuario';

        $body = "<p>Estimado/a, junto con saludar nos comunicamos con su empresa {$nombreEmpresa} con el fin de presentar a un usuario que fue seleccionado para una de sus ofertas laborales.</p>";
        $body .= "<p>A continuacion los datos correspondientes, el usuario seleccionado es {$nombre} {$apellidos} el cual ha sido seleccionado/a para su oferta de empleo con las siguientes caracteristicas:</p>";
        $body .= "<ul>";
        $body .= "<li>Nombre de la vacante: {$nombreVacante}</li>";
        $body .= "<li>Rubro de la oferta: {$rubroOferta}</li>";
        $body .= "<p>Para mas informacion y contacto con el usuario comuniquese mediante su correo electronico: {$correoUsuario} o dispositivo movil: {$telefonoUsuario}</p>";
        // Agrega más detalles según sea necesario
        $body .= "</ul>";
        $body .= "<p>Atentamente,<br>SG-OMIL MELIPILLA</p>";
        
        $mail->Body = $body;

        $mail->send();
        echo "Correo enviado con éxito a: {$destinatario}";
        header("Location: presentar_usuarios.php");
        exit;
    } catch (Exception $e) {
        echo "Error al enviar el correo: {$mail->ErrorInfo}";
    }
}

// Verificar si se ha enviado la solicitud POST para enviar el correo
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["correo"])) {
        $correoDestino = $_POST["correo"];
        $nombre = $_POST["nombre"];
        $apellidos = $_POST["apellidos"];
        $nombreVacante = $_POST["nombreVacante"];
        $rubroOferta = $_POST["rubroOferta"];
        $nombreEmpresa = $_POST["nombreEmpresa"];
        $correoUsuario = $_POST["correoUsuario"];
        $telefonoUsuario = $_POST["telefonoUsuario"];

        $resultadoEnvio = enviarCorreo($correoDestino, $nombre, $apellidos, $nombreVacante, $rubroOferta, $nombreEmpresa, $correoUsuario, $telefonoUsuario);

        // Mostrar mensaje en la misma página
        echo $resultadoEnvio;
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
    <!-- Agregar la tabla de seleccionados -->
    <h2>Presentar Usuarios</h2>
    <div class="row">
    <div class="col-md-12">
        <!-- Advanced Tables -->
        <div class="panel panel-default">
            <div class="panel-heading">
                Presentar Usuarios Vecinales
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
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
                            $sql_seleccionados = "SELECT uv.CorreoElectronico, uv.Telefono, uv.UsuarioID, uv.Rut, uv.Nombres, uv.Apellidos, oc.OfertaEmpleoID, oe.NombreVacante, oe.NombreEmpresa, oe.RutEmpresa, oe.RubroOferta, oe.CorreoContacto FROM sg_omil_usuariosvecinales AS uv JOIN sg_omil_seleccionados_empleo AS oc ON uv.UsuarioID = oc.UsuarioID JOIN sg_omil_ofertasempleo AS oe ON oc.OfertaEmpleoID = oe.OfertaEmpleoID";
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
                                    echo "<td>
                                        <form method='post'>
                                            <input type='hidden' name='correo' value='{$row_seleccionados['CorreoContacto']}'>
                                            <input type='hidden' name='nombre' value='{$row_seleccionados['Nombres']}'>
                                            <input type='hidden' name='apellidos' value='{$row_seleccionados['Apellidos']}'>
                                            <input type='hidden' name='nombreVacante' value='{$row_seleccionados['NombreVacante']}'>
                                            <input type='hidden' name='rubroOferta' value='{$row_seleccionados['RubroOferta']}'>
                                            <input type='hidden' name='nombreEmpresa' value='{$row_seleccionados['NombreEmpresa']}'>
                                            <input type='hidden' name='correoUsuario' value='{$row_seleccionados['CorreoElectronico']}'>
                                            <input type='hidden' name='telefonoUsuario' value='{$row_seleccionados['Telefono']}'>
                                            <input type='submit' value='Enviar Correo'>
                                        </form>
                                    </td>";
                                    echo "</tr>";
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
</div>

        <!-- Botón para descargar la tabla -->
        <input type="button" id="descargarTabla" value="Descargar Tabla" style="margin-left: 15px;"></input><br><br>

        <!-- Script para descargar la tabla como XLSX -->
        <script>
            document.getElementById('descargarTabla').addEventListener('click', function () {
                var table = document.getElementById('dataTables-example'); // Selecciona la tabla por su id
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