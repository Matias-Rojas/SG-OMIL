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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rutEmpresaOTEC = $_POST["RutEmpresaOTEC"];
    $nombre = $_POST["nombre"];
    $sector = $_POST["sector"];
    $nombreContacto = $_POST["nombre_contacto"];
    $correoContacto = $_POST["correo_contacto"];
    $telefonoContacto = $_POST["telefono_contacto"];
    $tematica = $_POST["tematica"];
    $cupos = $_POST["cupos"];
    $horarios = $_POST["horarios"];
    $lugar = $_POST["lugar"];
    $costo = $_POST["costo"];
    $estudiosRequeridos = $_POST["estudios_requeridos"];
    $documentacionRequerida = $_POST["documentacion_requerida"];
    $gruposObjetivos = $_POST["grupos_objetivos"];

    // Verificar si ya existe una oferta de capacitación para esta OTEC con la misma temática
    $sql_verificar = "SELECT * FROM sg_omil_ofertascapacitacion WHERE RutEmpresaOTEC = ? AND NombreCurso = ?";
    $stmt_verificar = $conn->prepare($sql_verificar);
    $stmt_verificar->bind_param("ss", $rutEmpresaOTEC, $tematica);
    $stmt_verificar->execute();
    $result_verificar = $stmt_verificar->get_result();

    if ($result_verificar->num_rows > 0) {
        // Ya existe una oferta con la misma temática para esta OTEC
        echo "Ya existe una oferta de capacitación con la misma temática para esta OTEC.";
    } else {
        // Query para insertar la nueva oferta de capacitación con la fecha de creación
        $sql_insert = "INSERT INTO sg_omil_ofertascapacitacion (FechaCreacion, RutEmpresaOTEC, NombreOTEC, Sector, NombreContacto, CorreoContacto, TelefonoContacto, NombreCurso, Cupos, Horarios, Lugar, Costo, EstudiosRequeridos, DocumentacionRequerida, GruposObjetivos) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("sssssssissdsss", $rutEmpresaOTEC, $nombre, $sector, $nombreContacto, $correoContacto, $telefonoContacto, $tematica, $cupos, $horarios, $lugar, $costo, $estudiosRequeridos, $documentacionRequerida, $gruposObjetivos);


        if ($stmt_insert->execute()) {
            // Éxito al registrar la oferta de capacitación
            echo "Oferta de capacitación registrada con éxito.";
        } else {
            // Error al registrar la oferta de capacitación
            echo "Error al registrar la oferta de capacitación.";
        }

        $stmt_insert->close();
    }

    $stmt_verificar->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Oferta de Capacitación</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
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
    <h2>Registro de Oferta de Capacitación</h2>
    <form method="POST" action="registro_oferta.php" class="formulario" id="formulario_registro_oferta">
        <div class="formulario__grupo">
            <label for="RutEmpresaOTEC" class="formulario__label">RUT de la OTEC:</label>
            <div class="formulario__grupo-input">
                <input type="text" id="RutEmpresaOTEC" name="RutEmpresaOTEC" class="formulario__input" required>
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="nombre" class="formulario__label">Nombre de OTEC:</label>
            <div class="formulario__grupo-input">
                <input type="text" id="nombre" name="nombre" class="formulario__input" required>
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="sector" class="formulario__label">Sector:</label>
            <div class="formulario__grupo-input">
                <input type="text" id="sector" name="sector" class="formulario__input">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="nombre_contacto" class="formulario__label">Nombre de Contacto:</label>
            <div class="formulario__grupo-input">
                <input type="text" id="nombre_contacto" name="nombre_contacto" class="formulario__input">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="correo_contacto" class="formulario__label">Correo de Contacto:</label>
            <div class="formulario__grupo-input">
                <input type="email" id="correo_contacto" name="correo_contacto" class="formulario__input">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="telefono_contacto" class="formulario__label">Teléfono de Contacto:</label>
            <div class="formulario__grupo-input">
                <input type="tel" id="telefono_contacto" name="telefono_contacto" class="formulario__input">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="tematica" class="formulario__label">Nombre del Curso:</label>
            <div class="formulario__grupo-input">
                <input type="text" id="tematica" name="tematica" class="formulario__input">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="cupos" class="formulario__label">Cupos:</label>
            <div class="formulario__grupo-input">
                <input type="number" id="cupos" name="cupos" class="formulario__input">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="horarios" class="formulario__label">Horarios:</label>
            <div class="formulario__grupo-input">
                <input type="text" id="horarios" name="horarios" class="formulario__input">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="lugar" class="formulario__label">Lugar:</label>
            <div class="formulario__grupo-input">
                <input type="text" id="lugar" name="lugar" class="formulario__input">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="costo" class="formulario__label">Costo:</label>
            <div class="formulario__grupo-input">
                <input type="number" id="costo" name="costo" class="formulario__input">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="estudios_requeridos" class="formulario__label">Estudios Requeridos:</label>
            <div class="formulario__grupo-input">
                <select type="text" id="estudios_requeridos" name="estudios_requeridos" class="formulario__input">
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
            <label for="documentacion_requerida" class="formulario__label">Documentación Requerida:</label>
            <div class="formulario__grupo-input">
                <textarea id="documentacion_requerida" name="documentacion_requerida" class="formulario__input"></textarea>
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="grupos_objetivos" class="formulario__label">Grupos Objetivos:</label>
            <div class="formulario__grupo-input">
                <select type="text" id="grupos_objetivos" name="grupos_objetivos" class="formulario__input">
                    <option value="Indigenas">Indigenas</option>
                    <option value="Migrantes">Migrantes</option>
                    <option value="Adultos Mayores">Adultos Mayores</option>
                    <option value="Personas Infractoras de Ley">Personas Infractoras de Ley</option>
                    <option value="Jovenes">Jovenes</option>
                    <option value="Personas con Discapacidad">Personas con Discapacidad</option>
                    <option value="Personas con Pension de Invalidez">Personas con Pension de Invalidez</option>
                    <option value="Mujer">Mujer</option>
                </select>
            </div>
        </div>
        <br>
        <div class="formulario__grupo formulario__grupo-btn-enviar">
            <input type="submit" value="Registrar Oferta" class="formulario__btn">
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
