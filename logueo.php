<?php
include_once 'includes/dbconexion.php';

$usuario = $_POST["usuario"];
$password = $_POST["password"];

$query = "SELECT * FROM usuarios WHERE usuario='$usuario' AND password='$password'";
$resultado = mysql_query($query, $conexion);

if (mysql_num_rows($resultado) > 0) {
    $datos = mysql_fetch_array($resultado);
    $idUsuario = trim($datos["idusuario"]); 

    session_start();
    $_SESSION["access"] = md5($usuario);
    $_SESSION["idusuario"] = $idUsuario;

    
    $hash = md5($idUsuario);

    header("Location: inicio.php?user=" . urlencode($usuario) . "&i=" . $hash);
} else {
    header("Location: login.php?r=error");
}
?>
