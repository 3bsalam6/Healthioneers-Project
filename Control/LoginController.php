<?php
session_start();
$conn = sqlsrv_connect("DESKTOP-OG4GIGD", ["Database" => "Vaccination"]);
if ($conn === false) { die(print_r(sqlsrv_errors(), true)); }

require_once __DIR__ . "/../Model/LoginModel.php";
$model = new LoginModel($conn);

$error = "";
$message = "";
$showOtpBox = false;

$tables = [
    "Admin" => ["Admin_Username", "Admin_HashedPassword", "Admin/DashboardController.php", "admin"],
    "Vaccination_Centers" => ["Center_Username", "Center_HashedPassword", "Center/CenterDashboardController.php", "center"],
    "Patients" => ["Patient_Username", "Hashed_Password", "Patient/PatientHomeController.php", "patient"]
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['back_to_login'])) {
        $showOtpBox = false;
        unset($_SESSION['otp'], $_SESSION['otp_expiry'], $_SESSION['redirect'], $_SESSION['user_email'], $_SESSION['role'], $_SESSION['fname']);
    }
    elseif (isset($_POST['verify_otp'])) {
        $userOTP = $_POST['otp'] ?? '';
        $validOTP = $_SESSION['otp'] ?? '';
        $expiry = $_SESSION['otp_expiry'] ?? 0;

        if (!$validOTP) {
            $error = "❌ No OTP session found. Please login again.";
            $showOtpBox = false;
        } elseif (time() > $expiry) {
            $error = "⏰ OTP expired. Please resend OTP.";
            $showOtpBox = true;
        } elseif ($userOTP === $validOTP) {
            $redirect = $_SESSION['redirect'] ?? '';
            unset($_SESSION['otp'], $_SESSION['otp_expiry']);
            header("Location: $redirect");
            exit();
        } else {
            $error = "❌ Invalid OTP.";
            $showOtpBox = true;
        }
    }
    elseif (isset($_POST['resend_otp'])) {
        $email = $_SESSION['user_email'] ?? '';
        if (!$email) {
            $error = "❌ No user session. Please login again.";
            $showOtpBox = false;
        } else {
            $otp = $model->generateOTP($model->getOtpLength());
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_expiry'] = time() + ($model->getExpiryMinutes() * 60);
            $message = $model->sendOTP($email, $otp) ? "✅ OTP resent to $email" : "❌ Failed to resend OTP.";
            $showOtpBox = true;
        }
    }
    else {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $auth = $model->authenticate($email, $password, $tables);

        if ($auth) {
            list($row, $emailCol, $redirect, $role) = $auth;
            $_SESSION['user_email'] = $row[$emailCol];
            $_SESSION['role'] = $role;
            $_SESSION['redirect'] = $redirect;

            if ($role === 'admin' && isset($row['Admin_Fname'])) {
                $_SESSION['fname'] = $row['Admin_Fname'];
            } elseif ($role === 'center') {
                if (isset($row['Center_Name'])) {
                    $_SESSION['fname'] = $row['Center_Name'];
                }
                if (isset($row['Center_ID'])) {
                    $_SESSION['center_id'] = (int)$row['Center_ID'];
















                }
            } elseif ($role === 'patient' && isset($row['Patient_FName'])) {
                $_SESSION['fname'] = $row['Patient_FName'];
            }

            $otp = $model->generateOTP($model->getOtpLength());
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_expiry'] = time() + ($model->getExpiryMinutes() * 60);

            $message = $model->sendOTP($row[$emailCol], $otp)
                ? "✅ OTP sent to {$row[$emailCol]}"
                : "❌ Failed to send OTP.";

            $showOtpBox = true;
        } else {
            $error = "❌ Invalid login credentials.";
            $showOtpBox = false;
        }
    }
}

require_once __DIR__ . "/../View/LoginView.php";
renderLoginView($showOtpBox, $message, $error);