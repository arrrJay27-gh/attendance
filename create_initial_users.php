<?php
/**
 * Creates initial users: admin, hr, and user.
 * Run once and then delete this file.
 */
require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

$users = [
    [
        'username' => 'arnold',
        'email' => 'arnold.camar@kiwidigital.local',
        'first_name' => 'Arnold Jay',
        'last_name' => 'Camar',
        'role' => 'admin',
        'password' => 'Arnold123!'
    ],
    [
        'username' => 'hr',
        'email' => 'hr@kiwidigital.local',
        'first_name' => 'HR',
        'last_name' => 'Manager',
        'role' => 'hr',
        'password' => 'HrPass123!'
    ],
    [
        'username' => 'user',
        'email' => 'user@kiwidigital.local',
        'first_name' => 'Default',
        'last_name' => 'User',
        'role' => 'employee',
        'password' => 'UserPass123!'
    ],
];

foreach ($users as $u) {
    $checkSql = "SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param('ss', $u['email'], $u['username']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        echo "User {$u['username']} already exists.\n";
        $stmt->close();
        continue;
    }
    $stmt->close();

    $hash = password_hash($u['password'], PASSWORD_BCRYPT);
    $insert = "INSERT INTO users (username, password, role, first_name, last_name, email) VALUES (?, ?, ?, ?, ?, ?)";
    $ins = $conn->prepare($insert);
    if (!$ins) {
        echo "Prepare failed: " . $conn->error . "\n";
        continue;
    }
    $ins->bind_param('ssssss', $u['username'], $hash, $u['role'], $u['first_name'], $u['last_name'], $u['email']);
    if ($ins->execute()) {
        echo "Created user: {$u['username']} (role: {$u['role']}) with password: {$u['password']}\n";
    } else {
        echo "Insert failed for {$u['username']}: " . $ins->error . "\n";
    }
    $ins->close();
}

$conn->close();

?>
