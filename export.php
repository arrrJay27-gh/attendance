<?php

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/class/employee.php';
require_once __DIR__ . '/class/attendance.php';
require_once __DIR__ . '/class/leave.php';
require_once __DIR__ . '/class/ExportService.php';

$db = new Database();
$conn = $db->getConnection();

$type = $_GET['type'] ?? '';
$format = strtolower($_GET['format'] ?? 'csv');

$employeeService = new Employee($conn);
$attendanceService = new Attendance($conn);
$leaveService = new Leave($conn);

switch ($type) {
    case 'employees':
        $rows = $employeeService->getAll('', 1000, 0);
        $headers = ['Employee ID', 'Name', 'Department', 'Position', 'Employment Type', 'Status', 'Join Date'];
        $data = array_map(static function ($row) {
            return [
                $row['employee_id'] ?? '',
                $row['name'] ?? '',
                $row['department'] ?? '',
                $row['position'] ?? '',
                $row['employment_type'] ?? 'Full-time',
                $row['status'] ?? '',
                !empty($row['created_at']) ? date('Y-m-d', strtotime($row['created_at'])) : '',
            ];
        }, $rows);
        exportTable('employees', $format, 'Employees Report', $headers, $data);
        break;

    case 'attendance':
    case 'timekeeping':
        $search = trim($_GET['search'] ?? '');
        $department = trim($_GET['department'] ?? '');
        $date = trim($_GET['date'] ?? '');
        $rows = $attendanceService->getRecords($search, $department, $date, 1000, 0);
        $headers = ['ID', 'Employee', 'Department', 'Clock In', 'Clock Out', 'Status', 'Date'];
        $data = array_map(static function ($row) {
            return [
                $row['id'] ?? '',
                $row['employee_name'] ?? '',
                $row['department'] ?? '',
                formatTime($row['time_in'] ?? null),
                formatTime($row['time_out'] ?? null),
                $row['status'] ?? '',
                $row['date_record'] ?? '',
            ];
        }, $rows);
        exportTable('attendance', $format, 'Attendance Report', $headers, $data);
        break;

    case 'leave':
        $search = trim($_GET['search'] ?? '');
        $rows = $leaveService->getAll($search, '', 1000, 0);
        $headers = ['Employee', 'Designation', 'Leave Type', 'Reason', 'Start Date', 'End Date', 'Days', 'Status'];
        $data = array_map(static function ($row) {
            return [
                $row['employee_name'] ?? '',
                $row['designation'] ?? '',
                $row['leave_type'] ?? '',
                $row['reason'] ?? '',
                $row['start_date'] ?? '',
                $row['end_date'] ?? '',
                $row['days'] ?? '',
                $row['status'] ?? '',
            ];
        }, $rows);
        exportTable('leave_requests', $format, 'Leave Requests Report', $headers, $data);
        break;

    default:
        http_response_code(400);
        echo 'Invalid export type.';
}

$conn->close();

function exportTable($filename, $format, $title, array $headers, array $rows)
{
    if ($format === 'pdf') {
        ExportService::sendPdf($title, $headers, $rows, $filename);
    }
    ExportService::sendCsv($filename, $headers, $rows);
}

function formatTime($value)
{
    if (empty($value) || $value === '00:00:00') {
        return '';
    }
    return date('h:i A', strtotime($value));
}
