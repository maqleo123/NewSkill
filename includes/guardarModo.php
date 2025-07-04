<?php
session_start();
include_once 'includes/PDOdb.php';

if (!isset($_SESSION['idusuario']) || !isset($_POST['modo'])) {
    http_response_code(400);
    exit("Faltan datos.");
}

$modo = $_POST['modo'];
if (!in_array($modo, ['claro', 'oscuro'])) {
    http_response_code(400);
    exit("Modo inválido.");
}

$idusuario = $_SESSION['idusuario'];

$stmt = $pdo->prepare("UPDATE usuarios SET modo_tema = ? WHERE idusuario = ?");
if ($stmt->execute([$modo, $idusuario])) {
    echo "OK";
} else {
    http_response_code(500);
    echo "Error al guardar";
}
