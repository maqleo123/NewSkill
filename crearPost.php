<?php
include_once 'includes/session.php';
include_once 'includes/PDOdb.php';
include_once 'includes/head.php';

if (!isset($_GET['user'], $_GET['i']) || empty($_GET['user']) || empty($_GET['i'])) {
   die("Parámetros inválidos.");
}

$usuario = htmlspecialchars(trim($_GET['user']));
$hashId = htmlspecialchars(trim($_GET['i']));
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'programacion.php';

$fondo = "default.jpg";
if ($_GET["or"] == "artes") $fondo = "pexels-cottonbro-3777876.jpg";
if ($_GET["or"] == "deportes") $fondo = "freepik__candid-image-photography-natural-textures-highly-r__56599.jpeg";
if ($_GET["or"] == "matematicas") $fondo = "pexels-silverkblack-22690752.jpg";
if ($_GET["or"] == "videojuegos") $fondo = "pexels-lulizler-3165335.jpg";
if ($_GET["or"] == "programacion") $fondo = "pexels-markusspiske-965345.jpg";
?>

<link rel="stylesheet" href="css/crearpost.css">

<body class="crearpost-body" style="background-image: url('imagenes/<?= $fondo ?>');">
   <form action="guardarPost.php?user=<?= htmlspecialchars($usuario); ?>&i=<?= htmlspecialchars($hashId); ?>" method="POST" class="crearpost-form">

      <label for="titulo">Título:</label>
      <input type="text" id="titulo" name="titulo" required>

      <label for="skill">Skill:</label>
      <input type="text" id="skill" name="skill" value="<?= $_GET["or"] ?>" readonly>

      <label for="contenido">Contenido:</label>
      <textarea id="contenido" name="contenido" rows="5" required></textarea>

      <input type="hidden" name="redirect" value="<?= htmlspecialchars($referer); ?>">

      <button type="submit">Publicar</button>
   </form>
</body>
<?php include_once 'includes/header.php'; ?>
</html>
