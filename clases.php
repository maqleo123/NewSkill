<?php
session_start();
include("includes/PDOdb.php");

// Verificar sesión
if (!isset($_SESSION['idusuario'])) {
    header("Location: index.php");
    exit();
}

$mi_id = $_SESSION['idusuario'];
$modoTema = 'oscuro';

// Obtener modo del tema del usuario
try {
    $pdo = Conectarse(); // Asegúrate de que retorna PDO
    $stmt = $pdo->prepare("SELECT modo_tema FROM usuarios WHERE idusuario = ?");
    $stmt->execute([$mi_id]);
    $modoTema = $stmt->fetchColumn() ?: 'oscuro';
} catch (PDOException $e) {
    $modoTema = 'oscuro';
}

// Obtener lista de usuarios
$stmt = $pdo->prepare("SELECT idusuario, nombre, img_perfil FROM usuarios WHERE idusuario != :mi_id");
$stmt->execute([':mi_id' => $mi_id]);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener nombre e imagen del usuario en sesión
$stmt = $pdo->prepare("SELECT idusuario, usuario, img_perfil FROM usuarios WHERE MD5(idusuario) = :id_encriptado");
$stmt->execute([':id_encriptado' => $_GET['i']]);
if ($f = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $idusuarioLimpio = $f['idusuario'];
    $Nombre = $f['usuario'];
    $rutaimagen = $f['img_perfil'];
} else {
    $idusuarioLimpio = null;
    $Nombre = "Usuario no encontrado";
    $rutaimagen = "ruta/por_defecto.jpg";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="imagenes/logons50.png">
    <title>Usuarios</title>
    <link rel="stylesheet" href="css/usuariosChatt.css">
</head>

<body class="usuarios-body <?= $modoTema === 'claro' ? 'claro' : '' ?>">

    <header class="usuarios-header">
        <h2>Usuarios disponibles</h2>
    </header>

    <div class="usuarios-container">
        <?php foreach ($usuarios as $u): ?>
            <div class="usuario">
                <a href="chat.php?id=<?= htmlspecialchars($u['idusuario']) ?>&user=<?= urlencode($_GET['user']) ?>&UsuarioB=<?= urlencode($u['nombre']) ?>&idUsuarioB=<?= $u['idusuario'] ?>&i=<?= urlencode($_GET['i']) ?>">
                    <img src="<?= htmlspecialchars($u['img_perfil']) ?>" alt="Foto de <?= htmlspecialchars($u['nombre']) ?>" class="foto-perfil">
                    <?= htmlspecialchars($u['nombre']) ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <?php include_once 'includes/header.php'; ?>
</body>

</html>