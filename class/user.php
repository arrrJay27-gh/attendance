<?php
// classes/User.php

class User {
    protected $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    /**
     * Requirement 1: User Profile Creation & Database Enrollment
     */
    public function createProfile($empId, $firstName, $lastName, $email, $department, $position, $shiftStart, $shiftEnd, $role = 'employee') {
        // Step 1: Insert into core authentication accounts table
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

        // Step 2: Bind profile definitions to structural target role tables
        if ($role === 'employee') {
            $sqlProfile = "INSERT INTO employee_profiles (user_id, employee_id_number, department, position, shift_start, shift_end) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtProfile = $this->db->prepare($sqlProfile);
            $stmtProfile->bind_param("isssss", $generatedUserId, $empId, $department, $position, $shiftStart, $shiftEnd);
        } else {
            // Intern variant profile assignment mapping
            $sqlProfile = "INSERT INTO intern_profiles (user_id, intern_id_number, university_name, course_program, required_hours) VALUES (?, ?, 'Default Univ', 'IT Program', 300)";
            $stmtProfile = $this->db->prepare($sqlProfile);
            $stmtProfile->bind_param("is", $generatedUserId, $empId);
        }

        $result = $stmtProfile->execute();
        $stmtProfile->close();
        return $result ? $generatedUserId : false;
    }

    /**
     * Requirement 1: Multi-Modal Enrollment Register (RFID, Fingerprint vectors, Face Arrays)
     */
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