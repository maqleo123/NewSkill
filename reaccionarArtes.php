<?php
include_once 'includes/session.php';
include_once 'includes/PDOdb.php';

if (!isset($_GET['post'], $_GET['tipo'], $_SESSION['idusuario'])) {
    header("Location: index.php");
    exit;
}

$idPublicacion = intval($_GET['post']);
$tipoReaccion = $_GET['tipo'];
$idUsuario = $_SESSION['idusuario'];

// Validar tipos de reacción permitidos
$tiposValidos = ['like', 'love', 'haha', 'sad', 'angry'];
if (!in_array($tipoReaccion, $tiposValidos)) {
    // Tipo inválido, redirigir o mostrar error
    header("Location: videojuegos.php?user=" . urlencode($_GET['user']) . "&i=" . urlencode($_GET['i']));
    exit;
}

// Verificar si ya reaccionó
$stmt = $pdo->prepare("SELECT * FROM reacciones WHERE id_publicacion = ? AND id_usuario = ?");
$stmt->execute([$idPublicacion, $idUsuario]);

if ($stmt->rowCount() > 0) {
    // Ya reaccionó → actualiza
    $stmt = $pdo->prepare("UPDATE reacciones SET tipo_reaccion = ?, fecha = NOW() WHERE id_publicacion = ? AND id_usuario = ?");
    $stmt->execute([$tipoReaccion, $idPublicacion, $idUsuario]);
} else {
    // No ha reaccionado → inserta
    $stmt = $pdo->prepare("INSERT INTO reacciones (id_usuario, id_publicacion, tipo_reaccion) VALUES (?, ?, ?)");
    $stmt->execute([$idUsuario, $idPublicacion, $tipoReaccion]);
}

// Redirigir de nuevo a la página original
$redirect = "artes.php?user=" . urlencode($_GET['user']) . "&i=" . urlencode($_GET['i']);
header("Location: $redirect");
exit;
?>
