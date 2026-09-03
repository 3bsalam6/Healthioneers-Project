<?php
session_start();

require 'C:/PHP-MAIL/PHPMailer-master/src/Exception.php';
require 'C:/PHP-MAIL/PHPMailer-master/src/PHPMailer.php';
require 'C:/PHP-MAIL/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// CONFIG
$senderEmail    = 'softwarehealthioneers2313@gmail.com';
$senderName     = 'Healthineers OTP';
$senderPassword = 'llln xvmo kqol xrok'; // Gmail App Password
$otpLength      = 6;
$expiryMinutes  = 5;

function generateOTP($length = 6) {
    return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}
function sendOTP($email, $otp) {
    global $senderEmail, $senderName, $senderPassword, $expiryMinutes;
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
            'ssl'=>[
                'verify_peer'=>false,
                'verify_peer_name'=>false,
                'allow_self_signed'=>true
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

// Fetch cities
$cities = [];
$sql = "SELECT City_ID, City_Name FROM Cities ORDER BY City_Name ASC";
$stmt = sqlsrv_query($conn, $sql);
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $cities[] = $row;
}

$error = "";

//  registration
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $fname       = $_POST['fname'] ?? '';
    $lname       = $_POST['lname'] ?? '';
    $username    = $_POST['username'] ?? '';
    $password    = $_POST['password'] ?? '';
    $confirmPass = $_POST['confirm-password'] ?? '';
    $city_id     = $_POST['city'] ?? null;
    $phone       = $_POST['phone'] ?? '';
    $national_id = $_POST['national_id'] ?? '';

    if (!$city_id) {
        $error = "❌ Invalid city selected.";
    } elseif ($password !== $confirmPass) {
        $error = "❌ Passwords do not match.";
    } elseif (!preg_match('/^\d{14}$/',$national_id)) {
        $error = "❌ National ID must be exactly 14 digits.";
    } elseif (!preg_match('/^\d{11}$/',$phone)) {
        $error = "❌ Phone must be 11 digits.";
    } elseif ($model->emailExists($username)) {
        $error = "❌ Email already registered.";
    }

    if (empty($error)) {
        $otp = generateOTP($otpLength);
        // Store plain password temporarily in session until OTP verified متلمسهاش
        $_SESSION['pending_user'] = compact('fname','lname','username','password','city_id','phone','national_id');
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiry'] = time()+($expiryMinutes*60);

        if (sendOTP($username,$otp)) {
            header("Location: VerifyController.php");
            exit();
        } else {
            $error = "❌ Failed to send OTP.";
        }
    }
}

require_once __DIR__ . "/../View/RegisterView.php";
renderRegisterView($error,$cities);