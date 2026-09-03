<?php
class CenterDashboardModel {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function getReservationById($reservationId) {
        $sql = "SELECT r.Reservation_ID, r.Patient_ID, r.Vaccine_ID, r.Center_ID,
                       r.First_Confirmation, r.Second_Confirmation,
                       p.Patient_FName, p.Patient_LName, p.Patient_National_ID,
                       v.Vaccine_Name, c.Center_Name, r.Scheduled_Date, r.Reservation_Status
                FROM Reservations r
                JOIN Patients p ON r.Patient_ID = p.Patient_ID
                JOIN Vaccines v ON r.Vaccine_ID = v.Vaccine_ID
                JOIN Vaccination_Centers c ON r.Center_ID = c.Center_ID
                WHERE r.Reservation_ID = ?";
        $stmt = sqlsrv_query($this->conn, $sql, [$reservationId]);
        return $stmt ? sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) : null;
    }

    public function confirmDose($reservationId, $doseNumber) {
        if ($doseNumber == 1) {
            $sql = "UPDATE Reservations
                    SET First_Confirmation = 1, First_Confirmation_Date = GETDATE()
                    WHERE Reservation_ID = ?";
        } elseif ($doseNumber == 2) {
            $sql = "UPDATE Reservations
                    SET Second_Confirmation = 1, Second_Confirmation_Date = GETDATE(),
                        Reservation_Status = 'Finished'
                    WHERE Reservation_ID = ?";
        } else {
            return false;
        }
        return sqlsrv_query($this->conn, $sql, [$reservationId]) !== false;
    }

    public function getTodayReservations() {
        $today = date('Y-m-d');
        $sql = "SELECT r.Reservation_ID, p.Patient_FName, p.Patient_LName,
                       v.Vaccine_Name, r.Scheduled_Date, r.First_Confirmation, r.Second_Confirmation
                FROM Reservations r
                JOIN Patients p ON r.Patient_ID = p.Patient_ID
                JOIN Vaccines v ON r.Vaccine_ID = v.Vaccine_ID
                WHERE CAST(r.Scheduled_Date AS DATE) = ?";
        $stmt = sqlsrv_query($this->conn, $sql, [$today]);
        $list = [];
        while ($stmt && ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
            $list[] = $row;
        }
        return $list;
    }
}