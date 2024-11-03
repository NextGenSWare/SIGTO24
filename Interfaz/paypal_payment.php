<?php
// Establece el tipo de contenido de la respuesta como JSON
header('Content-Type: application/json');

// Configuración para mostrar errores (útil en desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // Reporta todos los errores

// Define la URL base dependiendo del entorno
$baseUrl = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . '/SIGTO24/Interfaz';

// Función para obtener el token de acceso de PayPal
function getPayPalAccessToken()
{
    // Credenciales de la aplicación PayPal (clientId y secret)
    $clientId = 'ATN-XlaZhFiMju_7O1dD1LYm9VDSdBgwamJyQZnkkCUAaF4nI8D808sZqCLG8bb3svAZ7NftDr2dR5YZ'
    $secret = 'EPJcHCGNTUAWcbkMAu5-teXTRPR3jKEGKgkSRvZcAOobQJhbe6dtB3CJuf0GXc84HgSZd1hoT_1wLqYa';

    // URL del endpoint de PayPal para obtener el token
    $url = "https://api.sandbox.paypal.com/v1/oauth2/token";
    $ch = curl_init(); // Inicializa una nueva sesión cURL

    // Configuración de cURL para la solicitud
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$secret");
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

    // Ejecuta la solicitud
    $response = curl_exec($ch);

    // Manejo de errores de cURL
    if (curl_errno($ch)) {
        throw new Exception('Error en cURL: ' . curl_error($ch));
    }

    // Decodifica la respuesta JSON
    $result = json_decode($response);
    curl_close($ch);

    // Verificación y retorno del token de acceso
    if (isset($result->access_token)) {
        return $result->access_token;
    } else {
        throw new Exception('Error al obtener el token de acceso: ' . json_encode($result));
    }
}

// Función para crear el pago en PayPal
function createPayPalPayment($amount, $currency, $description, $baseUrl)
{
    $accessToken = getPayPalAccessToken();
    $url = "https://api.sandbox.paypal.com/v1/payments/payment";

    $data = json_encode([
        "intent" => "sale",
        "payer" => ["payment_method" => "paypal"],
        "transactions" => [[
            "amount" => ["total" => $amount, "currency" => $currency],
            "description" => $description
        ]],
        "redirect_urls" => [
            "return_url" => "$baseUrl/paypal_execute.php", // URL de retorno usando la variable base
            "cancel_url" => "$baseUrl/cancel.html" // URL de cancelación usando la variable base
        ]
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $accessToken"
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception('Error en cURL: ' . curl_error($ch));
    }

    $result = json_decode($response);
    curl_close($ch);

    if (!empty($result->links)) {
        foreach ($result->links as $link) {
            if ($link->rel == 'approval_url') {
                return $link->href;
            }
        }
    } else {
        throw new Exception('Error en la creación del pago: no se recibieron enlaces');
    }
}

// Leer los datos enviados por el cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['amount'], $input['currency'], $input['description'])) {
        $amount = $input['amount'];
        $currency = $input['currency'];
        $description = $input['description'];

        try {
            $approvalUrl = createPayPalPayment($amount, $currency, $description, $baseUrl);
            echo json_encode(['approvalUrl' => $approvalUrl]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'Datos de entrada inválidos.']);
    }
} else {
    echo json_encode(['error' => 'Método de solicitud no permitido.']);
}
