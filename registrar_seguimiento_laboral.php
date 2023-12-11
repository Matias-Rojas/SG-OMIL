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
    <link rel="stylesheet" type="text/css" href="assets/css/estilos.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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
    <h2>Registro de Seguimiento Laboral</h2>
    <form method="POST" action="registrar_seguimiento_laboral.php" class="formulario" id="formulario_seguimiento_laboral">
        <div class="formulario__grupo">
            <label for="rut_usuario" class="formulario__label">Rut de Usuario Vecinal Matriculado:</label>
            <div class="formulario__grupo-input">
                <select id="rut_usuario" name="rut_usuario" class="formulario__input" required>
                    <?php
                    // Genera las opciones del menú desplegable con los RUTs de los usuarios vecinales obtenidos desde la base de datos
                    foreach ($ruts_usvecinal as $rut) {
                        echo "<option value='$rut' " . ($rut == $rut_usuario ? "selected" : "") . ">$rut</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="nombre_capacitacion" class="formulario__label">Nombre del Empleo:</label>
            <div class="formulario__grupo-input">
                <select name="nombre_capacitacion" id="nombre_capacitacion" class="formulario__input" required>
                    <?php
                    // Llena el select con las capacitaciones asociadas al usuario seleccionado
                    foreach ($capacitaciones as $capacitacion) {
                        echo "<option value='$capacitacion'>$capacitacion</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="seguimiento_postulacion" class="formulario__label">Seguimiento a la postulacion:</label>
            <div class="formulario__grupo-input">
                <textarea name="seguimiento_postulacion" id="seguimiento_postulacion" class="formulario__input" required></textarea>
            </div>
        </div>

        <div class="formulario__grupo formulario__grupo-btn-enviar">
            <input type="submit" value="Registrar Seguimiento Laboral" class="formulario__btn">
        </div>
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
      <!-- CUSTOM SCRIPTS -->
    <script src="assets/js/custom.js"></script>
</body>
</html>
