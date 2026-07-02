<?php
class Employee {
    protected $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function getById($id) {
        $sql = "SELECT * FROM employees WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row;
    }

    public function update($id, $employeeId, $name, $department, $position, $status = 'Active') {
        $sql = "UPDATE employees SET employee_id = ?, name = ?, department = ?, position = ?, status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssssi', $employeeId, $name, $department, $position, $status, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function delete($id) {
        $sql = "DELETE FROM employees WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
?>
