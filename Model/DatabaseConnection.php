<?php
class DatabaseConnection {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $serverName = "DESKTOP-OG4GIGD"; // your server
        $connectionOptions = [
            "Database" => "Vaccination",
            "Uid" => "",
            "PWD" => ""
        ];
        $this->conn = sqlsrv_connect($serverName, $connectionOptions);

        if ($this->conn === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new DatabaseConnection();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>