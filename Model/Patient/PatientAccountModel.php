<?php
class PatientAccountModel {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getPatientByUsername($username) {
        $sql = "SELECT * FROM Patients WHERE Patient_Username = ?";
        $stmt = sqlsrv_query($this->conn, $sql, [$username]);
        return $stmt ? sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) : null;
    }

    public function getCities() {
        $cities = [];
        $sql = "SELECT City_ID, City_Name FROM Cities ORDER BY City_Name";
        $stmt = sqlsrv_query($this->conn, $sql);
        while ($stmt && ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
            $cities[] = $row;
        }
        return $cities;
    }

    public function updatePatientInfo($patientId, $fname, $lname, $username, $cityId, $phone) {
        $sql = "UPDATE Patients
                SET Patient_FName=?, Patient_LName=?, Patient_Username=?, Patient_City_ID=?, Patient_Phone=?
                WHERE Patient_ID=?";
        $params = [$fname, $lname, $username, $cityId, $phone, $patientId];
        return sqlsrv_query($this->conn, $sql, $params);
    }

    public function updatePassword($patientId, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE Patients SET Hashed_Password=? WHERE Patient_ID=?";
        return sqlsrv_query($this->conn, $sql, [$hashed, $patientId]);
    }


    public function updateProfileImage($patientId, $imagePath) {
        $sql = "UPDATE Patients SET Patient_Image=? WHERE Patient_ID=?";
        return sqlsrv_query($this->conn, $sql, [$imagePath, $patientId]);
    }

    public function getUploadCount($patientId) {
        $sql = "SELECT Upload_Count FROM Patients WHERE Patient_ID=?";
        $stmt = sqlsrv_query($this->conn, $sql, [$patientId]);
        $row = $stmt ? sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) : null;
        // null = zero
        return $row && isset($row['Upload_Count']) ? (int)$row['Upload_Count'] : 0;
    }
}
?>