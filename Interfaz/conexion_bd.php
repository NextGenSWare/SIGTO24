<?php
$servidor = "localhost"; // Cambia por tu servidor de base de datos
$usuario = "root"; // Cambia por tu usuario de base de datos
$contrasena = ""; // Cambia por tu contraseña de base de datos
$base_datos = "flipcoin"; // Nombre de la base de datos

// Crear la conexión
$conexion = mysqli_connect($servidor, $usuario, $contrasena, $base_datos);

// Verificar la conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>
