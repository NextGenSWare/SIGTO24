<?php
$servidor = "localhost";
$usuario = "root"; // Cambia esto por tu usuario de base de datos
$contrasena = ""; // Cambia esto por tu contraseña de base de datos
$base_datos = "flipcoin"; // Nombre de la base de datos

// Crear conexión
$conexion = new mysqli($servidor, $usuario, $contrasena, $base_datos);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
