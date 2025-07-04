<?php
include_once 'includes/session.php';
include_once 'includes/PDOdb.php';

// Validar los parámetros GET
if (!isset($_GET['user'], $_GET['i']) || empty($_GET['user']) || empty($_GET['i'])) {
    die("Parámetros inválidos.");
}

$usuario = htmlspecialchars(trim($_GET['user'])); // Usuario
$hashId = htmlspecialchars(trim($_GET['i'])); // Hash del idusuario

// Verificar si se recibió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y sanitizar los datos del formulario
    $titulo = htmlspecialchars(trim($_POST['titulo']));
    $skill = htmlspecialchars(trim($_POST['skill']));
    $contenido = htmlspecialchars(trim($_POST['contenido']));

    try {
        // Verificar si el hash MD5 corresponde a algún idusuario en la tabla `usuarios`
        $stmt = $pdo->prepare("SELECT idusuario FROM usuarios WHERE MD5(idusuario) = :hash");
        $stmt->execute([':hash' => $hashId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            die("El ID proporcionado no es válido o el usuario no existe.");
        }

        $userId = $user['idusuario'];

        // Insertar el post con el ID del usuario
        $stmt = $pdo->prepare("INSERT INTO posts (idusuario, titulo, skill, contenido) VALUES (:idusuario, :titulo, :skill, :contenido)");
        $stmt->execute([
            ':idusuario' => $userId,
            ':titulo' => $titulo,
            ':skill' => $skill,
            ':contenido' => $contenido
        ]);

        // Obtener la URL de la página anterior (Referer) desde el formulario
        $redirectUrl = isset($_POST['redirect']) ? $_POST['redirect'] : 'programacion.php?user=' . $usuario . '&i=' . $hashId;

        // Redirigir a la página anterior o a la página predeterminada
        header("Location: $redirectUrl");
        exit();
    } catch (PDOException $e) {
        // Manejo de errores
        die("Error al guardar la publicación: " . $e->getMessage());
    }
} else {
    die("Método no permitido.");
}
?>
