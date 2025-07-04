<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'includes/dbconexion.php'; // usa mysql_*

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $token = trim($_POST['token']);
    $password = trim($_POST['password']);
    $confirmar = trim($_POST['confirmar_password']);

    if (empty($email) || empty($token) || empty($password) || empty($confirmar)) {
        $mensaje = "Todos los campos son obligatorios.";
    } elseif ($password !== $confirmar) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        $email_esc = mysql_real_escape_string($email);
        $token_esc = mysql_real_escape_string($token);

        $query = "SELECT * FROM usuarios WHERE email='$email_esc' AND reset_token='$token_esc'";
        $resultado = mysql_query($query, $conexion);

        if (mysql_num_rows($resultado) > 0) {
            // Guardar contraseña sin cifrado
            $nuevaPassword = mysql_real_escape_string($password);
            $update = "UPDATE usuarios SET password='$nuevaPassword', reset_token=NULL, reset_expira=NULL WHERE email='$email_esc'";
            if (mysql_query($update, $conexion)) {
                $mensaje = "✅ Contraseña actualizada correctamente.";
            } else {
                $mensaje = "❌ Error al actualizar la contraseña.";
            }
        } else {
            $mensaje = "❌ Correo o token inválido.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar contraseña</title>
    <link rel="stylesheet" href="css/contrasena.css">
</head>
<body>
    <div class="form-container">
        <h2>Cambiar Contraseña</h2>
        <form method="POST" class="formulario">
            <input type="email" name="email" placeholder="Correo electrónico" required class="input">
            <input type="text" name="token" placeholder="Código de recuperación" required class="input">
            <input type="password" name="password" placeholder="Nueva contraseña" required class="input">
            <input type="password" name="confirmar_password" placeholder="Confirmar contraseña" required class="input">
            <button type="submit" class="boton">Cambiar contraseña</button>
        </form>
        <?php if (!empty($mensaje)): ?>
            <p id="mensaje" class="mensaje"><strong><?= $mensaje ?></strong></p>
            <?php if (strpos($mensaje, 'actualizada') !== false): ?>
                <script>
                    setTimeout(() => window.location.href = 'index.php', 3000);
                </script>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
