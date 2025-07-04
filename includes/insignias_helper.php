<?php
function asignarInsignia($pdo, $idUsuario, $tipo) {
    $metas = [
        'compartidas' => [
            1 => 1,
            2 => 10,
            3 => 30,
            4 => 50,
            5 => 70,
            6 => 100
        ],
        'recibidas' => [
            7 => 1,
            8 => 10,
            9 => 30,
            10 => 50,
            11 => 70,
            12 => 100
        ]
    ];

    // Elegimos el campo correcto según el tipo
    $campo = ($tipo === 'compartidas') ? 'id_emisor' : 'id_receptor';

    // Contamos las clases compartidas o recibidas desde la tabla clases_compartidas
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM clases_compartidas WHERE $campo = ?");
    $stmt->execute([$idUsuario]);
    $total = (int) $stmt->fetchColumn();

    // Obtenemos las insignias que ya tiene el usuario
    $insigniasActuales = $pdo->prepare("SELECT id_insignia FROM usuarios_insignias WHERE id_usuario = ?");
    $insigniasActuales->execute([$idUsuario]);
    $existentes = array_column($insigniasActuales->fetchAll(PDO::FETCH_ASSOC), 'id_insignia');

    // Asignamos nuevas insignias si cumple con los requisitos
    foreach ($metas[$tipo] as $idInsignia => $requerido) {
        if ($total >= $requerido && !in_array($idInsignia, $existentes)) {
            $insert = $pdo->prepare("INSERT INTO usuarios_insignias (id_usuario, id_insignia, fecha_obtenida) VALUES (?, ?, NOW())");
            $insert->execute([$idUsuario, $idInsignia]);
        }
    }
}
?>
