<?php
$contraseñaOriginal = '12345678'; // La contraseña en texto plano
$hash = password_hash($contraseñaOriginal, PASSWORD_DEFAULT);

if (password_verify($contraseñaOriginal, $hash)) {
    echo "La verificación con el nuevo hash fue exitosa.";
} else {
    echo "La verificación con el nuevo hash falló.";
}

?>
