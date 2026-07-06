<?php
-- Schema updates for attendance system

ALTER TABLE `employees`
    ADD COLUMN IF NOT EXISTS `employment_type` VARCHAR(50) NOT NULL DEFAULT 'Full-time' AFTER `position`;

CREATE TABLE IF NOT EXISTS `leave_requests` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `employee_id` INT(11) DEFAULT NULL,
    `employee_name` VARCHAR(100) NOT NULL,
    `designation` VARCHAR(100) DEFAULT NULL,
    `leave_type` VARCHAR(50) NOT NULL,
    `reason` TEXT,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `days` INT(11) NOT NULL DEFAULT 1,
    `status` ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `leave_requests` (`employee_name`, `designation`, `leave_type`, `reason`, `start_date`, `end_date`, `days`, `status`)
SELECT * FROM (
    SELECT 'Samantha Paul', 'Sr. UI Developer', 'Sick Leave', 'To support my spouse and care for family', '2025-07-10', '2025-07-12', 2, 'Pending' UNION ALL
    SELECT 'Gray Noal', 'React Developer', 'Casual Leave', 'Attending a family function out of town', '2025-07-14', '2025-07-30', 15, 'Approved' UNION ALL
    SELECT 'Cameron Williamson', 'Team Lead', 'Personal Leave', 'Need time off to manage personal matters', '2025-07-06', '2025-07-16', 10, 'Rejected' UNION ALL
    SELECT 'Ralph Edwards', 'Full Stack Developer', 'Maternity Leave', 'Starting maternity leave as per doctor advice', '2025-07-02', '2025-07-06', 4, 'Rejected' UNION ALL
    SELECT 'Annette Black', 'Jr. Java Developer', 'Gifted Leave', 'Team leave gifted by management', '2025-08-26', '2025-08-30', 4, 'Approved' UNION ALL
    SELECT 'Marvin McKinney', 'Sr. UI Developer', 'Sick Leave', 'Welcoming our second child and recovery', '2025-08-05', '2025-08-06', 1, 'Pending' UNION ALL
    SELECT 'Theresa Webb', 'React Developer', 'Casual Leave', 'Traveling for a friend wedding', '2025-08-14', '2025-08-16', 2, 'Pending' UNION ALL
    SELECT 'Arlene McCoy', 'Business Analyst', 'Personal Leave', 'Taking a day off to accompany family', '2025-08-02', '2025-08-12', 10, 'Approved'
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM leave_requests LIMIT 1);

INSERT INTO `attendance` (`employee_table_id`, `time_in`, `time_out`, `date_record`, `status`)
SELECT e.id, '08:05:00', '17:00:00', CURDATE(), 'Present'
FROM employees e
WHERE e.status = 'Active'
  AND NOT EXISTS (
      SELECT 1 FROM attendance a WHERE a.employee_table_id = e.id AND a.date_record = CURDATE()
  )
LIMIT 3;
