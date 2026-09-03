<?php
session_start();
require 'C:/PHP-MAIL/PHPMailer-master/src/Exception.php';
require 'C:/PHP-MAIL/PHPMailer-master/src/PHPMailer.php';
require 'C:/PHP-MAIL/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = sqlsrv_connect("DESKTOP-OG4GIGD", ["Database"=>"Vaccination"]);
if ($conn === false) { die("DB connection failed."); }

$message = $error = "";


function sendOtpMail($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "softwarehealthioneers2313@gmail.com";
        $mail->Password = "llln xvmo kqol xrok"; // Gmail App Password
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom("softwarehealthioneers2313@gmail.com", "Healthioneers");
        $mail->addAddress($email);
        $mail->Subject = "Password Reset OTP";
        $mail->Body    = "Your OTP is: $otp";
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Step 1: Request reset
    if (isset($_POST['request_reset'])) {
        $email = $_POST['email'] ?? '';
        $sql = "SELECT Patient_ID FROM Patients WHERE Patient_Username = ?";
        $stmt = sqlsrv_query($conn, $sql, [$email]);
        $row  = $stmt ? sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) : null;

        if ($row) {
            $otp = rand(100000, 999999);
            $_SESSION['reset_user']   = $row['Patient_ID'];
            $_SESSION['reset_email']  = $email;
            $_SESSION['reset_otp']    = $otp;
            $_SESSION['reset_expiry'] = time() + 300;

            if (sendOtpMail($email, $otp)) {
                $message = "✅ OTP sent to $email";
            } else {
                $error = "❌ Failed to send OTP.";
            }
        } else {
            $error = "❌ No account found with that email.";
        }
    }

    // Verify OTP
    elseif (isset($_POST['verify_otp'])) {
        $enteredOtp = $_POST['otp'] ?? '';
        if ($enteredOtp == ($_SESSION['reset_otp'] ?? '') && time() < ($_SESSION['reset_expiry'] ?? 0)) {
            $_SESSION['otp_verified'] = true;
            $message = "✅ OTP verified. Please enter new password.";
        } else {
            $error = "❌ Invalid or expired OTP.";
        }
    }

    // Resend OTP
    elseif (isset($_POST['resend_otp'])) {
        if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_user'])) {
            $error = "❌ No pending reset request. Please start again.";
        } else {
            $otp = rand(100000, 999999);
            $_SESSION['reset_otp']    = $otp;
            $_SESSION['reset_expiry'] = time() + 300;

            if (sendOtpMail($_SESSION['reset_email'], $otp)) {
                $message = "📩 New OTP sent to {$_SESSION['reset_email']}";
            } else {
                $error = "❌ Failed to resend OTP.";
            }
        }
    }

    // Start over (clear session)
    elseif (isset($_POST['start_over'])) {
        unset($_SESSION['reset_user'], $_SESSION['reset_email'], $_SESSION['reset_otp'], $_SESSION['reset_expiry'], $_SESSION['otp_verified']);
        $message = "🔄 enter the new email.";
    }

    // Set new password
    elseif (isset($_POST['set_new_password'])) {
        $newPass = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if ($newPass !== $confirm) {
            $error = "❌ Passwords do not match.";
        } elseif (!isset($_SESSION['otp_verified'])) {
            $error = "❌ OTP not verified.";
        } else {
            $hashed = password_hash($newPass, PASSWORD_DEFAULT);
            $sql = "UPDATE Patients SET Hashed_Password = ? WHERE Patient_ID = ?";
            $ok = sqlsrv_query($conn, $sql, [$hashed, $_SESSION['reset_user']]);
            if ($ok) {
                $message = "✅ Password updated. You can now login.";
                session_unset();
                header("Refresh:2; url=LoginController.php");
                exit;
            } else {
                $error = "❌ Failed to update password.";
            }
        }
    }
}

require_once __DIR__ . "/../View/UpdatePasswordView.php";
renderUpdatePasswordView($message, $error);
?>