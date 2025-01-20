<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = new mysqli('localhost', 'root', '', 'newsletter');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sendMail($to, $subject, $body, $attachments = [])
{
    $mail = new PHPMailer(true);
    $mail->CharSet = "UTF-8";
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Вкажіть ваш SMTP-сервер
        $mail->SMTPAuth = true;
        $mail->Username = 'igorhorban19@gmail.com';  // Ваша електронна пошта
        $mail->Password = 'fnru baei lksz ncar';  // Ваш пароль
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('igorhorban19@gmail.com', 'Тестова розсилка');
        $mail->addAddress($to);

        foreach ($attachments as $attachment) {
            $mail->addAttachment($attachment);
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
    } catch (Exception $e) {
        echo "Помилка надсилання: {$mail->ErrorInfo}";
    }
}
?>