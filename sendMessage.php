<?php
session_start();
include("includes/PDOdb.php"); // Usa el que tiene tu conexión PDO
include("notificaciones.php"); // Para usar crearNotificacion()

$emisor = $_SESSION['idusuario'];
$receptor = $_POST['receptor'];
$mensaje = trim($_POST['mensaje']);

if (!empty($mensaje)) {
    try {
        // Insertar mensaje en la base de datos
        $stmt = $pdo->prepare("INSERT INTO mensajes (id_emisor, id_receptor, mensaje, created_at, visto) VALUES (?, ?, ?, NOW(), 0)");
        $stmt->execute([$emisor, $receptor, $mensaje]);

       // Obtener nombre del emisor (opcional si ya lo tienes en $_SESSION)
$stmtNombre = $pdo->prepare("SELECT usuario FROM usuarios WHERE idusuario = ?");
$stmtNombre->execute([$emisor]);
$nombre_emisor = $stmtNombre->fetchColumn();

// Obtener nombre del receptor
$stmtReceptor = $pdo->prepare("SELECT usuario FROM usuarios WHERE idusuario = ?");
$stmtReceptor->execute([$receptor]);
$nombre_receptor = $stmtReceptor->fetchColumn();

// Generar hash 'i' en base al receptor
$i = md5($receptor);

// Generar URL de redirección para el chat
$url_chat = "https://newskill.com.mx/chat.php?id=$emisor&user=$nombre_receptor&UsuarioB=$nombre_emisor&idUsuarioB=$emisor&i=$i";

// Crear notificación para el receptor
crearNotificacion(
    $receptor,
    'mensaje',
    "$nombre_emisor te envió un mensaje",
    $url_chat,
    $pdo
);


    } catch (PDOException $e) {
        echo "Error al enviar mensaje: " . $e->getMessage();
    }
}
?>
