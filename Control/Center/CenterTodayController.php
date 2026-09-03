<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'center') {
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



require_once __DIR__ . "/../../Model/Center/CenterTodayModel.php";
$centerId = $_SESSION['center_id'];
$model    = new CenterTodayModel($conn, $centerId);

$message   = "";
$error     = "";
$todayList = $model->getTodayReservations();

// Search
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_today'])) {
    $searchTerm = trim($_POST['search_term'] ?? "");
    if ($searchTerm === "") {
        $error = "❌ Please enter a search term.";
    } else {
        $todayList = $model->searchTodayReservations($searchTerm);
        if (empty($todayList)) {
            $error = "❌ No reservations found for today.";
        }
    }
}

// Dose confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_dose'])) {
    $reservationId = (int)($_POST['reservation_id'] ?? 0);
    $doseNumber    = (int)($_POST['confirm_dose'] ?? 0);

    if ($reservationId <= 0 || ($doseNumber !== 1 && $doseNumber !== 2)) {
        $error = "❌ Invalid reservation or dose number.";
    } else {
        if ($model->confirmDose($reservationId, $doseNumber)) {
            $message   = "✅ Dose $doseNumber confirmed successfully.";
            $todayList = $model->getTodayReservations();
        } else {
            $err = sqlsrv_errors();
            $errorDetails = $err ? json_encode($err) : "Unknown error";
            $error = "❌ Failed to confirm dose. Details: $errorDetails";
        }
    }
}

require_once __DIR__ . "/../../View/Center/CenterTodayView.php";
renderCenterTodayView($todayList, $message, $error);