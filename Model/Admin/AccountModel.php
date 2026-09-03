<?php
require_once __DIR__ . "/../DatabaseConnection.php";

class AccountModel {
    private $conn;

    public function __construct() {
        // Use Singleton connection
        $this->conn = DatabaseConnection::getInstance()->getConnection();
    }

    public function getAdminByUsername($username) {
        $sql = "SELECT Admin_ID, Admin_Fname, Admin_Lname, Admin_Username, Admin_HashedPassword
                FROM Admin WHERE Admin_Username = ?";
        $stmt = sqlsrv_query($this->conn, $sql, [$username]);
        return $stmt ? sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) : null;
    }

    public function updateAccount($id, $fname, $lname, $username) {
        $sql = "UPDATE Admin SET Admin_Fname=?, Admin_Lname=?, Admin_Username=? WHERE Admin_ID=?";
        return sqlsrv_query($this->conn, $sql, [$fname, $lname, $username, $id]);
    }

    public function getPassword($id) {
        $sql = "SELECT Admin_HashedPassword FROM Admin WHERE Admin_ID=?";
        $stmt = sqlsrv_query($this->conn, $sql, [$id]);
        return $stmt ? sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) : null;
    }

    public function updatePassword($id, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE Admin SET Admin_HashedPassword=? WHERE Admin_ID=?";
        return sqlsrv_query($this->conn, $sql, [$hashed, $id]);
    }
}
?>