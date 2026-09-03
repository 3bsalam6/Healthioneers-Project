<?php
class RegisterModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Check if email already exists
    public function emailExists($email) {
        $sql = "SELECT Patient_ID FROM Patients WHERE Patient_Username=?";
        $stmt = sqlsrv_query($this->conn,$sql,[$email]);
        return $stmt && sqlsrv_fetch_array($stmt,SQLSRV_FETCH_ASSOC);
    }

    public function registerUser($u) {
        $hashed = password_hash($u['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO Patients 
            (Patient_FName, Patient_LName, Patient_Username, Hashed_Password, Patient_City_ID, Patient_Phone, Patient_National_ID, Created_Date, Status)
            VALUES (?, ?, ?, ?, ?, ?, ?, GETDATE(), 'Active')";

        $params = [
            $u['fname'],
            $u['lname'],
            $u['username'],
            $hashed,
            $u['city_id'],
            $u['phone'],
            $u['national_id']
        ];

        $stmt = sqlsrv_query($this->conn,$sql,$params);

        if (!$stmt) {
            $errors = sqlsrv_errors();
            foreach ($errors as $err) {
                echo "SQL Error: " . $err['message'] . "<br>";
            }
            return false;
        }

        return true;
    }
}