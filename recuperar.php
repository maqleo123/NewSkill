<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña</title>
    <link rel="stylesheet" href="css/recuperar.css">
</head>
<body>
    <div class="form-container">
        <h2>¿Olvidaste tu contraseña?</h2>
        <form action="procesar_recuperar.php" method="post" class="formulario">
            <input type="email" name="email" placeholder="Ingresa tu correo" required class="input">
            <button type="submit" class="boton">Enviar enlace de recuperación</button>
        </form>
    </div>
</body>
</html>
