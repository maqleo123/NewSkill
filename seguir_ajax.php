<?php
session_start();
include_once 'includes/dbconexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['idusuario']) || !isset($_GET['id'])) {
    echo json_encode(['error' => 'Acceso denegado']);
    exit();
}

$id_usuario = intval($_SESSION['idusuario']);
$id_seguido = intval($_GET['id']);

if ($id_usuario == $id_seguido) {
    echo json_encode(['error' => 'No puedes seguirte a ti mismo']);
    exit();
}

function crearNotificacion($idusuario, $tipo, $mensaje, $url, $conexion) {
    $idusuario = intval($idusuario);
    $tipo = mysql_real_escape_string($tipo, $conexion);
    $mensaje = mysql_real_escape_string($mensaje, $conexion);
    $url = mysql_real_escape_string($url, $conexion);

    $sql = "INSERT INTO notificaciones (idusuario, tipo, mensaje, url) 
            VALUES ($idusuario, '$tipo', '$mensaje', '$url')";
    mysql_query($sql, $conexion);
}

// Obtener nombre usuario que hace follow para el mensaje
$result = mysql_query("SELECT usuario FROM usuarios WHERE idusuario = $id_usuario", $conexion);
$nombre_usuario = ($row = mysql_fetch_assoc($result)) ? $row['usuario'] : 'Alguien';

// Verificar si ya sigue
$check = mysql_query("SELECT * FROM seguidores WHERE id_usuario = $id_usuario AND id_seguido = $id_seguido", $conexion);

if (mysql_num_rows($check) > 0) {
    // Dejar de seguir
    mysql_query("DELETE FROM seguidores WHERE id_usuario = $id_usuario AND id_seguido = $id_seguido", $conexion);
    $accion = 'seguir';

    $mensaje = "$nombre_usuario ha dejado de seguirte.";
    $url_notif = "https://newskill.com.mx/verSeguidores.php?id=$id_seguido";
    crearNotificacion($id_seguido, "seguidores", $mensaje, $url_notif, $conexion);

} else {
    // Seguir
    mysql_query("INSERT INTO seguidores (id_usuario, id_seguido) VALUES ($id_usuario, $id_seguido)", $conexion);
    $accion = 'siguiendo';

    $mensaje = "$nombre_usuario ha comenzado a seguirte.";
    $url_notif = "https://newskill.com.mx/verSeguidores.php?id=$id_seguido";
    crearNotificacion($id_seguido, "seguidores", $mensaje, $url_notif, $conexion);
}

// Obtener contadores actualizados
$seguidores = mysql_query("SELECT COUNT(*) as total FROM seguidores WHERE id_seguido = $id_seguido", $conexion);
$seguidores_count = mysql_fetch_assoc($seguidores)['total'];

$seguidos = mysql_query("SELECT COUNT(*) as total FROM seguidores WHERE id_usuario = $id_usuario", $conexion);
$seguidos_count = mysql_fetch_assoc($seguidos)['total'];

echo json_encode([
    'accion' => $accion,
    'seguidores_count' => $seguidores_count,
    'seguidos_count' => $seguidos_count,
]);
?>
