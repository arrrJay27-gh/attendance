<?php
// classes/Attendance.php

class Attendance {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    /**
     * Requirement 2: Multi-Factor Authentication & Biometric Identity Verification Gateway
     */
    public function verifyIdentity($authMode, $rfidValue = null, $biometricData = null, $isFace = false) {
        if ($authMode === 'single') {
            $sql = "SELECT id, role FROM users WHERE biometric_rfid = ? OR fingerprint_template = ? OR facial_map = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("sss", $rfidValue, $biometricData, $biometricData);
        } else {
            // High-Security Multi-Factor: Match RFID AND Biometric Vector
            if ($isFace) {
                $sql = "SELECT id, role FROM users WHERE biometric_rfid = ? AND facial_map = ?";
            } else {
                $sql = "SELECT id, role FROM users WHERE biometric_rfid = ? AND fingerprint_template = ?";
            }
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ss", $rfidValue, $biometricData);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        return $user ? $user : false;
    }

    /**
     * Requirement 2 & 3: Contextual Action Routing Engine (Grace periods, Multi-breaks, Overtime)
     */
    public function logTimePunch($userId, $userRole, $liveTimestamp) {
        $currentDate = date('Y-m-d', strtotime($liveTimestamp));
        $currentTime = date('H:i:s', strtotime($liveTimestamp));

        // Get assigned profile shift parameters
        $shiftStart = "08:00:00"; // Fallback defaults
        if ($userRole === 'employee') {
            $sqlShift = "SELECT shift_start, shift_end FROM employee_profiles WHERE user_id = ?";
            $stmtS = $this->db->prepare($sqlShift);
            $stmtS->bind_param("i", $userId);
            $stmtS->execute();
            $resS = $stmtS->get_result()->fetch_assoc();
            if ($resS) {
                $shiftStart = $resS['shift_start'];
            }
            $stmtS->close();
        }

        // Check if an attendance log entry exists for today
        $sqlLog = "SELECT * FROM attendance_logs WHERE user_id = ? AND punch_date = ?";
        $stmtL = $this->db->prepare($sqlLog);
        $stmtL->bind_param("is", $userId, $currentDate);
        $stmtL->execute();
        $existingLog = $stmtL->get_result()->fetch_assoc();
        $stmtL->close();

        // Execution Logic: Determine action type automatically based on previous punches
        if (!$existingLog) {
            // Action Type 1: Clock In (Start of Shift)
            $isLate = 0;
            $allowedThreshold = strtotime($shiftStart) + (GRACE_PERIOD_MINUTES * 60);
            if (strtotime($currentTime) > $allowedThreshold) {
                $isLate = 1;
            }

            $sqlInsert = "INSERT INTO attendance_logs (user_id, punch_date, time_in, is_late) VALUES (?, ?, ?, ?)";
            $stmtI = $this->db->prepare($sqlInsert);
            $stmtI->bind_param("issi", $userId, $currentDate, $liveTimestamp, $isLate);
            $stmtI->execute();
            $stmtI->close();
            return ["status" => "success", "action" => "Clock In", "is_late" => $isLate];
        }

        // Sequential Check Matrix for Middle Breaks and End Shifts
        if (is_null($existingLog['break_out_am'])) {
            $column = 'break_out_am'; $action = 'AM Break Out';
        } elseif (is_null($existingLog['break_in_am'])) {
            $column = 'break_in_am'; $action = 'AM Break In';
        } elseif (is_null($existingLog['break_out_pm'])) {
            $column = 'break_out_pm'; $action = 'PM Break Out';
        } elseif (is_null($existingLog['break_in_pm'])) {
            $column = 'break_in_pm'; $action = 'PM Break In';
        } elseif (is_null($existingLog['time_out'])) {
            $column = 'time_out'; $action = 'Clock Out';
        } elseif (is_null($existingLog['ot_start'])) {
            $column = 'ot_start'; $action = 'Overtime Start';
        } elseif (is_null($existingLog['ot_end'])) {
            $column = 'ot_end'; $action = 'Overtime End';
        } else {
            return ["status" => "error", "message" => "All terminal operational loops for today are closed."];
        }

        $sqlUpdate = "UPDATE attendance_logs SET {$column} = ? WHERE id = ?";
        $stmtU = $this->db->prepare($sqlUpdate);
        $stmtU->bind_param("si", $liveTimestamp, $existingLog['id']);
        $stmtU->execute();
        $stmtU->close();

        return ["status" => "success", "action" => $action];
    }
}
?>