<?php
session_start(); // Inicia la sesión

require_once 'conexion_bd.php';

header('Content-Type: application/json'); // Indicamos que la respuesta será JSON

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
