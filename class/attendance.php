<?php
// class/attendance.php

class Attendance {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
        $this->db->query("SET time_zone = '+08:00'");
    }

    public function verifyIdentity($authMode, $rfidValue = null) {
        $sql = "SELECT id, role FROM users WHERE rfid_uid = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param("s", $rfidValue);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $user ? $user : false;
    }

    public function logTimePunch($employeeName, $liveTimestamp) {
    $currentDate = date('Y-m-d', strtotime($liveTimestamp));
    // Use 'h:i:s A' for 12-hour format with AM/PM
    $currentTime = date('h:i:s A', strtotime($liveTimestamp)); 
    $department = 'General';

        

        $sqlLog = "SELECT id, time_out FROM attendance WHERE employee_name = ? AND date_record = ?";
        $stmtL = $this->db->prepare($sqlLog);
        $stmtL->bind_param("ss", $employeeName, $currentDate);
        $stmtL->execute();
        $existingLog = $stmtL->get_result()->fetch_assoc();
        $stmtL->close();

        if (!$existingLog) {
            $isLate = (strtotime($currentTime) > strtotime("08:15:00")) ? 'Late' : 'Present';
            $sqlInsert = "INSERT INTO attendance (employee_name, department, time_in, date_record, status) VALUES (?, ?, ?, ?, ?)";
            $stmtI = $this->db->prepare($sqlInsert);
            $stmtI->bind_param("sssss", $employeeName, $department, $currentTime, $currentDate, $isLate);
            if ($stmtI->execute()) {
                $stmtI->close();
                return ["status" => "success", "action" => "Clock In", "status_type" => $isLate];
            }
            return ["status" => "error", "message" => "Insert Error: " . $this->db->error];
        }

        if (empty($existingLog['time_out']) || $existingLog['time_out'] === '00:00:00') {
            $sqlUpdate = "UPDATE attendance SET time_out = ? WHERE id = ?";
            $stmtU = $this->db->prepare($sqlUpdate);
            $stmtU->bind_param("si", $currentTime, $existingLog['id']);
            $stmtU->execute();
            $stmtU->close();
            return ["status" => "success", "action" => "Clock Out"];
        }

        return ["status" => "error", "message" => "Shift completed for today."];
    }
}
?>