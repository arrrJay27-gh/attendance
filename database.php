<?php
// config/database.php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'attendance_kiwi'); // Replace with your actual database name

// Core Business & Compliance Configuration Parameters
define('GRACE_PERIOD_MINUTES', 15);
define('AM_BREAK_LIMIT_MINUTES', 20);
define('PM_BREAK_LIMIT_MINUTES', 30);

class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    public $conn;

    public function __construct() {
        // Initialize MySQLi in OOP mode
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

        if ($this->conn->connect_error) {
            die(json_encode([
                "status" => "error", 
                "message" => "Database link failure: " . $this->conn->connect_error
            ]));
        }
        
        // Ensure standard UTF-8 collation encoding
        $this->conn->set_charset("utf8mb4");
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>