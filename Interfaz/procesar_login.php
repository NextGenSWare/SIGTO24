<?php
session_start(); // Inicia la sesión

$host = 'localhost';
$dbname = 'flipcoin';
$username = 'root';
$password = '';

// Establecer la conexión con la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}

// Verificar si se enviaron los datos mediante POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email || !$password) {
        echo "Correo electrónico y contraseña son obligatorios.";
        exit();
    }

    // Consultar en las tablas de compradores, vendedores y administradores
    try {
        $tablas = [
            'comprador' => 'EmailC',
            'vendedor' => 'EmailV',
            'administrador' => 'EmailA'
        ];
        $usuarioEncontrado = false;

        foreach ($tablas as $tabla => $campoEmail) {
            $stmt = $pdo->prepare("SELECT * FROM $tabla WHERE $campoEmail = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                $usuarioEncontrado = true; // Indica que el usuario fue encontrado
                $campoContrasena = ($tabla == 'comprador') ? 'ContrasenaC' : (($tabla == 'vendedor') ? 'ContrasenaV' : 'ContrasenaA');
            

                if (password_verify($password, $usuario[$campoContrasena])) {
                    // Guardar los datos del usuario en la sesión
                    $_SESSION['usuario'] = [
                        'cedula' => $usuario['C_I_C'] ?? $usuario['C_I_V'] ?? $usuario['C_I_A'],
                        'nombre' => $usuario['UsernameC'] ?? $usuario['UsernameV'] ?? $usuario['UsernameA'],
                        'email' => $email,
                        'tipo' => ucfirst($tabla)
                    ];

                    // Redirigir al usuario a la página de inicio
                    header("Location: homeC.html");
                    exit();
                } else {
                    echo "Contraseña incorrecta.";
                    exit();
                }
            }
        }

        if (!$usuarioEncontrado) {
            echo "No se encontró un usuario con ese correo electrónico.";
        }
    } catch (PDOException $e) {
        echo "Error al consultar la base de datos: " . $e->getMessage();
    }
}

$pdo = null; // Cerrar la conexión a la base de datos
?>
