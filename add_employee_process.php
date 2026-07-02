<?php
require_once 'database.php';

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: employee.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$employeeId = trim($_POST['employee_id'] ?? '');
$jobTitle = trim($_POST['job_title'] ?? '');
$department = trim($_POST['department'] ?? '');
$employmentType = trim($_POST['employment_type'] ?? '');

if ($name === '' || $jobTitle === '' || $department === '' || $employmentType === '') {
    header('Location: employee.php?error=1');
    exit;
}

if ($employeeId === '') {
    $employeeId = 'EMP-' . date('Ymd') . '-' . sprintf('%03d', rand(1, 999));
}

$email = '';
$status = 'Active';

$stmt = $conn->prepare('INSERT INTO `employees` (`employee_id`, `name`, `email`, `department`, `position`, `status`) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->bind_param('ssssss', $employeeId, $name, $email, $department, $jobTitle, $status);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header('Location: employee.php?success=1');
    exit;
}

$stmt->close();
$conn->close();
header('Location: employee.php?error=2');
exit;
