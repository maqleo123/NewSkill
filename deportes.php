<?php
include_once 'includes/session.php';
include_once 'includes/PDOdb.php';
include_once 'includes/head.php';
?>

<?php
$stmt = $pdo->prepare("
    SELECT idusuario, usuario, img_perfil 
    FROM usuarios 
    WHERE MD5(idusuario) = :id_encriptado
");
$idEncriptado = $_GET['i'];
$stmt->execute([':id_encriptado' => $idEncriptado]);
if ($f = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $idusuarioLimpio = $f['idusuario'];
    $Nombre = $f['usuario'];
    $rutaimagen = $f['img_perfil'];
} else {
    $idusuarioLimpio = null;
    $Nombre = "Usuario no encontrado";
    $rutaimagen = "ruta/por_defecto.jpg";
}
?>

<?php
$stmt = $pdo->query("
    SELECT posts.*, usuarios.usuario AS usuario_nombre, usuarios.img_perfil AS usuario_foto
    FROM posts
    JOIN usuarios ON posts.idusuario = usuarios.idusuario
    WHERE posts.skill = 'Deportes'
    ORDER BY posts.fecha DESC
");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/deportes.css">

<body class="videojuegos-body">
    <header class="header-Relaciones">
        <p class="videojuegos-title">Deportes</p>
    </header>

    <div class="publicaciones-container">
        <div class="publicar-contenedor">
            <div class="publicar-box">
                <a href="crearPost.php?user=<?= urlencode($_GET['user']); ?>&i=<?= urlencode($_GET['i']); ?>&or=deportes" class="publicar-link">
                    <img src="<?= htmlspecialchars($rutaimagen) ?>" alt="Foto perfil usuario" class="publicar-img">
                </a>
            </div>
        </div>

        <?php
        function mostrarComentarios($pdo, $idPublicacion, $idPadre = null, $margen = 0)
        {
            $stmt = $pdo->prepare("
                SELECT c.id, c.comentario, c.fecha, u.usuario, u.img_perfil, u.idusuario
                FROM comentarios c
                JOIN usuarios u ON c.id_usuario = u.idusuario
                WHERE c.id_publicacion = ? AND " . ($idPadre === null ? "c.id_padre IS NULL" : "c.id_padre = ?") . "
                ORDER BY c.fecha ASC
            ");
            $idPadre === null ? $stmt->execute([$idPublicacion]) : $stmt->execute([$idPublicacion, $idPadre]);

            while ($coment = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="comentario" style="margin-left: ' . $margen . 'px;">';
                echo '<div class="comentario-flex">';
                echo '<img src="' . htmlspecialchars($coment['img_perfil']) . '" class="comentario-img">';
                echo '<div>';
                echo '<strong><a href="perfilUsuario.php?user=' . htmlspecialchars($_GET['user']) .
                    '&UsuarioB=' . htmlspecialchars($coment['usuario']) .
                    '&idUsuarioB=' . htmlspecialchars($coment['idusuario']) .
                    '&i=' . htmlspecialchars($_GET['i']) . '" class="comentario-usuario">' .
                    htmlspecialchars($coment['usuario']) . '</a></strong><br>';
                echo '<span>' . htmlspecialchars($coment['comentario']) . '</span><br>';
                echo '<small class="comentario-fecha">' . $coment['fecha'] . '</small>';
                echo '<div>';
                echo '<button onclick="mostrarRespuesta(' . $coment['id'] . ')" class="btn-responder">Responder</button>';
                echo '<div id="respuesta-' . $coment['id'] . '" class="respuesta-form" style="display:none;">';
                echo '<form action="comentar.php?user=' . urlencode($_GET["user"]) . '&i=' . urlencode($_GET["i"]) . '&or=deportes" method="POST">';
                echo '<input type="hidden" name="id_publicacion" value="' . $idPublicacion . '">';
                echo '<input type="hidden" name="id_padre" value="' . $coment['id'] . '">';
                echo '<textarea name="comentario" rows="2" required class="textarea-respuesta" placeholder="Responder..."></textarea>';
                echo '<button type="submit" class="btn-enviar">Enviar respuesta</button>';
                echo '</form>';
                echo '</div></div></div></div></div>';
                mostrarComentarios($pdo, $idPublicacion, $coment['id'], $margen + 25);
            }
        }
        ?>

        <?php foreach ($posts as $post): ?>
            <div class="post-card" id="<?= $post['idPost']; ?>">
                <div class="user-info">
                    <img src="<?= htmlspecialchars($post['usuario_foto']); ?>" alt="Foto de perfil" class="profile-pic">
                    <div>
                        <h3 class="username">
                            <a href="perfilUsuario.php?user=<?= urlencode($_GET['user']) ?>&UsuarioB=<?= urlencode($post['usuario_nombre']) ?>&idUsuarioB=<?= $post['idusuario'] ?>&i=<?= urlencode($_GET['i']) ?>"
                                class="username-link">
                                <?= htmlspecialchars($post['usuario_nombre']); ?>
                            </a>
                        </h3>
                        <span class="skill">Habilidad: <?= nl2br(htmlspecialchars($post['skill'])); ?></span>
                    </div>
                </div>

                <strong class="titulo"><?= htmlspecialchars($post['titulo']); ?></strong>
                <p class="post-content"><?= nl2br(htmlspecialchars($post['contenido'])); ?></p>

                <!-- Reacciones -->
                <?php
                $tiposReaccion = [
                    'like' => '👍',
                    'love' => '❤️',
                    'haha' => '😂',
                    'sad' => '😢',
                    'angry' => '😡'
                ];

                $stmtReacciones = $pdo->prepare("
                    SELECT tipo_reaccion, COUNT(*) AS total
                    FROM reacciones
                    WHERE id_publicacion = ?
                    GROUP BY tipo_reaccion
                ");
                $stmtReacciones->execute([$post['idPost']]);
                $reacciones = $stmtReacciones->fetchAll(PDO::FETCH_ASSOC);

                $stmtUserReact = $pdo->prepare("
                    SELECT tipo_reaccion
                    FROM reacciones
                    WHERE id_publicacion = ? AND id_usuario = ?
                    LIMIT 1
                ");
                $stmtUserReact->execute([$post['idPost'], $idusuarioLimpio]);
                $reaccionUsuario = $stmtUserReact->fetchColumn();
                ?>

                <div class="reacciones-contenedor">
                    <?php foreach ($tiposReaccion as $tipo => $emoji):
                        $cantidad = 0;
                        foreach ($reacciones as $r) {
                            if ($r['tipo_reaccion'] === $tipo) {
                                $cantidad = $r['total'];
                                break;
                            }
                        }
                        $seleccionado = ($reaccionUsuario === $tipo) ? "reaccion-seleccionada" : "";
                        $enlace = "reaccionarDeportes.php?post=" . $post['idPost'] . "&tipo=" . urlencode($tipo) . "&user=" . urlencode($_GET['user']) . "&i=" . urlencode($_GET['i']);
                    ?>
                        <a href="<?= $enlace ?>" class="btn-reaccion <?= $seleccionado ?>">
                            <?= $emoji ?> <span><?= $cantidad ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="post-footer">
                    <span class="post-date"><?= $post['fecha']; ?></span>
                    <button class="contact-btn">
                        <a href="chat.php?id=<?= $post['idusuario'] ?>&user=<?= urlencode($_GET['user']) ?>&UsuarioB=<?= urlencode($post['usuario_nombre']) ?>&idUsuarioB=<?= $post['idusuario'] ?>&i=<?= urlencode($_GET['i']) ?>"
                            class="contact-link">Contactar</a>
                    </button>
                </div>

                <button onclick="toggleComentarios(<?= $post['idPost'] ?>)" class="btn-ver-comentarios">💬 Ver comentarios</button>

                <div id="comentarios-<?= $post['idPost'] ?>" class="comentarios-contenedor" style="display:none;">
                    <h4 class="comentarios-titulo">Comentarios:</h4>
                    <?php mostrarComentarios($pdo, $post['idPost']); ?>

                    <form action="comentar.php?user=<?= urlencode($_GET['user']) ?>&i=<?= urlencode($_GET['i']) ?>&or=deportes" method="POST" class="form-comentario">
                        <input type="hidden" name="id_publicacion" value="<?= $post['idPost'] ?>">
                        <textarea name="comentario" rows="2" placeholder="Escribe tu comentario..." required class="textarea-comentario"></textarea><br>
                        <button type="submit" class="btn-comentar">Comentar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        function toggleComentarios(idPost) {
            const contenedor = document.getElementById('comentarios-' + idPost);
            if (contenedor.style.display === 'none') {
                contenedor.style.display = 'block';
            } else {
                contenedor.style.display = 'none';
            }
        }

        function mostrarRespuesta(idComentario) {
            const respuestaForm = document.getElementById('respuesta-' + idComentario);
            respuestaForm.style.display = respuestaForm.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
<?php include_once 'includes/header.php'; ?>
</html>
