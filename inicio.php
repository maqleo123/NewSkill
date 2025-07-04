<?php
include_once 'includes/dbconexion.php';
include_once 'includes/head.php';

$modoTema = 'oscuro';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['idusuario'])) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=newskillcom_newskill;charset=utf8", "newskillcom_santy", "Diciembre1224#*");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT modo_tema FROM usuarios WHERE idusuario = ?");
        $stmt->execute([$_SESSION['idusuario']]);
        $modoTemaDB = $stmt->fetchColumn();
        if ($modoTemaDB === 'claro') {
            $modoTema = 'claro';
        }
    } catch (PDOException $e) {
        $modoTema = 'oscuro';
    }
}
?>

<link rel="stylesheet" href="css/inicioo.css">

<!-- Aquí sí se aplica bien la clase -->

<body class="body-inicio <?= $modoTema === 'claro' ? 'claro' : '' ?>">
    <header class="header-foro" id="Home">
        <h1>Foro de Aprendizaje</h1>
    </header>

    <section class="portfolio" id="Portfolio">
        <div class="portfolio_project-container">
            <div class="portfolio_project programacion" style="background-image: linear-gradient(#0009, #0009), url(imagenes/pexels-markusspiske-965345.jpg)">
                <h2><a href="programacion.php?user=<?= $_GET["user"]; ?>&i=<?= $_GET["i"]; ?>">Programación</a></h2>
            </div>
            <div class="portfolio_project deportes" style="background-image: linear-gradient(#0009, #0009), url(imagenes/freepik__candid-image-photography-natural-textures-highly-r__56599.jpeg)">
                <h2><a href="deportes.php?user=<?= $_GET["user"]; ?>&i=<?= $_GET["i"]; ?>">Deportes</a></h2>
            </div>
            <div class="portfolio_project artes" style="background-image: linear-gradient(#0009, #0009), url(imagenes/pexels-cottonbro-3777876.jpg)">
                <h2><a href="artes.php?user=<?= $_GET["user"]; ?>&i=<?= $_GET["i"]; ?>">Artes</a></h2>
            </div>
            <div class="portfolio_project matematicas" style="background-image: linear-gradient(#0009, #0009), url(imagenes/pexels-silverkblack-22690752.jpg)">
                <h2><a href="matematicas.php?user=<?= $_GET["user"]; ?>&i=<?= $_GET["i"]; ?>">Matemáticas</a></h2>
            </div>
            <div class="portfolio_project videojuegos" style="background-image: linear-gradient(#0009, #0009), url(imagenes/pexels-lulizler-3165335.jpg)">
                <h2><a href="videojuegos.php?user=<?= $_GET["user"]; ?>&i=<?= $_GET["i"]; ?>">Videojuegos</a></h2>
            </div>
        </div>
    </section>

    <?php include_once 'includes/header.php'; ?>
</body>