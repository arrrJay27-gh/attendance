<?php 
require_once 'auth.php';
$activePage = 'timekeeping'; // Keeps the Timekeeping tab highlighted cleanly

// 1. Establish OOP MySQLi Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance_kiwi"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get Today's Date dynamically for accurate metrics calculation
$today = date('Y-m-d');

// --- METRIC CARD 1: Total Employees Present Today ---
$sqlPresent = "SELECT COUNT(DISTINCT employee_name) as total FROM timekeeping_table WHERE date_record = '$today' AND status = 'Present'";
$resPresent = $conn->query($sqlPresent);
$presentCount = ($resPresent && $row = $resPresent->fetch_assoc()) ? $row['total'] : 0;

// --- METRIC CARD 2: Late Arrivals Today ---
$sqlLate = "SELECT COUNT(DISTINCT employee_name) as total FROM timekeeping_table WHERE date_record = '$today' AND status = 'Late'";
$resLate = $conn->query($sqlLate);
$lateCount = ($resLate && $row = $resLate->fetch_assoc()) ? $row['total'] : 0;

// --- METRIC CARD 3: Employees Absent Today ---
$sqlAbsent = "SELECT COUNT(DISTINCT employee_name) as total FROM timekeeping_table WHERE date_record = '$today' AND status = 'Absent'";
$resAbsent = $conn->query($sqlAbsent);
$absentCount = ($resAbsent && $row = $resAbsent->fetch_assoc()) ? $row['total'] : 0;

// --- METRIC CARD 4: Average Check-In Time Today ---
$sqlAvgTime = "SELECT SEC_TO_TIME(AVG(TIME_TO_SEC(time_in))) as avg_time FROM timekeeping_table WHERE date_record = '$today' AND time_in IS NOT NULL AND time_in != '00:00:00'";
$resAvgTime = $conn->query($sqlAvgTime);
$avgTimeValue = "--:-- --";
if ($resAvgTime && $row = $resAvgTime->fetch_assoc()) {
    if (!empty($row['avg_time'])) {
        $avgTimeValue = date("h:i A", strtotime($row['avg_time']));
    }
}

// --- DROPDOWN: Fetch Unique Departments Dynamically ---
$sqlDepts = "SELECT DISTINCT department FROM timekeeping_table WHERE department IS NOT NULL AND department != '' ORDER BY department ASC";
$resultDepts = $conn->query($sqlDepts);

// --- MAIN TABLE: Fetch Timekeeping Records (Sorted by recent date and time) ---
$sql = "SELECT id, employee_name, department, time_in, time_out, status 
        FROM timekeeping_table 
        ORDER BY date_record DESC, time_in DESC"; 
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timekeeping - Kiwi Digital</title>
    
   
    <link class="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <script>try{if(localStorage.getItem('sidebarMinimized')==='true'){document.documentElement.classList.add('sidebar-minimized');}}catch(e){}</script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; padding: 20px; height: 100vh; overflow: hidden; }
        
        /* Layout Grid System - Matched exactly to index.php */
        .app-container { 
            display: grid; 
            grid-template-columns: 310px 1fr; 
            gap: 30px; 
            height: calc(100vh - 40px); 
            width: 100%; 
            position: relative;
            transition: grid-template-columns 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-minimized .app-container {
            grid-template-columns: 85px 1fr;
        }

        .main-content { 
            flex-grow: 1; 
            padding-top: 10px; 
            height: 100%; 
            display: flex; 
            flex-direction: column; 
            gap: 16px; 
            overflow-y: auto; 
        }

        /* ==================================================================
           SIDEBAR STYLES (MATCHED EXACTLY TO PREVENT JUMPING)
           ================================================================== */
        .sidebar {
            width: 100%;
            background-color: #dcdddf; 
            border-radius: 36px;       
            padding: 45px 0 35px 0;
            display: flex;
            flex-direction: column;
            position: relative;
            height: 100%;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-header {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 35px;
            width: 100%;
            position: relative;
            padding: 0 20px;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }
        
        .logo-img {
            max-width: 140px;
            height: auto;
            transition: max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s ease;
        }
        
        .sidebar-toggle-btn {
            position: absolute;
            top: 10px;
            right: -13px;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background-color: #ffffff;
            border: none;
            box-shadow: 0 2px 6px rgba(0,0,0,0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 100;
            color: #52525b;
        }
        
        .sidebar-toggle-btn i {
            font-size: 11px;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .nav-links {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex-grow: 1;
        }
        
        .nav-item {
            width: 100%;
        }
        
        .nav-item a {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 15px 35px;
            color: #434850; 
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            transition: color 0.2s;
        }
        
        .nav-item.active a {
            background-color: #ffffff;
            color: #11161e;
            border-top-right-radius: 18px;
            border-bottom-right-radius: 18px;
            margin-right: 20px;
            padding-left: 35px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }
        
        .nav-item a i.icon {
            font-size: 20px;
            width: 26px;
            text-align: center;
            color: #434850;
        }
        
        .nav-item.active a i.icon {
            color: #11161e;
        }
        
        .sidebar-footer {
            margin-top: auto;
        }
        
        .logout-btn {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 15px 35px;
            color: #434850;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
        }
        
        /* ==================================================================
           MINIMIZED SIDEBAR CONFIGURATIONS
           ================================================================== */
        .sidebar-minimized .sidebar {
            padding: 45px 0 35px 0;
        }
        
        .sidebar-minimized .sidebar .logo-img {
            max-width: 40px; 
            transform: scale(1);
        }
        
        .sidebar-minimized .sidebar .nav-item a span,
        .sidebar-minimized .sidebar .logout-btn span {
            display: none;
        }
        
        .sidebar-minimized .sidebar .nav-item a {
            justify-content: center;
            padding: 15px 0;
        }
        
        .sidebar-minimized .sidebar .nav-item.active a {
            margin-right: 10px;
            padding-left: 0;
            border-radius: 0 16px 16px 0;
        }
        
        .sidebar-minimized .sidebar .logout-btn {
            justify-content: center;
            padding: 15px 0;
        }
        
        .sidebar-minimized .sidebar-toggle-btn i {
            transform: rotate(180deg);
        }

        /* ==========================================================================
           MAIN CONTENT CONTAINER & METRICS ROW
           ========================================================================== */
        .metrics-straight-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            width: 100%;
        }

        .card {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 16px 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f3f5;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 125px;
        }

        .card-header { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
        .card-title { font-size: 13px; font-weight: 600; color: #1f2937; white-space: nowrap; }
        .card-value { font-size: 28px; font-weight: 700; color: #111827; line-height: 1.1; }
        .card-footer { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #6b7280; white-space: nowrap; }

        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 6px; border-radius: 6px; font-size: 11px; font-weight: 600; }
        .badge.positive { background-color: #ecfdf5; color: #10b981; }
        .badge.negative { background-color: #fef2f2; color: #ef4444; }

        /* ==========================================================================
           ATTENDANCE PANEL & CONTROLS STYLES
           ========================================================================== */
        .attendance-panel {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 24px;
            width: 100%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f3f5;
        }

        .panel-header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .panel-title-area { display: flex; align-items: center; gap: 10px; }
        .panel-title-area i { color: #3b82f6; font-size: 18px; }
        .panel-title { font-size: 18px; font-weight: 700; color: #1e293b; }
        .panel-menu-dot { color: #94a3b8; cursor: pointer; }

        .table-controls-strip { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 16px; }
        .filter-group-left { display: flex; align-items: center; gap: 12px; }

        .select-filter-dropdown, .date-filter-picker {
            padding: 10px 16px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 14px;
            color: #475569;
            background-color: #ffffff;
            outline: none;
            cursor: pointer;
        }

        .action-group-right { display: flex; align-items: center; gap: 12px; }
        .table-search-box { position: relative; width: 260px; }
        .table-search-box i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }
        .table-search-box input { width: 100%; padding: 10px 14px 10px 40px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 14px; outline: none; color: #334155; }

        .btn-download-data {
            background-color: #3b82f6;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        /* ==========================================================================
           ATTENDANCE DATA TABLE
           ========================================================================== */
        .custom-table-wrapper { overflow-x: auto; width: 100%; }
        .attendance-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
        .attendance-table th { color: #94a3b8; font-weight: 600; padding: 16px; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
        .attendance-table th i { font-size: 11px; margin-left: 4px; color: #cbd5e1; }
        .attendance-table td { padding: 16px; color: #1e293b; vertical-align: middle; border-bottom: 1px solid #f8fafc; }

        .id-cell { color: #94a3b8 !important; }
        .profile-meta-cell { display: flex; align-items: center; gap: 10px; font-weight: 600; color: #1e293b; }
        .avatar-image { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; background-color: #e2e8f0; }
        .dept-cell { color: #64748b; }
        .time-cell { font-weight: 600; color: #0f172a; }
        .empty-log { color: #94a3b8; font-weight: 500; }

        .status-pill { display: inline-flex; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: capitalize; }
        .status-pill.present { background-color: #f0fdf4; color: #16a34a; }
        .status-pill.late { background-color: #fff7ed; color: #ea580c; }
        .status-pill.absent { background-color: #fef2f2; color: #ef4444; }
        .action-dot-menu { color: #cbd5e1; cursor: pointer; font-size: 16px; text-align: right; width: 30px; }
    </style>
</head>
<body>

    <div class="app-container">
        
        <!-- SIDEBAR COMPONENT -->
        <?php
        $imgPrefix = $imgPrefix ?? '';
        $navItems = [
            ['id' => 'dashboard',   'href' => 'index.php',       'icon' => 'fa-table-cells-large',       'label' => 'Dashboard'],
            ['id' => 'employee',    'href' => 'employee.php',    'icon' => 'fa-users-rectangle',         'label' => 'Employee'],
            ['id' => 'biometric',   'href' => '#',               'icon' => 'fa-fingerprint',             'label' => 'Biometric Enrollment'],
            ['id' => 'timekeeping', 'href' => 'timekeeping.php', 'icon' => 'fa-clipboard-user',          'label' => 'Timekeeping'],
            ['id' => 'shift',       'href' => '#',               'icon' => 'fa-right-left',              'label' => 'Shift Configuration'],
            ['id' => 'leave',       'href' => 'leave.php',       'icon' => 'fa-user-gear',               'label' => 'Leave Management'],
            ['id' => 'internship',  'href' => '#',               'icon' => 'fa-cubes',                   'label' => 'Internship Registry'],
            ['id' => 'audit',       'href' => '#',               'icon' => 'fa-square-poll-horizontal',  'label' => 'System Audit'],
        ];
        ?>

        <nav class="sidebar" id="sidebarContainer">
            <div class="sidebar-header">
                <div class="logo-container">
                    <img src="<?php echo htmlspecialchars($imgPrefix); ?>img/kiwi.png" alt="KIWI DIGITAL TECH INC." class="logo-img">
                </div>
                <button type="button" class="sidebar-toggle-btn" id="toggleSidebarBtn" aria-label="Toggle sidebar">
                    <i class="fa-solid fa-chevron-left" id="toggleIcon"></i>
                </button>
            </div>

            <ul class="nav-links">
                <?php foreach ($navItems as $item): ?>
                <li class="nav-item<?php echo $activePage === $item['id'] ? ' active' : ''; ?>">
                    <a href="<?php echo htmlspecialchars($item['href']); ?>">
                        <i class="fa-solid <?php echo htmlspecialchars($item['icon']); ?> icon"></i>
                        <span><?php echo htmlspecialchars($item['label']); ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn">
                    <i class="fa-solid fa-right-from-bracket icon"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>

        <!-- MAIN VIEW WRAPPER -->
        <main class="main-content">
            
            <!-- DYNAMIC METRIC CARDS OVERVIEW -->
            <div class="metrics-straight-row">
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-circle-check" style="color: #3b82f6;"></i>
                        <span class="card-title">Total Employees Present</span>
                    </div>
                    <div class="card-value"><?php echo $presentCount; ?></div>
                    <div class="card-footer">
                        <span>Recorded today</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-user-clock" style="color: #3b82f6;"></i>
                        <span class="card-title">Late Arrivals Today</span>
                    </div>
                    <div class="card-value"><?php echo $lateCount; ?></div>
                    <div class="card-footer">
                        <span>Recorded today</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-user-minus" style="color: #ef4444;"></i>
                        <span class="card-title">Employees Absent</span>
                    </div>
                    <div class="card-value"><?php echo $absentCount; ?></div>
                    <div class="card-footer">
                        <span>Recorded today</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-clock" style="color: #3b82f6;"></i>
                        <span class="card-title">Average Check-In Time</span>
                    </div>
                    <div class="card-value"><?php echo $avgTimeValue; ?></div>
                    <div class="card-footer">
                        <span>Based on active entries</span>
                    </div>
                </div>
            </div>
            
            <!-- ATTENDANCE PANEL BLOCK -->
            <div class="attendance-panel">
                <div class="panel-header-row">
                    <div class="panel-title-area">
                        <i class="fa-solid fa-list-check"></i>
                        <span class="panel-title">Attendance History</span>
                    </div>
                    <div class="panel-menu-dot"><i class="fa-solid fa-ellipsis"></i></div>
                </div>

                <!-- SPECIFIC TABLE CONTROLS -->
                <div class="table-controls-strip">
                    <div class="filter-group-left">
                        <!-- DYNAMIC DEPARTMENT SELECT FILTER -->
                        <select class="select-filter-dropdown">
                            <option value="">Select department</option>
                            <?php if ($resultDepts && $resultDepts->num_rows > 0): ?>
                                <?php while($deptRow = $resultDepts->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($deptRow['department']); ?>">
                                        <?php echo htmlspecialchars($deptRow['department']); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                        <input type="text" class="date-filter-picker" value="<?php echo date('d M Y'); ?>" readonly>
                    </div>
                    
                    <div class="action-group-right">
                        <div class="table-search-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" placeholder="Search employee">
                        </div>
                        <button class="btn-download-data">
                            <i class="fa-solid fa-download"></i> Download data <i class="fa-solid fa-chevron-down" style="font-size: 11px; margin-left: 4px;"></i>
                        </button>
                    </div>
                </div>

                <!-- ATTENDANCE DATA TABLE -->
                <div class="custom-table-wrapper">
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th style="width: 100px;"># ID <i class="fa-solid fa-sort"></i></th>
                                <th>Name <i class="fa-solid fa-sort"></i></th>
                                <th>Department <i class="fa-solid fa-sort"></i></th>
                                <th>Clock-in <i class="fa-solid fa-sort"></i></th>
                                <th>Clock-out <i class="fa-solid fa-sort"></i></th>
                                <th>Status <i class="fa-solid fa-sort"></i></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="id-cell">#E<?php echo htmlspecialchars($row['id']); ?></td>
                                        <td>
                                            <div class="profile-meta-cell">
                                                <div class="avatar-image"></div>
                                                <span><?php echo htmlspecialchars($row['employee_name']); ?></span>
                                            </div>
                                        </td>
                                        <td class="dept-cell"><?php echo htmlspecialchars($row['department']); ?></td>
                                        <td class="time-cell">
                                            <?php 
                                                echo ($row['time_in'] && $row['time_in'] != '00:00:00') 
                                                    ? date("h:i A", strtotime($row['time_in'])) 
                                                    : '<span class="empty-log">--:--</span>'; 
                                            ?>
                                        </td>
                                        <td class="time-cell">
                                            <?php 
                                                echo ($row['time_out'] && $row['time_out'] != '00:00:00') 
                                                    ? date("h:i A", strtotime($row['time_out'])) 
                                                    : '<span class="empty-log">--:--</span>'; 
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                $statusClass = strtolower($row['status']); 
                                                if (!in_array($statusClass, ['present', 'late', 'absent'])) {
                                                    $statusClass = 'present'; 
                                                }
                                            ?>
                                            <span class="status-pill <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($row['status']); ?>
                                            </span>
                                        </td>
                                        <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; color: #94a3b8; padding: 30px;">
                                        No timekeeping records found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
            toggleSidebarBtn.addEventListener('click', function() {
                const isMinimized = document.documentElement.classList.toggle('sidebar-minimized');
                localStorage.setItem('sidebarMinimized', isMinimized);
            });
        });
    </script>
</body>
</html>
<?php 
$conn->close(); 
?>