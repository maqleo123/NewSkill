<?php
include_once './includes/head.php';
?>

<link rel="stylesheet" href="css/registrar.css">

<body class="body-r1">

    <div class="login">
        <div class="login-texto">Registro NewSkill</div>
        <div class="login-imagen">
            <img src="imagenes/LogoNS250.png" alt="Logo NewSkill">
        </div>
    </div>

    <div class="incio-sesion">
        <form name="Registro" action="guardarNuevoUsuario.php" method="post">

            <div class="input-container">
                <ion-icon name="person-circle-outline" class="input-icon"></ion-icon>
                <input name="nombre" id="id_ipt_nombre" type="text" maxlength="50" placeholder="Nombre" />
            </div>

            <div class="input-container">
                <ion-icon name="person-outline" class="input-icon"></ion-icon>
                <input name="usuario" id="id_ipt_usuario" type="text" maxlength="50" placeholder="Usuario" />
            </div>

            <div class="input-container">
                <ion-icon name="lock-closed-outline" class="input-icon"></ion-icon>
                <input name="password" id="id_ipt_password" type="password" maxlength="50" placeholder="Contraseña" />
            </div>

            <div class="input-container">
                <ion-icon name="lock-closed-outline" class="input-icon"></ion-icon>
                <input name="password2" id="id_ipt_password2" type="password" maxlength="50" placeholder="Confirmar contraseña" />
            </div>

            <!-- Contenedor para error único -->
            <div id="error-mensajes" class="error server-error" style="display:none;"></div>

            <div class="submit-container">
                <input type="submit" name="registro" class="boton-entrar" value="Registrar">
            </div>
        </form>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
        document.querySelector('form[name="Registro"]').addEventListener('submit', function (e) {
            const nombre = document.getElementById('id_ipt_nombre');
            const usuario = document.getElementById('id_ipt_usuario');
            const password = document.getElementById('id_ipt_password');
            const password2 = document.getElementById('id_ipt_password2');
            const errorBox = document.getElementById('error-mensajes');

            // Limpiar errores previos
            [nombre, usuario, password, password2].forEach(el => el.classList.remove('input-error'));
            errorBox.style.display = 'none';
            errorBox.textContent = '';

            // Array con errores
            const errores = [];

            if (nombre.value.trim() === '') {
                errores.push({campo: nombre, mensaje: 'El nombre es obligatorio.'});
            }

            if (usuario.value.trim() === '') {
                errores.push({campo: usuario, mensaje: 'El usuario es obligatorio.'});
            }

            if (password.value.trim() === '') {
                errores.push({campo: password, mensaje: 'La contraseña es obligatoria.'});
            } else {
                const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
                if (!regex.test(password.value)) {
                    errores.push({campo: password, mensaje: 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número.'});
                }
            }

            if (password2.value.trim() === '') {
                errores.push({campo: password2, mensaje: 'Confirma tu contraseña.'});
            } else if (password.value !== password2.value) {
                errores.push({campo: password2, mensaje: 'Las contraseñas no coinciden.'});
            }

            if (errores.length > 0) {
                e.preventDefault();

                const primerError = errores[0];
                primerError.campo.classList.add('input-error');

                errorBox.textContent = primerError.mensaje;
                errorBox.style.display = 'block';

                // Opcional: hacer foco en el campo con error
                primerError.campo.focus();
            }
        });
    </script>

</body>
