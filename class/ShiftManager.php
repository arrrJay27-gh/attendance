<?php
require_once 'Database.php';

class ShiftManager {
    private $conn;

    // Change the constructor to take the open connection directly
    public function __construct($dbConnection) {
        if (!$dbConnection instanceof mysqli) {
            die("Invalid database connection passed to ShiftManager.");
        }
        $this->conn = $dbConnection;
    }

    public function getEmployeesWithShifts($startDate, $endDate) {
        $sql = "SELECT id, name, department FROM employees ORDER BY name ASC";
        $employeeResult = $this->conn->query($sql);
        
        if (!$employeeResult) {
            die("Database Query Failed: " . $this->conn->error);
        }
        
        $schedule = [];
        while ($employee = $employeeResult->fetch_assoc()) {
            $employee['shifts'] = [];
            
            $stmt = $this->conn->prepare("SELECT shift_date, TIME_FORMAT(start_time, '%H:%i') as start, TIME_FORMAT(end_time, '%H:%i') as end, role, color_class FROM shifts WHERE employee_id = ? AND shift_date BETWEEN ? AND ?");
            
            if ($stmt) {
                $stmt->bind_param("iss", $employee['id'], $startDate, $endDate);
                $stmt->execute();
                $shiftResult = $stmt->get_result();
                
                while ($shift = $shiftResult->fetch_assoc()) {
                    $schedule[$employee['id']]['shifts'][$shift['shift_date']] = $shift;
                }
                $stmt->close();
            }
            
            $schedule[$employee['id']]['info'] = $employee;
        }
        return $schedule;
    }
}
?>