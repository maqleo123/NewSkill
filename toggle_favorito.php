<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once 'includes/PDOdb.php';

if (!isset($_SESSION['idusuario']) || !isset($_POST['id_clase'])) {
    echo json_encode(['success' => false, 'message' => 'Datos faltantes']);
    exit;
}

$id_usuario = $_SESSION['idusuario'];
$id_clase = $_POST['id_clase'];

// Verificar si ya es favorita
$stmt = $pdo->prepare("SELECT id FROM favoritos WHERE id_usuario = ? AND id_clase = ?");
$stmt->execute([$id_usuario, $id_clase]);
$favorito = $stmt->fetch();

if ($favorito) {
    // Eliminar de favoritos
    $stmt = $pdo->prepare("DELETE FROM favoritos WHERE id = ?");
    $stmt->execute([$favorito['id']]);
    echo json_encode(['success' => true, 'es_favorita' => false]);
    exit;
} else {
    // Agregar a favoritos
    $stmt = $pdo->prepare("INSERT INTO favoritos (id_usuario, id_clase) VALUES (?, ?)");
    $stmt->execute([$id_usuario, $id_clase]);
    echo json_encode(['success' => true, 'es_favorita' => true]);
    exit;
}
