<?php
session_start();
include_once 'includes/PDOdb.php';

if (!isset($_GET['id'])) {
    echo "Clase no especificada.";
    exit();
}

$id_clase = $_GET['id'];

// Obtener clase y creador
$stmt = $pdo->prepare("
    SELECT c.*, u.usuario AS creador_nombre
    FROM clases c
    JOIN usuarios u ON c.id_creador = u.idusuario
    WHERE c.id_clase = ?
");
$stmt->execute([$id_clase]);
$clase = $stmt->fetch();

if (!$clase) {
    echo "Clase no encontrada.";
    exit();
}


// Obtener materiales
$stmt = $pdo->prepare("
    SELECT tipo, contenido
    FROM materiales_clase
    WHERE id_clase = ?
");
$stmt->execute([$id_clase]);
$materiales = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($clase['titulo']) ?> - NewSkill</title>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<style>
    body {
        background: #0f172a;
        color: #e2e8f0;
        font-family: sans-serif;
        padding: 20px;
    }
    a { color: #60a5fa; text-decoration: none; }
    .contenedor {
        max-width: 700px;
        margin: auto;
        background: #1e293b;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(100, 149, 237, 0.1);
    }
    h1 { color: #93c5fd; }
    .material {
        margin-bottom: 25px;
    }
    img {
        max-width: 100%;
        border-radius: 8px;
    }
    video {
        width: 100%;
        border-radius: 8px;
    }
    iframe {
        width: 100%;
        height: 500px;
        border: none;
    }
</style>
</head>
<body>
<div class="contenedor">
    <h1><?= htmlspecialchars($clase['titulo']) ?></h1>
    <p><strong>Por:</strong> <?= htmlspecialchars($clase['creador_nombre']) ?></p>
    <p><strong>Descripción:</strong></p>
<div class="ql-editor" style="background:white; color:black; padding:10px; border-radius:8px;">
  <?= $clase['descripcion']; ?>
</div>


    <hr>

    <h2>Materiales</h2>

    <?php if ($materiales): ?>
        <?php foreach ($materiales as $m): ?>
            <div class="material">
                <?php
                $tipo = $m['tipo'];
                $archivo = htmlspecialchars($m['contenido']);

                if ($tipo === 'imagen') {
                    echo "<img src='$archivo' alt='Imagen'>";
                } elseif ($tipo === 'pdf') {
                    echo "<iframe src='$archivo'></iframe>";
                } elseif ($tipo === 'video') {
                    echo "<video controls><source src='$archivo' type='video/mp4'>Tu navegador no soporta video.</video>";
                } else {
                    echo "<a href='$archivo' download>Descargar archivo</a>";
                }
                ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay materiales en esta clase.</p>
    <?php endif; ?>
</div>
</body>
</html>
