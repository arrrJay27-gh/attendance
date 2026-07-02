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
if ($id <= 0) {
    header('Location: employee.php?error=1');
    exit;
}

if ($employee->delete($id)) {
    header('Location: employee.php?success=1');
    exit;
}

header('Location: employee.php?error=2');
exit;
