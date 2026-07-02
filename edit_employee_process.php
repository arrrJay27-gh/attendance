<?php
require_once 'database.php';
require_once 'class/employee.php';

$database = new Database();
$conn = $database->getConnection();
$employee = new Employee($conn);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: employee.php');
    exit;
}

$id = intval($_POST['id'] ?? 0);
$employeeId = trim($_POST['employee_id'] ?? '');
$name = trim($_POST['name'] ?? '');
$position = trim($_POST['position'] ?? '');
$department = trim($_POST['department'] ?? '');
$status = trim($_POST['status'] ?? 'Active');

if ($id <= 0 || $name === '' || $position === '') {
    header('Location: employee.php?error=1');
    exit;
}

if ($employee->update($id, $employeeId, $name, $department, $position, $status)) {
    header('Location: employee.php?success=1');
    exit;
}

header('Location: employee.php?error=2');
exit;
