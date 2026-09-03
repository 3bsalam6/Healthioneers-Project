<?php
class CenterAccountModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get center by username (email/username used at login)
    public function getCenterByUsername($username) {
        $sql = "SELECT Center_ID, Center_Name, Center_Username, Center_HashedPassword,
                   Center_Address, Center_Contact_No, Center_Type, Status
            FROM Vaccination_Centers
            WHERE Center_Username = ?";
        $stmt = sqlsrv_query($this->conn, $sql, [$username]);
        return $stmt ? sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) : null;
    }


    // Update account details
    public function updateAccount($id, $name, $username, $address, $contactNo) {
        $sql = "UPDATE Vaccination_Centers
            SET Center_Name=?, Center_Username=?, Center_Address=?, Center_Contact_No=?
            WHERE Center_ID=?";
        return sqlsrv_query($this->conn, $sql, [$name, $username, $address, $contactNo, $id]);
    }

    // Get password for verification
    public function getPassword($id) {
        $sql = "SELECT Center_HashedPassword FROM Vaccination_Centers WHERE Center_ID = ?";
        $stmt = sqlsrv_query($this->conn, $sql, [$id]);
        return $stmt ? sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) : null;
    }


    // Update password
    public function updatePassword($id, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE Vaccination_Centers SET Center_HashedPassword = ? WHERE Center_ID = ?";
        return sqlsrv_query($this->conn, $sql, [$hashed, $id]);
    }

}