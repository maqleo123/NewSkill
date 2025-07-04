<?php
include_once 'includes/session.php';
header('Content-Type: application/json');
include_once 'includes/PDOdb.php';

$busqueda = $_GET['q'] ?? '';
if (strlen($busqueda) < 2) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT idusuario, usuario, img_perfil FROM usuarios WHERE usuario LIKE ? LIMIT 5");
$stmt->execute(["%$busqueda%"]);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($usuarios);
