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
    die("Error al conectar con la base de datos: " . $e->getMessage());
}

// Verificar si el formulario fue enviado por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener la acción a realizar (insertar, actualizar, eliminar, obtener)
    $accion = $_POST['accion'];

    if ($accion === 'obtener') {
        // Obtener todos los productos
        try {
            $stmt = $pdo->query("SELECT * FROM producto");
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($productos);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error al obtener productos: " . $e->getMessage()]);
        }
    } elseif ($accion === 'insertar') {
        // Insertar un nuevo producto
        $nombreProd = filter_input(INPUT_POST, 'nombreProd', FILTER_SANITIZE_STRING);
        $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
        $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
        $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);
        $fechaPubli = date('Y-m-d'); // Fecha actual
        $idCategoria = filter_input(INPUT_POST, 'idCategoria', FILTER_VALIDATE_INT);

        if (!$nombreProd || !$precio || !$descripcion || $stock === false || !$idCategoria) {
            echo json_encode(["error" => "Todos los campos son obligatorios y deben tener el formato correcto."]);
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO producto (NombreProd, Precio, Stock, Descripcion, FechaPubli, ID_Categoria) 
                                       VALUES (:nombreProd, :precio, :stock, :descripcion, :fechaPubli, :idCategoria)");
                $stmt->bindParam(':nombreProd', $nombreProd);
                $stmt->bindParam(':precio', $precio);
                $stmt->bindParam(':stock', $stock);
                $stmt->bindParam(':descripcion', $descripcion);
                $stmt->bindParam(':fechaPubli', $fechaPubli);
                $stmt->bindParam(':idCategoria', $idCategoria);
                $stmt->execute();
                echo json_encode(["mensaje" => "Producto agregado con éxito"]);
            } catch (PDOException $e) {
                echo json_encode(["error" => "Error al agregar el producto: " . $e->getMessage()]);
            }
        }
    } elseif ($accion === 'actualizar') {
        // Actualizar un producto existente
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nombreProd = filter_input(INPUT_POST, 'nombreProd', FILTER_SANITIZE_STRING);
        $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
        $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
        $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);
        $idCategoria = filter_input(INPUT_POST, 'idCategoria', FILTER_VALIDATE_INT);

        if (!$id || !$nombreProd || !$precio || !$descripcion || $stock === false || !$idCategoria) {
            echo json_encode(["error" => "Todos los campos son obligatorios y deben tener el formato correcto."]);
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE producto SET NombreProd = :nombreProd, Precio = :precio, Stock = :stock, Descripcion = :descripcion, ID_Categoria = :idCategoria WHERE ID_Producto = :id");
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':nombreProd', $nombreProd);
                $stmt->bindParam(':precio', $precio);
                $stmt->bindParam(':stock', $stock);
                $stmt->bindParam(':descripcion', $descripcion);
                $stmt->bindParam(':idCategoria', $idCategoria);
                $stmt->execute();
                echo json_encode(["mensaje" => "Producto actualizado con éxito"]);
            } catch (PDOException $e) {
                echo json_encode(["error" => "Error al actualizar el producto: " . $e->getMessage()]);
            }
        }
    } elseif ($accion === 'eliminar') {
        // Eliminar un producto
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            echo json_encode(["error" => "El ID del producto es obligatorio."]);
        } else {
            try {
                $stmt = $pdo->prepare("DELETE FROM producto WHERE ID_Producto = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                echo json_encode(["mensaje" => "Producto eliminado con éxito"]);
            } catch (PDOException $e) {
                echo json_encode(["error" => "Error al eliminar el producto: " . $e->getMessage()]);
            }
        }
    } else {
        echo json_encode(["error" => "Acción no soportada"]);
    }
}

$pdo = null; // Cerrar la conexión a la base de datos
?>
