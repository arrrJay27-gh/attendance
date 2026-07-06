<?php

class Leave
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    public function getAll($search = '', $status = '', $limit = 100, $offset = 0)
    {
        $conditions = ['1=1'];
        $types = '';
        $params = [];

        if ($status !== '') {
            $conditions[] = 'status = ?';
            $types .= 's';
            $params[] = $status;
        }

        if ($search !== '') {
            $conditions[] = '(employee_name LIKE ? OR designation LIKE ? OR leave_type LIKE ?)';
            $like = '%' . $search . '%';
            $types .= 'sss';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $where = implode(' AND ', $conditions);
        $sql = "SELECT * FROM leave_requests WHERE {$where} ORDER BY id DESC LIMIT ? OFFSET ?";
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

    public function updateStatus($id, $status)
    {
        $allowed = ['Approved', 'Rejected', 'Pending'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $sql = "UPDATE leave_requests SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('si', $status, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function approveAllPending()
    {
        $sql = "UPDATE leave_requests SET status = 'Approved' WHERE status = 'Pending'";
        return (bool) $this->db->query($sql);
    }

    public function create($employeeId, $employeeName, $designation, $leaveType, $reason, $startDate, $endDate, $days)
    {
        $status = 'Pending';
        $sql = "INSERT INTO leave_requests (employee_id, employee_name, designation, leave_type, reason, start_date, end_date, days, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('issssssis', $employeeId, $employeeName, $designation, $leaveType, $reason, $startDate, $endDate, $days, $status);
        $ok = $stmt->execute();
        $newId = $ok ? $this->db->insert_id : false;
        $stmt->close();
        return $newId;
    }
}
