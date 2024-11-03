<?php
$host = 'localhost';
$dbname = 'flipcoin';
$username = 'root';
$password = '';
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cedula = $_POST['cedula'];
    $nombre = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $contrasena = $_POST['password'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $telefono = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $direccion = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_STRING);
    $tipo_registro = $_POST['tipo_registro'];

    if (!$cedula || !$nombre || !$contrasena || !$fecha_nacimiento || !$email || !$telefono || !$direccion || !$tipo_registro) {
        die("Todos los campos son obligatorios y deben tener el formato correcto.");
    }

    // Encriptar la contraseña antes de guardarla en la base de datos
    $contrasena_encriptada = password_hash($contrasena, PASSWORD_DEFAULT);

    // Verificar en qué tabla se debe guardar la información
    if ($tipo_registro === 'comprador') {
        $consulta = "INSERT INTO comprador (C_I_C, UsernameC, ContrasenaC, FnacC, EmailC, TelefonoC, DireccionC) 
                     VALUES (:cedula, :nombre, :contrasena, :fecha_nacimiento, :email, :telefono, :direccion)";
    } elseif ($tipo_registro === 'vendedor') {
        $consulta = "INSERT INTO vendedor (C_I_V, UsernameV, ContrasenaV, FnacV, EmailV, TelefonoV, DireccionV) 
                     VALUES (:cedula, :nombre, :contrasena, :fecha_nacimiento, :email, :telefono, :direccion)";
    } else {
        die("Tipo de registro no válido.");
    }

    try {
        $stmt = $pdo->prepare($consulta);
        $stmt->bindParam(':cedula', $cedula);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':contrasena', $contrasena_encriptada);
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->execute();

        // Redirigir al index sin mostrar información sensible
        header("Location: homeC.html");
        exit(); // Detener el script después de la redirección
    } catch (PDOException $e) {
        echo "Error al registrar: " . $e->getMessage();
    }

    $pdo = null; // Cerrar la conexión a la base de datos
}
?>
