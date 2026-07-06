<?php

class Employee
{
    protected $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    public function getAll($search = '', $limit = 100, $offset = 0)
    {
        $search = trim($search);
        if ($search !== '') {
            $like = '%' . $search . '%';
            $sql = "SELECT * FROM employees
                    WHERE name LIKE ? OR employee_id LIKE ? OR department LIKE ? OR position LIKE ?
                    ORDER BY id DESC LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('ssssii', $like, $like, $like, $like, $limit, $offset);
        } else {
            $sql = "SELECT * FROM employees ORDER BY id DESC LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('ii', $limit, $offset);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }

    public function countAll($search = '')
    {
        $search = trim($search);
        if ($search !== '') {
            $like = '%' . $search . '%';
            $sql = "SELECT COUNT(*) AS total FROM employees
                    WHERE name LIKE ? OR employee_id LIKE ? OR department LIKE ? OR position LIKE ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('ssss', $like, $like, $like, $like);
        } else {
            $sql = "SELECT COUNT(*) AS total FROM employees";
            $stmt = $this->db->prepare($sql);
        }

        $stmt->execute();
        $total = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
        $stmt->close();
        return $total;
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM employees WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function create($name, $employeeId, $position, $department, $employmentType = 'Full-time', $email = '', $status = 'Active')
    {
        if ($employeeId === '') {
            $employeeId = 'EMP-' . date('Ymd') . '-' . sprintf('%03d', rand(1, 999));
        }

        $sql = "INSERT INTO employees (employee_id, name, email, department, position, employment_type, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssssss', $employeeId, $name, $email, $department, $position, $employmentType, $status);
        $ok = $stmt->execute();
        $newId = $ok ? $this->db->insert_id : false;
        $stmt->close();
        return $newId;
    }

    public function update($id, $employeeId, $name, $department, $position, $status = 'Active')
    {
        $sql = "UPDATE employees SET employee_id = ?, name = ?, department = ?, position = ?, status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssssi', $employeeId, $name, $department, $position, $status, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function delete($id)
    {
        $sql = "DELETE FROM employees WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getDepartments()
    {
        $sql = "SELECT DISTINCT department FROM employees WHERE department IS NOT NULL AND department != '' ORDER BY department ASC";
        $result = $this->db->query($sql);
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row['department'];
            }
        }
        return $rows;
    }

    public function findByRfid($rfid)
    {
        $sql = "SELECT id, name, department, employee_id FROM employees WHERE biometric_rfid = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $rfid);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }
}
