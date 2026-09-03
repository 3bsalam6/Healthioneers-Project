<?php
session_start();

// Strict access control
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<h2 style='color:red; text-align:center;'>Unauthorized Access</h2>";
    header("Refresh:2; url=../LoginController.php");
    exit();
}

// Connect to SQL Server
$conn = sqlsrv_connect("DESKTOP-OG4GIGD", ["Database" => "Vaccination"]);
if ($conn === false) { die(print_r(sqlsrv_errors(), true)); }

require_once __DIR__ . "/../../Model/Admin/DashboardModel.php";
$model = new DashboardModel($conn);

$message   = "";
$adminName = $_SESSION['fname'] ?? "";

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Add City
    if (isset($_POST['add_city'])) {
        $result = $model->addCity(trim($_POST['city_name']));
        $message = $result; // result already contains success or error message
    }

    // Add Center (with hashed password)
    elseif (isset($_POST['add_center'])) {
        $username = trim($_POST['username']);
        if ($model->isCenterUsernameExists($username)) {
            $message = "❌ Error: The email/username '$username' is already registered for another center.";
        } else {
            $newPassword = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
            $params = [
                trim($_POST['center_name']),
                (int)$_POST['city_id'],
                trim($_POST['address']),
                trim($_POST['contact']),
                $username,
                trim($_POST['center_type']),
                $newPassword
            ];
            $message = $model->addCenter($params)
                ? "✅ Center added successfully."
                : "❌ Failed to add center.";
        }
    }

    // Add Vaccine
    elseif (isset($_POST['add_vaccine'])) {
        $params = [
            trim($_POST['vaccine_name']),
            (int)$_POST['gap_days'],
            trim($_POST['precautions'])
        ];
        $message = $model->addVaccine($params)
            ? "✅ Vaccine added successfully."
            : "❌ Failed to add vaccine.";
    }

    // Update Center (with optional new password)
    elseif (isset($_POST['update_center'])) {
        $username = trim($_POST['username'] ?? '');
        $centerId = (int)($_POST['center_id'] ?? 0);

        if ($model->isCenterUsernameExists($username, $centerId)) {
            $message = "❌ Error: The email/username '$username' is already registered for another center.";
        } else {
            $newPassword = !empty($_POST['password'])
                ? password_hash(trim($_POST['password']), PASSWORD_DEFAULT)
                : null;

            $params = [
                trim($_POST['center_name'] ?? ''),
                (int)($_POST['city_id'] ?? 0),
                trim($_POST['address'] ?? ''),
                trim($_POST['contact'] ?? ''),
                $username,
                $newPassword, // null if blank
                trim($_POST['center_type'] ?? ''),
                trim($_POST['status'] ?? ''),
                $centerId
            ];

            $message = $model->updateCenter($params)
                ? "✅ Center updated successfully."
                : "❌ Failed to update center.";
        }
    }

    // Delete Center
    elseif (isset($_POST['delete_center'])) {
        $message = $model->deleteCenter((int)$_POST['center_id'])
            ? "✅ Center deleted successfully."
            : "❌ Failed to delete center.";
    }
}

// Prefetch cities
$cities = $model->getCities();

// Search centers
$searchResults = [];
$searchedCityId = null;
if (isset($_POST['search_center'])) {
    $searchedCityId = (int)$_POST['search_city_id'];
    $searchResults = $model->searchCentersByCity($searchedCityId);
}

// List patients
$userList = $model->getPatients();

// Search patients
$userSearchResults = [];
if (isset($_POST['search_user'])) {
    $conditions = [];
    $params = [];

    if (!empty($_POST['fname'])) {
        $conditions[] = "Patient_FName LIKE ?";
        $params[] = "%" . trim($_POST['fname']) . "%";
    }
    if (!empty($_POST['lname'])) {
        $conditions[] = "Patient_LName LIKE ?";
        $params[] = "%" . trim($_POST['lname']) . "%";
    }
    if (!empty($_POST['userid'])) {
        $conditions[] = "Patient_ID = ?";
        $params[] = (int)$_POST['userid'];
    }
    if (!empty($_POST['nid'])) {
        $conditions[] = "Patient_National_ID LIKE ?";
        $params[] = "%" . trim($_POST['nid']) . "%";
    }

    if ($conditions) {
        $userSearchResults = $model->searchPatients($conditions, $params);
    }
}

require_once __DIR__ . "/../../View/Admin/DashboardView.php";
renderDashboardView($adminName, $message, $cities, $searchResults, $userList, $userSearchResults, $searchedCityId);
?>