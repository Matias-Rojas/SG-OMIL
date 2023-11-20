<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.html");
    exit;
}

// Establecer la conexión a la base de datos
$servername = "localhost";
$db_username = "HURDOX";
$db_password = "gokudeus2023";
$database = "sg_omil";

$conn = new mysqli($servername, $db_username, $db_password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta para obtener todos los usuarios
$sql = "SELECT * FROM sg_omil_usuarios";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Administrar Usuarios</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        .actions {
            display: flex;
            justify-content: space-between;
        }

        .edit-btn, .delete-btn {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .delete-btn {
            background-color: #f44336;
        }
        /* Estilo para el modal */
        .modal {
            display: none;
            position: fixed;
            top: 50%; /* Centrar verticalmente */
            left: 50%; /* Centrar horizontalmente */
            transform: translate(-50%, -50%); /* Centrar exactamente en el centro de la pantalla */
            z-index: 1;
            background-color: rgba(0, 0, 0, 0.7);
        }

        .modal-content {
            background-color: white;
            padding: 100px;
            max-width: 80%; /* Ancho máximo del modal */
            max-height: 80%; /* Altura máxima del modal */
            overflow-y: auto; /* Permitir desplazamiento vertical si el contenido es demasiado largo */
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
        }

        /* Estilos para el botón de cerrar el modal */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .savebt{
            width: 100%;
            height: 100%;
            margin-top: -80px;
        }
    </style>
</head>
<body>
    <h2>Administrar Usuarios</h2>

    <!-- Modal de edición de usuario -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModalBtn">&times;</span>
            <h3>Editar Usuario</h3>
            
            <form method="POST" action="editar_usuario.php">
                <input type="hidden" id="editUserId" name="id" value="">
                <label for="editUsername">Nombre de Usuario:</label>
                <input type="text" id="editUsername" name="username" required><br><br>

                <label for="editMail">Correo Electrónico:</label>
                <input type="text" id="editMail" name="email" required><br><br>

                <label for="editPassword">Nueva Contraseña:</label>
                <input type="password" id="editPassword" name="password"><br><br>

                <label for="editRol">Rol:</label>
                <select id="editRol" name="rol" required>
                    <option value="usuario">Usuario</option>
                    <option value="administrador">Administrador</option>
                </select><br><br>

                <input class="savebt" type="submit" value="Guardar Cambios">
            </form>
        </div>
    </div>

    <table>
    <tr>
        <th>ID</th>
        <th>Nombre de Usuario</th>
        <th>Correo Electrónico</th>
        <th>Contraseña</th>
        <th>Rol</th>
        <th>Acciones</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row["id"]; ?></td>
            <td><?php echo $row["username"]; ?></td>
            <td><?php echo $row["email"]; ?></td>
            <td class="password">
                <span class="password-text">******</span>
                <button class="password-toggle" data-password="<?php echo $row['password']; ?>" onclick="togglePassword(this)">Mostrar</button>
            </td>
            <td><?php echo $row["rol"]; ?></td>
            <td class="actions">
                <button class="edit-btn" onclick="editUser(<?php echo $row['id']; ?>)">Editar</button>
                <form method="POST" action="eliminar_usuario.php">
                    <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                    <input type="submit" class="delete-btn" value="Eliminar">
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<script>
function togglePassword(button) {
    var passwordText = button.parentElement.querySelector('.password-text');
    
    if (passwordText.textContent === "******") {
        // Mostrar contraseña
        passwordText.textContent = button.getAttribute('data-password');
        button.textContent = "Ocultar";
    } else {
        // Ocultar contraseña
        passwordText.textContent = "******";
        button.textContent = "Mostrar";
    }
}
</script>




    <a href="welcome.php">Volver a la página de bienvenida</a>

    <script>
        function editUser(userId) {
            // Obtener los datos del usuario por AJAX y llenar el formulario
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var userData = JSON.parse(this.responseText);
                    document.getElementById('editUserId').value = userData.id;
                    document.getElementById('editUsername').value = userData.username;
                    document.getElementById('editMail').value = userData.email;
                    document.getElementById('editRol').value = userData.rol;
                    // Mostrar el modal
                    var modal = document.getElementById("editModal");
                    modal.style.display = "block";
                }
            };
            xhttp.open("GET", "obtener_usuario.php?id=" + userId, true);
            xhttp.send();
        }

        var closeModalBtn = document.getElementById("closeModalBtn");
        closeModalBtn.onclick = function() {
            // Cerrar el modal
            var modal = document.getElementById("editModal");
            modal.style.display = "none";
        };
    </script>
</body>
</html>


<?php
// Cerrar la conexión
$conn->close();
?>
