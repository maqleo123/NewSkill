<?php
include_once 'includes/session.php';
include_once 'includes/PDOdb.php';
include_once 'notificaciones.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $id_usuario = $_SESSION['idusuario'];
        $nombre_usuario = $_SESSION['usuario'];
        $id_publicacion = $_POST['id_publicacion'];
        $or = $_GET['or'];
        $comentario = trim($_POST['comentario']);
        $id_padre = isset($_POST['id_padre']) && is_numeric($_POST['id_padre']) ? $_POST['id_padre'] : null;
        $usuario_autor = $_POST['nombre_usuarioP'];
        $id_usuario_autor =$_POST['id_usuarioP'];
        $usuarioN = $_POST['nombreU'];       

        if (!empty($comentario)) {
            // Insertar comentario
            $stmt = $pdo->prepare("INSERT INTO comentarios (id_publicacion, id_usuario, comentario, fecha, id_padre) VALUES (?, ?, ?, NOW(), ?)");
            $stmt->execute([$id_publicacion, $id_usuario, $comentario, $id_padre]);

            // Obtener autor del post
            $stmtAutor = $pdo->prepare("SELECT idusuario  FROM posts WHERE idPost = ?");
            $stmtAutor->execute([$id_publicacion]);
            $id_autor = $stmtAutor->fetchColumn();
            // Notificar al autor del post
            if ($id_autor && $id_autor != $id_usuario) {
                crearNotificacion(
                    $id_autor,
                    'comentario',
                    $usuarioN . " comentó tu publicación: " . $comentario,
                    $or . ".php?user=" . $usuario_autor . "&tipo=" . $_GET['tipo']  ."&i=" . md5($key.$id_usuario_autor) . "#" . $id_publicacion,
                    $pdo
                );
            }

            // Si es una respuesta a otro comentario
            if ($id_padre) {
                $stmtPadre = $pdo->prepare("SELECT id_usuario FROM comentarios WHERE id_comentario = ?");
                $stmtPadre->execute([$id_padre]);
                $id_autor_padre = $stmtPadre->fetchColumn();

                // Notificar al autor del comentario padre (si no es él mismo ni el autor del post)
                if ($id_autor_padre && $id_autor_padre != $id_usuario && $id_autor_padre != $id_autor) {
                    crearNotificacion(
                        $id_autor_padre,
                        'respuesta',
                        $_SESSION['usuario'] . " respondió tu comentario.",
                        $or . ".php?user=" . $usuario_autor . "&tipo=" . $_GET['tipo'] .  "#" . $id_publicacion,
                        $pdo
                    );
                }
            }
        }

        // Redirigir de vuelta
        $paginas = ["artes", "deportes", "matematicas", "videojuegos", "programacion"];
        if (in_array($or, $paginas)) {
            header("Location: {$or}.php?user=" . $_GET['user'] . "&tipo=" . $_GET['tipo'] . "&i=" . $_GET['i'] . "#" . $id_publicacion);
            exit;
        } else {
            header("Location: programacion.php"); // fallback
            exit;
        }

    } catch (PDOException $e) {
        echo "Error al comentar: " . $e->getMessage();
    }
}
?>
