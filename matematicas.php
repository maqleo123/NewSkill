<?php
include_once 'includes/session.php';
include_once 'includes/PDOdb.php';
include_once 'includes/head.php';
?>

<?
// Preparar la consulta para buscar el usuario cuyo id coincide con el valor desencriptado de 'i'
$stmt = $pdo->prepare("
    SELECT idusuario, usuario, img_perfil 
    FROM usuarios 
    WHERE MD5(idusuario) = :id_encriptado
");

// Obtener el valor de 'i' desde la URL
$idEncriptado = $_GET['i'];

// Ejecutar la consulta pasando el valor encriptado
$stmt->execute([':id_encriptado' => $idEncriptado]);

// Obtener el resultado
if ($f = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $idusuarioLimpio = $f['idusuario']; // ID real del usuario
    $Nombre = $f['usuario'];           // Nombre del usuario
    $rutaimagen = $f['img_perfil'];    // Ruta de la imagen de perfil
} else {
    // Manejo de error si no se encuentra el usuario
    $idusuarioLimpio = null;
    $Nombre = "Usuario no encontrado";
    $rutaimagen = "ruta/por_defecto.jpg"; // Imagen por defecto
}
?>

<?php
// Consulta con JOIN para obtener los datos del usuario y los posts
$stmt = $pdo->query("
    SELECT posts.*, usuarios.usuario AS usuario_nombre, usuarios.img_perfil AS usuario_foto
    FROM posts
    JOIN usuarios ON posts.idusuario = usuarios.idusuario
    WHERE posts.skill = 'Matematicas'
    ORDER BY posts.fecha DESC
");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<body style="background-image: url(imagenes/pexels-silverkblack-22690752.jpg); background-size: cover; background-position: center;">
    <header class="header-Relaciones">
        <p style="color: white; font-family:sans-serif">Matematicas</p>
    </header>
    <div class="publicaciones-container">
        <div style="display: flex; flex-direction:row; align-items:center; justify-content:center; gap:3%; margin-top: 10px;"  >
            <div style=" background-color: rgba(200, 200, 200, 0.5); /* Gris claro con 50% de opacidad */
    padding: 20px; /* Añade espacio interno opcional */
    border-radius: 8px; /* Bordes redondeados opcionales */ width:min-content; height:fit-content; border: radius 50px; padding:10px;display: flex; flex-direction:row; align-items:center; justify-content:center; gap:3%; margin-top: 10px;">
            <img src="<?=$rutaimagen?>" alt="" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
              <a href="crearPost.php?user=<?=$_GET['user'];?>&i=<?=$_GET['i'];?>&or=matematicas" style="text-decoration: none; color:white; font-family:sans-serif">Publicar</a>
            </div>
        </div>
        <?php foreach ($posts as $post): ?>
            <?
                function mostrarComentarios($pdo, $idPublicacion, $idPadre = null, $margen = 0) {
                    $stmt = $pdo->prepare("
                        SELECT c.id, c.comentario, c.fecha, u.usuario, u.idusuario, u.img_perfil
                        FROM comentarios c
                        JOIN usuarios u ON c.id_usuario = u.idusuario
                        WHERE c.id_publicacion = ? AND ".($idPadre === null ? "c.id_padre IS NULL" : "c.id_padre = ?")."
                        ORDER BY c.fecha ASC
                    ");
                
                    if ($idPadre === null) {
                        $stmt->execute([$idPublicacion]);
                    } else {
                        $stmt->execute([$idPublicacion, $idPadre]);
                    }
                
                    while ($coment = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<div style="margin-left: '.$margen.'px; margin-bottom: 10px; background-color: #f2f2f2; padding: 10px; border-radius: 5px;">';
                        echo '<div style="display: flex; align-items: flex-start;">';
                        echo '<img src="'.htmlspecialchars($coment['img_perfil']).'" style="width: 35px; height: 35px; border-radius: 50%; margin-right: 10px;">';
                        echo '<div>';
                        echo '<strong><a href="perfilUsuario.php?user=' . htmlspecialchars($_GET['user']) .
    '&UsuarioB=' . htmlspecialchars($coment['usuario']) .
    '&idUsuarioB=' . htmlspecialchars($coment['idusuario']) .
    '&i=' . htmlspecialchars($_GET['i']) . '" style="text-decoration: none; color: #000; font-weight: bold; font-size: 16px;">' .
    htmlspecialchars($coment['usuario']) . '</a></strong><br>';
                        echo '<span>'.htmlspecialchars($coment['comentario']).'</span><br>';
                        echo '<small style="color:gray;">'.$coment['fecha'].'</small>';
                
                        // Botón para responder
                        echo '<div>';
                        echo '<button onclick="mostrarRespuesta('.$coment['id'].')" style="margin-top:5px; font-size: 12px;">Responder</button>';
                        echo '<div id="respuesta-'.$coment['id'].'" style="display:none; margin-top:5px;">';
                        echo '<form action="comentar.php?user='.$_GET["user"].'&i='.$_GET["i"].'&or=matematicas" method="POST">';
                        echo '<input type="hidden" name="id_publicacion" value="'.$idPublicacion.'">';
                        echo '<input type="hidden" name="id_padre" value="'.$coment['id'].'">';
                        echo '<textarea name="comentario" rows="2" style="width:100%;" required placeholder="Responder..."></textarea>';
                        echo '<button type="submit" style="margin-top: 5px;">Enviar respuesta</button>';
                        echo '</form>';
                        echo '</div>';
                        echo '</div>';
                
                        echo '</div></div></div>';
                
                        // Mostrar respuestas recursivamente
                        mostrarComentarios($pdo, $idPublicacion, $coment['id'], $margen + 25);
                    }
                }
                
                
                
                
                
                
                
                
                ?>
        <div class="post-card" id="<?=$post['idPost'];?>" style="margin: 60px; max-width: 95%; height: 30%; background: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 20px; display: flex; flex-direction: column; margin-bottom:120px;">
            <div class="user-info" style="display: flex; align-items: center; margin-bottom: 15px;">
                <!-- Mostrar la foto de perfil -->
                <img src="<?php echo htmlspecialchars($post['usuario_foto']); ?>" alt="Foto de perfil" class="profile-pic" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
                <div>
                    <!-- Mostrar el nombre del usuario -->
                    <h3 class="username" style="margin: 0; font-size: 18px; font-weight: bold; color: #333;">
                    <a href="perfilUsuario.php?user=<?= $_GET['user'] ?>&UsuarioB=<?= $post['usuario_nombre'] ?>&idUsuarioB=<?= $post['idusuario'] ?>&i=<?= $_GET['i'] ?>"style="text-decoration: none; color: #000; font-weight: bold; font-size: 16px;"
                    ><?php echo htmlspecialchars($post['usuario_nombre']);?></a>
                    </h3>
                    <span class="skill" style="font-size: 14px; color: #777;">
                        Habilidad: <?php echo nl2br(htmlspecialchars($post['skill'])); ?>
                    </span>
                </div>
            </div>
            <strong class="titulo"><?php echo htmlspecialchars($post['titulo']); ?></strong>
            <p class="post-content" style="font-size: 16px; color: #555; line-height: 1.6; margin-bottom: 20px;">
                <?php echo nl2br(htmlspecialchars($post['contenido'])); ?>
            </p>
            <div class="post-footer" style="display: flex; justify-content: space-between; align-items: center;">
                <span class="post-date" style="font-size: 14px; color: #aaa;"><?php echo $post['fecha']; ?></span>
                <button class="contact-btn" style="background-color: #007bff; color: white; border: none; padding: 10px 15px; font-size: 14px; border-radius: 5px; cursor: pointer; transition: background-color 0.3s;"><a href="chat.php?id=<?= $post['idusuario'] ?>&user=<?= urlencode($_GET['user']) ?>&UsuarioB=<?= urlencode($post['usuario_nombre']) ?>&idUsuarioB=<?= $post['idusuario'] ?>&i=<?= urlencode($_GET['i']) ?>" style="color: white; text-decoration:none">Contactar</a></button>
            </div>
            <?php
// Obtener comentarios de este post
$stmtComentarios = $pdo->prepare("
    SELECT c.comentario, c.fecha, u.usuario, u.img_perfil
    FROM comentarios c
    JOIN usuarios u ON c.id_usuario = u.idusuario
    WHERE c.id_publicacion = ?
    ORDER BY c.fecha ASC
");
$stmtComentarios->execute([$post['idPost']]);
$comentarios = $stmtComentarios->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- COMENTARIOS -->
 <!-- BOTÓN para mostrar/ocultar comentarios -->
 <button onclick="toggleComentarios(<?= $post['idPost'] ?>)" style="margin-top: 15px; background-color: #eee; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer;">
    💬 Ver comentarios
</button>

<!-- CONTENEDOR de comentarios -->
<div id="comentarios-<?= $post['idPost'] ?>" style="display: none; margin-top: 15px; border-top: 1px solid #ccc; padding-top: 10px;">
<h4 style="font-family: sans-serif;">Comentarios:</h4>
<?php
    mostrarComentarios($pdo, $post['idPost']);
?>

<!-- Comentario principal -->
<form action="comentar.php?user=<?= $_GET['user'] ?>&i=<?= $_GET['i'] ?>&or=matematicas" method="POST" style="margin-top: 10px;">
    <input type="hidden" name="id_publicacion" value="<?= $post['idPost'] ?>">
    <textarea name="comentario" rows="2" placeholder="Escribe tu comentario..." required style="width: 100%;"></textarea><br>
    <button type="submit" style="margin-top: 5px;">Comentar</button>
</form>

</div>
        </div>
        <?php endforeach; ?>
    </div>

    <script>
function toggleComentarios(idPost) {
    const div = document.getElementById("comentarios-" + idPost);
    if (div.style.display === "none") {
        div.style.display = "block";
    } else {
        div.style.display = "none";
    }
}
</script>
<script>
function mostrarRespuesta(idComentario) {
    const div = document.getElementById("respuesta-" + idComentario);
    div.style.display = div.style.display === "none" ? "block" : "none";
}
</script>

</body>

<?php include_once 'includes/header.php'; ?>
</html>
