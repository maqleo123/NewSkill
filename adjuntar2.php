<?php
include_once 'includes/dbconexion.php';
include_once 'includes/session.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NewSkill</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/adjuntar2.css">
</head>

<body>
    <div class="header">
       Actualizar foto de perfil
    </div>

    <?php
    $idUsuario = $_GET["i"];
    $query = "select * from usuarios where MD5(idusuario) = '$idUsuario'";
    $resultt = mysql_query($query, $conexion);

    while ($f = mysql_fetch_array($resultt)) {
        $idusuarioLimpio = $f["idusuario"];
        $Nombre = $f["usuario"];
    }
    ?>

    <div class="container">
        <img src="imagenes/LogoNS250.png" alt="Logo" class="logo">
        <h1>Subir foto de perfil<br><span><?= $Nombre ?> <br>(<?= $curp ?>)</span></h1>
        <form>
            <label for="fileToUpload">Subir archivo</label>
            <input type="file" id="fileToUpload" onchange="upload_image();">
            <p>Selecciona un archivo.</p>
            <div class="upload-msg"></div>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>
        function upload_image() {
            document.querySelector(".upload-msg").textContent = 'Cargando...';
            var inputFileImage = document.getElementById("fileToUpload");
            var file = inputFileImage.files[0];
            var data = new FormData();
            data.append('fileToUpload', file);

            $.ajax({
                url: "upload2.php?user=<?= $_GET["user"] ?>&i=<?= $_GET["i"] ?>&idLimpio=<?= $idusuarioLimpio ?>",
                type: "POST",
                data: data,
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    document.querySelector(".upload-msg").innerHTML = data;
                    window.setTimeout(function() {
                        var alertElem = document.querySelector(".alert-dismissible");
                        if (alertElem) {
                            alertElem.style.transition = "opacity 0.5s";
                            alertElem.style.opacity = 0;
                            setTimeout(() => alertElem.remove(), 500);
                        }
                    }, 5000);
                }
            });
        }
    </script>
</body>

</html>
