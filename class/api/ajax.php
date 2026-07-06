<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../../database.php';
require_once __DIR__ . '/../Dashboard.php';
require_once __DIR__ . '/../Employee.php';
require_once __DIR__ . '/../Attendance.php';
require_once __DIR__ . '/../Leave.php';

$db = new Database();
$conn = $db->getConnection();

$dashboard = new Dashboard($conn);
$employeeService = new Employee($conn);
$attendanceService = new Attendance($conn);
$leaveService = new Leave($conn);

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);
if (!is_array($input)) {
    $input = [];
}

$action = $_GET['action'] ?? $_POST['action'] ?? ($input['action'] ?? '');

try {
    switch ($action) {
        case 'dashboard_stats':
            echo json_encode([
                'status' => 'success',
                'data' => $dashboard->getTodayStats(),
                'analytics' => $dashboard->getMonthlyAnalytics(),
                'activity' => $dashboard->getRecentActivity(),
            ]);
            break;

        case 'employee_list':
            $search = trim($_GET['search'] ?? ($input['search'] ?? ''));
            $limit = (int) ($_GET['limit'] ?? ($input['limit'] ?? 100));
            $offset = (int) ($_GET['offset'] ?? ($input['offset'] ?? 0));
            echo json_encode([
                'status' => 'success',
                'data' => $employeeService->getAll($search, $limit, $offset),
                'total' => $employeeService->countAll($search),
            ]);
            break;

        case 'employee_create':
            $name = trim($input['name'] ?? '');
            $employeeId = trim($input['employee_id'] ?? '');
            $position = trim($input['position'] ?? '');
            $department = trim($input['department'] ?? '');
            $employmentType = trim($input['employment_type'] ?? 'Full-time');

            if ($name === '' || $position === '' || $department === '') {
                throw new RuntimeException('Required employee fields are missing.');
            }

            $newId = $employeeService->create($name, $employeeId, $position, $department, $employmentType);
            if (!$newId) {
                throw new RuntimeException('Unable to create employee record.');
            }

            echo json_encode(['status' => 'success', 'message' => 'Employee created.', 'id' => $newId]);
            break;

        case 'employee_update':
            $id = (int) ($input['id'] ?? 0);
            $employeeId = trim($input['employee_id'] ?? '');
            $name = trim($input['name'] ?? '');
            $position = trim($input['position'] ?? '');
            $department = trim($input['department'] ?? '');
            $status = trim($input['status'] ?? 'Active');

            if ($id <= 0 || $name === '' || $position === '') {
                throw new RuntimeException('Invalid employee update payload.');
            }

            if (!$employeeService->update($id, $employeeId, $name, $department, $position, $status)) {
                throw new RuntimeException('Unable to update employee record.');
            }

            echo json_encode(['status' => 'success', 'message' => 'Employee updated.']);
            break;

        case 'employee_delete':
            $id = (int) ($input['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid employee id.');
            }
            if (!$employeeService->delete($id)) {
                throw new RuntimeException('Unable to delete employee record.');
            }
            echo json_encode(['status' => 'success', 'message' => 'Employee deleted.']);
            break;

        case 'attendance_list':
            $search = trim($_GET['search'] ?? ($input['search'] ?? ''));
            $department = trim($_GET['department'] ?? ($input['department'] ?? ''));
            $date = trim($_GET['date'] ?? ($input['date'] ?? ''));
            echo json_encode([
                'status' => 'success',
                'data' => $attendanceService->getRecords($search, $department, $date),
                'stats' => $dashboard->getTodayStats($date ?: null),
                'departments' => $attendanceService->getDepartments(),
            ]);
            break;

        case 'attendance_punch':
            $rfid = trim($input['rfid_uid'] ?? '');
            $timestamp = $input['timestamp'] ?? date('Y-m-d H:i:s');

            if ($rfid === '') {
                throw new RuntimeException('RFID value is required.');
            }

            $user = $attendanceService->verifyIdentity('rfid', $rfid);
            if (!$user) {
                throw new RuntimeException('Access denied: employee not found for RFID.');
            }

            $response = $attendanceService->logTimePunch((int) $user['id'], $user['name'], $timestamp);
            echo json_encode($response);
            break;

        case 'leave_list':
            $search = trim($_GET['search'] ?? ($input['search'] ?? ''));
            $status = trim($_GET['status'] ?? ($input['status'] ?? ''));
            echo json_encode([
                'status' => 'success',
                'data' => $leaveService->getAll($search, $status),
            ]);
            break;

        case 'leave_update_status':
            $id = (int) ($input['id'] ?? 0);
            $status = trim($input['status'] ?? '');
            if ($id <= 0 || $status === '') {
                throw new RuntimeException('Invalid leave status payload.');
            }
            if (!$leaveService->updateStatus($id, $status)) {
                throw new RuntimeException('Unable to update leave status.');
            }
            echo json_encode(['status' => 'success', 'message' => 'Leave status updated.']);
            break;

        case 'leave_approve_all':
            if (!$leaveService->approveAllPending()) {
                throw new RuntimeException('Unable to approve pending leave requests.');
            }
            echo json_encode(['status' => 'success', 'message' => 'All pending leave requests approved.']);
            break;

        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Unknown action.']);
    }
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
