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

// Consulta para obtener las ofertas de capacitación disponibles
$sql = "SELECT OfertaCapacitacionID, CONCAT(' Id de Oferta: ',OfertaCapacitacionID, ' | ', ' Nombre de Capacitación: ', NombreCurso, ' | ', ' Rut de Empresa Otec: ', RutEmpresaOTEC, ' | ', ' Nombre de OTEC: ', NombreOTEC) AS OfertaNombreCap FROM sg_omil_ofertascapacitacion";
$result = $conn->query($sql);

$OfertaCapacitacionID = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $OfertaCapacitacionID[] = $row;
    }
}

// Consulta para obtener los RUTs de los usuarios vecinales desde la base de datos
$sql = "SELECT Rut FROM sg_omil_usuariosvecinales";
$result = $conn->query($sql);
        
$ruts_usvecinal = array();
        
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ruts_usvecinal[] = $row["Rut"];
    }
}

// Comprueba si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtiene los datos del formulario
    $rut = $_POST["rut"];
    $porcentaje_rsh = $_POST["porcentaje_rsh"];
    $nivel_estudios = $_POST["nivel_estudios"];
    $jefa_hogar = isset($_POST["jefa_hogar"]) ? 1 : 0;
    $discapacidad = isset($_POST["discapacidad"]) ? 1 : 0;
    $rango_etario = $_POST["rango_etario"];
    $puntaje_entrevista = $_POST["puntaje_entrevista"];
    $oferta_capacitacion = $_POST["oferta_capacitacion"];

    // Realiza la inserción en la tabla de datos de postulación
    $sql_insert = "INSERT INTO sg_omil_datos_postulacion_capacitacion (Rut, PorcentajeRSH, NivelEstudios, EsJefaHogar, Discapacidad, RangoEtario, PuntajeEntrevista, OfertaCapacitacionID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);

    $stmt_insert->bind_param("sdsiiisi", $rut, $porcentaje_rsh, $nivel_estudios, $jefa_hogar, $discapacidad, $rango_etario, $puntaje_entrevista, $oferta_capacitacion);

    if ($stmt_insert->execute()) {
        // Éxito al postular
        echo "Postulación exitosa.";
    } else {
        // Error al postular
        echo "Error al postular.";
    }

    $stmt_insert->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postular Usuario Vecinal a Capacitación</title>
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
    <h2>Postular Usuario Vecinal a Capacitación</h2>
    <form method="POST" action="postulacion_capacitacion.php" class="formulario" id="formulario_postulacion_capacitacion">
        <div class="formulario__grupo">
            <label for="rut" class="formulario__label">RUT de Usuario Vecinal:</label>
            <div class="formulario__grupo-input">
                <select id="rut" name="rut" class="formulario__input" required>
                    <?php
                    // Genera las opciones del menú desplegable con los RUTs de los usuarios vecinales obtenidos desde la base de datos
                    foreach ($ruts_usvecinal as $rut) {
                        echo "<option value='$rut'>$rut</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="porcentaje_rsh" class="formulario__label">Porcentaje RSH:</label>
            <div class="formulario__grupo-input">
                <input type="text" id="porcentaje_rsh" name="porcentaje_rsh" class="formulario__input" required>
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="nivel_estudios" class="formulario__label">Nivel de Estudios:</label>
            <div class="formulario__grupo-input">
                <select type="text" id="nivel_estudios" name="nivel_estudios" class="formulario__input">
                    <option value="Sin Educacion Formal">Sin Educacion Formal</option>
                    <option value="Educacion Basica Incompleta">Educacion Basica Incompleta</option>
                    <option value="Educacion Basica Completa">Educacion Basica Completa</option>
                    <option value="Educacion Media Incompleta">Educacion Media Incompleta</option>
                    <option value="Educacion Media Completa">Educacion Media Completa</option>
                    <option value="Educacion Superior Incompleta">Educacion Superior Incompleta</option>
                    <option value="Educacion Superior Completa">Educacion Superior Completa</option>
                    <option value="Magister">Magister</option>
                    <option value="Educacion Especial">Educacion Especial</option>
                    <option value="Doctorado">Doctorado</option>
                </select>
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="jefa_hogar" class="formulario__label">¿Es Jefa de Hogar?</label>
            <div class="formulario__grupo-input">
                <input type="checkbox" id="jefa_hogar" name="jefa_hogar" class="formulario__input">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="discapacidad" class="formulario__label">¿Tiene Discapacidad?</label>
            <div class="formulario__grupo-input">
                <input type="checkbox" id="discapacidad" name="discapacidad" class="formulario__input">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="rango_etario" class="formulario__label">Rango Etario:</label>
            <div class="formulario__grupo-input">
                <select type="text" id="rango_etario" name="rango_etario" class="formulario__input" required>
                    <option value="Infancia">Infancia</option>
                    <option value="Adolecencia">Adolecencia</option>
                    <option value="Adultez">Adultez</option>
                    <option value="Vejez">Vejez</option>
                </select>
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="puntaje_entrevista" class="formulario__label">Puntaje Total de la Entrevista:</label>
            <div class="formulario__grupo-input">
                <input type="text" id="puntaje_entrevista" name="puntaje_entrevista" class="formulario__input" required>
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="oferta_capacitacion" class="formulario__label">Oferta de Capacitación:</label>
            <div class="formulario__grupo-input">
                <select id="oferta_capacitacion" name="oferta_capacitacion" class="formulario__input" required>
                    <?php
                    // Genera las opciones del menú desplegable con los IDs de las ofertas de capacitación
                    foreach ($OfertaCapacitacionID as $oferta) {
                        echo "<option value='" . $oferta["OfertaCapacitacionID"] . "'>" . $oferta["OfertaNombreCap"] . "</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="formulario__grupo formulario__grupo-btn-enviar">
            <input type="submit" value="Postular" class="formulario__btn">
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
