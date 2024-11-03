<?php
session_start(); // Inicia la sesión

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'flipcoin';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}

// Verificar si se recibieron los datos del total
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $total = filter_input(INPUT_POST, 'total', FILTER_VALIDATE_FLOAT);

    if ($total === false) {
        die("Error: Total inválido.");
    }

    // Aquí puedes agregar la lógica para procesar el pago (e.g., enviar el monto a PayPal)
    echo json_encode(["mensaje" => "Pago procesado con éxito", "total" => $total]);
}
?>
