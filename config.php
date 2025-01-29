<?php
$host = 'localhost';
$db = 'linktree';
$user = 'root'; // Cambia si usas otro usuario
$pass = ''; // Cambia si usas otra contraseña

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
