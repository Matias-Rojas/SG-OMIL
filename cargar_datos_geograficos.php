<?php
// Archivo PHP para manejar la lógica de carga de datos

// Ejemplo de conexión a la base de datos MySQL
$servername = "localhost";
$username = "HURDOX";
$password = "gokudeus2023";
$dbname = "sg_omil";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Truncar las tablas antes de agregar nuevos datos
$conn->query("TRUNCATE TABLE sg_omil_regiones");
$conn->query("TRUNCATE TABLE sg_omil_comunas");

// Llamada a la función para obtener regiones desde la API
$regiones = obtener_regiones();

// Inicializar un array para mensajes
$mensajes = array();

// Proceso para almacenar las regiones en la base de datos
foreach ($regiones as $region) {
    $region_id = $region['codigo']; // Asegúrate de que 'codigo' sea el campo correcto
    $region_nombre = $region['nombre'];

    // Imprime detalles para depurar
    echo "ID de región: $region_id, Nombre de región: $region_nombre <br>";

    // Ejemplo de inserción en una tabla llamada 'regiones'
    $sql = "INSERT INTO sg_omil_regiones (id, nombre) VALUES ('$region_id', '$region_nombre')";

    if ($conn->query($sql) === TRUE) {
        $mensajes[] = "Región insertada correctamente: $region_nombre";
    } else {
        $mensajes[] = "Error al insertar región: " . $conn->error;
    }

    // Llamada a la función para obtener comunas de la región
    $comunas = obtener_comunas_por_region($region_id);

    // Proceso para almacenar las comunas en la base de datos
    if ($comunas !== null) { // Verifica que la respuesta de la API no sea nula
        foreach ($comunas as $comuna) {
            $comuna_id = $comuna['codigo']; // Asegúrate de que 'codigo' sea el campo correcto
            $comuna_nombre = $comuna['nombre'];

            // Imprime detalles para depurar
            echo "ID de comuna: $comuna_id, Nombre de comuna: $comuna_nombre <br>";

            // Ejemplo de inserción en una tabla llamada 'comunas'
            $sql_comuna = "INSERT INTO sg_omil_comunas (id, nombre, id_region) VALUES ('$comuna_id', '$comuna_nombre', '$region_id')";

            if ($conn->query($sql_comuna) === TRUE) {
                $mensajes[] = "Comuna insertada correctamente: $comuna_nombre";
            } else {
                $mensajes[] = "Error al insertar comuna: " . $conn->error;
            }
        }
    } else {
        $mensajes[] = "Error al obtener comunas para la región: $region_id";
    }
}

// Cierra la conexión a la base de datos
$conn->close();

// Devuelve un JSON con los mensajes
echo json_encode(['mensajes' => $mensajes]);

// Función para obtener regiones desde la API
function obtener_regiones() {
    $url = "https://apis.digital.gob.cl/dpa/regiones";
    $json = file_get_contents($url);

    // Imprime la respuesta para depuración
    echo "Respuesta de la API (Regiones): $json <br>";

    return json_decode($json, true);
}

// Función para obtener comunas por región desde la API
function obtener_comunas_por_region($region_id) {
    $url = "https://apis.digital.gob.cl/dpa/regiones/$region_id/comunas";
    $json = file_get_contents($url);

    // Imprime la respuesta para depuración
    echo "Respuesta de la API (Comunas): $json <br>";

    return json_decode($json, true);
}
?>
