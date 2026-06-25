<?php
// api/process.php
header("Content-Type: application/json");
require_once '../../database.php';
require_once '../attendance.php';

$dbObj = new Database();
$conn = $dbObj->getConnection();
$attendanceEngine = new Attendance($conn);

// Capture asynchronous JSON payloads (supporting AJAX and hardware interfaces)
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (!$input) {
    echo json_encode(["status" => "error", "message" => "Malformed Payload Vector."]);
    exit();
}

$authMode = $input['auth_mode'] ?? 'single';
$rfid = $input['rfid_uid'] ?? null;
$biometricTemplate = $input['biometric_data'] ?? null;
$isFace = $input['is_face'] ?? false;
$timestamp = $input['timestamp'] ?? date('Y-m-d H:i:s');
$isOfflineCached = $input['is_cached'] ?? false; // True if synced from edge cache storage

// Step 1: Run Anti-Spoofing & Live Face Detection Controls
if ($isFace && (!isset($input['liveness_verified']) || $input['liveness_verified'] == false)) {
    echo json_encode(["status" => "error", "message" => "Anti-Spoofing: Liveness test failed."]);
    exit();
}

// Step 2: Process User Identity Validation
$user = $attendanceEngine->verifyIdentity($authMode, $rfid, $biometricTemplate, $isFace);

if (!$user) {
    echo json_encode(["status" => "error", "message" => "Access Denied: Invalid biometric signatures."]);
    exit();
}

// Step 3: Log Clock Event with Sync Tracker Details
$response = $attendanceEngine->logTimePunch($user['id'], $user['role'], $timestamp);

if ($response['status'] === 'success' && $isOfflineCached) {
    $response['sync_status'] = "cached_sync_reconciled";
}

echo json_encode($response);
?>