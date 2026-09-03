<?php
require_once __DIR__ . "/../libs/mpdf/src/autoload.php";

$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML("<h1 style='color:#009688'>Hello mPDF</h1>");
$mpdf->Output("test.pdf", "D");