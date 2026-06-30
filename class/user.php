<?php
class User {
    protected $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function createProfile($empId, $firstName, $lastName, $email, $department, $position, $shiftStart, $shiftEnd, $role = 'employee') {
        $stubUsername = strtolower($empId);
        $defaultHash = password_hash("SecurTime2026!", PASSWORD_BCRYPT);
        
        $sqlUser = "INSERT INTO users (username, password, role, first_name, last_name, email) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtUser = $this->db->prepare($sqlUser);
        $stmtUser->bind_param("ssssss", $stubUsername, $defaultHash, $role, $firstName, $lastName, $email);
        
        if (!$stmtUser->execute()) {
            return false;
        }
        
        $generatedUserId = $this->db->insert_id;
        $stmtUser->close();

        if ($role === 'employee') {
            $sqlProfile = "INSERT INTO employee_profiles (user_id, employee_id_number, department, position, shift_start, shift_end) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtProfile = $this->db->prepare($sqlProfile);
            $stmtProfile->bind_param("isssss", $generatedUserId, $empId, $department, $position, $shiftStart, $shiftEnd);
        } else {
        
            $sqlProfile = "INSERT INTO intern_profiles (user_id, intern_id_number, university_name, course_program, required_hours) VALUES (?, ?, 'Default Univ', 'IT Program', 300)";
            $stmtProfile = $this->db->prepare($sqlProfile);
            $stmtProfile->bind_param("is", $generatedUserId, $empId);
        }

        $result = $stmtProfile->execute();
        $stmtProfile->close();
        return $result ? $generatedUserId : false;
    }

    public function enrollBiometricTemplates($userId, $rfidUid = null, $fingerprintTemplate = null, $facialMapJson = null) {
        $sql = "UPDATE users SET biometric_rfid = ?, fingerprint_template = ?, facial_map = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssi", $rfidUid, $fingerprintTemplate, $facialMapJson, $userId);
        
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
?>