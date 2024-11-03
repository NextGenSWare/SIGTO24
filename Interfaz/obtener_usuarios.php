<?php
$host = 'localhost';
$dbname = 'flipcoin';
$username = 'root';
$password = '';
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consultar compradores
    $stmtCompradores = $pdo->query("SELECT C_I_C AS id, UsernameC AS nombre, 'Comprador' AS tipo, EmailC AS email, TelefonoC AS telefono FROM comprador");
    $compradores = $stmtCompradores->fetchAll(PDO::FETCH_ASSOC);

    // Consultar vendedores
    $stmtVendedores = $pdo->query("SELECT C_I_V AS id, UsernameV AS nombre, 'Vendedor' AS tipo, EmailV AS email, TelefonoV AS telefono FROM vendedor");
    $vendedores = $stmtVendedores->fetchAll(PDO::FETCH_ASSOC);

    // Consultar administradores
    $stmtAdmins = $pdo->query("SELECT C_I_A AS id, UsernameA AS nombre, 'Administrador' AS tipo, EmailA AS email, TelefonoA AS telefono FROM administrador");
    $admins = $stmtAdmins->fetchAll(PDO::FETCH_ASSOC);

    // Unir los resultados
    $usuarios = array_merge($compradores, $vendedores, $admins);

    echo json_encode($usuarios);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error al obtener usuarios: " . $e->getMessage()]);
}
?>
