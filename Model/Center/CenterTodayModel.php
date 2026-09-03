<?php
class CenterTodayModel {
    private $conn;
    private $centerId;

    public function __construct($conn, $centerId) {
        $this->conn     = $conn;
        $this->centerId = $centerId;
    }

    public function getTodayReservations() {
        $today = date('Y-m-d');
        $sql = "SELECT r.Reservation_ID, p.Patient_FName, p.Patient_LName, p.Patient_National_ID,
                       v.Vaccine_Name, v.dose_gap_days, c.Center_Name, r.Scheduled_Date,
                       r.First_Confirmation, r.First_Confirmation_Date,
                       r.Second_Confirmation, r.Second_Confirmation_Date,
                       r.Reservation_Status
                FROM Reservations r
                JOIN Patients p ON r.Patient_ID = p.Patient_ID
                JOIN Vaccines v ON r.Vaccine_ID = v.Vaccine_ID
                JOIN Vaccination_Centers c ON r.Center_ID = c.Center_ID
                WHERE CAST(r.Scheduled_Date AS DATE) = ?
                  AND r.Center_ID = ?";
        $stmt = sqlsrv_query($this->conn, $sql, [$today, $this->centerId]);
        $list = [];
        while ($stmt && ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
            $list[] = $row;
        }
        return $list;
    }

    public function searchTodayReservations($term) {
        $today = date('Y-m-d');
        $like = "%$term%";
        $sql = "SELECT r.Reservation_ID, p.Patient_FName, p.Patient_LName, p.Patient_National_ID,
                       v.Vaccine_Name, v.dose_gap_days, c.Center_Name, r.Scheduled_Date,
                       r.First_Confirmation, r.First_Confirmation_Date,
                       r.Second_Confirmation, r.Second_Confirmation_Date,
                       r.Reservation_Status
                FROM Reservations r
                JOIN Patients p ON r.Patient_ID = p.Patient_ID
                JOIN Vaccines v ON r.Vaccine_ID = v.Vaccine_ID
                JOIN Vaccination_Centers c ON r.Center_ID = c.Center_ID
                WHERE CAST(r.Scheduled_Date AS DATE) = ?
                  AND r.Center_ID = ?
                  AND (
                      CAST(r.Reservation_ID AS VARCHAR) LIKE ?
                      OR p.Patient_FName LIKE ?
                      OR p.Patient_LName LIKE ?
                      OR v.Vaccine_Name LIKE ?
                  )";
        $stmt = sqlsrv_query($this->conn, $sql, [$today, $this->centerId, $like, $like, $like, $like]);
        $list = [];
        while ($stmt && ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
            $list[] = $row;
        }
        return $list;
    }

    public function confirmDose($reservationId, $doseNumber) {
        if ($doseNumber === 1) {
            // For one‑dose vaccines, finish immediately
            $sql = "UPDATE Reservations
                    SET First_Confirmation = 1,
                        First_Confirmation_Date = GETDATE(),
                        Reservation_Status = CASE 
                            WHEN v.dose_gap_days = 0 THEN 'Finished'
                            ELSE 'Ongoing'
                        END
                    FROM Reservations r
                    JOIN Vaccines v ON r.Vaccine_ID = v.Vaccine_ID
                    WHERE r.Reservation_ID = ? AND r.Center_ID = ?";
            $params = [$reservationId, $this->centerId];
        } elseif ($doseNumber === 2) {
            $sql = "UPDATE Reservations
                    SET Second_Confirmation = 1,
                        Second_Confirmation_Date = GETDATE(),
                        Reservation_Status = 'Finished'
                    WHERE Reservation_ID = ? AND Center_ID = ?";
            $params = [$reservationId, $this->centerId];
        } else {
            return false;
        }
        return sqlsrv_query($this->conn, $sql, $params) !== false;
    }
}