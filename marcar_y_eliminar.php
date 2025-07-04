<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'includes/session.php';
include_once 'includes/PDOdb.php';

$id_usuario = $_SESSION['idusuario'] ?? null;
$id_notificacion = $_GET['id'] ?? null;

if ($id_usuario && $id_notificacion) {
    try {
        $stmt = $pdo->prepare("DELETE FROM notificaciones WHERE id = ? AND idusuario = ?");
        $stmt->execute([$id_notificacion, $id_usuario]);

        echo "ok";
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Error SQL: " . $e->getMessage();
    }
} else {
    http_response_code(400);
    echo "Faltan datos o sesión";
}
?>
