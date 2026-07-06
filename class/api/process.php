<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../../database.php';
require_once __DIR__ . '/../Attendance.php';
require_once __DIR__ . '/../Employee.php';

$dbObj = new Database();
$conn = $dbObj->getConnection();
$attendanceEngine = new Attendance($conn);

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (!$input) {
    echo json_encode(['status' => 'error', 'message' => 'Malformed payload.']);
    exit;
}

$rfid = trim($input['rfid_uid'] ?? '');
$isFace = !empty($input['is_face']);
$timestamp = $input['timestamp'] ?? date('Y-m-d H:i:s');
$isOfflineCached = !empty($input['is_cached']);

if ($isFace && empty($input['liveness_verified'])) {
    echo json_encode(['status' => 'error', 'message' => 'Anti-Spoofing: Liveness test failed.']);
    exit;
}

if ($rfid === '') {
    echo json_encode(['status' => 'error', 'message' => 'RFID value is required.']);
    exit;
}

$user = $attendanceEngine->verifyIdentity('rfid', $rfid);
if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'Access Denied: Invalid biometric signature.']);
    exit;
}

$response = $attendanceEngine->logTimePunch((int) $user['id'], $user['name'], $timestamp);

if (($response['status'] ?? '') === 'success' && $isOfflineCached) {
    $response['sync_status'] = 'cached_sync_reconciled';
}

echo json_encode($response);
$conn->close();
