<?php
session_start();

// Guard: only patients
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    echo "<h2 style='color:red; text-align:center;'>Unauthorized Access</h2>";
    header("Refresh:2; url=../LoginController.php");
    exit();
}

// DB connect
$server = "DESKTOP-OG4GIGD";
$db     = "Vaccination";
$conn   = sqlsrv_connect($server, ["Database" => $db]);
if ($conn === false) { die(print_r(sqlsrv_errors(), true)); }

require_once __DIR__ . "/../../Model/Patient/PatientVaccinesModel.php";
$model = new PatientVaccinesModel($conn);


$patientUsername = $_SESSION['user_email'] ?? '';
$patient = $model->getPatientByUsername($patientUsername);
if (!$patient || strtolower($patient['Status'] ?? 'active') !== 'active') {
    echo "<h2 style='color:red; text-align:center;'>Account Suspended</h2>";
    header("Refresh:2; url=../LoginController.php");
    exit();
}
$patientId    = (int)$patient['Patient_ID'];
$patientFName = $patient['Patient_FName'] ?? "Patient";

$message = "";
$error   = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    $centerId      = (int)($_POST['center_id'] ?? 0);
    $vaccineId     = (int)($_POST['vaccine_id'] ?? 0);
    $firstDoseDate = $_POST['first_dose_date'] ?? '';

    if (!$centerId || !$vaccineId || !$firstDoseDate) {
        $error = "❌ Please complete all fields.";
    } elseif ($model->hasOngoingReservation($patientId)) {
        // Block if patient already has ongoing reservation
        $error = "❌ You already have an ongoing reservation. Finish it before booking another.";
    } else {
        $firstDoseDateSql = date('Y-m-d', strtotime($firstDoseDate));
        $row = $model->createReservation($patientId, $vaccineId, $centerId, $firstDoseDateSql);
        if ($row) {
            $reservationId = $row['Reservation_ID'];
            header("Location: PatientReservationController.php?reservation_id=" . $reservationId);
            exit();
        } else {
            $error = "❌ Failed to create reservation.";
        }
    }
}

// Handle vaccine search
$searchTerm = $_GET['search_term'] ?? null;
if ($searchTerm) {
    $vaccines = $model->searchVaccines($searchTerm);
} else {
    $vaccines = $model->getActiveVaccines();
}

// Fetch centers (with address)
$centers = $model->getActiveCenters(); // must return Center_ID, Center_Name, Center_Address

// Render view
require_once __DIR__ . "/../../View/Patient/PatientVaccinesView.php";
renderPatientReservationView($patientFName, $vaccines, $centers, $message, $error);