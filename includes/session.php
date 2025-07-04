<?php
session_start();

// Validamos que estén definidas las variables necesarias
if (!isset($_SESSION['access']) || !isset($_GET['user'])) {
    header("Location: index.php");
    exit();
}

$sesion = $_SESSION['access']; // se supone que aquí guardaste md5 del usuario en login
$usuario = $_GET['user'];

// Comprobamos si coincide el hash con el usuario actual
if ($sesion !== md5($usuario)) {
    header("Location: index.php");
    exit();
}
?>
