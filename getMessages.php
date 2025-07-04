<?php
session_start();
include("includes/dbconexion.php");

$conexion = Conectarse();

$mi_id = $_SESSION['idusuario'];
$otro_id = $_POST['receptor'];

// Marcar como visto todos los mensajes entrantes aún no vistos
mysql_query("UPDATE mensajes SET visto = 1 WHERE id_emisor = $otro_id AND id_receptor = $mi_id AND visto = 0", $conexion);

// Obtener mensajes entre ambos
$result = mysql_query("SELECT * FROM mensajes WHERE 
    (id_emisor = $mi_id AND id_receptor = $otro_id)
    OR (id_emisor = $otro_id AND id_receptor = $mi_id)
    ORDER BY created_at ASC", $conexion);

while ($row = mysql_fetch_assoc($result)) {
    $clase = ($row['id_emisor'] == $mi_id) ? "saliente" : "entrante";
    $hora = date('H:i', strtotime($row['created_at']));
    $visto = ($row['visto'] == 1) ? "<span class='visto'>✔✔</span>" : "<span class='no-visto'>✔</span>";

    echo "<div class='mensaje $clase'>";
    echo htmlspecialchars($row['mensaje']);
    echo "<div class='meta'>$hora";

    // Solo mostrar 'visto' si yo soy el emisor
    if ($clase == "saliente") {
        echo " $visto";
    }

    echo "</div></div>";
}
?>
