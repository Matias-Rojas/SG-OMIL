<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.php");
    exit;
}

$rol = $_SESSION["rol"];
$username = $_SESSION["username"];
$correo = $_SESSION["email"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Procesar la edición del usuario y actualizar la base de datos
    $servername = "localhost";
    $db_username = "HURDOX";
    $db_password = "gokudeus2023";
    $database = "sg_omil";

    $conn = new mysqli($servername, $db_username, $db_password, $database);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $user_id = $_POST["user_id"];
    $nombres = $_POST["nombres"];
    $apellidos = $_POST["apellidos"];
    $rut = $_POST["rut"];
    $genero = $_POST["genero"];
    $fecha_nacimiento = $_POST["fecha_nacimiento"];
    $estado_civil = $_POST["estado_civil"];
    $direccion = $_POST["direccion"];
    $region = $_POST["region"];
    $comuna = $_POST["comuna"];
    $sector = $_POST["sector"];
    $nacionalidad = $_POST["nacionalidad"];
    $correo_electronico = $_POST["correo_electronico"];
    $telefono = $_POST["telefono"];
    $telefono_alternativo = $_POST["telefono_alternativo"];
    $nivel_educacional = $_POST["nivel_educacional"];
    $area = $_POST["area"];
    $titulo = $_POST["titulo"];
    $nombre_curso = $_POST["nombre_curso"];
    $institucion = $_POST["institucion"];
    $fecha = $_POST["fecha"];
    $motivo_consulta = $_POST["motivo_consulta"];
    $estado_motivo_consulta = $_POST["estado_motivo_consulta"];

    // Actualizar la información del usuario
    $sql = "UPDATE sg_omil_usuariosvecinales
            SET Nombres = ?, Apellidos = ?, Rut = ?, Genero = ?, FechaNacimiento = ?, EstadoCivil = ?, Direccion = ?, Region = ?, Comuna = ?, Sector = ?, Nacionalidad = ?, CorreoElectronico = ?, Telefono = ?, TelefonoAlternativo = ?, NivelEducacional = ?, Area = ?, Titulo = ?, NombreCurso = ?, Institucion = ?, Fecha = ?, MotivoConsulta = ?, EstadoMotivoConsulta = ?
            WHERE UsuarioID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssssssssssss", $nombres, $apellidos, $rut, $genero, $fecha_nacimiento, $estado_civil, $direccion, $region, $comuna, $sector, $nacionalidad, $correo_electronico, $telefono, $telefono_alternativo, $nivel_educacional, $area, $titulo, $nombre_curso, $institucion, $fecha, $motivo_consulta, $estado_motivo_consulta, $user_id);


    if ($stmt->execute()) {
        // Éxito al actualizar el usuario
        header("location: adminVecinal.php");
        exit;
    } else {
        // Error al actualizar el usuario
        echo "Error al actualizar el usuario: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
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

// Obtener el ID del usuario a editar desde la URL
if (isset($_GET["id"])) {
    $user_id = $_GET["id"];

    // Consulta para obtener los detalles del usuario
    $sql = "SELECT * FROM sg_omil_usuariosvecinales WHERE UsuarioID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
} else {
    // Redireccionar si no se proporciona un ID de usuario válido
    header("location: adminVecinal.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario Vecinal</title>
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
    <link rel="stylesheet" href="assets/css/estilos.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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
    <h2>Editar Usuario Vecinal</h2>

    <form method="POST" class="formulario" id="formulario_registro_oferta">

    <input type="hidden" name="user_id" value="<?php echo $user["UsuarioID"]; ?>">

    <div class="formulario__grupo">
        <label for="nombres" class="formulario__label">Nombres:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="nombres" name="nombres" class="formulario__input" value="<?php echo $user["Nombres"]; ?>" required>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="apellidos" class="formulario__label">Apellidos:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="apellidos" name="apellidos" class="formulario__input" value="<?php echo $user["Apellidos"]; ?>" required>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="rut" class="formulario__label">RUT:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="rut" name="rut" class="formulario__input" value="<?php echo $user["Rut"]; ?>" required>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="genero" class="formulario__label">Género:</label>
        <div class="formulario__grupo-input">
            <select name="genero" id="genero" class="formulario__input" required>
                <option value="Masculino" <?php if ($user["Genero"] == "Masculino") echo "selected"; ?>>Masculino</option>
                <option value="Femenino" <?php if ($user["Genero"] == "Femenino") echo "selected"; ?>>Femenino</option>
                <option value="Otro" <?php if ($user["Genero"] == "Otro") echo "selected"; ?>>Otro</option>
            </select>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="fecha_nacimiento" class="formulario__label">Fecha de Nacimiento:</label>
        <div class="formulario__grupo-input">
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="formulario__input" value="<?php echo $user["FechaNacimiento"]; ?>" required>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="estado_civil" class="formulario__label">Estado Civil:</label>
        <div class="formulario__grupo-input">
            <select name="estado_civil" id="estado_civil" class="formulario__input" required>
                <option value="Soltero/a" <?php if ($user["EstadoCivil"] == "Soltero/a") echo "selected"; ?>>Soltero/a</option>
                <option value="Casado/a" <?php if ($user["EstadoCivil"] == "Casado/a") echo "selected"; ?>>Casado/a</option>
                <option value="Divorciado/a" <?php if ($user["EstadoCivil"] == "Divorciado/a") echo "selected"; ?>>Divorciado/a</option>
                <option value="Viudo/a" <?php if ($user["EstadoCivil"] == "Viudo/a") echo "selected"; ?>>Viudo/a</option>
            </select>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="direccion" class="formulario__label">Dirección:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="direccion" name="direccion" class="formulario__input" value="<?php echo $user["Direccion"]; ?>" required>
        </div>
    </div>

    <div class="formulario__grupo" id="grupo_region">
        <label for="region" class="formulario__label">Región:</label>
        <div class="formulario__grupo-input">
            <select name="region" id="region" class="formulario__input" required>
                <!-- Opciones de regiones se cargarán dinámicamente aquí -->
            </select>
        </div>
    </div>

    <div class="formulario__grupo" id="grupo_comuna" style="display:none;">
        <label for="comuna" class="formulario__label">Comuna:</label>
        <div class="formulario__grupo-input">
            <select name="comuna" id="comuna" class="formulario__input" required>
                <!-- Opciones de comunas se cargarán dinámicamente aquí -->
            </select>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="sector" class="formulario__label">Sector:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="sector" name="sector" class="formulario__input" value="<?php echo $user["Sector"]; ?>" required>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="nacionalidad" class="formulario__label">Nacionalidad:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="nacionalidad" name="nacionalidad" class="formulario__input" value="<?php echo $user["Nacionalidad"]; ?>" required>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="correo_electronico" class="formulario__label">Correo Electrónico:</label>
        <div class="formulario__grupo-input">
            <input type="email" id="correo_electronico" name="correo_electronico" class="formulario__input" value="<?php echo $user["CorreoElectronico"]; ?>" required>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="telefono" class="formulario__label">Teléfono:</label>
        <div class="formulario__grupo-input">
            <input type="tel" id="telefono" name="telefono" class="formulario__input" value="<?php echo $user["Telefono"]; ?>" required>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="telefono_alternativo" class="formulario__label">Teléfono Alternativo:</label>
        <div class="formulario__grupo-input">
            <input type="tel" id="telefono_alternativo" name="telefono_alternativo" class="formulario__input" value="<?php echo $user["TelefonoAlternativo"]; ?>">
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="nivel_educacional" class="formulario__label">Nivel Educacional:</label>
        <div class="formulario__grupo-input">
            <select type="text" id="nivel_educacional" name="nivel_educacional" class="formulario__input" required>
                <option value="Sin Educacion Formal" <?php if ($user["NivelEducacional"] == "Sin Educacion Formal") echo "selected"; ?>>Sin Educacion Formal</option>
                <option value="Educacion Basica Incompleta" <?php if ($user["NivelEducacional"] == "Educacion Basica Incompleta") echo "selected"; ?>>Educacion Basica Incompleta</option>
                <option value="Educacion Basica Completa" <?php if ($user["NivelEducacional"] == "Educacion Basica Completa") echo "selected"; ?>>Educacion Basica Completa</option>
                <option value="Educacion Media Incompleta" <?php if ($user["NivelEducacional"] == "Educacion Media Incompleta") echo "selected"; ?>>Educacion Media Incompleta</option>
                <option value="Educacion Media Completa" <?php if ($user["NivelEducacional"] == "Educacion Media Completa") echo "selected"; ?>>Educacion Media Completa</option>
                <option value="Educacion Superior Incompleta" <?php if ($user["NivelEducacional"] == "Educacion Superior Incompleta") echo "selected"; ?>>Educacion Superior Incompleta</option>
                <option value="Educacion Superior Completa" <?php if ($user["NivelEducacional"] == "Educacion Superior Completa") echo "selected"; ?>>Educacion Superior Completa</option>
                <option value="Magister" <?php if ($user["NivelEducacional"] == "Magister") echo "selected"; ?>>Magister</option>
                <option value="Educacion Especial" <?php if ($user["NivelEducacional"] == "Educacion Especial") echo "selected"; ?>>Educacion Especial</option>
                <option value="Doctorado" <?php if ($user["NivelEducacional"] == "Doctorado") echo "selected"; ?>>Doctorado</option>
            </select>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="area" class="formulario__label">Área:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="area" name="area" class="formulario__input" value="<?php echo $user["Area"]; ?>" required>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="titulo" class="formulario__label">Título:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="titulo" name="titulo" class="formulario__input" value="<?php echo $user["Titulo"]; ?>" required>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="nombre_curso" class="formulario__label">Nombre del Curso:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="nombre_curso" name="nombre_curso" class="formulario__input" value="<?php echo $user["NombreCurso"]; ?>" required>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="institucion" class="formulario__label">Institución:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="institucion" name="institucion" class="formulario__input" value="<?php echo $user["Institucion"]; ?>" required>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="fecha" class="formulario__label">Fecha:</label>
        <div class="formulario__grupo-input">
            <input type="date" id="fecha" name="fecha" class="formulario__input" value="<?php echo $user["Fecha"]; ?>" required>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="motivo_consulta" class="formulario__label">Motivo de Consulta:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="motivo_consulta" name="motivo_consulta" class="formulario__input" value="<?php echo $user["MotivoConsulta"]; ?>" required>
        </div>
    </div>

    <div class="formulario__grupo">
        <label for="estado_motivo_consulta" class="formulario__label">Estado del Motivo de Consulta:</label>
        <div class="formulario__grupo-input">
            <input type="text" id="estado_motivo_consulta" name="estado_motivo_consulta" class="formulario__input" value="<?php echo $user["EstadoMotivoConsulta"]; ?>" required>
        </div>
    </div>
    <br>
    <div class="formulario__grupo formulario__grupo-btn-enviar">
        <input type="submit" value="Guardar Cambios" class="formulario__btn">
    </div>
    </form>
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
      <!-- CUSTOM SCRIPTS -->
    <script src="assets/js/custom.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Función para cargar dinámicamente las opciones de regiones
            function cargarRegiones() {
                $.ajax({
                    url: 'obtener_regiones.php', // Nombre del script PHP que obtiene las regiones desde la base de datos
                    method: 'GET',
                    success: function(data) {
                        $('#region').html(data); // Actualiza las opciones de la lista de regiones
                        // Al cargar las regiones, también cargamos las comunas
                        cargarComunas();
                    }
                });
            }

            // Función para cargar dinámicamente las opciones de comunas
            function cargarComunas() {
                var idRegionSeleccionada = $('#region').val();

                $.ajax({
                    url: 'obtener_comunas.php?id_region=' + (idRegionSeleccionada || ''), // Si no hay región seleccionada, carga todas las comunas
                    method: 'GET',
                    success: function(data) {
                        $('#comuna').html(data); // Actualiza las opciones de la lista de comunas
                        $('#grupo_comuna').show(); // Muestra el grupo de comunas
                    }
                });
            }

            // Cargar regiones al cargar la página
            cargarRegiones();

            // Asignar la función cargarComunas al evento onchange de la lista de regiones
            $('#region').on('change', cargarComunas);
        });
    </script>
</body>
</html>
