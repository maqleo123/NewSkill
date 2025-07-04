<?php
// NO session_start()
// NO require de PDO

function crearNotificacion($idusuario, $tipo, $mensaje, $url, $pdo) {
    $sql = "INSERT INTO notificaciones (idusuario, tipo, mensaje, url)
            VALUES (:idusuario, :tipo, :mensaje, :url)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':idusuario' => $idusuario,
        ':tipo'      => $tipo,
        ':mensaje'   => $mensaje,
        ':url'       => $url
    ]);
}
