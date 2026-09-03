<?php
session_start();

require 'C:/PHP-MAIL/PHPMailer-master/src/Exception.php';
require 'C:/PHP-MAIL/PHPMailer-master/src/PHPMailer.php';
require 'C:/PHP-MAIL/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$senderEmail    = 'softwarehealthioneers2313@gmail.com';
$senderName     = 'Healthineers OTP';
$senderPassword = 'llln xvmo kqol xrok'; // Gmail App Password
$expiryMinutes  = 5;

function sendOTP($email, $otp, $expiryMinutes) {
    global $senderEmail, $senderName, $senderPassword;
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $senderEmail;
        $mail->Password = $senderPassword;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom($senderEmail, $senderName);
        $mail->addAddress($email);
        $mail->Subject = 'Verify Your Registration';
        $mail->Body = "Your OTP is: $otp\nIt expires in $expiryMinutes minutes.";
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

$serverName = "DESKTOP-OG4GIGD";
$conn = sqlsrv_connect($serverName, ["Database"=>"Vaccination"]);
if ($conn === false) { die(print_r(sqlsrv_errors(), true)); }

require_once __DIR__ . "/../Model/RegisterModel.php";
$model = new RegisterModel($conn);

$error = "";
$message = "";


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['pending_user'], $_SESSION['otp'])) {
    $email = $_SESSION['pending_user']['username'];
    $message = "📩 OTP sent to $email.";
}

// Verify OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $enteredOtp = isset($_POST['otp']) ? trim($_POST['otp']) : '';


    if (!preg_match('/^\d{6}$/', $enteredOtp)) {
        $error = "❌ OTP must be 6 digits.";
    } elseif (!isset($_SESSION['otp'], $_SESSION['otp_expiry'], $_SESSION['pending_user'])) {
        $error = "❌ No pending verification. Please register again.";
    } elseif (time() >= (int)$_SESSION['otp_expiry']) {
        $error = "❌ OTP expired. Please resend.";
    } elseif ($enteredOtp !== (string)$_SESSION['otp']) {
        $error = "❌ Incorrect OTP.";
    } else {

        $u = $_SESSION['pending_user'];


        $ok = $model->registerUser($u);

        if ($ok) {
            // Clear session OTP data
            unset($_SESSION['pending_user'], $_SESSION['otp'], $_SESSION['otp_expiry']);

            // Use a clean redirect (avoid echo before header)
            header("Location: ../Control/loginController.php");
            exit();
        } else {
            $error = "❌ Registration failed.";
        }
    }
}

// Resend OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_otp'])) {
    if (!isset($_SESSION['pending_user'])) {
        $error = "❌ No pending registration. Please register again.";
    } else {
        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiry'] = time() + ($expiryMinutes * 60);
        $email = $_SESSION['pending_user']['username'];

        if (sendOTP($email, $otp, $expiryMinutes)) {
            $message = "📩 New OTP sent to $email.";
        } else {
            $error = "❌ Failed to resend OTP.";
        }
    }
}

require_once __DIR__ . "/../View/VerifyView.php";
renderVerifyView($message, $error);