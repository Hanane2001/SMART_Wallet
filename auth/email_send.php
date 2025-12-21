<?php
include '../config/database.php';
session_start();
require '../vendor/autoload.php';
require 'pending_email.php';

if (!isset($_SESSION['pending_email'])) {
    header("Location: login.php");
    exit;
}

$emailL = $_SESSION['pending_email'];

if(isset($_GET['resend'])) {
    unset($_SESSION['otp'], $_SESSION['otp_time']);
}

if(!isset($_SESSION['otp']) || (isset($_SESSION['otp_time']) && (time() - $_SESSION['otp_time']) > 300)) {
    if(!sendOTP($emailL)){
        die("Failed to send OTP");
    }
}

header("Location: verify_otp.php");
exit;
?>