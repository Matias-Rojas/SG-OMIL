<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
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

$search = ""; // Inicializa la cadena de búsqueda vacía
if (isset($_GET["search"])) {
    $search = $_GET["search"];
}

// Consulta para obtener la lista de usuarios con opciones de búsqueda
$sql = "SELECT * FROM sg_omil_usuariosvecinales 
        WHERE Nombres LIKE ? OR Apellidos LIKE ? OR Rut LIKE ?";
$stmt = $conn->prepare($sql);
$searchParam = "%$search%";
$stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración de Usuarios</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            border-collapse: collapse;
            max-width: 100%; /* Establece un ancho máximo para la tabla */
            width: 100%; /* Hace que la tabla ocupe todo el espacio disponible */
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .search-box {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .search-box input[type="text"] {
            padding: 8px;
            width: 100px;
        }

        .search-button {
            padding: 8px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        

        .search-form {
            width: 50%; /* Ajusta el ancho del formulario según tus necesidades */
            text-align: center;
            margin: 0 auto; /* Centra el formulario horizontalmente */
        }

        .search-form input[type="text"] {
            padding: 8px;
            width: 70%; /* Ajusta el ancho del campo de búsqueda */
        }

        .search-form input[type="submit"] {
            padding: 8px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
            width: 28%; /* Ajusta el ancho del botón */
        }
    </style>
<body>
    <h2>Administración de Usuarios</h2>
    <a href="welcome.php">Volver al Inicio</a>

    <!-- Formulario de búsqueda -->
    <div class="search-form">
        <form method="GET">
            <input type="text" name="search" placeholder="Buscar por nombre, apellido o RUT" value="<?php echo $search; ?>">
            <input type="submit" value="Buscar">
        </form>
    </div>

    <!-- Tabla de usuarios -->
    <div class="table-container">
    <table>
        <tr>
            <th>ID</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>RUT</th>
            <th>Genero</th>
            <th>Fecha de Nacimiento</th>
            <th>Estado Civil</th>
            <th>Dirección</th>
            <th>Región</th>
            <th>Comuna</th>
            <th>Sector</th>
            <th>Nacionalidad</th>
            <th>Correo Electrónico</th>
            <th>Teléfono</th>
            <th>Teléfono Alternativo</th>
            <th>Nivel Educacional</th>
            <th>Área</th>
            <th>Título</th>
            <th>Nombre del Curso</th>
            <th>Institución</th>
            <th>Fecha</th>
            <th>Motivo de Consulta</th>
            <th>Estado del Motivo de Consulta</th>
            <th>Acciones</th>
        </tr>
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["UsuarioID"] . "</td>";
            echo "<td>" . $row["Nombres"] . "</td>";
            echo "<td>" . $row["Apellidos"] . "</td>";
            echo "<td>" . $row["Rut"] . "</td>";
            echo "<td>" . $row["Genero"] . "</td>";
            echo "<td>" . $row["FechaNacimiento"] . "</td>";
            echo "<td>" . $row["EstadoCivil"] . "</td>";
            echo "<td>" . $row["Direccion"] . "</td>";
            echo "<td>" . $row["Region"] . "</td>";
            echo "<td>" . $row["Comuna"] . "</td>";
            echo "<td>" . $row["Sector"] . "</td>";
            echo "<td>" . $row["Nacionalidad"] . "</td>";
            echo "<td>" . $row["CorreoElectronico"] . "</td>";
            echo "<td>" . $row["Telefono"] . "</td>";
            echo "<td>" . $row["TelefonoAlternativo"] . "</td>";
            echo "<td>" . $row["NivelEducacional"] . "</td>";
            echo "<td>" . $row["Area"] . "</td>";
            echo "<td>" . $row["Titulo"] . "</td>";
            echo "<td>" . $row["NombreCurso"] . "</td>";
            echo "<td>" . $row["Institucion"] . "</td>";
            echo "<td>" . $row["Fecha"] . "</td>";
            echo "<td>" . $row["MotivoConsulta"] . "</td>";
            echo "<td>" . $row["EstadoMotivoConsulta"] . "</td>";
            echo "<td><a href='editar_usuario_vecinal.php?id=" . $row["UsuarioID"] . "'>Editar</a></td>";
            echo "</tr>";
        }
        ?>
    </table>
    </div>

    <!-- Botón para generar un informe PDF -->
    <form action="generate_pdf.php" method="post">
        <input type="submit" name="generate_report_pdf" value="Generar Informe en PDF">
    </form>
</body>
</html>
