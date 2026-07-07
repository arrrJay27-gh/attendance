<?php

class Attendance
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
        $this->db->query("SET time_zone = '+08:00'");
    }

    public function verifyIdentity($authMode, $rfidValue = null)
    {
        if (empty($rfidValue)) {
            return false;
        }

        $sql = "SELECT id, name, department FROM users WHERE biometric_rfid = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $rfidValue);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $user ?: false;
    }

    public function logTimePunch($employeeId, $employeeName, $liveTimestamp = null)
    {
        $liveTimestamp = $liveTimestamp ?: date('Y-m-d H:i:s');
        $currentDate = date('Y-m-d', strtotime($liveTimestamp));
        $currentTime = date('H:i:s', strtotime($liveTimestamp));

        $sqlLog = "SELECT id, time_out FROM attendance WHERE employee_table_id = ? AND date_record = ?";
        $stmtL = $this->db->prepare($sqlLog);
        $stmtL->bind_param('is', $employeeId, $currentDate);
        $stmtL->execute();
        $existingLog = $stmtL->get_result()->fetch_assoc();
        $stmtL->close();

        if (!$existingLog) {
            $graceLimit = date('H:i:s', strtotime('08:00:00') + (GRACE_PERIOD_MINUTES * 60));
            $isLate = ($currentTime > $graceLimit) ? 'Late' : 'Present';

            $sqlInsert = "INSERT INTO attendance (employee_table_id, time_in, date_record, status) VALUES (?, ?, ?, ?)";
            $stmtI = $this->db->prepare($sqlInsert);
            if (!$stmtI) {
                return ['status' => 'error', 'message' => 'Prepare Error: ' . $this->db->error];
            }

            $stmtI->bind_param('isss', $employeeId, $currentTime, $currentDate, $isLate);
            if ($stmtI->execute()) {
                $stmtI->close();
                return [
                    'status' => 'success',
                    'action' => 'Time In',
                    'status_type' => $isLate,
                    'employee_name' => $employeeName,
                ];
            }

            return ['status' => 'error', 'message' => 'Insert Error: ' . $this->db->error];
        }

        if (empty($existingLog['time_out']) || $existingLog['time_out'] === '00:00:00') {
            $sqlUpdate = "UPDATE attendance SET time_out = ? WHERE id = ?";
            $stmtU = $this->db->prepare($sqlUpdate);
            if (!$stmtU) {
                return ['status' => 'error', 'message' => 'Prepare Error: ' . $this->db->error];
            }

            $stmtU->bind_param('si', $currentTime, $existingLog['id']);
            $stmtU->execute();
            $stmtU->close();
            return [
                'status' => 'success',
                'action' => 'Clock Out',
                'employee_name' => $employeeName,
            ];
        }

        return ['status' => 'error', 'message' => 'Shift completed for today.'];
    }

    public function getRecords($search = '', $department = '', $date = '', $limit = 200, $offset = 0)
    {
        $conditions = ['1=1'];
        $types = '';
        $params = [];

        if ($department !== '') {
            $conditions[] = 'e.department = ?';
            $types .= 's';
            $params[] = $department;
        }

        if ($date !== '') {
            $conditions[] = 'a.date_record = ?';
            $types .= 's';
            $params[] = $date;
        }

        if ($search !== '') {
            $conditions[] = '(e.name LIKE ? OR e.employee_id LIKE ? OR e.department LIKE ?)';
            $like = '%' . $search . '%';
            $types .= 'sss';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $where = implode(' AND ', $conditions);
        $sql = "SELECT a.id, e.name AS employee_name, e.department, e.employee_id,
                       a.time_in, a.time_out, a.status, a.date_record
                FROM attendance a
                INNER JOIN employees e ON e.id = a.employee_table_id
                WHERE {$where}
                ORDER BY a.date_record DESC, a.time_in DESC
                LIMIT ? OFFSET ?";

        $types .= 'ii';
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }

    public function getDepartments()
    {
        $sql = "SELECT DISTINCT e.department
                FROM attendance a
                INNER JOIN employees e ON e.id = a.employee_table_id
                WHERE e.department IS NOT NULL AND e.department != ''
                ORDER BY e.department ASC";
        $result = $this->db->query($sql);
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row['department'];
            }
        }
        return $rows;
    }
}
