<?php
session_start();
header('Content-Type: application/json');
include_once 'includes/PDOdb.php';

if (!isset($_SESSION['idusuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$idpost = $_POST['id_post'];
$comentario = trim($_POST['comentario']);
$idusuario = $_SESSION['idusuario'];

if (empty($comentario)) {
    echo json_encode(['success' => false, 'message' => 'Comentario vacío']);
    exit;
}

// Insertar comentario
$stmt = $pdo->prepare("INSERT INTO comentarios (idpost, idusuario, comentario, fecha) VALUES (?, ?, ?, NOW())");
$stmt->execute([$idpost, $idusuario, $comentario]);

// Obtener nombre del usuario y fecha
$stmtUser = $pdo->prepare("SELECT usuario FROM usuarios WHERE idusuario = ?");
$stmtUser->execute([$idusuario]);
$usuario = $stmtUser->fetchColumn();

echo json_encode([
    'success' => true,
    'usuario' => $usuario,
    'comentario' => htmlspecialchars($comentario),
    'fecha' => date('Y-m-d H:i:s')
]);
?>
