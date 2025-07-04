<?php
// Mostrar errores para depurar (quitar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión a la base de datos (usa mysql_*)
require 'includes/dbconexion.php';

// Incluir las clases de PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Función para generar token alfanumérico
function generarToken($longitud = 8) {
    $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $token = '';
    for ($i = 0; $i < $longitud; $i++) {
        $token .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }
    return $token;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['email'])) {
    $email = trim($_POST['email']);
    $email_esc = mysql_real_escape_string($email); // escapamos para evitar inyecciones

    // Generar token seguro y expiración de 1 hora
    $token = generarToken(8);
    $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Verificar si el correo existe
    $consulta = "SELECT idusuario FROM usuarios WHERE email = '$email_esc'";
    $resultado = mysql_query($consulta, $conexion);

    if (mysql_num_rows($resultado) == 1) {
        // Actualizar token y expiración
        $actualiza = "UPDATE usuarios SET reset_token = '$token', reset_expira = '$expira' WHERE email = '$email_esc'";
        if (mysql_query($actualiza, $conexion)) {
            // Enviar correo
            $mail = new PHPMailer(true);

            try {
                // Configuración del servidor
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'newskill66@gmail.com';  // Cambia por tu Gmail
                $mail->Password   = 'mvpvgsarwlaisfgf';       // Cambia por tu contraseña de app
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Remitente y destinatario
                $mail->setFrom('newskill66@gmail.com', 'NewSkill');
                $mail->addAddress($email);

                // Contenido
                $mail->isHTML(true);
                $mail->Subject = 'Recupera tu contraseña';
                $mail->Body = "
                    <h2>Recuperación de contraseña</h2>
                    <p>Usa este código para restablecer tu contraseña:</p>
                    <h3 style='color:#00bcd4;'>$token</h3>
                    <p>Este código expirará en 1 hora.</p>
                ";

                $mail->send();

                // Redirigir
                header("Location: cambiar_contrasena.php");
                exit();

            } catch (Exception $e) {
                echo "<p style='color:red; text-align:center;'>Error al enviar correo: {$mail->ErrorInfo}</p>";
            }
        } else {
            echo "<p style='color:red; text-align:center;'>Error al guardar el token en la base de datos.</p>";
        }
    } else {
        echo "<p style='color:orange; text-align:center;'>Este correo no está registrado.</p>";
    }
} else {
    echo "<p style='color:red; text-align:center;'>Acceso no permitido.</p>";
}
?>
