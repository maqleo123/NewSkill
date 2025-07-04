<?php
// index.php
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>NewSkill - Cargando</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="css/carga.css" />
</head>

<body>

    <div class="loader-container">
        <img src="imagenes/LogoNS.png" alt="Logo NewSkill" class="logo" />
        <h1 class="titulo">NewSkill</h1>

        <div class="loader-bar">
            <div class="progress" id="progress"></div>
        </div>
        <div class="porcentaje" id="porcentaje">0%</div>
    </div>

    <script>
        let progreso = 0;
        const porcentaje = document.getElementById('porcentaje');
        const progressBar = document.getElementById('progress');

        let intervalo = setInterval(() => {
            if (progreso < 100) {
                progreso++;
                porcentaje.textContent = progreso + "%";
                progressBar.style.width = progreso + "%";
            } else {
                clearInterval(intervalo);
                setTimeout(() => {
                    window.location.href = "login.php";
                }, 500);
            }
        }, 30);
    </script>

</body>

</html>
