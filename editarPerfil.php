<?php include_once 'includes/session.php'; ?>
<?php include_once 'includes/dbconexion.php'; ?>
<?php include_once 'includes/head.php'; ?>

<script>
    function abrirmontos(url) {
        window.open(url, '', 'top=50,left=30,width=1100,height=650');
    }

    
    window.onload = function () {
        setTimeout(function () {
            const mensaje = document.getElementById("mensaje-estado");
            if (mensaje) mensaje.style.display = 'none';
        }, 3000);
    }
</script>

<link rel="stylesheet" href="css/editarPerfil.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<body class="editar-body">
    <header class="editar-header">
        <p style="color: white;">Editar Perfil</p>
    </header>

    <?php
    $i = $_GET["i"];
    $query = "SELECT * FROM usuarios WHERE MD5(idusuario) = '$i'";
    $resultt = mysql_query($query, $conexion);

    while ($f = mysql_fetch_array($resultt)) {
        $idusuarioLimpio = $f["idusuario"];
        $Nombre = $f["usuario"];
        $rutaimagen = $f["img_perfil"];
        $descripcion = $f["descripcion"];
        $nivel = isset($f["nivel"]) ? $f["nivel"] : '';
        $telefono = isset($f["numero_telefono"]) ? $f["numero_telefono"] : '';
    }

    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'ok') {
            echo "<div id='mensaje-estado' style='color: green; text-align: center;'>Datos actualizados</div>";
        } else {
            echo "<div id='mensaje-estado' style='color: red; text-align: center;'>Error al actualizar</div>";
        }
    }
    ?>

    <form action="editarinformacion.php?user=<?= $_GET["user"]; ?>&i=<?= $_GET["i"] ?>" method="post">
        <div class="ePerfil-contenedor">
            <img src="<?= $rutaimagen ?>" style="width:100px; height: 100px; border-radius:50px">
            <div class="editar-nombre">
                <a href="javascript:abrirmontos('adjuntar2.php?user=<?= $_GET["user"]; ?>&i=<?= $_GET["i"] ?>')" style="color:#666; font-size:9px;">Editar Foto</a>
            </div>

            <div class="input-container">
                <i class="fas fa-user input-icon"></i>
                <input type="text" placeholder="Nombre de usuario" name="nombre" id="nombre" value="<?= $Nombre ?>">
            </div>

            <div class="input-container">
                <i class="fas fa-align-left input-icon"></i>
                <input type="text" placeholder="Descripción" name="descripcion" value="<?= $descripcion ?>">
            </div>

            <div class="input-container">
                <i class="fas fa-signal input-icon"></i>
                <input type="text" placeholder="Nivel" name="nivel" value="<?= $nivel ?>">
            </div>

            <div class="input-container">
                <i class="fas fa-phone input-icon"></i>
                <input type="text" placeholder="123 456 7890" name="telefono" value="<?= $telefono ?>">
            </div>

            <button type="submit">Actualizar</button>
        </div>
    </form>

    <?php include_once 'includes/header.php'; ?>
</body>
