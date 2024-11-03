<?php
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

if (isset($_GET['query'])) {
    $query = filter_input(INPUT_GET, 'query', FILTER_SANITIZE_STRING);

    $stmt = $pdo->prepare("SELECT * FROM producto WHERE NombreProd LIKE :query OR Descripcion LIKE :query");
    $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
    $stmt->execute();

    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($resultados) {
        echo "<h2>Resultados de la búsqueda para '$query':</h2>";
        foreach ($resultados as $producto) {
            echo "<div>";
            echo "<h3>" . htmlspecialchars($producto['NombreProd']) . "</h3>";
            echo "<p>Precio: U$S " . htmlspecialchars($producto['Precio']) . "</p>";
            echo "<p>" . htmlspecialchars($producto['Descripcion']) . "</p>";
            echo "</div><hr>";
        }
    } else {
        echo "<p>No se encontraron productos.</p>";
    }
}

$pdo = null; // Cierra la conexión
?>
