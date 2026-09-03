<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    echo "<h2 style='color:red; text-align:center;'>Unauthorized Access</h2>";
    header("Refresh:2; url=../LoginController.php");
    exit();
}

require 'C:/PHP-MAIL/PHPMailer-master/src/Exception.php';
require 'C:/PHP-MAIL/PHPMailer-master/src/PHPMailer.php';
require 'C:/PHP-MAIL/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

require_once __DIR__ . "/../../Model/DatabaseConnection.php";
require_once __DIR__ . "/../../Model/Patient/PatientAccountModel.php";

$conn = DatabaseConnection::getInstance()->getConnection();
$model = new PatientAccountModel($conn);

$patientUsername = $_SESSION['user_email'] ?? '';
$patient = $model->getPatientByUsername($patientUsername);
$cities = $model->getCities();

$message = $error = $modalError = $modalMessage = "";

// ---------------- Account Update ----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $fname = $_POST['fname'] ?? '';
    $lname = $_POST['lname'] ?? '';
    $username = $_POST['username'] ?? '';
    $cityId = (int) ($_POST['city_id'] ?? 0);
    $phone = $_POST['phone'] ?? '';

    if ($username !== $patient['Patient_Username']) {
        $otp = rand(100000, 999999);
        $_SESSION['pending_username'] = $username;
        $_SESSION['otp'] = $otp;

        try {
            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Username = "softwarehealthioneers2313@gmail.com";
            $mail->Password = "llln xvmo kqol xrok";
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
            $mail->addAddress($username);
            $mail->Subject = "Healthioneers - Verify Your Email";
            $mail->Body = "Your OTP code is: $otp";

            if ($mail->send()) {
                $message = "✅ OTP sent to $username";
            } else {
                $error = "❌ Failed to send OTP.";
            }
        } catch (Exception $e) {
            $error = "❌ Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        if ($model->updatePatientInfo($patient['Patient_ID'], $fname, $lname, $username, $cityId, $phone)) {
            $message = "✅ Account updated successfully.";
            $_SESSION['user_email'] = $username;
            $patient = $model->getPatientByUsername($username);
        } else {
            $error = "❌ Failed to update account.";
        }
    }
}

// ---------------- Password Change ----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $oldPassword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $currentHash = $patient['Hashed_Password'] ?? null;

    if (!$currentHash || !password_verify($oldPassword, $currentHash)) {
        $modalError = "❌ Old password incorrect.";
    } elseif ($newPassword !== $confirmPassword) {
        $modalError = "❌ New passwords do not match.";
    } else {
        if ($model->updatePassword($patient['Patient_ID'], $newPassword)) {
            $modalMessage = "✅ Password changed successfully.";
            $patient = $model->getPatientByUsername($patientUsername);
        } else {
            $modalError = "❌ Failed to change password.";
        }
    }
}

// ---------------- OTP Verification ----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $enteredOtp = $_POST['otp'] ?? '';
    if ($enteredOtp == ($_SESSION['otp'] ?? '')) {
        $model->updatePatientInfo(
            $patient['Patient_ID'],
            $patient['Patient_FName'],
            $patient['Patient_LName'],
            $_SESSION['pending_username'],
            $patient['Patient_City_ID'],
            $patient['Patient_Phone']
        );
        $_SESSION['user_email'] = $_SESSION['pending_username'];
        unset($_SESSION['otp'], $_SESSION['pending_username']);
        $message = "✅ Email verified and updated successfully.";
        $patient = $model->getPatientByUsername($_SESSION['user_email']);
    } else {
        $error = "❌ Invalid OTP.";
    }
}

// ---------------- Resend OTP ----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_otp'])) {
    if (isset($_SESSION['pending_username'])) {
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        try {
            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Username = "softwarehealthioneers2313@gmail.com";
            $mail->Password = "llln xvmo kqol xrok";
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
            $mail->addAddress($_SESSION['pending_username']);
            $mail->Subject = "Healthioneers - Verify Your Email (Resent)";
            $mail->Body = "Your new OTP code is: $otp";

            if ($mail->send()) {
                $message = "✅ OTP resent to " . $_SESSION['pending_username'];
            } else {
                $error = "❌ Failed to resend OTP.";
            }
        } catch (Exception $e) {
            $error = "❌ Mailer Error: " . $mail->ErrorInfo;
        }
    }
}

// ---------------- Profile Picture Upload ----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['patient_image'])) {
    $targetDir = __DIR__ . "/../../uploads/patients/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $ext = strtolower(pathinfo($_FILES["patient_image"]["name"], PATHINFO_EXTENSION));
    $allowedTypes = ["jpg", "jpeg", "png", "gif"];

    if (!in_array($ext, $allowedTypes)) {
        $error = "❌ Only JPG, JPEG, PNG & GIF files are allowed.";
    } else {
        // Begin transaction to ensure atomicity
        sqlsrv_begin_transaction($conn);

        // Increment Upload_Count with ISNULL to handle NULL -> 0
        $incSql = "UPDATE Patients SET Upload_Count = ISNULL(Upload_Count, 0) + 1 WHERE Patient_ID = ?";
        $incOk = sqlsrv_query($conn, $incSql, [$patient['Patient_ID']]);

        // Read the NEW count
        $countSql = "SELECT Upload_Count FROM Patients WHERE Patient_ID = ?";
        $stmt = sqlsrv_query($conn, $countSql, [$patient['Patient_ID']]);
        $row = $stmt ? sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) : null;
        $newCount = $row && isset($row['Upload_Count']) ? (int) $row['Upload_Count'] : 1;

        if ($incOk && $row) {
            // Filename: patientId--count.ext
            $fileName = $patient['Patient_ID'] . "--" . $newCount . "." . $ext;
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($_FILES["patient_image"]["tmp_name"], $targetFile)) {
                $relativePath = "/uploads/patients/" . $fileName;

                $saveSql = "UPDATE Patients SET Patient_Image = ? WHERE Patient_ID = ?";
                $saveOk = sqlsrv_query($conn, $saveSql, [$relativePath, $patient['Patient_ID']]);

                if ($saveOk) {
                    sqlsrv_commit($conn);
                    $message = "✅ Profile picture updated successfully.";
                    $patient = $model->getPatientByUsername($patient['Patient_Username']);
                } else {
                    sqlsrv_rollback($conn);
                    $error = "❌ Failed to save image path in database.";
                }
            } else {
                sqlsrv_rollback($conn);
                $error = "❌ Error uploading file.";
            }
        } else {
            sqlsrv_rollback($conn);
            $error = "❌ Failed to increment upload count.";
        }
    }
}

// ---------------- Remove Profile Picture ----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_image'])) {
    // set Patient_Image to default placeholder path
    $defaultPath = "/uploads/patients/default.jpeg";
    $sql = "UPDATE Patients SET Patient_Image = ? WHERE Patient_ID = ?";
    if (sqlsrv_query($conn, $sql, [$defaultPath, $patient['Patient_ID']])) {
        $message = "✅ Profile picture removed successfully.";
        $patient = $model->getPatientByUsername($patient['Patient_Username']);
    } else {
        $error = "❌ Failed to remove profile picture.";
    }
}

require_once __DIR__ . "/../../View/Patient/PatientAccountView.php";
renderPatientAccountView($patient, $cities, $message, $error, $modalMessage, $modalError);