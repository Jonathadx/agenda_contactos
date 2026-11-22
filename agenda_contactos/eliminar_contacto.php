<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit();
}

require_once 'conexion.php';

$user_id = $_SESSION['user_id'];
$contacto_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($contacto_id > 0) {
    try {
        // Eliminar asegurando que pertenezca al usuario actual (Seguridad estricta)
        $stmt = $pdo->prepare("DELETE FROM contactos WHERE id = ? AND id_usuario = ?");
        $stmt->execute([$contacto_id, $user_id]);
    } catch (PDOException $e) {
        // Manejo silencioso o redirección
    }
}

header("Location: dashboard.php");
exit();
?>
