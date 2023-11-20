<?php
require('fpdf/fpdf.php');

class PDF extends FPDF
{
    function Header()
    {
        // Encabezado del PDF (opcional)
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Listado de Usuarios', 0, 1, 'C');
        $this->Ln(10); // Espacio después del encabezado
    }

    function Footer()
    {
        // Pie de página del PDF (opcional)
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }

    function ChapterTitle($title)
    {
        // Título de un "capítulo" o sección del listado
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 6, utf8_decode($title), 0, 1, 'L'); // Decodificar a UTF-8
    }

    function ChapterBody($data)
    {
        // Cuerpo de un "capítulo" o sección del listado
        $this->SetFont('Arial', '', 12);

        foreach ($data as $line) {
            $this->MultiCell(0, 10, utf8_decode($line)); // Decodificar a UTF-8
        }
        $this->Ln(); // Espacio después del capítulo
    }

    function GenerateList($userList)
    {
        foreach ($userList as $user) {
            $this->AddPage();

            $data = array(
                "Id de Usuario: " . $user['UsuarioID'],
                "Rut: " . $user['Rut'],
                "Nombres: " . $user['Nombres'],
                "Apellidos: " . $user['Apellidos'],
                "Género: " . $user['Genero'],
                "Fecha de Nacimiento: " . $user['FechaNacimiento'],
                "Estado Civil: " . $user['EstadoCivil'],
                "Dirección: " . $user['Direccion'],
                "Región: " . $user['Region'],
                "Comuna: " . $user['Comuna'],
                "Sector: " . $user['Sector'],
                "Nacionalidad: " . $user['Nacionalidad'],
                "Correo Electrónico: " . $user['CorreoElectronico'],
                "Teléfono: " . $user['Telefono'],
                "Teléfono Alternativo: " . $user['TelefonoAlternativo'],
                "Nivel Educacional: " . $user['NivelEducacional'],
                "Área: " . $user['Area'],
                "Título: " . $user['Titulo'],
                "Nombre del Curso: " . $user['NombreCurso'],
                "Institución: " . $user['Institucion'],
                "Fecha: " . $user['Fecha'],
                "Motivo de Consulta: " . $user['MotivoConsulta'],
                "Estado del Motivo de Consulta: " . $user['EstadoMotivoConsulta'],
            );

            $this->ChapterTitle("Información del Usuario");
            $this->ChapterBody($data);
        }
    }
}

// Crear un objeto PDF
$pdf = new PDF();

// Realizar la conexión a la base de datos
$servername = "localhost";
$db_username = "HURDOX";
$db_password = "gokudeus2023";
$database = "sg_omil";

$conn = new mysqli($servername, $db_username, $db_password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Realizar una consulta a la base de datos para obtener los datos de los usuarios
$sql = "SELECT * FROM sg_omil_usuariosvecinales";
$result = $conn->query($sql);

// Procesar los datos y generar el PDF
$userList = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userList[] = $row;
    }
}

// Generar el PDF
$pdf->GenerateList($userList);

// Salida del PDF (descarga o visualización en el navegador)
$filename = "Reporte_de_Usuarios_Vecinales.pdf"; // Establece el nombre de archivo
$pdf->Output($filename, 'D'); // 'D' para descargar, 'I' para abrir en el navegador
