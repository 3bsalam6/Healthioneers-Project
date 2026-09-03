<?php
session_start();

// Guard: only vaccination center staff
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'center') {
    echo "<h2 style='color:red; text-align:center;'>Unauthorized Access</h2>";
    header("Refresh:2; url=../LoginController.php");
    exit();
}

require_once __DIR__ . "/../../View/Center/CenterDashboardView.php";
renderCenterHomeView();