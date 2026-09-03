<?php
class PatientReservationModel {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getPatientByUsername($username) {
        $sql = "SELECT Patient_ID, Patient_FName, Patient_LName, Status 
                FROM Patients WHERE Patient_Username = ?";
        $stmt = sqlsrv_query($this->conn, $sql, [$username]);
        return $stmt ? sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) : null;
    }

    public function getReservationsByPatient($patientId) {
        $reservations = [];
        $sql = "SELECT r.Reservation_ID,
                   r.Reservation_Date,         
                   r.Scheduled_Date,
                   r.Reservation_Status,
                   r.First_Confirmation,
                   r.First_Confirmation_Date,
                   r.Second_Confirmation,
                   r.Second_Confirmation_Date,
                   v.Vaccine_Name,
                   v.dose_gap_days,
                   c.Center_Name,
                   c.Center_Address
            FROM Reservations r
            JOIN Vaccines v ON r.Vaccine_ID = v.Vaccine_ID
            JOIN Vaccination_Centers c ON r.Center_ID = c.Center_ID
            WHERE r.Patient_ID = ?
            ORDER BY r.Reservation_ID DESC";
        $stmt = sqlsrv_query($this->conn, $sql, [$patientId]);
        while ($stmt && ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
            $reservations[] = $row;
        }
        return $reservations;
    }


}