<?php
include_once './includes/head.php';
?>

<link rel="stylesheet" href="css/logueo.css">

<body>
    <div>
        <div>Login NewSkill</div>
        <div>
            <img src="imagenes/LogoNS250.png" alt="Logo NewSkill">
        </div>
    </div>

    <div>
        <form name="Registro" action="logueo.php" method="post">

            <div class="input-container">
                <ion-icon name="person" class="input-icon"></ion-icon>
                <input name="usuario" id="id_ipt_usuario" type="text" maxlength="50" placeholder="Usuario" />
            </div>

            <div class="input-container">
                <ion-icon name="lock-closed" class="input-icon"></ion-icon>
                <input name="password" id="id_ipt_password" type="password" maxlength="50" placeholder="Contraseña" />
            </div>

            <!-- Contenedor para mostrar un error global -->
            <div id="error-global" class="error server-error" style="display:none;"></div>

            <div class="input-container">
                <input type="submit" name="entrar" value="Entrar">
            </div>

            <div>
                Si no tiene cuenta <a href="registro.php">Regístrese aquí</a>
            </div>

        </form>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
        document.querySelector('form[name="Registro"]').addEventListener('submit', function(e) {
            const usuario = document.getElementById('id_ipt_usuario');
            const password = document.getElementById('id_ipt_password');
            const errorGlobal = document.getElementById('error-global');

            // Limpia estilos y error global
            usuario.classList.remove('input-error');
            password.classList.remove('input-error');
            errorGlobal.style.display = 'none';
            errorGlobal.textContent = '';

            // Array con posibles errores, cada uno con campo y mensaje
            const errores = [];

            if (usuario.value.trim() === '') {
                errores.push({campo: usuario, mensaje: 'El usuario es obligatorio'});
            }
            if (password.value.trim() === '') {
                errores.push({campo: password, mensaje: 'La contraseña es obligatoria'});
            }

            if (errores.length > 0) {
                e.preventDefault();

                // Mostrar solo el primer error del arreglo
                const primerError = errores[0];
                errorGlobal.textContent = primerError.mensaje;
                errorGlobal.style.display = 'block';

                // Añadir clase de error solo al campo que tiene el error visible
                primerError.campo.classList.add('input-error');
                primerError.campo.focus();
            }
        });
    </script>
</body>
