<?php
include_once 'includes/session.php';
include_once 'includes/dbconexion.php';

$i = $_GET["i"];

$nombre = $_POST["nombre"];
$descripcion = $_POST["descripcion"];
$nivel = $_POST["nivel"];
$telefono = $_POST["telefono"];

$query = "UPDATE usuarios SET 
    usuario = '$nombre', 
    descripcion = '$descripcion', 
    nivel = '$nivel',
    numero_telefono = '$telefono'
    WHERE MD5(idusuario) = '$i'";

if (mysql_query($query, $conexion)) {
    header("Location: editarPerfil.php?user=".$_GET["user"]."&i=$i&status=ok");
} else {
    header("Location: editarPerfil.php?user=".$_GET["user"]."&i=$i&status=error");
}
?>
