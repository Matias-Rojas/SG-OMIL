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

// Inicializa $row para evitar errores si no se envía el formulario
$row = [];

// Verifica si se ha enviado el ID para editar
if (isset($_GET['id'])) {
    $editId = $_GET['id'];

    // Recupera los datos de la oferta de empleo para prellenar el formulario
    $sqlSelect = "SELECT * FROM sg_omil_ofertasempleo WHERE OfertaEmpleoID = $editId";
    $result = $conn->query($sqlSelect);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verifica si se ha enviado el formulario de edición
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Recupera los datos del formulario
            $rutEmpresa = $_POST["RutEmpresa"];
            $nombreEmpresa = $_POST["NombreEmpresa"];
            $sector = $_POST["Sector"];
            $nombreContacto = $_POST["NombreContacto"];
            $correoContacto = $_POST["CorreoContacto"];
            $telefonoContacto = $_POST["TelefonoContacto"];
            $nombreVacante = $_POST["NombreVacante"];
            $rubroOferta = $_POST["RubroOferta"];
            $cupos = $_POST["Cupos"];
            $lugarTrabajo = $_POST["LugarTrabajo"];
            $rentaLiquida = $_POST["RentaLiquida"];
            $estudiosRequeridos = $_POST["EstudiosRequeridos"];
            $horarios = $_POST["Horarios"];
            $documentacionRequerida = $_POST["DocumentacionRequerida"];
            $gruposObjetivos = $_POST["GruposObjetivos"];
            $tipoContrato = $_POST["TipoContrato"];

            // Actualiza los datos en la base de datos
            $sqlUpdate = "UPDATE sg_omil_ofertasempleo SET 
            RutEmpresa = '" . $_POST["RutEmpresa"] . "',
            NombreEmpresa = '" . $_POST["NombreEmpresa"] . "',
            Sector = '" . $_POST["Sector"] . "',
            NombreContacto = '" . $_POST["NombreContacto"] . "',
            CorreoContacto = '" . $_POST["CorreoContacto"] . "',
            TelefonoContacto = '" . $_POST["TelefonoContacto"] . "',
            NombreVacante = '" . $_POST["NombreVacante"] . "',
            RubroOferta = '" . $_POST["RubroOferta"] . "',
            Cupos = '" . $_POST["Cupos"] . "',
            LugarTrabajo = '" . $_POST["LugarTrabajo"] . "',
            RentaLiquida = '" . $_POST["RentaLiquida"] . "',
            EstudiosRequeridos = '" . $_POST["EstudiosRequeridos"] . "',
            Horarios = '" . $_POST["Horarios"] . "',
            DocumentacionRequerida = '" . $_POST["DocumentacionRequerida"] . "',
            GruposObjetivos = '" . $_POST["GruposObjetivos"] . "',
            TipoContrato = '" . $_POST["TipoContrato"] . "'
            WHERE OfertaEmpleoID = $editId";

            if ($conn->query($sqlUpdate) === TRUE) {
                echo "Oferta de empleo actualizada correctamente.";
                // Puedes redirigir a la página principal o realizar otras acciones después de la actualización
            } else {
                echo "Error al actualizar la oferta de empleo: " . $conn->error;
            }
        }
    } else {
        echo "Oferta de empleo no encontrada o ID no válido.";
    }
} else {
    echo "ID de oferta de empleo no proporcionado.";
}

// Cierra la conexión
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Oferta de Empleo</title>
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
                <h2>Editar Oferta de Empleo</h2>
                <form method="POST" action="" class="formulario" id="formulario_editar_oferta_empleo">
    <div class="formulario__grupo">
        <label for="RutEmpresa" class="formulario__label">RUT de la Empresa:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="RutEmpresa" name="RutEmpresa" class="formulario__input" required value="<?php echo $row['RutEmpresa']; ?>">
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="NombreEmpresa" class="formulario__label">Nombre de la Empresa:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="NombreEmpresa" name="NombreEmpresa" class="formulario__input" required value="<?php echo $row['NombreEmpresa']; ?>">
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="Sector" class="formulario__label">Sector:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="Sector" name="Sector" class="formulario__input" required value="<?php echo $row['Sector']; ?>">
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="NombreContacto" class="formulario__label">Nombre de Contacto:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="NombreContacto" name="NombreContacto" class="formulario__input" required value="<?php echo $row['NombreContacto']; ?>">
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="CorreoContacto" class="formulario__label">Correo de Contacto:</label>
        <div class="formulario__grupo-input">
            <input type="email" id="CorreoContacto" name="CorreoContacto" class="formulario__input" required value="<?php echo $row['CorreoContacto']; ?>">
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="TelefonoContacto" class="formulario__label">Teléfono de Contacto:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="TelefonoContacto" name="TelefonoContacto" class="formulario__input" required value="<?php echo $row['TelefonoContacto']; ?>">
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="NombreVacante" class="formulario__label">Nombre de la Vacante:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="NombreVacante" name="NombreVacante" class="formulario__input" required value="<?php echo $row['NombreVacante']; ?>">
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="RubroOferta" class="formulario__label">Rubro de Oferta:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="RubroOferta" name="RubroOferta" class="formulario__input" required value="<?php echo $row['RubroOferta']; ?>">
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="Cupos" class="formulario__label">Cupos:</label>
        <div class="formulario__grupo-input">
            <input type="number" id="Cupos" name="Cupos" class="formulario__input" required value="<?php echo $row['Cupos']; ?>">
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="LugarTrabajo" class="formulario__label">Lugar de Trabajo:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="LugarTrabajo" name="LugarTrabajo" class="formulario__input" required value="<?php echo $row['LugarTrabajo']; ?>">
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="RentaLiquida" class="formulario__label">Renta Líquida:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="RentaLiquida" name="RentaLiquida" class="formulario__input" required value="<?php echo $row['RentaLiquida']; ?>">
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="EstudiosRequeridos" class="formulario__label">Estudios Requeridos:</label>
        <div class="formulario__grupo-input">
            <select id="EstudiosRequeridos" name="EstudiosRequeridos" class="formulario__input">
                <option value="Sin Educacion Formal" <?php if ($row['EstudiosRequeridos'] === "Sin Educacion Formal") echo "selected"; ?>>Sin Educación Formal</option>
                <option value="Educacion Basica Incompleta" <?php if ($row['EstudiosRequeridos'] === "Educacion Basica Incompleta") echo "selected"; ?>>Educación Básica Incompleta</option>
                <option value="Educacion Basica Completa" <?php if ($row['EstudiosRequeridos'] === "Educacion Basica Completa") echo "selected"; ?>>Educación Básica Completa</option>
                <option value="Educacion Media Incompleta" <?php if ($row['EstudiosRequeridos'] === "Educacion Media Incompleta") echo "selected"; ?>>Educación Media Incompleta</option>
                <option value="Educacion Media Completa" <?php if ($row['EstudiosRequeridos'] === "Educacion Media Completa") echo "selected"; ?>>Educación Media Completa</option>
                <option value="Educacion Superior Incompleta" <?php if ($row['EstudiosRequeridos'] === "Educacion Superior Incompleta") echo "selected"; ?>>Educación Superior Incompleta</option>
                <option value="Educacion Superior Completa" <?php if ($row['EstudiosRequeridos'] === "Educacion Superior Completa") echo "selected"; ?>>Educación Superior Completa</option>
                <option value="Magister" <?php if ($row['EstudiosRequeridos'] === "Magister") echo "selected"; ?>>Magíster</option>
                <option value="Educacion Especial" <?php if ($row['EstudiosRequeridos'] === "Educacion Especial") echo "selected"; ?>>Educación Especial</option>
                <option value="Doctorado" <?php if ($row['EstudiosRequeridos'] === "Doctorado") echo "selected"; ?>>Doctorado</option>
            </select>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="Horarios" class="formulario__label">Horarios:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="Horarios" name="Horarios" class="formulario__input" required value="<?php echo $row['Horarios']; ?>">
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="DocumentacionRequerida" class="formulario__label">Documentación Requerida:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="DocumentacionRequerida" name="DocumentacionRequerida" class="formulario__input" required value="<?php echo $row['DocumentacionRequerida']; ?>">
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="GruposObjetivos" class="formulario__label">Grupos Objetivos:</label>
        <div class="formulario__grupo-input">
            <select id="GruposObjetivos" name="GruposObjetivos" class="formulario__input">
                <option value="Indigenas" <?php if ($row['GruposObjetivos'] === "Indigenas") echo "selected"; ?>>Indígenas</option>
                <option value="Migrantes" <?php if ($row['GruposObjetivos'] === "Migrantes") echo "selected"; ?>>Migrantes</option>
                <option value="Adultos Mayores" <?php if ($row['GruposObjetivos'] === "Adultos Mayores") echo "selected"; ?>>Adultos Mayores</option>
                <option value="Personas Infractoras de Ley" <?php if ($row['GruposObjetivos'] === "Personas Infractoras de Ley") echo "selected"; ?>>Personas Infractoras de Ley</option>
                <option value="Jovenes" <?php if ($row['GruposObjetivos'] === "Jovenes") echo "selected"; ?>>Jóvenes</option>
                <option value="Personas con Discapacidad" <?php if ($row['GruposObjetivos'] === "Personas con Discapacidad") echo "selected"; ?>>Personas con Discapacidad</option>
                <option value="Personas con Pension de Invalidez" <?php if ($row['GruposObjetivos'] === "Personas con Pension de Invalidez") echo "selected"; ?>>Personas con Pensión de Invalidez</option>
                <option value="Mujer" <?php if ($row['GruposObjetivos'] === "Mujer") echo "selected"; ?>>Mujer</option>
            </select>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="TipoContrato" class="formulario__label">Tipo de Contrato:</label>
        <div class="formulario__grupo-input">
            <select id="TipoContrato" name="TipoContrato" class="formulario__input">
                <option value="Contrato por Obra o Faena" <?php if ($row['TipoContrato'] === "Contrato por Obra o Faena") echo "selected"; ?>>Contrato por Obra o Faena</option>
                <option value="Contrato a Plazo Fijo" <?php if ($row['TipoContrato'] === "Contrato a Plazo Fijo") echo "selected"; ?>>Contrato a Plazo Fijo</option>
                <option value="Contrato Indefinido" <?php if ($row['TipoContrato'] === "Contrato Indefinido") echo "selected"; ?>>Contrato Indefinido</option>
                <option value="Administracion Publica" <?php if ($row['TipoContrato'] === "Administracion Publica") echo "selected"; ?>>Administración Pública</option>
                <option value="Honorarios" <?php if ($row['TipoContrato'] === "Honorarios") echo "selected"; ?>>Honorarios</option>
                <option value="Indiferente" <?php if ($row['TipoContrato'] === "Indiferente") echo "selected"; ?>>Indiferente</option>
                <option value="Contrato de Formacion" <?php if ($row['TipoContrato'] === "Contrato de Formacion") echo "selected"; ?>>Contrato de Formación</option>
                <option value="Contrato de Aprendizaje" <?php if ($row['TipoContrato'] === "Contrato de Aprendizaje") echo "selected"; ?>>Contrato de Aprendizaje</option>
                <option value="Sin Contrato" <?php if ($row['TipoContrato'] === "Sin Contrato") echo "selected"; ?>>Sin Contrato</option>
            </select>
        </div>
    </div>

    <div class="formulario__grupo formulario__grupo-btn-enviar">
        <input type="submit" value="Guardar Cambios" class="formulario__btn">
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