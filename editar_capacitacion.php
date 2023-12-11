<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
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

// Conecta a la base de datos
$conn = new mysqli($servername, $db_username, $db_password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verifica si se ha enviado el ID para editar
if (isset($_GET['id'])) {
    $editId = $_GET['id'];

    // Recupera los datos de la capacitación para prellenar el formulario
    $sqlSelect = "SELECT * FROM sg_omil_ofertascapacitacion WHERE OfertaCapacitacionID = $editId";
    $result = $conn->query($sqlSelect);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verifica si se ha enviado el formulario de edición
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Recupera los datos del formulario
            $rutEmpresaOTEC = $_POST["RutEmpresaOTEC"];
            $nombreOTEC = $_POST["nombre"];
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

            // Validaciones adicionales según tus necesidades
            // ...

            // Actualiza los datos en la base de datos
            $sqlUpdate = "UPDATE sg_omil_ofertascapacitacion SET RutEmpresaOTEC = '$rutEmpresaOTEC', NombreOTEC = '$nombreOTEC', Sector = '$sector', NombreContacto = '$nombreContacto', CorreoContacto = '$correoContacto', TelefonoContacto = '$telefonoContacto', NombreCurso = '$tematica', Cupos = '$cupos', Horarios = '$horarios', Lugar = '$lugar', Costo = '$costo', EstudiosRequeridos = '$estudiosRequeridos', DocumentacionRequerida = '$documentacionRequerida', GruposObjetivos = '$gruposObjetivos' WHERE OfertaCapacitacionID = $editId";

            if ($conn->query($sqlUpdate) === TRUE) {
                echo "Capacitación actualizada correctamente.";
                // Puedes redirigir a la página principal o realizar otras acciones después de la actualización
            } else {
                echo "Error al actualizar la capacitación: " . $conn->error;
            }
        }
    } else {
        echo "Capacitación no encontrada.";
    }
} else {
    echo "ID de capacitación no proporcionado.";
}

// Cierra la conexión
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Oferta de Capacitación</title>
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
                <h2>Editar Capacitacion</h2>
    <!-- Formulario para editar la capacitación -->
    <form method="POST" action="" class="formulario" id="formulario_registro_oferta">
        <div class="formulario__grupo">
            <label for="RutEmpresaOTEC" class="formulario__label">RUT de la OTEC:</label>
            <div class="formulario__grupo-input">
                <input type="text" id="RutEmpresaOTEC" name="RutEmpresaOTEC" class="formulario__input" value="<?php echo $row['RutEmpresaOTEC']; ?>" required>
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="nombre" class="formulario__label">Nombre de OTEC:</label>
            <div class="formulario__grupo-input">
                <input type="text" id="nombre" name="nombre" class="formulario__input" value="<?php echo $row['NombreOTEC']; ?>" required>
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="sector" class="formulario__label">Sector:</label>
            <div class="formulario__grupo-input">
                <input type="text" id="sector" name="sector" class="formulario__input" value="<?php echo $row['Sector']; ?>">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="nombre_contacto" class="formulario__label">Nombre de Contacto:</label>
            <div class="formulario__grupo-input">
                <input type="text" id="nombre_contacto" name="nombre_contacto" class="formulario__input" value="<?php echo $row['NombreContacto']; ?>">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="correo_contacto" class="formulario__label">Correo de Contacto:</label>
            <div class="formulario__grupo-input">
                <input type="email" id="correo_contacto" name="correo_contacto" class="formulario__input" value="<?php echo $row['CorreoContacto']; ?>">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="telefono_contacto" class="formulario__label">Teléfono de Contacto:</label>
            <div class="formulario__grupo-input">
                <input type="tel" id="telefono_contacto" name="telefono_contacto" class="formulario__input" value="<?php echo $row['TelefonoContacto']; ?>">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="tematica" class="formulario__label">Nombre del Curso:</label>
            <div class="formulario__grupo-input">
                <input type="text" id="tematica" name="tematica" class="formulario__input" value="<?php echo $row['NombreCurso']; ?>">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="cupos" class="formulario__label">Cupos:</label>
            <div class="formulario__grupo-input">
                <input type="number" id="cupos" name="cupos" class="formulario__input" value="<?php echo $row['Cupos']; ?>">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="horarios" class="formulario__label">Horarios:</label>
            <div class="formulario__grupo-input">
                <input type="text" id="horarios" name="horarios" class="formulario__input" value="<?php echo $row['Horarios']; ?>">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="lugar" class="formulario__label">Lugar:</label>
            <div class="formulario__grupo-input">
                <input type="text" id="lugar" name="lugar" class="formulario__input" value="<?php echo $row['Lugar']; ?>">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="costo" class="formulario__label">Costo:</label>
            <div class="formulario__grupo-input">
                <input type="number" id="costo" name="costo" class="formulario__input" value="<?php echo $row['Costo']; ?>">
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="estudios_requeridos" class="formulario__label">Estudios Requeridos:</label>
            <div class="formulario__grupo-input">
                <select id="estudios_requeridos" name="estudios_requeridos" class="formulario__input">
                    <option value="Sin Educacion Formal" <?php if ($row['EstudiosRequeridos'] == 'Sin Educacion Formal') echo 'selected'; ?>>Sin Educacion Formal</option>
                    <option value="Educacion Basica Incompleta" <?php if ($row['EstudiosRequeridos'] == 'Educacion Basica Incompleta') echo 'selected'; ?>>Educacion Basica Incompleta</option>
                    <option value="Educacion Basica Completa" <?php if ($row['EstudiosRequeridos'] == 'Educacion Basica Completa') echo 'selected'; ?>>Educacion Basica Completa</option>
                    <option value="Educacion Media Incompleta" <?php if ($row['EstudiosRequeridos'] == 'Educacion Media Incompleta') echo 'selected'; ?>>Educacion Media Incompleta</option>
                    <option value="Educacion Media Completa" <?php if ($row['EstudiosRequeridos'] == 'Educacion Media Completa') echo 'selected'; ?>>Educacion Media Completa</option>
                    <option value="Educacion Superior Incompleta" <?php if ($row['EstudiosRequeridos'] == 'Educacion Superior Incompleta') echo 'selected'; ?>>Educacion Superior Incompleta</option>
                    <option value="Educacion Superior Completa" <?php if ($row['EstudiosRequeridos'] == 'Educacion Superior Completa') echo 'selected'; ?>>Educacion Superior Completa</option>
                    <option value="Magister" <?php if ($row['EstudiosRequeridos'] == 'Magister') echo 'selected'; ?>>Magister</option>
                    <option value="Educacion Especial" <?php if ($row['EstudiosRequeridos'] == 'Educacion Especial') echo 'selected'; ?>>Educacion Especial</option>
                    <option value="Doctorado" <?php if ($row['EstudiosRequeridos'] == 'Doctorado') echo 'selected'; ?>>Doctorado</option>
                </select>
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="documentacion_requerida" class="formulario__label">Documentación Requerida:</label>
            <div class="formulario__grupo-input">
                <textarea id="documentacion_requerida" name="documentacion_requerida" class="formulario__input"><?php echo $row['DocumentacionRequerida']; ?></textarea>
            </div>
        </div>

        <div class="formulario__grupo">
            <label for="grupos_objetivos" class="formulario__label">Grupos Objetivos:</label>
            <div class="formulario__grupo-input">
                <select id="grupos_objetivos" name="grupos_objetivos" class="formulario__input">
                    <option value="Indigenas" <?php if ($row['GruposObjetivos'] == 'Indigenas') echo 'selected'; ?>>Indigenas</option>
                    <option value="Migrantes" <?php if ($row['GruposObjetivos'] == 'Migrantes') echo 'selected'; ?>>Migrantes</option>
                    <option value="Adultos Mayores" <?php if ($row['GruposObjetivos'] == 'Adultos Mayores') echo 'selected'; ?>>Adultos Mayores</option>
                    <option value="Personas Infractoras de Ley" <?php if ($row['GruposObjetivos'] == 'Personas Infractoras de Ley') echo 'selected'; ?>>Personas Infractoras de Ley</option>
                    <option value="Jovenes" <?php if ($row['GruposObjetivos'] == 'Jovenes') echo 'selected'; ?>>Jovenes</option>
                    <option value="Personas con Discapacidad" <?php if ($row['GruposObjetivos'] == 'Personas con Discapacidad') echo 'selected'; ?>>Personas con Discapacidad</option>
                    <option value="Personas con Pension de Invalidez" <?php if ($row['GruposObjetivos'] == 'Personas con Pension de Invalidez') echo 'selected'; ?>>Personas con Pension de Invalidez</option>
                    <option value="Mujer" <?php if ($row['GruposObjetivos'] == 'Mujer') echo 'selected'; ?>>Mujer</option>
                </select>
            </div>
        </div>

        <br>

        <div class="formulario__grupo formulario__grupo-btn-enviar">
            <input type="submit" value="Actualizar Oferta" class="formulario__btn">
        </div>
    </form>
    <!-- Fin del formulario para editar la capacitación -->
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