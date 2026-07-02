<?php
/**
 * run_sql.php
 * Executes the SQL statements in users.sql using the project's Database class.
 * Run this from the browser or CLI once, then delete it.
 */
require_once 'database.php';

$sqlFile = __DIR__ . DIRECTORY_SEPARATOR . 'users.sql';
if (!file_exists($sqlFile)) {
    echo "users.sql not found at: $sqlFile\n";
    exit(1);
}

$sql = file_get_contents($sqlFile);
if ($sql === false) {
    echo "Failed to read users.sql\n";
    exit(1);
}

$db = new Database();
$conn = $db->getConnection();

if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());

    if ($conn->errno) {
        echo "SQL finished with warnings/errors: (" . $conn->errno . ") " . $conn->error . "\n";
        exit(1);
    }

    echo "users.sql executed successfully.\n";
    exit(0);
} else {
    echo "Failed to execute SQL: (" . $conn->errno . ") " . $conn->error . "\n";
    exit(1);
}
