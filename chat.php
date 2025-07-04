<?php
session_start();
include("includes/PDOdb.php");

if (!isset($_SESSION['idusuario'])) {
    header("Location: index.php");
    exit();
}

$mi_id = $_SESSION['idusuario'];
$pdo = Conectarse();

if (!isset($_GET['id']) || !isset($_GET['i']) || !isset($_GET['user'])) {
    header("Location: usuarios.php");
    exit();
}

$otro_id = intval($_GET['id']);
$idUsuarioB = $otro_id;

// Obtener datos del usuario receptor
$stmt = $pdo->prepare("SELECT nombre, img_perfil FROM usuarios WHERE idusuario = :id");
$stmt->execute([':id' => $otro_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Usuario no encontrado.";
    exit();
}

// Obtener datos por hash para el perfil
$stmt = $pdo->prepare("SELECT idusuario, usuario, img_perfil FROM usuarios WHERE MD5(idusuario) = :id_encriptado");
$stmt->execute([':id_encriptado' => $_GET['i']]);
$f = $stmt->fetch(PDO::FETCH_ASSOC);

$idusuarioLimpio = $f ? $f['idusuario'] : null;
$Nombre = $f ? $f['usuario'] : "Usuario no encontrado";
$rutaimagen = $f ? $f['img_perfil'] : "ruta/por_defecto.jpg";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="imagenes/logons50.png">
    <title>Chat con <?= htmlspecialchars($usuario['nombre']) ?></title>
    <link rel="stylesheet" href="css/chat.css">
</head>
<body class="chat-body">
    <div class="chat-container">
        <header class="chat-header">
            <a href="javascript:history.back()" class="volver">←</a>
            <div class="perfil-info">
                <img src="<?= htmlspecialchars($usuario['img_perfil']) ?>" alt="Perfil" class="foto-perfil">
                <h3>
                    <a href="perfilUsuario.php?user=<?= urlencode($_GET['user']) ?>&UsuarioB=<?= urlencode($usuario['nombre']) ?>&idUsuarioB=<?= $otro_id ?>&i=<?= urlencode($_GET['i']) ?>" style="color:white; text-decoration:none;">
                        <?= htmlspecialchars($usuario['nombre']) ?>
                    </a>
                </h3>
            </div>
        </header>

        <div id="chat-box" class="chat-box"></div>

        <div class="input-area">
            <input type="text" id="mensaje" placeholder="Escribe un mensaje...">
            <button onclick="enviarMensaje()">→</button>
        </div>
    </div>

    <script>
        let receptorId = <?= $otro_id ?>;

        function enviarMensaje() {
            let mensaje = document.getElementById('mensaje').value;
            if (mensaje.trim() === '') return;

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "sendMessage.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("mensaje=" + encodeURIComponent(mensaje) + "&receptor=" + receptorId);
            document.getElementById('mensaje').value = "";
        }

        function cargarMensajes() {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "getMessages.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                document.getElementById("chat-box").innerHTML = this.responseText;
                const box = document.getElementById("chat-box");
                box.scrollTop = box.scrollHeight;
            };
            xhr.send("receptor=" + receptorId);
        }

        setInterval(cargarMensajes, 3000);
        window.onload = cargarMensajes;
    </script>
</body>
</html>
