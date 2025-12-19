<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../vendor/autoload.php';

// $dotenv = DotenvVault\DotenvVault::createImmutable(__DIR__);
$dotenv = DotenvVault\DotenvVault::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();
function sendOTP($email) {

    $otp = random_int(100000, 999999);
    $_SESSION['otp'] = (string)$otp;
    $_SESSION['otp_time'] = time();

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $_ENV['stmp_host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['stmp_username'];
        $mail->Password   = $_ENV['stmp_password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  
        $mail->Port       = $_ENV['stmp_port'];

        $mail->setFrom('hanan2122hanan@gmail.com', 'Money Track');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body    = "<h2>Your OTP is: <strong>$otp</strong></h2>";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Email error: " . $e->getMessage());
        return false;
    }
}
?>