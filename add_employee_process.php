<?php
require_once 'database.php';

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: employee.php');
    exit;
}

// 1. SANITIZE AND COLLECT DATA PAYLOAD (Fixed 'job_title' to 'position')
$name = trim($_POST['name'] ?? '');
$employeeId = trim($_POST['employee_id'] ?? '');
$jobTitle = trim($_POST['position'] ?? ''); // Correct key sent from form modal
$department = trim($_POST['department'] ?? '');
$employmentType = trim($_POST['employment_type'] ?? '');

// 2. STAGE VALIDATION ROUTINE
if ($name === '' || $jobTitle === '' || $department === '' || $employmentType === '') {
    header('Location: employee.php?error=1');
    exit;
}

// 3. AUTO-ID STRATEGY BACKUP
if ($employeeId === '') {
    $employeeId = 'EMP-' . date('Ymd') . '-' . sprintf('%03d', rand(1, 999));
}

$email = '';
$status = 'Active';

// 4. PREPARED DATA ENGINE INSERTION
$stmt = $conn->prepare('INSERT INTO `employees` (`employee_id`, `name`, `email`, `department`, `position`, `status`) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->bind_param('ssssss', $employeeId, $name, $email, $department, $jobTitle, $status);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header('Location: employee.php?success=1');
    exit;
}

// 5. TERMINAL FAILURE CATCH
$stmt->close();
$conn->close();
header('Location: employee.php?error=2');
exit;