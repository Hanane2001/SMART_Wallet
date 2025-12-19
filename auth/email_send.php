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

if(!sendOTP($emailL)){
    die("Failed to send OTP");
}

header("Location: verify_otp.php");
exit;
?>