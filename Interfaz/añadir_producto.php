<?php
$host = 'localhost';
$dbname = 'flipcoin';
$username = 'root';
$password = '';
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error al conectar con la base de datos: " . $e->getMessage()]);
    exit();
}

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capturar y sanitizar los datos del formulario
    $nombreProd = filter_input(INPUT_POST, 'nombreProd', FILTER_SANITIZE_STRING);
    $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
    $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);
    $fechaPubli = date('Y-m-d'); // Fecha actual
    $idCategoria = filter_input(INPUT_POST, 'idCategoria', FILTER_VALIDATE_INT);

    // Validar campos requeridos
    if (!$nombreProd || !$precio || !$descripcion || $stock === false || !$idCategoria) {
        echo json_encode(["error" => "Todos los campos son obligatorios y deben tener el formato correcto."]);
        exit();
    }

    // Verificar si el ID de categoría existe en la tabla 'categoria'
    $consultaCategoria = "SELECT COUNT(*) FROM categoria WHERE ID_Categoria = :idCategoria";
    $stmtCategoria = $pdo->prepare($consultaCategoria);
    $stmtCategoria->bindParam(':idCategoria', $idCategoria, PDO::PARAM_INT);
    $stmtCategoria->execute();
    $categoriaExiste = $stmtCategoria->fetchColumn();

    if (!$categoriaExiste) {
        echo json_encode(["error" => "La categoría seleccionada no existe."]);
        exit();
    }

    // Preparar la consulta SQL para insertar los datos en la tabla 'producto'
    $consulta = "INSERT INTO producto (NombreProd, Precio, Descripcion, Stock, FechaPubli, ID_Categoria) 
                 VALUES (:nombreProd, :precio, :descripcion, :stock, :fechaPubli, :idCategoria)";

    // Preparar y ejecutar la sentencia
    $stmt = $pdo->prepare($consulta);
    $stmt->bindParam(':nombreProd', $nombreProd);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':stock', $stock);
    $stmt->bindParam(':fechaPubli', $fechaPubli);
    $stmt->bindParam(':idCategoria', $idCategoria);

    if ($stmt->execute()) {
        // Obtener el ID del producto recién insertado
        $idProducto = $pdo->lastInsertId();
        echo json_encode([
            "success" => true,
            "id" => $idProducto,
            "nombreProd" => $nombreProd,
            "precio" => $precio,
            "descripcion" => $descripcion,
            "stock" => $stock,
            "idCategoria" => $idCategoria
        ]);
    } else {
        echo json_encode(["error" => "Error al agregar el producto."]);
    }
}
?>
