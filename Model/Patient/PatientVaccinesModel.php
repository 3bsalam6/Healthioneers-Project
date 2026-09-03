<?php
class PatientVaccinesModel {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function getPatientByUsername($username) {
        $sql = "SELECT Patient_ID, Patient_FName, Status 
                FROM Patients WHERE Patient_Username = ?";
        $stmt = sqlsrv_query($this->conn, $sql, [$username]);
        return $stmt ? sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) : null;
    }

    public function getActiveVaccines() {
        $vaccines = [];
        $sql = "SELECT Vaccine_ID, Vaccine_Name, dose_gap_days, Precautions, Status 
                FROM Vaccines WHERE Status='Active' ORDER BY Vaccine_Name";
        $stmt = sqlsrv_query($this->conn, $sql);
        while ($stmt && ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
            $vaccines[] = $row;
        }
        return $vaccines;
    }

    public function getActiveCenters() {
        $centers = [];
        $sql = "SELECT c.Center_ID, c.Center_Name, c.Center_Address, ci.City_Name
            FROM Vaccination_Centers c
            JOIN Cities ci ON c.Center_City_ID = ci.City_ID -- adjust if column differs
            WHERE LOWER(c.Status) = 'active'
            ORDER BY c.Center_Name";
        $stmt = sqlsrv_query($this->conn, $sql);
        while ($stmt && ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
            $centers[] = $row;
        }
        return $centers;
    }

    public function hasOngoingReservation($patientId) {
        $sql = "SELECT COUNT(*) AS cnt 
                FROM Reservations 
                WHERE Patient_ID = ? AND Reservation_Status = 'Ongoing'";
        $stmt = sqlsrv_query($this->conn, $sql, [$patientId]);
        $row = $stmt ? sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) : null;
        return $row && $row['cnt'] > 0;
    }

    public function createReservation($patientId, $vaccineId, $centerId, $firstDoseDateSql) {
        $sql = "INSERT INTO Reservations
            (Patient_ID, Vaccine_ID, Center_ID, Dose_Number, Scheduled_Date,
             Reservation_Date, First_Confirmation, Second_Confirmation, Reservation_Status)
            OUTPUT INSERTED.Reservation_ID
            VALUES (?, ?, ?, 1, ?, GETDATE(), 0, 0, 'Ongoing')";
        $params = [$patientId, $vaccineId, $centerId, $firstDoseDateSql];
        $stmt = sqlsrv_query($this->conn, $sql, $params);
        return $stmt ? sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) : null;
    }

    public function searchVaccines($term) {
        $sql = "SELECT Vaccine_ID, Vaccine_Name, dose_gap_days, Precautions, Status
            FROM Vaccines
            WHERE Status = 'Active' AND Vaccine_Name LIKE ?";
        $stmt = sqlsrv_query($this->conn, $sql, ["%$term%"]);
        $results = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $results[] = $row;
        }
        return $results;
    }

}