<?php
/**
 * Attendance Tracking & Timekeeping Engine
 * Maps dynamically to `attendance`, `employees`, and `employee_shifts` table structures.
 */
class Attendance {
    private $db;
    private $grace_period;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
        $this->grace_period = defined('GRACE_PERIOD_MINUTES') ? GRACE_PERIOD_MINUTES : 30;
    }

    /**
     * Core Logger Engine for processing terminal time punches (RFID/Kiosk)
     * Dynamically handles Time In and Time Out enforcement based on the specific shift row timing.
     */
    public function logTimePunch($fullName, $liveTimestamp, $punchType = 'shift') {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $time_now_formatted = date('H:i:s'); 
        $fullName = trim($fullName); 

        // Initial setup placeholders
        $shift_start = "";
        $shift_end = ""; 
        $target_shift_date = $today; 
        $employee_id = 0; 
        $actualRealName = $fullName;

        // 1. SMART ID LOOKUP ENGINE
        if (is_numeric($fullName)) {
            $sqlEmp = "SELECT id, name FROM employees WHERE id = ? OR employee_id = ? LIMIT 1";
            $stmtEmp = $this->db->prepare($sqlEmp);
            $empIdInt = (int)$fullName;
            $stmtEmp->bind_param("ii", $empIdInt, $empIdInt); 
        } elseif (strpos($fullName, 'EMP-') === 0) {
            $sqlEmp = "SELECT id, name FROM employees WHERE employee_id = ? LIMIT 1";
            $stmtEmp = $this->db->prepare($sqlEmp);
            $stmtEmp->bind_param("s", $fullName);
        } else {
            $sqlEmp = "SELECT id, name FROM employees WHERE name = ? LIMIT 1";
            $stmtEmp = $this->db->prepare($sqlEmp);
            $stmtEmp->bind_param("s", $fullName);
        }

        if ($stmtEmp) {
            $stmtEmp->execute();
            $empRes = $stmtEmp->get_result()->fetch_assoc();
            $stmtEmp->close();
            if ($empRes) {
                $employee_id = $empRes['id'];
                $actualRealName = $empRes['name'];
            }
        }

        if ($employee_id === 0) {
            return [
                'status' => 'error',
                'message' => "Access Denied: Identifier '" . htmlspecialchars($fullName) . "' does not match any registered employee profile."
            ];
        }

        // 2. CHECK UNCLOSED LOGS FROM YESTERDAY (For Overnight/Graveyard Shifts)
        // Only count it as an existing active log if time_in is actually recorded and not empty
        $checkYesterdaySql = "SELECT id, time_in, break_in, break_out, time_out, date_record FROM attendance WHERE employee_id = ? AND date_record = ? AND time_in IS NOT NULL AND time_in != '00:00:00' LIMIT 1";
        $checkYesterdayStmt = $this->db->prepare($checkYesterdaySql);
        $checkYesterdayStmt->bind_param("is", $employee_id, $yesterday);
        $checkYesterdayStmt->execute();
        $existingYesterdayLog = $checkYesterdayStmt->get_result()->fetch_assoc();
        $checkYesterdayStmt->close();

        if ($existingYesterdayLog && (empty($existingYesterdayLog['time_out']) || $existingYesterdayLog['time_out'] == '00:00:00')) {
            $target_shift_date = $yesterday;
            $existingLog = $existingYesterdayLog;
        } else {
            $target_shift_date = $today;
            // Only capture today's row if a valid Time In punch was already stored
            $checkTodaySql = "SELECT id, time_in, break_in, break_out, time_out, date_record FROM attendance WHERE employee_id = ? AND date_record = ? AND time_in IS NOT NULL AND time_in != '00:00:00' LIMIT 1";
            $checkTodayStmt = $this->db->prepare($checkTodaySql);
            $checkTodayStmt->bind_param("is", $employee_id, $today);
            $checkTodayStmt->execute();
            $existingLog = $checkTodayStmt->get_result()->fetch_assoc();
            $checkTodayStmt->close();
        }

        // 3. SMART ROSTER LOOKUP (Finds shift even if checking in early or late across day boundaries)
        $has_valid_shift = false;
        
        $sqlShift = "SELECT start_time, end_time, shift_date FROM employee_shifts 
                     WHERE employee_id = ? AND shift_date IN (?, ?, ?) 
                     ORDER BY FIELD(shift_date, ?, ?, ?)";
        $stmtShift = $this->db->prepare($sqlShift);
        
        if ($stmtShift) {
            $stmtShift->bind_param("issssss", $employee_id, $yesterday, $today, $tomorrow, $yesterday, $today, $tomorrow);
            $stmtShift->execute();
            $resultShifts = $stmtShift->get_result();
            
            $potential_shifts = [];
            while ($row = $resultShifts->fetch_assoc()) {
                $potential_shifts[] = $row;
            }
            $stmtShift->close();

            $current_epoch = strtotime($time_now_formatted);

            // If performing a Time Out and tied to an existing valid log, prioritize that exact date
            if ($existingLog && !empty($existingLog['time_in']) && $existingLog['time_in'] != '00:00:00') {
                foreach ($potential_shifts as $pshift) {
                    if ($pshift['shift_date'] === $existingLog['date_record']) {
                        $shift_start = $pshift['start_time'];
                        $shift_end = $pshift['end_time'];
                        $target_shift_date = $pshift['shift_date'];
                        $has_valid_shift = true;
                        break;
                    }
                }
            }

            // Otherwise (Time In), find the closest logical shift matching current clock time
            if (!$has_valid_shift && !empty($potential_shifts)) {
                foreach ($potential_shifts as $pshift) {
                    $p_start = strtotime($pshift['start_time']);
                    $p_end = strtotime($pshift['end_time']);
                    
                    if ($pshift['shift_date'] === $today) {
                        // Regular day shift or early check-in / late check-in window
                        if ($current_epoch >= ($p_start - 14400) && $current_epoch <= ($p_end + 14400)) {
                            $shift_start = $pshift['start_time'];
                            $shift_end = $pshift['end_time'];
                            $target_shift_date = $pshift['shift_date'];
                            $has_valid_shift = true;
                            break;
                        }
                    } elseif ($pshift['shift_date'] === $yesterday && $p_start > $p_end) {
                        // Graveyard rollover from yesterday
                        if ($current_epoch <= ($p_end + 14400)) {
                            $shift_start = $pshift['start_time'];
                            $shift_end = $pshift['end_time'];
                            $target_shift_date = $pshift['shift_date'];
                            $existingLog = $existingYesterdayLog;
                            $has_valid_shift = true;
                            break;
                        }
                    } elseif ($pshift['shift_date'] === $tomorrow) {
                        // Checking in late for a midnight shift tomorrow
                        if ($current_epoch >= ($p_start - 14400)) {
                            $shift_start = $pshift['start_time'];
                            $shift_end = $pshift['end_time'];
                            $target_shift_date = $pshift['shift_date'];
                            $has_valid_shift = true;
                            break;
                        }
                    }
                }
                
                if (!$has_valid_shift) {
                    foreach ($potential_shifts as $pshift) {
                        if ($pshift['shift_date'] === $today) {
                            $shift_start = $pshift['start_time'];
                            $shift_end = $pshift['end_time'];
                            $target_shift_date = $pshift['shift_date'];
                            $has_valid_shift = true;
                            break;
                        }
                    }
                }
            }
        }

        // Reject if no rostered schedule row maps to the tracking timeline
        if (!$has_valid_shift) {
            $formatted_date = date('F d, Y', strtotime($today));
            return [
                'status' => 'error',
                'message' => "Access Denied: You do not have an assigned rostered shift scheduled for today ($formatted_date)."
            ];
        }

        $day_of_shift = date('l', strtotime($target_shift_date));

        // ==================================================================
        // ROUTE A: PROCESSING BREAK OPERATIONS (Break In / Break Out Button)
        // ==================================================================
        if ($punchType === 'break') {
            if (!$existingLog || empty($existingLog['time_in']) || $existingLog['time_in'] == '00:00:00') {
                return ['status' => 'error', 'message' => "Operation Blocked: You must clock a 'Time In' punch before logging a break."];
            }

            if (empty($existingLog['break_in']) || $existingLog['break_in'] == '00:00:00') {
                if ($existingLog['break_out'] !== '00:00:00') {
                    $lastBreakOut = strtotime($existingLog['break_out']);
                    $now = strtotime($time_now_formatted);
                    if (($now - $lastBreakOut) < 300) {
                        return [
                            'status' => 'error', 
                            'message' => 'Cooldown Active: Please wait 5 minutes after your Break Out before breaking in.'
                        ];
                    }
                }

                $updateSql = "UPDATE attendance SET break_in = ? WHERE id = ?";
                $updateStmt = $this->db->prepare($updateSql);
                $updateStmt->bind_param("si", $time_now_formatted, $existingLog['id']);
                $updateStmt->execute();
                $updateStmt->close();
                return ['status' => 'success', 'action' => 'Break In', 'message' => "Enjoy your break, " . htmlspecialchars($actualRealName) . "!"];
            }

            if (empty($existingLog['break_out']) || $existingLog['break_out'] == '00:00:00') {
                $updateSql = "UPDATE attendance SET break_out = ? WHERE id = ?";
                $updateStmt = $this->db->prepare($updateSql);
                $updateStmt->bind_param("si", $time_now_formatted, $existingLog['id']);
                $updateStmt->execute();
                $updateStmt->close();
                return ['status' => 'success', 'action' => 'Break Out', 'message' => "Welcome back, " . htmlspecialchars($actualRealName) . "!"];
            }
            
            return ['status' => 'error', 'message' => 'Lunch break cycles for this shift are already logged.'];
        }

        // ==================================================================
        // ROUTE B: PROCESSING SHIFT OPERATIONS (Time In / Time Out Button)
        // ==================================================================
        
        // --- CASE 1: TIME IN OPERATION ---
        if (!$existingLog || empty($existingLog['time_in']) || $existingLog['time_in'] == '00:00:00') {
            $punch_epoch = strtotime($time_now_formatted);
            $shift_start_epoch = strtotime($shift_start);
            
            $late_threshold_epoch = strtotime("+$this->grace_period minutes", $shift_start_epoch);

            if ($punch_epoch > $late_threshold_epoch) {
                $calculatedStatus = 'Late';
                $statusMessage = "Welcome, " . htmlspecialchars($actualRealName) . "! Shift entry logged for your **$day_of_shift** shift. Status: **Late** (Your shift started at " . date('h:i A', $shift_start_epoch) . ").";
            } else {
                $calculatedStatus = 'Present';
                $statusMessage = "Welcome, " . htmlspecialchars($actualRealName) . "! Shift entry logged successfully on time for your **$day_of_shift** shift (" . date('h:i A', $shift_start_epoch) . ").";
            }

            // Check if an empty/placeholder roster row already exists for today. If it does, UPDATE it instead of inserting a duplicate.
            $checkEmptySql = "SELECT id FROM attendance WHERE employee_id = ? AND date_record = ? LIMIT 1";
            $checkEmptyStmt = $this->db->prepare($checkEmptySql);
            $checkEmptyStmt->bind_param("is", $employee_id, $target_shift_date);
            $checkEmptyStmt->execute();
            $emptyRowExists = $checkEmptyStmt->get_result()->fetch_assoc();
            $checkEmptyStmt->close();

            if ($emptyRowExists) {
                $updateSql = "UPDATE attendance SET time_in = ?, status = ?, created_at = ? WHERE id = ?";
                $updateStmt = $this->db->prepare($updateSql);
                $updateStmt->bind_param("sssi", $time_now_formatted, $calculatedStatus, $liveTimestamp, $emptyRowExists['id']);
                $executionSuccess = $updateStmt->execute();
                $updateStmt->close();
            } else {
                $insertSql = "INSERT INTO attendance (employee_id, name, time_in, break_in, break_out, date_record, status, created_at) 
                              VALUES (?, ?, ?, '00:00:00', '00:00:00', ?, ?, ?)";
                $insertStmt = $this->db->prepare($insertSql);
                if (!$insertStmt) {
                    return ['status' => 'error', 'message' => 'Insert formulation broken: ' . $this->db->error];
                }
                $insertStmt->bind_param("isssss", $employee_id, $actualRealName, $time_now_formatted, $target_shift_date, $calculatedStatus, $liveTimestamp);
                $executionSuccess = $insertStmt->execute();
                $insertStmt->close();
            }
            
            if ($executionSuccess) {
                return [
                    'status' => 'success',
                    'action' => 'Time In',
                    'message' => $statusMessage
                ];
            }
            return ['status' => 'error', 'message' => 'Failed database write commit.'];
        }

        // --- CASE 2: STRICT TIME OUT OPERATION ---
        if (empty($existingLog['time_out']) || $existingLog['time_out'] == '00:00:00' || $existingLog['time_out'] == NULL) {
            
            $current_time_epoch = strtotime($time_now_formatted);
            $shift_start_epoch = strtotime($shift_start);
            $shift_end_epoch = strtotime($shift_end);

            if ($shift_start_epoch > $shift_end_epoch) {
                if ($target_shift_date === $today && $current_time_epoch > $shift_start_epoch) {
                    return [
                        'status' => 'error',
                        'message' => "Too early to shift out! Your overnight **$day_of_shift** shift ends tomorrow at " . date('h:i A', $shift_end_epoch) . "."
                    ];
                }
            }

            if ($current_time_epoch < $shift_end_epoch && $target_shift_date === $today) {
                return [
                    'status' => 'error',
                    'message' => "Too early to shift out! Your **$day_of_shift** shift ends at " . date('h:i A', $shift_end_epoch) . "."
                ];
            }

            $logId = $existingLog['id'];
            $updateSql = "UPDATE attendance SET time_out = ? WHERE id = ?";
            $updateStmt = $this->db->prepare($updateSql);
            $updateStmt->bind_param("si", $time_now_formatted, $logId);

            if ($updateStmt->execute()) {
                $updateStmt->close();
                return [
                    'status' => 'success',
                    'action' => 'Time Out',
                    'message' => "Goodbye, " . htmlspecialchars($actualRealName) . "! Shift exit verified successfully for your **$day_of_shift** shift."
                ];
            }
            $updateStmt->close();
            return ['status' => 'error', 'message' => 'Failed to update departure log records.'];
        }

        return [
            'status' => 'error',
            'message' => "Daily threshold reached. Shift logs for your $day_of_shift shift are already complete."
        ];
    }

    public function getDepartments() {
        $departments = [];
        $sql = "SELECT DISTINCT department FROM employees WHERE department IS NOT NULL AND department != '' ORDER BY department ASC";
        $result = $this->db->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $departments[] = $row['department'];
            }
        }
        return $departments;
    }

    public function getRecords($search = '', $department = '', $date = '') {
        $records = [];
        $sql = "SELECT a.id, a.name AS employee_name, e.department, a.time_in, a.break_in, a.break_out, a.time_out, a.status 
                FROM attendance a
                LEFT JOIN employees e ON a.employee_id = e.id
                WHERE 1=1";
        
        $params = [];
        $types = "";

        if (!empty($date)) {
            $sql .= " AND a.date_record = ?";
            $params[] = $date;
            $types .= "s";
        }
        if (!empty($department)) {
            $sql .= " AND e.department = ?";
            $params[] = $department;
            $types .= "s";
        }
        if (!empty($search)) {
            $sql .= " AND (a.name LIKE ? OR e.department LIKE ? OR a.employee_id LIKE ?)";
            $searchTerm = "%" . $search . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "sss";
        }

        $sql .= " ORDER BY a.id DESC";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $records[] = $row;
            }
            $stmt->close();
        }
        return $records;
    }
}
?>