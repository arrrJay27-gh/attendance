<?php

class Dashboard
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    public function getTodayStats($date = null)
    {
        $date = $date ?: date('Y-m-d');

        $present = $this->countByStatus($date, 'Present');
        $late = $this->countByStatus($date, 'Late');
        $absent = $this->countAbsent($date);
        $avgCheckIn = $this->getAverageCheckIn($date);
        $totalEmployees = $this->getTotalEmployees();

        return [
            'date' => $date,
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'avg_check_in' => $avgCheckIn,
            'total_employees' => $totalEmployees,
        ];
    }

    public function getMonthlyAnalytics($months = 6)
    {
        $labels = [];
        $counts = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $target = strtotime("-{$i} months");
            $labels[] = date('M', $target);
            $month = date('m', $target);
            $year = date('Y', $target);

            $sql = "SELECT COUNT(*) AS total FROM attendance WHERE MONTH(date_record) = ? AND YEAR(date_record) = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('ss', $month, $year);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $counts[] = (int) ($row['total'] ?? 0);
        }

        $max = max($counts) > 0 ? max($counts) : 10;
        $yPositions = [];
        foreach ($counts as $count) {
            $yPositions[] = 180 - (($count / $max) * 120);
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
            'max' => $max,
            'y_positions' => $yPositions,
        ];
    }

    public function getRecentActivity($limit = 4)
    {
        // Fixed: changed a.employee_table_id to a.employee_id
        $sql = "SELECT e.name, a.status, a.time_in, a.date_record
                FROM attendance a
                INNER JOIN employees e ON e.id = a.employee_id
                ORDER BY a.id DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }

    private function countByStatus($date, $status)
    {
        // Fixed: changed employee_table_id to employee_id
        $sql = "SELECT COUNT(DISTINCT employee_id) AS total
                FROM attendance
                WHERE date_record = ? AND status = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ss', $date, $status);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int) ($row['total'] ?? 0);
    }

    private function countAbsent($date)
    {
        $sql = "SELECT COUNT(*) AS total FROM employees WHERE status = 'Active'";
        $res = $this->db->query($sql);
        $totalActive = (int) (($res && $row = $res->fetch_assoc()) ? $row['total'] : 0);

        // Fixed: changed employee_table_id to employee_id
        $sqlChecked = "SELECT COUNT(DISTINCT employee_id) AS total
                       FROM attendance
                       WHERE date_record = ? AND status IN ('Present', 'Late')";
        $stmt = $this->db->prepare($sqlChecked);
        $stmt->bind_param('s', $date);
        $stmt->execute();
        $checkedIn = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
        $stmt->close();

        return max(0, $totalActive - $checkedIn);
    }

    private function getAverageCheckIn($date)
    {
        $sql = "SELECT SEC_TO_TIME(AVG(TIME_TO_SEC(time_in))) AS avg_time
                FROM attendance
                WHERE date_record = ? AND time_in IS NOT NULL AND time_in != '00:00:00'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $date);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (empty($row['avg_time'])) {
            return '--:-- --';
        }

        return date('h:i A', strtotime($row['avg_time']));
    }

    private function getTotalEmployees()
    {
        $res = $this->db->query("SELECT COUNT(*) AS total FROM employees");
        return (int) (($res && $row = $res->fetch_assoc()) ? $row['total'] : 0);
    }
}