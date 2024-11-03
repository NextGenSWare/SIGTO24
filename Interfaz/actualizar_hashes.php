<?php
$host = 'localhost';
$dbname = 'flipcoin';
$username = 'root';
$password = '';

// Conectar a la base de datos con PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Consultar todos los usuarios
    $stmt = $pdo->query("SELECT ID_Comprador, ContrasenaC FROM comprador");
    while ($usuario = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $usuario['ID_Comprador'];
        $oldHash = $usuario['ContrasenaC'];

        // Rehash si es necesario
        if (!password_verify('contraseña_plana', $oldHash)) { // Reemplaza con la contraseña correcta para la prueba
            $nuevoHash = password_hash('contraseña_plana', PASSWORD_DEFAULT); // Reemplaza con la lógica correcta para cada usuario

            // Actualizar el hash en la base de datos
            $updateStmt = $pdo->prepare("UPDATE comprador SET ContrasenaC = :nuevoHash WHERE ID_Comprador = :id");
            $updateStmt->bindParam(':nuevoHash', $nuevoHash);
            $updateStmt->bindParam(':id', $id);
            $updateStmt->execute();

            echo "Hash actualizado para el usuario con ID: $id<br>";
        }
    }
    
    echo "Proceso de actualización de hashes completado.";
} catch (PDOException $e) {
    echo "Error al conectar con la base de datos: " . $e->getMessage();
}
?>
