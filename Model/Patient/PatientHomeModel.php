<?php
class PatientHomeModel {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getPatientByUsername($username) {
        $sql = "SELECT Patient_ID, Patient_FName, Status 
                FROM Patients 
                WHERE Patient_Username = ?";
        $stmt = sqlsrv_query($this->conn, $sql, [$username]);
        if ($stmt && ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
            return $row;
        }
        return null;
    }
}