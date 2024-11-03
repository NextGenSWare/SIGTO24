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
    // Capturar y sanitizar los datos del formulario
    $cedula = $_POST['cedula'];
    $nombre = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $contrasena = $_POST['password'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $telefono = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $direccion = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_STRING);

    // Validar campos requeridos y formato de entrada
    if (!$cedula || !$nombre || !$contrasena || !$fecha_nacimiento || !$email || !$telefono || !$direccion) {
        die("Todos los campos son obligatorios y deben tener el formato correcto.");
    }

    // Encriptar la contraseña antes de guardarla en la base de datos
    $contrasena_encriptada = password_hash($contrasena, PASSWORD_DEFAULT);

    // Preparar la consulta SQL para insertar los datos en la tabla 'comprador'
    $consulta = "INSERT INTO comprador (C_I_C, UsernameC, ContrasenaC, FnacC, EmailC, TelefonoC, DireccionC) 
                 VALUES (:cedula, :nombre, :contrasena, :fecha_nacimiento, :email, :telefono, :direccion)";

    try {
        $stmt = $pdo->prepare($consulta);
        $stmt->execute([
            ':cedula' => $cedula,
            ':nombre' => $nombre,
            ':contrasena' => $contrasena_encriptada,
            ':fecha_nacimiento' => $fecha_nacimiento,
            ':email' => $email,
            ':telefono' => $telefono,
            ':direccion' => $direccion
        ]);

        // Redirigir a otra página después de un registro exitoso
        header("Location: homeC.html");
        exit(); // Asegura que el script se detenga después de la redirección
    } catch (PDOException $e) {
        echo "Error al registrar: " . $e->getMessage();
    }
}

// Cerrar la conexión a la base de datos
$pdo = null;
?>
