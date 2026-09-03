<?php
class DashboardModel {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function q($sql, $params = []) {
        $stmt = sqlsrv_query($this->conn, $sql, $params);
        if ($stmt === false) {
            $errors = sqlsrv_errors();
            throw new RuntimeException('SQL Error: ' . json_encode($errors, JSON_UNESCAPED_UNICODE));
        }
        return $stmt;
    }

    // Add City
    public function addCity($cityName) {
        // Check if city already exists
        $checkSql = "SELECT 1 FROM Vaccination.dbo.Cities WHERE City_Name = ?";
        $checkStmt = sqlsrv_query($this->conn, $checkSql, [$cityName]);

        if ($checkStmt && sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC)) {
            // City already exists
            return "❌ City '$cityName' already exists.";
        }

        // Insert new city
        $sql = "INSERT INTO Vaccination.dbo.Cities (City_Name) VALUES (?)";
        $stmt = sqlsrv_query($this->conn, $sql, [$cityName]);

        if ($stmt) {
            return "✅ City '$cityName' added successfully.";
        } else {
            return "❌ Failed to add city.";
        }
    }

    // Check if Center Username exists
    public function isCenterUsernameExists($username, $excludeCenterId = null) {
        $sql = "SELECT 1 FROM Vaccination.dbo.Vaccination_Centers WHERE Center_Username = ?";
        $params = [$username];

        if ($excludeCenterId !== null) {
            $sql .= " AND Center_ID != ?";
            $params[] = $excludeCenterId;
        }

        $stmt = sqlsrv_query($this->conn, $sql, $params);
        if ($stmt && sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            return true;
        }
        return false;
    }

    // Add Center (store hashed password)
    public function addCenter($params) {
        $sql = "INSERT INTO Vaccination.dbo.Vaccination_Centers
                (Center_Name, Center_City_ID, Center_Address, Center_Contact_No, Center_Username,Center_Type, Center_HashedPassword, Status)
                VALUES (?, ?, ?, ?, ?, ?,?, 'Active')";
        return $this->q($sql, $params) !== false;
    }

    // Update Center (optional new password)
    public function updateCenter($params) {
        list($name, $cityId, $address, $contact, $username, $newPassword, $type, $status, $centerId) = $params;

        if ($newPassword) {
            // Update everything including password
            $sql = "UPDATE Vaccination_Centers
                SET Center_Name=?, Center_City_ID=?, Center_Address=?, Center_Contact_No=?,
                    Center_Username=?, Center_HashedPassword=?, Center_Type=?, Status=?
                WHERE Center_ID=?";
            $bind = [$name, $cityId, $address, $contact, $username, $newPassword, $type, $status, $centerId];
        } else {
            // Leave password unchanged
            $sql = "UPDATE Vaccination_Centers
                SET Center_Name=?, Center_City_ID=?, Center_Address=?, Center_Contact_No=?,
                    Center_Username=?, Center_Type=?, Status=?
                WHERE Center_ID=?";
            $bind = [$name, $cityId, $address, $contact, $username, $type, $status, $centerId];
        }

        return sqlsrv_query($this->conn, $sql, $bind);
    }

    // Delete Center
    public function deleteCenter($centerId) {
        $sql = "DELETE FROM Vaccination.dbo.Vaccination_Centers WHERE Center_ID = ?";
        return $this->q($sql, [$centerId]) !== false;
    }

    // Get all cities
    public function getCities() {
        $cities = [];
        $stmt = $this->q("SELECT City_ID, City_Name FROM Vaccination.dbo.Cities ORDER BY City_Name ASC");
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $cities[] = $row;
        }
        return $cities;
    }

    // Search centers by city
    public function searchCentersByCity($cityId) {
        $results = [];
        $stmt = $this->q("SELECT * FROM Vaccination.dbo.Vaccination_Centers WHERE Center_City_ID = ?", [$cityId]);
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $results[] = $row;
        }
        return $results;
    }

    // List all patients
    public function getPatients() {
        $users = [];
        $stmt = $this->q("SELECT Patient_ID, Patient_FName, Patient_LName, Patient_Username, Patient_Phone, Patient_National_ID
                          FROM Vaccination.dbo.Patients ORDER BY Patient_ID DESC");
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $users[] = $row;
        }
        return $users;
    }

    // Search patients
    public function searchPatients($conditions, $params) {
        $sql = "SELECT Patient_ID, Patient_FName, Patient_LName, Patient_Username, Patient_Phone, Patient_National_ID
                FROM Vaccination.dbo.Patients WHERE " . implode(" AND ", $conditions);
        $results = [];
        $stmt = $this->q($sql, $params);
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $results[] = $row;
        }
        return $results;
    }

    // Add Vaccine
    public function addVaccine($params) {
        $sql = "INSERT INTO Vaccines (Vaccine_Name, dose_gap_days, Precautions)
            VALUES (?, ?, ?)";
        $stmt = sqlsrv_query($this->conn, $sql, $params);

        if ($stmt === false) {
            $errors = sqlsrv_errors();
            throw new RuntimeException('SQL Error: ' . json_encode($errors, JSON_UNESCAPED_UNICODE));
        }

        return true;
    }
}
?>