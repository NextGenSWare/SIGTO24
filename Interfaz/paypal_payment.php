<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$baseUrl = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . '/SIGTO24/Interfaz';

function getPayPalAccessToken() {
    $clientId = 'ATN-XlaZhFiMju_7O1dD1LYm9VDSdBgwamJyQZnkkCUAaF4nI8D808sZqCLG8bb3svAZ7NftDr2dR5YZ';
    $secret = 'EPJcHCGNTUAWcbkMAu5-teXTRPR3jKEGKgkSRvZcAOobQJhbe6dtB3CJuf0GXc84HgSZd1hoT_1wLqYa';

    $url = "https://api.sandbox.paypal.com/v1/oauth2/token";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$secret");
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception('Error en cURL: ' . curl_error($ch));
    }

    $result = json_decode($response);
    curl_close($ch);

    if (isset($result->access_token)) {
        return $result->access_token;
    } else {
        throw new Exception('Error al obtener el token de acceso: ' . json_encode($result));
    }
}

function createPayPalPayment($amount, $currency, $description, $baseUrl) {
    if ($amount <= 0) {
        throw new Exception('El total no es válido para realizar un pago.');
    }

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
            "return_url" => "$baseUrl/paypal_execute.php",
            "cancel_url" => "$baseUrl/cancel.html"
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
?>
