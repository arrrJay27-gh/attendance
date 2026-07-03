<?php
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $conn = $database->getConnection();

    $emp_id = isset($_POST['emp_id']) ? trim($_POST['emp_id']) : '';
    $rfid = isset($_POST['rfid']) ? trim($_POST['rfid']) : '';

    if (!empty($emp_id) && !empty($rfid)) {
        
        // 1. Explicitly check if the user profile already exists
        $check_query = "SELECT name FROM users WHERE name = ?";
        $exists = false;
        
        if ($check_stmt = $conn->prepare($check_query)) {
            $check_stmt->bind_param("s", $emp_id);
            $check_stmt->execute();
            $check_stmt->store_result();
            if ($check_stmt->num_rows > 0) {
                $exists = true;
            }
            $check_stmt->close();
        }

        if ($exists) {
            // 2. Profile exists: Safe to update. We don't rely on affected_rows here
            $update_query = "UPDATE users SET biometric_rfid = ? WHERE name = ?";
            if ($stmt = $conn->prepare($update_query)) {
                $stmt->bind_param("ss", $rfid, $emp_id);
                if ($stmt->execute()) {
                    echo "success";
                } else {
                    echo "Database update error: " . $stmt->error;
                }
                $stmt->close();
            }
        } else {
            // 3. Profile doesn't exist: Provide a unique fallback username to satisfy constraints
            // We use the employee ID/Name to guarantee username uniqueness
            $generated_username = "user_" . $emp_id; 
            
            $insert_query = "INSERT INTO users (name, username, biometric_rfid) VALUES (?, ?, ?)";
            if ($insert_stmt = $conn->prepare($insert_query)) {
                $insert_stmt->bind_param("sss", $emp_id, $generated_username, $rfid);
                if ($insert_stmt->execute()) {
                    echo "success";
                } else {
                    echo "Failed to create new user profile record: " . $insert_stmt->error;
                }
                $insert_stmt->close();
            }
        }
    } else {
        echo "Missing required enrollment fields.";
    }
} else {
    echo "Invalid Request Method.";
}
?>