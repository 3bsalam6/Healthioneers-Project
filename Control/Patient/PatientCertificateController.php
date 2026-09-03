<?php
session_start();
require_once __DIR__ . "/../../Model/Patient/PatientReservationModel.php";

$server = "DESKTOP-OG4GIGD";
$db = "Vaccination";
$conn = sqlsrv_connect($server, ["Database"=>$db]);

$model = new PatientReservationModel($conn);

$reservationId = $_GET['reservation_id'] ?? null;
$reservation = null;
if ($reservationId) {
    $sql = "SELECT r.Reservation_ID, r.First_Confirmation_Date, r.Second_Confirmation_Date,
                   v.Vaccine_Name, p.Patient_FName, p.Patient_LName
            FROM Reservations r
            JOIN Vaccines v ON r.Vaccine_ID = v.Vaccine_ID
            JOIN Patients p ON r.Patient_ID = p.Patient_ID
            WHERE r.Reservation_ID = ?";
    $stmt = sqlsrv_query($conn, $sql, [$reservationId]);
    $reservation = $stmt ? sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) : null;
}

require_once __DIR__ . "/../../View/Patient/PatientCertificateView.php";
renderPatientCertificateView($reservation);