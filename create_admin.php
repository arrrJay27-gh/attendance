<?php
/**
 * Creates an admin user in the `users` table. Run once and then delete this file.
 */
require_once 'database.php';

$database = new Database();
$conn = $database->getConnection();

$adminEmail = 'arnold.camar@kiwidigital.local';
$adminUsername = 'arnold';
$adminFirst = 'Arnold Jay';
$adminLast = 'Camar';
$adminRole = 'admin';
$plainPassword = 'Arnold123!'; // default password for Arnold Jay Camar (change after first login)

$hash = password_hash($plainPassword, PASSWORD_BCRYPT);

$checkSql = "SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1";
if ($stmt = $conn->prepare($checkSql)) {
    $stmt->bind_param('ss', $adminEmail, $adminUsername);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        echo "Admin user already exists.\n";
        exit;
    }
    $stmt->close();
}

$sql = "INSERT INTO users (username, password, role, first_name, last_name, email) VALUES (?, ?, ?, ?, ?, ?)";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param('ssssss', $adminUsername, $hash, $adminRole, $adminFirst, $adminLast, $adminEmail);
    if ($stmt->execute()) {
        echo 'Admin user created successfully.\nEmail: ' . $adminEmail . '\nPassword: ' . $plainPassword . "\n";
    } else {
        echo 'Insert failed: ' . $stmt->error . "\n";
    }
    $stmt->close();
} else {
    echo 'Prepare failed: ' . $conn->error . "\n";
}

$conn->close();

?>
