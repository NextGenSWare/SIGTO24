<?php
// Incluir archivo de conexión a la base de datos
include('conexion_bd.php'); // Asegúrate de tener un archivo 'conexion_bd.php' para conectarte a la base de datos

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capturar y sanitizar los datos del formulario
    $nombre = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $telefono = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $cedula = $_POST['cedula'];
    $direccion = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_STRING);
    $contrasena = $_POST['password'];

    // Validar campos requeridos y formato de entrada
    if (!$nombre || !$email || !$telefono || !$fecha_nacimiento || !$cedula || !$direccion || !$contrasena) {
        die("Todos los campos son obligatorios y deben tener el formato correcto.");
    }

    // Encriptar la contraseña antes de guardarla en la base de datos
    $contrasena_encriptada = password_hash($contrasena, PASSWORD_DEFAULT);

    // Preparar la consulta SQL para insertar los datos en la tabla 'comprador'
    $consulta = "INSERT INTO comprador (UsernameC, ContrasenaC, EmailC, TelefonoC, DireccionC, FnacC, C_I_C) 
                 VALUES ('$nombre', '$contrasena_encriptada', '$email', '$telefono', '$direccion', '$fecha_nacimiento', '$cedula')";

    // Ejecutar la consulta y verificar si se guardaron los datos
    if (mysqli_query($conexion, $consulta)) {
        echo "Registro exitoso.";
    } else {
        echo "Error al registrar: " . mysqli_error($conexion);
    }

    // Cerrar la conexión a la base de datos
    mysqli_close($conexion);
}
?>
