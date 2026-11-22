<?php
$host = 'localhost';
$dbname = 'agenda_db';
$username = 'root'; // Usuario por defecto en XAMPP
$password = '';     // Contraseña por defecto en XAMPP (usualmente vacía)

try {
    // Creamos la conexión PDO con codificación utf8mb4
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Configuramos el modo de error para que lance excepciones si algo falla
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Si hay error en la conexión, detenemos el script y mostramos un mensaje seguro
    die("Error de conexión a la base de datos. Por favor, intente más tarde.");
}
?>
