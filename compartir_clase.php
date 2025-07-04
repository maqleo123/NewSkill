<?php
// Activar errores (solo para desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include_once 'includes/PDOdb.php';
include_once 'notificaciones.php';
include_once 'includes/insignias_helper.php';

if (!isset($_SESSION['idusuario'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_clase = $_POST['id_clase'];
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $permiso = $_POST['permiso'];
    $id_emisor = $_SESSION['idusuario'];

    // Buscar el usuario al que se quiere compartir la clase
    $stmt = $pdo->prepare("SELECT idusuario FROM usuarios WHERE usuario = ?");
    $stmt->execute([$nombre_usuario]);
    $usuarioDestino = $stmt->fetch();

    if ($usuarioDestino) {
        $id_destino = $usuarioDestino['idusuario'];

        // Evitar compartirte a ti mismo
        if ($id_destino == $id_emisor) {
            echo "<script>alert('No puedes compartir una clase contigo mismo.'); window.history.back();</script>";
            exit;
        }

        // Verificar si ya está compartida
        $check = $pdo->prepare("SELECT COUNT(*) FROM repositorio WHERE id_usuario = ? AND id_clase = ?");
        $check->execute([$id_destino, $id_clase]);
        if ($check->fetchColumn() > 0) {
            echo "<script>alert('Ya compartiste esta clase con ese usuario.'); window.history.back();</script>";
            exit;
        }

        // Insertar en repositorio
        $stmt = $pdo->prepare("INSERT INTO repositorio (id_usuario, id_clase, permiso, fecha_agregado) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$id_destino, $id_clase, $permiso]);

        // Insertar en clases_compartidas
        $stmtCompartida = $pdo->prepare("INSERT INTO clases_compartidas (id_emisor, id_receptor, id_clase, fecha_compartida) VALUES (?, ?, ?, NOW())");
        $stmtCompartida->execute([$id_emisor, $id_destino, $id_clase]);

        asignarInsignia($pdo, $id_emisor, 'compartidas');
asignarInsignia($pdo, $id_destino, 'recibidas');






          // Obtener título de la clase
$stmtClase = $pdo->prepare("SELECT titulo FROM clases WHERE id_clase = ?");
$stmtClase->execute([$id_clase]);
$tituloClase = $stmtClase->fetchColumn();

// Crear notificación
$mensajeNotif = "Te han compartido la clase: $tituloClase ";
$urlNotif = "ver_clase.php?id=$id_clase";
crearNotificacion($id_destino, 'clase_compartida', $mensajeNotif, $urlNotif, $pdo);



   echo "<script>alert('Clase compartida correctamente con $nombre_usuario.'); window.history.back();</script>";

        exit;
    } else {
        echo "<script>alert('Usuario no encontrado.'); window.history.back();</script>";
        exit;
    }
} else {
echo "<script>window.history.back();</script>";
exit;

}
?>
