<?php

require_once __DIR__ . '/database.php';

$db = new Database();
$conn = $db->getConnection();

function columnExists($conn, $table, $column)
{
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $result = $conn->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
    return $result && $result->num_rows > 0;
}

function tableExists($conn, $table)
{
    $table = $conn->real_escape_string($table);
    $result = $conn->query("SHOW TABLES LIKE '{$table}'");
    return $result && $result->num_rows > 0;
}

if (!columnExists($conn, 'employees', 'employment_type')) {
    $conn->query("ALTER TABLE employees ADD COLUMN employment_type VARCHAR(50) NOT NULL DEFAULT 'Full-time' AFTER position");
    echo "Added employees.employment_type\n";
}

if (!tableExists($conn, 'leave_requests')) {
    $conn->query("CREATE TABLE leave_requests (
        id INT(11) NOT NULL AUTO_INCREMENT,
        employee_id INT(11) DEFAULT NULL,
        employee_name VARCHAR(100) NOT NULL,
        designation VARCHAR(100) DEFAULT NULL,
        leave_type VARCHAR(50) NOT NULL,
        reason TEXT,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        days INT(11) NOT NULL DEFAULT 1,
        status ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Created leave_requests table\n";
}

$seedCount = $conn->query('SELECT COUNT(*) AS total FROM leave_requests')->fetch_assoc()['total'] ?? 0;
if ((int) $seedCount === 0) {
    $conn->query("INSERT INTO leave_requests (employee_name, designation, leave_type, reason, start_date, end_date, days, status) VALUES
        ('Samantha Paul', 'Sr. UI Developer', 'Sick Leave', 'To support my spouse and care for family', '2025-07-10', '2025-07-12', 2, 'Pending'),
        ('Gray Noal', 'React Developer', 'Casual Leave', 'Attending a family function out of town', '2025-07-14', '2025-07-30', 15, 'Approved'),
        ('Cameron Williamson', 'Team Lead', 'Personal Leave', 'Need time off to manage personal matters', '2025-07-06', '2025-07-16', 10, 'Rejected'),
        ('Ralph Edwards', 'Full Stack Developer', 'Maternity Leave', 'Starting maternity leave as per doctor advice', '2025-07-02', '2025-07-06', 4, 'Rejected'),
        ('Annette Black', 'Jr. Java Developer', 'Gifted Leave', 'Team leave gifted by management', '2025-08-26', '2025-08-30', 4, 'Approved'),
        ('Marvin McKinney', 'Sr. UI Developer', 'Sick Leave', 'Welcoming our second child and recovery', '2025-08-05', '2025-08-06', 1, 'Pending'),
        ('Theresa Webb', 'React Developer', 'Casual Leave', 'Traveling for a friend wedding', '2025-08-14', '2025-08-16', 2, 'Pending'),
        ('Arlene McCoy', 'Business Analyst', 'Personal Leave', 'Taking a day off to accompany family', '2025-08-02', '2025-08-12', 10, 'Approved')");
    echo "Seeded leave_requests\n";
}

$conn->query("INSERT INTO attendance (employee_table_id, time_in, time_out, date_record, status)
    SELECT e.id, '08:05:00', '17:00:00', CURDATE(), 'Present'
    FROM employees e
    WHERE e.status = 'Active'
      AND NOT EXISTS (
          SELECT 1 FROM attendance a WHERE a.employee_table_id = e.id AND a.date_record = CURDATE()
      )
    LIMIT 3");

echo "Setup complete.\n";
$conn->close();
