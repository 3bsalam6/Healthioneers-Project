<?php
require 'C:/PHP-MAIL/PHPMailer-master/src/Exception.php';
require 'C:/PHP-MAIL/PHPMailer-master/src/PHPMailer.php';
require 'C:/PHP-MAIL/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class LoginModel {
    private $conn;
    private $senderEmail = 'softwarehealthioneers2313@gmail.com';
    private $senderName = 'OTP System';
    private $senderPassword = 'llln xvmo kqol xrok';
    private $otpLength = 6;
    private $expiryMinutes = 5;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function generateOTP($length = 6) {
        return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    public function sendOTP($email, $otp) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $this->senderEmail;
            $mail->Password = $this->senderPassword;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->setFrom($this->senderEmail, $this->senderName);
            $mail->addAddress($email);
            $mail->Subject = 'Your OTP Code';
            $mail->Body = "Your OTP is: $otp\nIt expires in {$this->expiryMinutes} minutes.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
            return false;
        }
    }


    public function authenticate($email, $password, $tables) {
        foreach ($tables as $table => $info) {
            list($emailCol, $passCol, $redirect, $role) = $info;

            // Fetch row by email/username only
            $sql = "SELECT * FROM $table WHERE $emailCol = ?";
            $stmt = sqlsrv_query($this->conn, $sql, [$email]);

            if ($stmt && ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
                // Verify against hashed password
                if (password_verify($password, $row[$passCol])) {
                    return [$row, $emailCol, $redirect, $role];
                }
            }
        }
        return null;
    }

    public function getExpiryMinutes() {
        return $this->expiryMinutes;
    }

    public function getOtpLength() {
        return $this->otpLength;
    }
}