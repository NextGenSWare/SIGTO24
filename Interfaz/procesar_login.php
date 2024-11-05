<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once 'conexion_bd.php';

header('Content-Type: application/json'); // Indicamos que la respuesta será JSON

$response = ['success' => false, 'message' => '', 'redirect' => ''];

try {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['mail']) || !isset($data['password'])) {
        throw new Exception("Correo electrónico y contraseña son obligatorios.");
    }

    $email = $data['mail'];
    $password = $data['password'];

    $tablas = [
        'comprador' => ['email' => 'EmailC', 'password' => 'ContrasenaC', 'redirect' => 'homeC.html'],
        'vendedor' => ['email' => 'EmailV', 'password' => 'ContrasenaV', 'redirect' => 'homeV.html'],
        'administrador' => ['email' => 'EmailA', 'password' => 'ContrasenaA', 'redirect' => 'homeA.html']
    ];

    $usuarioEncontrado = false;

    foreach ($tablas as $tabla => $campos) {
        $stmt = $pdo->prepare("SELECT * FROM $tabla WHERE {$campos['email']} = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            $usuarioEncontrado = true;

            if (password_verify($password, $usuario[$campos['password']])) {
                $_SESSION['usuario'] = [
                    'cedula' => $usuario['C_I_' . strtoupper(substr($tabla, 0, 1))],
                    'nombre' => $usuario['Username' . strtoupper(substr($tabla, 0, 1))],
                    'email' => $email,
                    'tipo' => ucfirst($tabla)
                ];

                $response['success'] = true;
                $response['message'] = 'Login exitoso';
                $response['redirect'] = $campos['redirect'];
                break;
            } else {
                throw new Exception("Contraseña incorrecta.");
            }
        }
    }

    if (!$usuarioEncontrado) {
        throw new Exception("No se encontró un usuario con ese correo electrónico.");
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} catch (PDOException $e) {
    $response['message'] = "Error de base de datos: " . $e->getMessage();
}

echo json_encode($response);
$pdo = null;
?>