<?php
include_once 'includes/dbconexion.php';
include_once 'includes/session.php';

$id_usuario = $_SESSION['idusuario'];
$id_seguido = $_GET['id']; // ID del usuario que se quiere seguir/dejar de seguir

// Verificamos si ya lo sigue
$check = mysql_query("SELECT * FROM seguidores WHERE id_usuario = $id_usuario AND id_seguido = $id_seguido", $conexion);

if (mysql_num_rows($check) > 0) {
    // Ya lo sigue, entonces dejar de seguir
    mysql_query("DELETE FROM seguidores WHERE id_usuario = $id_usuario AND id_seguido = $id_seguido", $conexion);
    echo "Dejaste de seguir";
} else {
    // No lo sigue, entonces seguir
    mysql_query("INSERT INTO seguidores (id_usuario, id_seguido) VALUES ($id_usuario, $id_seguido)", $conexion);
    echo "Ahora sigues a este usuario";
}
?>
