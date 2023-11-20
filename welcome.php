<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.html");
    exit;
}

$rol = $_SESSION["rol"];
$username = $_SESSION["username"];
$correo = $_SESSION["email"];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bienvenido</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #3498db;
            color: #fff;
            padding: 20px 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 24px;
        }

        h3 {
            font-size: 20px;
        }

        p {
            font-size: 18px;
        }

        a {
            display: block;
            margin: 10px 0;
            font-size: 18px;
            text-decoration: none;
            color: #3498db;
        }

        a:hover {
            text-decoration: underline;
        }

        .admin-links {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Bienvenido, <?php echo $username; ?>!</h2>
    <h3>Su Email es: <?php echo $correo; ?></h3>
    <?php if ($rol === "administrador"): ?>
        <!-- Contenido exclusivo para administradores -->
        <h3>Panel de Administración</h3>
        <a href="crear_usuario.php">Crear Nuevo Usuario</a>
        <a href="admin.php">Administrar Usuarios</a>
        <a href="adminVecinal.php">Administrar Usuarios Vecinales</a>
        <a href="registro_usuario_vecinal.php">Registrar Usuario Vecinal</a> 
        <a href="asociar_empleo.php">Asociar Oferta de Empleo a Usuario Vecinal</a>
        <a href="asociar_capacitacion.php">Asociar Oferta de Capacitacion a Usuario Vecinal</a>
        <a href="registro_oferta.php">Registrar Oferta de Capacitación</a> 
        <a href="postulacion_capacitacion.php">Postular Usuario Vecinal a Oferta de Capacitacion</a>
        <a href="seleccion_usuarios.php">Seleccionar Usuario Vecinal Para Capacitacion</a>
        <a href="registrar_capacitacion.php">Registrar Capacitacion Realizada a Usuario Vecinal</a>
        <a href="registro_oferta_empleo.php">Registrar Oferta de Empleo</a>
        <a href="postulacion_empleo.php">Postular Usuario Vecinal a Oferta de Empleo</a>
        <a href="seleccion_usuario_empleo.php">Seleccionar Usuario Vecinal Para Oferta de Empleo</a> 
        <a href="presentar_usuarios.php">Presentar Usuario Vecinal Para Oferta de Empleo</a>  
        <a href="registrar_seguimiento_laboral.php">Realizar Seguimiento a Postulacion</a> 
        <a href="registrar_ocupacion.php">Registrar Ocupacion Usuario Vecinal</a>   
        <a href="visita_empresa.php">Programar Visita a Empresa</a>
        <a href="reporte_visita_empresa.php">Reporte de Visita a Empresa</a>
        <a href="visita_otec.php">Programar Visita a Otec</a>
        <a href="reporte_visita_otec.php">Reporte de Visita a Otec</a>
        <a href="registrar_meta_anual.php">Registrar Meta Anual</a>
        <a href="registrar_avance_mensual.php">Registrar Avance Mensual</a>
        <a href="registrar_requerimiento_capacitacion.php">Registrar Requerimiento de Capacitacion</a>
        <a href="registrar_requerimiento_laboral.php">Registrar Requerimiento Laboral</a>
        <!--<a href="matricular_usuarios.php">Matricular Usuario a Capacitacion</a>-->
        <!--<a href="registrar_capacitacion.php">Registrar Usuario a Capacitacion</a>-->
        
        
    <?php else: ?>
        <!-- Contenido para usuarios regulares -->
        <p>Esta es tu página de inicio.</p>
        <a href="registro_usuario_vecinal.php">Registrar Usuario Vecinal</a>
        <a href="seleccion_usuarios.php">Seleccionar Usuario Vecinal Para Capacitacion</a>
        <a href="seleccion_usuario_empleo.php">Seleccionar Usuario Vecinal Para Oferta de Empleo</a>
        <a href="postulacion_capacitacion.php">Postular Usuario Vecinal a Oferta de Capacitacion</a>
        <a href="postulacion_empleo.php">Postular Usuario Vecinal a Oferta de Empleo</a>
        <a href="asociar_empleo.php">Asociar Oferta de Empleo a Usuario Vecinal</a>
        <a href="asociar_capacitacion.php">Asociar Oferta de Capacitacion a Usuario Vecinal</a>
        <a href="registro_empresa.php">Registrar Nueva Empresa</a>
        <a href="visita_empresa.php">Programar Visita a Empresa</a>
        <a href="reporte_visita_empresa.php">Reporte de Visita a Empresa</a>
        <a href="visita_otec.php">Programar Visita a Otec</a>
        <a href="reporte_visita_otec.php">Reporte de Visita a Otec</a>
        <a href="registro_oferta_empleo.php">Registrar Oferta de Empleo</a>
        <a href="registro_perfil_empleo.php">Registrar Perfil de Empleo</a>
        <a href="registro_oferta.php">Registrar Oferta de Capacitación</a>
        <a href="registro_otecs.php">Registrar Nueva Otec</a>
        <a href="registrar_meta_anual.php">Registrar Meta Anual</a>
        <a href="registrar_avance_mensual.php">Registrar Avance Mensual</a>
    <?php endif; ?>
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>
