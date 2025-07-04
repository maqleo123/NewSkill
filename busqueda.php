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
        $modoTema = $stmt->fetchColumn() ?: 'oscuro';
    } catch (PDOException $e) {
        $modoTema = 'oscuro';
    }
}
?>
<link rel="stylesheet" href="css/busquedaa.css">

<body class="body-busqueda <?= $modoTema === 'claro' ? 'claro' : '' ?>">
    <header class="header-busqueda">
        <div class="buscar-container">
            <form action="busqueda.php?user=<?= $_GET["user"] ?>&i=<?= $_GET["i"] ?>" method="post">
                <input class="input-buscar" type="search" name="busuarios" list="listadoUsuarios" placeholder="Buscar usuarios..." onchange="this.form.submit()" />
                <datalist id="listadoUsuarios">
                    <?php
                    $queryConsultaUsuarios = "SELECT usuario FROM usuarios ORDER BY usuario ASC";
                    $resqcu = mysql_query($queryConsultaUsuarios, $conexion);
                    while ($row = mysql_fetch_assoc($resqcu)) {
                        echo '<option value="' . $row['usuario'] . '"/>';
                    }
                    ?>
                </datalist>
            </form>
        </div>
    </header>

    <main class="main-busqueda">
        <?php
        if (isset($_POST["busuarios"])) {
            $usuariobuscado = $_POST["busuarios"];
            $querybuscausuario = "SELECT * FROM usuarios WHERE usuario = '$usuariobuscado'";
            $consulta = mysql_query($querybuscausuario, $conexion);

            while ($row = mysql_fetch_array($consulta)) {
                $idusuarioencontrado = $row["idusuario"];
                $usuarioencontrado = $row["usuario"];
                $nombreencontrado = $row["nombre"];
                $fotoPerfilUsuario = $row["img_perfil"];
        ?>
                <div class="usuario-card">
                    <img src="<?= $fotoPerfilUsuario ?>" alt="Foto de perfil" class="perfil-img">
                    <div class="usuario-info">
                        <a href="perfilUsuario.php?user=<?= $_GET['user'] ?>&UsuarioB=<?= $usuarioencontrado ?>&idUsuarioB=<?= $idusuarioencontrado ?>&i=<?= $_GET['i'] ?>">
                            <?= $usuarioencontrado ?>
                        </a>
                    </div>
                </div>
        <?php
            }
        }
        ?>
    </main>

    <!-- Modal para imagen expandida -->
    <div id="modalImagen" class="modal" onclick="cerrarModal(event)">
        <span class="cerrar" onclick="cerrarModal(event)">&times;</span>
        <img class="modal-contenido" id="imgExpandida">
    </div>

    <?php include_once 'includes/header.php'; ?>

    <script>
        function expandirImagen(src) {
            const modal = document.getElementById("modalImagen");
            const img = document.getElementById("imgExpandida");
            modal.style.display = "flex";
            img.src = src;
        }

        function cerrarModal(e) {
            if (e.target.id === "modalImagen" || e.target.classList.contains("cerrar")) {
                document.getElementById("modalImagen").style.display = "none";
            }
        }
    </script>
</body>