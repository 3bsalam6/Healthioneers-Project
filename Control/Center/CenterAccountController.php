<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'center') {
    echo "<h2 style='color:red; text-align:center;'>Unauthorized Access</h2>";
    header("Refresh:2; url=../LoginController.php");
    exit();
}

// PHPMailer includes
require 'C:/PHP-MAIL/PHPMailer-master/src/Exception.php';
require 'C:/PHP-MAIL/PHPMailer-master/src/PHPMailer.php';
require 'C:/PHP-MAIL/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// CONFIG
$senderEmail    = 'softwarehealthioneers2313@gmail.com';
$senderName     = 'OTP System';
$senderPassword = 'llln xvmo kqol xrok'; // Gmail App Password
$otpLength      = 6;
$expiryMinutes  = 5;

// OTP helpers
function generateOTP($length = 6) {
    return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}
function sendOTP($email, $otp) {
    global $senderEmail, $senderName, $senderPassword, $expiryMinutes;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $senderEmail;
        $mail->Password   = $senderPassword;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom($senderEmail, $senderName);
        $mail->addAddress($email);
        $mail->Subject = 'Your OTP Code';
        $mail->Body    = "Your OTP is: $otp\nIt expires in $expiryMinutes minutes.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    }
}

// DB connect
$conn = sqlsrv_connect("DESKTOP-OG4GIGD", ["Database" => "Vaccination"]);
if ($conn === false) { die("Database connection failed."); }

require_once __DIR__ . "/../../Model/Center/CenterAccountModel.php";
$model = new CenterAccountModel($conn);

$centerUsername = $_SESSION['user_email'] ?? '';
$center = $model->getCenterByUsername($centerUsername);

$message = $error = $modalError = $modalMessage = "";

// Account update (with OTP if email changed)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $name     = $_POST['name'] ?? '';
    $username = $_POST['username'] ?? '';
    $address  = $_POST['address'] ?? '';
    $phone    = $_POST['phone'] ?? '';

    if ($username !== $center['Center_Username']) {
        // OTP flow
        $otp = generateOTP($otpLength);
        $_SESSION['pending_email'] = $username;
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiry'] = time() + ($expiryMinutes * 60);

        if (sendOTP($username, $otp)) {
            $message = "📩 OTP sent to $username. Please verify to complete email change.";
        } else {
            $error = "❌ Failed to send OTP.";
        }
    } else {
        if ($model->updateAccount($center['Center_ID'], $name, $username, $address, $phone)) {
            $message = "✅ Account updated successfully.";
            $_SESSION['user_email'] = $username;
            $center = $model->getCenterByUsername($username);
        } else {
            $error = "❌ Failed to update account.";
        }
    }
}

// OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $enteredOtp = $_POST['otp'] ?? '';
    if (isset($_SESSION['otp'], $_SESSION['pending_email']) &&
        time() < $_SESSION['otp_expiry'] &&
        $enteredOtp == $_SESSION['otp']) {

        if ($model->updateAccount($center['Center_ID'], $center['Center_Name'], $_SESSION['pending_email'], $center['Center_Address'], $center['Center_Phone'])) {
            $message = "✅ Email changed successfully.";
            $_SESSION['user_email'] = $_SESSION['pending_email'];
            $center = $model->getCenterByUsername($_SESSION['pending_email']);
        } else {
            $error = "❌ Failed to update email.";
        }
        unset($_SESSION['otp'], $_SESSION['pending_email'], $_SESSION['otp_expiry']);
    } else {
        $error = "❌ Invalid or expired OTP.";
    }
}

// Resend OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_otp'])) {
    if (!isset($_SESSION['pending_email'])) {
        $error = "❌ No pending email change. Please try again.";
    } else {
        $otp = generateOTP($otpLength);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiry'] = time() + ($expiryMinutes * 60);

        if (sendOTP($_SESSION['pending_email'], $otp)) {
            $message = "📩 New OTP sent to {$_SESSION['pending_email']}.";
        } else {
            $error = "❌ Failed to resend OTP.";
        }
    }
}

// Password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $oldPassword     = $_POST['old_password'] ?? '';
    $newPassword     = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $checkRow = $model->getPassword($center['Center_ID']);
    $currentHash = $checkRow['Center_HashedPassword'] ?? null;

    if (!$currentHash || !password_verify($oldPassword, $currentHash)) {
        $modalError = "❌ Old password is incorrect.";
    } elseif ($newPassword !== $confirmPassword) {
        $modalError = "❌ New passwords do not match.";
    } else {
        if ($model->updatePassword($center['Center_ID'], $newPassword)) {
            $modalMessage = "✅ Password changed successfully.";
            // Refresh center data
            $center = $model->getCenterByUsername($_SESSION['user_email']);
        } else {
            $modalError = "❌ Failed to change password.";
        }
    }
}

require_once __DIR__ . "/../../View/Center/CenterAccountView.php";
renderCenterAccountView($center, $message, $error, $modalError, $modalMessage);