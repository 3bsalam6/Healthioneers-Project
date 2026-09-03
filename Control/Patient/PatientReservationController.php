<?php
session_start();

// Guard: only patients
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    echo "<h2 style='color:red; text-align:center;'>Unauthorized Access</h2>";
    header("Refresh:2; url=../LoginController.php");
    exit();
}


$server = "DESKTOP-OG4GIGD";
$db     = "Vaccination";
$conn   = sqlsrv_connect($server, ["Database" => $db]);
if ($conn === false) {
    die("DB connection failed: " . print_r(sqlsrv_errors(), true));
}


require_once __DIR__ . "/../../Model/Patient/PatientReservationModel.php";
$model = new PatientReservationModel($conn);


$patientUsername = $_SESSION['user_email'] ?? '';
$patient = $model->getPatientByUsername($patientUsername);


if (!$patient || strtolower($patient['Status'] ?? 'active') !== 'active') {
    echo "<h2 style='color:red; text-align:center;'>Account Suspended</h2>";
    header("Refresh:2; url=../LoginController.php");
    exit();
}


$reservations = $model->getReservationsByPatient($patient['Patient_ID']);


require_once __DIR__ . "/../../View/Patient/PatientReservationView.php";
renderPatientReservationView($patient, $reservations);