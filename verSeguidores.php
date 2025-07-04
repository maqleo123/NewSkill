<?php
session_start();
include_once 'includes/dbconexion.php';

// Verifica que el usuario esté logueado
if (!isset($_SESSION['idusuario'])) {
    header("Location: login.php");
    exit();
}

include_once 'includes/head.php';

$idusuario = intval($_GET['id']);
$user= $_GET['user'];
$i= $_GET['i'];

$query = "
SELECT u.usuario, u.img_perfil, u.idusuario 
FROM seguidores s 
JOIN usuarios u ON u.idusuario = s.id_usuario 
WHERE s.id_seguido = $idusuario
";

$consulta = mysql_query($query, $conexion);
?>

<link rel="stylesheet" href="css/seguidores.css">

<body class="body-perfil">
    <main class="main-perfil">
        <h2>Seguidores</h2>
        <ul class="lista-seguidores">
            <?php
            if (mysql_num_rows($consulta) === 0) {
                echo "<p>Este usuario no tiene seguidores.</p>";
            } else {
                while ($row = mysql_fetch_array($consulta)) {
                    echo "<li class='seguidor-item'>";
                    echo "<img src='{$row['img_perfil']}' width='40' alt='Perfil'>";
                   echo "<a href='perfilUsuario.php?user={$user}&UsuarioB={$row['usuario']}&idUsuarioB={$row['idusuario']}&i={$i}' style='color:white; text-decoration:none;'>{$row['usuario']}</a>";
                    echo "</li>";
                }
            }
            ?>
        </ul>
    </main>
</body>


