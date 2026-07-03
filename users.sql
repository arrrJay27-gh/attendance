-- Create `users` table for authentication
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'employee',
  `first_name` VARCHAR(100) DEFAULT NULL,
  `last_name` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `biometric_rfid` VARCHAR(255) DEFAULT NULL, 
  `fingerprint_template` LONGTEXT DEFAULT NULL,
  `facial_map` LONGTEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  UNIQUE KEY `uq_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE attendance (
    id INT(11) NOT NULL AUTO_INCREMENT,
    employee_name VARCHAR(100) NOT NULL,
    department VARCHAR(150) NOT NULL,
    time_in TIME(6) NOT NULL,
    time_out TIME(6) NOT NULL,
    date_record DATE NOT NULL,
    status VARCHAR(150) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

CREATE TABLE employees (
    id INT(11) PRIMARY KEY,
    employee_id VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    department VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    status VARCHAR(50) NOT NULL,
    created_at DATETIME NOT NULL
);

INSERT INTO employees (id, employee_id, name, email, department, position, status, created_at) VALUES 
(1, 'EMP-001', 'Arnold Jay Camar', 'arnold@example.com', 'IT Department', 'Web Developer', 'Active', '2026-07-01 10:35:36'),
(2, 'EMP-002', 'Ma Princess Rumualdo', 'princess@example.com', 'Human Resources', 'HR Specialist', 'Active', '2026-07-01 10:35:36'),
(6, 'EM1002', 'Rendell Lopez', NULL, 'IT Department', 'cyber security', 'Active', '2026-07-02 09:54:50'),
(7, 'EMP-003', 'christian kiriben', NULL, 'IT Department', 'web dev.', 'Active', '2026-07-02 10:37:37'),
(8, 'EMP-20260702-247', 'Arnold Jay Camar', NULL, 'IT Department', 'ui/ux', 'Active', '2026-07-02 12:50:20'),
(9, 'EMP-20260702-514', 'Arnold Jay Camar', NULL, 'IT Department', 'ui/ux', 'Active', '2026-07-02 12:50:50');