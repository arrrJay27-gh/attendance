<?php

require_once 'database.php';
require_once 'class/Employee.php';

$database = new Database();
$conn = $database->getConnection();
$employee = new Employee($conn);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: employee.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$employeeId = trim($_POST['employee_id'] ?? '');
$position = trim($_POST['position'] ?? '');
$department = trim($_POST['department'] ?? '');
$employmentType = trim($_POST['employment_type'] ?? 'Full-time');

if ($name === '' || $position === '' || $department === '' || $employmentType === '') {
    header('Location: employee.php?error=1');
    exit;
}

$newId = $employee->create($name, $employeeId, $position, $department, $employmentType);
$conn->close();

if ($newId) {
    header('Location: employee.php?success=1');
    exit;
}

header('Location: employee.php?error=2');
exit;
