<?php
require_once("data.php");

class ParentClass {
    protected $mysqli;

    public function __construct() {
        $this->mysqli = new mysqli(SERVER_NAME, USER_NAME, PASSWORD, DB_NAME);
        if ($this->mysqli->connect_error) {
            error_log("Database connection failed: " . $this->mysqli->connect_error);
            die("Connection failed: " . $this->mysqli->connect_error);
        }
        error_log("Database connection successful");
    }

    public function getConnection() {
        return $this->mysqli;
    }
}
?>
