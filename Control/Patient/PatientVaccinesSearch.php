<?php
session_start();

$server = "DESKTOP-OG4GIGD";
$db     = "Vaccination";
$conn   = sqlsrv_connect($server, ["Database"=>$db]);
if ($conn === false) { die(json_encode(["error"=>"DB connect failed"])); }

require_once __DIR__ . "/../../Model/Patient/PatientVaccinesModel.php";
$model = new PatientVaccinesModel($conn);

$term = $_GET['q'] ?? '';
$vaccines = $model->searchVaccines($term);

header('Content-Type: application/json');
echo json_encode($vaccines);