<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../vendor/autoload.php';

// $dotenv = DotenvVault\DotenvVault::createImmutable(__DIR__);
$dotenv = DotenvVault\DotenvVault::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();
function sendOTP($email) {
    if(isset($_SESSION['otp_time']) && (time() - $_SESSION['otp_time']) < 60) {
        return true;
    }

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
        $mail->Subject = 'Your OTP Code - SmartBudget';
        $mail->Body    = "<h2>Your OTP Code</h2>
            <p>Your One-Time Password for SmartBudget is:</p>
            <h1 style='font-size: 32px; color: #3b82f6;'><strong>$otp</strong></h1>
            <p>This code will expire in 5 minutes.</p>
            <p>If you didn't request this code, please ignore this email.</p>";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Email error: " . $e->getMessage());
        return false;
    }
}
?>