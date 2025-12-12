<?php
// 1. Incluir las clases de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// La ruta es relativa a procesar_cita.php
require 'src/includes/Exception.php';
require 'src/includes/PHPMailer.php';
require 'src/includes/SMTP.php'; // Necesario para la conexión SMTP

// ------------------------------------------------------------------
// CONFIGURACIÓN DE GMAIL (SMTP) - ¡CAMBIAR ESTOS VALORES!
// ------------------------------------------------------------------
$SMTP_USER = 'lasmilreparaciones@gmail.com'; // Tu dirección de correo
$SMTP_PASS = 'mgmk qbax scpi jdeb'; // Contraseña de Aplicación de Google
$SMTP_HOST = 'smtp.gmail.com'; 
$SMTP_PORT = 587; // Puerto estándar para TLS
$SMTP_SECURE = PHPMailer::ENCRYPTION_STARTTLS; 
$DESTINATARIO = $SMTP_USER; // Puedes enviarlo a ti mismo
$ASUNTO = "NUEVA SOLICITUD DE CITA - Las Mil Reparaciones"; 
// ------------------------------------------------------------------

// 2. Recoger y Sanitizar los Datos del Formulario
$nombre = htmlspecialchars($_POST['nombre'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$telefono = htmlspecialchars($_POST['telefono'] ?? '');
$servicio = htmlspecialchars($_POST['servicio'] ?? '');
$comentarios = htmlspecialchars($_POST['comentarios'] ?? '');

// Validación Básica
if (empty($nombre) || empty($email) || empty($telefono) || empty($servicio)) {
    header('Location: error.html'); 
    exit;
}

// Componer el Cuerpo del Mensaje (HTML)
$cuerpo_html = "
<html>
<body style='font-family: Roboto, sans-serif; line-height: 1.6;'>
    <h2 style='color: #0A192F;'>Nueva Solicitud de Cita de Cliente Web</h2>
    <hr style='border: 1px solid #FFD700;'>
    <table cellpadding='10' cellspacing='0' style='border: 1px solid #EEEEEE; width: 100%;'>
        <tr><td style='background-color: #EEEEEE;'><strong>Nombre:</strong></td><td>$nombre</td></tr>
        <tr><td style='background-color: #EEEEEE;'><strong>Email:</strong></td><td>$email</td></tr>
        <tr><td style='background-color: #EEEEEE;'><strong>Teléfono:</strong></td><td>$telefono</td></tr>
        <tr><td style='background-color: #EEEEEE;'><strong>Servicio Solicitado:</strong></td><td><strong>$servicio</strong></td></tr>
        <tr><td style='background-color: #EEEEEE;'><strong>Comentarios:</strong></td><td>$comentarios</td></tr>
    </table>
    <hr style='border: 1px solid #FFD700;'>
    <p style='color: #8B0000;'><strong>ACCIÓN REQUERIDA:</strong> Llamar o responder al cliente en menos de 4 horas hábiles.</p>
</body>
</html>
";

// 3. Configurar y Enviar con PHPMailer
$mail = new PHPMailer(true); // Pasar true habilita excepciones
try {
    // Configuración del Servidor
    $mail->isSMTP();
    $mail->Host = $SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = $SMTP_USER;
    $mail->Password = $SMTP_PASS;
    $mail->SMTPSecure = $SMTP_SECURE;
    $mail->Port = $SMTP_PORT;
    
    // Remitente y Destinatario
    $mail->setFrom($SMTP_USER, 'Las Mil Reparaciones - Citas');
    $mail->addAddress($DESTINATARIO);
    $mail->addReplyTo($email, $nombre); // Permite responder al cliente

    // Contenido del Email
    $mail->isHTML(true);
    $mail->Subject = $ASUNTO;
    $mail->Body    = $cuerpo_html;
    $mail->AltBody = "Nueva Cita de: $nombre. Tel: $telefono. Servicio: $servicio."; // Texto plano para clientes sin HTML

    $mail->send();
    
    // Éxito
    header('Location: gracias.html'); 
    
} catch (Exception $e) {
    // Falla: Si hay un error, redirige al error.html
    // Opcional: Para debug, puedes imprimir el error antes de la redirección
    // echo "Mensaje no pudo ser enviado. Error de Mailer: {$mail->ErrorInfo}";
    header('Location: error.html');
}
exit;
?>