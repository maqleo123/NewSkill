<?php

include_once 'includes/PDOdb.php';

try {
    // Validar el parámetro 'i'
    if (!isset($_POST['i']) || empty($_POST['i'])) {
        die("El parámetro 'i' es obligatorio.");
    }

    $user= $_GET['user'];
    $idUsuario = $_GET['i'];

    // Preparar la consulta para obtener el usuario
    $query = "SELECT * FROM usuarios WHERE MD5(idusuario) = :idUsuario";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['idUsuario' => $idUsuario]);

    // Procesar los resultados
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $idusuarioLimpio = $usuario['idusuario'];
        $Nombre = $usuario['usuario'];
        $rutaimagen = $usuario['img_perfil'];
        $descripcion = $usuario['descripcion'];
    } else {
        die("No se encontró el usuario.");
    }

    // Verificar si se enviaron los datos necesarios para actualizar
    if (isset($_GET['descripcion'], $_GET['nombre'])) {
        $descripcion = htmlspecialchars($_GET['descripcion']);
        $nombre = htmlspecialchars($_GET['nombre']);

        // Preparar la consulta de actualización
        $sql = "UPDATE usuarios SET nombre = :nombre, descripcion = :descripcion WHERE id = :id";
        $stmt = $pdo->prepare($sql);

        // Ejecutar la consulta
        $stmt->execute([
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'id' => $idusuarioLimpio
        ]);

        header("Location: perfil.php?user=$user&i=$idUsuario");
        exit();
    } else {
        echo "No se recibieron todos los datos necesarios.";
    }
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}
?>
