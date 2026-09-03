<?php
// ايرورز كتير ملهاش لازمة بتظهر
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

require_once __DIR__ . "/../../libs/tcpdf/tcpdf.php";
require_once __DIR__ . "/../../Model/Patient/PatientReservationModel.php";

$server = "DESKTOP-OG4GIGD";
$db = "Vaccination";
$conn = sqlsrv_connect($server, ["Database"=>$db]);

$model = new PatientReservationModel($conn);
$reservationId = $_POST['reservation_id'] ?? null;

$sql = "SELECT r.Reservation_ID, r.First_Confirmation_Date, r.Second_Confirmation_Date,
               v.Vaccine_Name, p.Patient_FName, p.Patient_LName
        FROM Reservations r
        JOIN Vaccines v ON r.Vaccine_ID = v.Vaccine_ID
        JOIN Patients p ON r.Patient_ID = p.Patient_ID
        WHERE r.Reservation_ID = ?";
$stmt = sqlsrv_query($conn, $sql, [$reservationId]);
$reservation = $stmt ? sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) : null;

if ($reservation) {
    $firstDate = !empty($reservation['First_Confirmation_Date'])
        ? ($reservation['First_Confirmation_Date'] instanceof DateTime
            ? $reservation['First_Confirmation_Date']->format('Y-m-d')
            : $reservation['First_Confirmation_Date'])
        : 'Not recorded';

    $secondDate = !empty($reservation['Second_Confirmation_Date'])
        ? ($reservation['Second_Confirmation_Date'] instanceof DateTime
            ? $reservation['Second_Confirmation_Date']->format('Y-m-d')
            : $reservation['Second_Confirmation_Date'])
        : 'Not required / Not recorded';

    // certificate Body
    $html = "
    <style>
        .certificate {
            border: 10px solid #009688;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            font-family: Georgia, serif;
        }
        h1 { color: #009688; font-size: 28px; margin-bottom: 20px; }
        p { font-size: 16px; margin: 8px 0; color: #333; }
        .highlight { font-weight: bold; }
        .signature { margin-top: 50px; text-align: right; font-style: italic; }
    </style>
    <div class='certificate'>
        <h1>Vaccination Certificate</h1>
        <p>This certifies that <span class='highlight'>{$reservation['Patient_FName']} {$reservation['Patient_LName']}</span></p>
        <p>has successfully completed vaccination with</p>
        <p class='highlight'>{$reservation['Vaccine_Name']}</p>
        <p>First Dose Date: {$firstDate}</p>
        <p>Second Dose Date: {$secondDate}</p>
        <div class='signature'>Authorized Signature: ______________________</div>
    </div>";

    // Generate PDF
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('certificate.pdf', 'D');
}