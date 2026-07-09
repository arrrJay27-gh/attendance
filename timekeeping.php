<?php
require_once 'auth.php';
$activePage = 'timekeeping';

require_once 'database.php';
require_once 'class/Dashboard.php';
require_once 'class/attendance.php';

$database = new Database();
$conn = $database->getConnection();
$dashboard = new Dashboard($conn);
$attendanceService = new Attendance($conn);

$today = date('Y-m-d');
$stats = $dashboard->getTodayStats($today);
$presentCount = $stats['present'];
$lateCount = $stats['late'];
$absentCount = $stats['absent'];
$avgTimeValue = $stats['avg_check_in'];
$departments = $attendanceService->getDepartments();
$records = $attendanceService->getRecords('', '', $today);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timekeeping - Kiwi Digital</title>
    
    <link rel="stylesheet" href="bootstrap-5.3.5-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <script>try{if(localStorage.getItem('sidebarMinimized')==='true'){document.documentElement.classList.add('sidebar-minimized');}}catch(e){}</script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght=400;500;600;700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; padding: 20px; height: 100vh; overflow: hidden; }
        
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

        .card-header { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; border: none; background: transparent; padding: 0; }
        .card-title { font-size: 13px; font-weight: 600; color: #1f2937; white-space: nowrap; }
        .card-value { font-size: 28px; font-weight: 700; color: #111827; line-height: 1.1; }
        .card-footer { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #6b7280; white-space: nowrap; border: none; background: transparent; padding: 0; }

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

        .table-controls-strip { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px; 
            gap: 16px; 
            width: 100%;
        }
        
        .filter-group-left {
            flex-grow: 1;
            max-width: 420px;
        }

        .table-search-box { 
            position: relative; 
            width: 100%; 
        }
        
        .table-search-box i { 
            position: absolute; 
            left: 18px; 
            top: 50%; 
            transform: translateY(-50%); 
            color: #94a3b8; 
            font-size: 15px; 
        }
        
        .table-search-box input { 
            width: 100%; 
            padding: 11px 16px 11px 48px; 
            border-radius: 50px; 
            border: 1px solid #e2e8f0; 
            font-size: 14px; 
            outline: none; 
            color: #334155; 
            transition: all 0.2s ease;
        }
        
        .table-search-box input::placeholder {
            color: #94a3b8;
        }

        .table-search-box input:focus {
            border-color: #cbd5e1;
            box-shadow: 0 0 0 3px rgba(226, 232, 240, 0.4);
        }

        .action-group-right { display: flex; align-items: center; gap: 12px; }
        .filter-drawer { display: flex; align-items: center; gap: 12px; background-color: #f8fafc; padding: 12px 16px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 16px; }

        .select-filter-dropdown, .date-filter-picker {
            padding: 8px 14px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 14px;
            color: #475569;
            background-color: #ffffff;
            outline: none;
            cursor: pointer;
        }

        .btn-action-outline {
            background-color: #ffffff;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-action-outline:hover, .btn-action-outline:focus, .show > .btn-action-outline {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #1e293b;
        }

        .custom-dropdown-menu {
            border-radius: 12px;
            padding: 6px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            min-width: 140px;
        }
        .custom-dropdown-menu .dropdown-item {
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 14px;
            color: #475569;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .custom-dropdown-menu .dropdown-item:hover {
            background-color: #f1f5f9;
            color: #1e293b;
        }

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
            ['id' => 'dashboard',   'href' => 'index.php',             'icon' => 'fa-table-cells-large',       'label' => 'Dashboard'],
            ['id' => 'employee',    'href' => 'employee.php',          'icon' => 'fa-users-rectangle',         'label' => 'Employee'],
            ['id' => 'biometric',   'href' => 'biometrics.php',        'icon' => 'fa-fingerprint',             'label' => 'Biometric Enrollment'],
            ['id' => 'timekeeping', 'href' => 'timekeeping.php',       'icon' => 'fa-clipboard-user',          'label' => 'Timekeeping'],
            ['id' => 'shift',       'href' => 'shift_management.php',  'icon' => 'fa-right-left',              'label' => 'Shift Configuration'],
            ['id' => 'leave',       'href' => 'leave.php',             'icon' => 'fa-user-gear',               'label' => 'Leave Management'],
            ['id' => 'internship',  'href' => 'internship.php',        'icon' => 'fa-cubes',                   'label' => 'Internship Registry'],
            ['id' => 'audit',       'href' => 'audit.php',             'icon' => 'fa-square-poll-horizontal',  'label' => 'System Audit'],
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
            
            <div class="metrics-straight-row">
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-circle-check" style="color: #3b82f6;"></i>
                        <span class="card-title">Total Employees Present</span>
                    </div>
                    <div class="card-value" data-stat-present><?php echo $presentCount; ?></div>
                    <div class="card-footer">
                        <span>Recorded today</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-user-clock" style="color: #3b82f6;"></i>
                        <span class="card-title">Late Arrivals Today</span>
                    </div>
                    <div class="card-value" data-stat-late><?php echo $lateCount; ?></div>
                    <div class="card-footer">
                        <span>Recorded today</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-user-minus" style="color: #ef4444;"></i>
                        <span class="card-title">Employees Absent</span>
                    </div>
                    <div class="card-value" data-stat-absent><?php echo $absentCount; ?></div>
                    <div class="card-footer">
                        <span>Recorded today</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-clock" style="color: #3b82f6;"></i>
                        <span class="card-title">Average Check-In Time</span>
                    </div>
                    <div class="card-value" data-stat-avg-checkin><?php echo htmlspecialchars($avgTimeValue); ?></div>
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

                <div class="table-controls-strip">
                    <div class="filter-group-left">
                        <div class="table-search-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" placeholder="Search by name, role, or employee ID" data-table-search>
                        </div>
                    </div>
                    
                    <div class="action-group-right">
                        <button type="button" class="btn-action-outline" id="btnFilterToggle">
                            <i class="fa-solid fa-sliders"></i> Filter
                        </button>

                        <div class="dropdown">
                            <button type="button" class="btn-action-outline dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="btnExportDropdown">
                                <i class="fa-solid fa-download"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end custom-dropdown-menu">
                                <li>
                                    <button class="dropdown-item btn-export" data-export-type="attendance" data-export-format="csv">
                                        <i class="fa-solid fa-file-csv text-success"></i> CSV Format
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item btn-export" data-export-type="attendance" data-export-format="pdf">
                                        <i class="fa-solid fa-file-pdf text-danger"></i> PDF Document
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="filter-drawer" id="filterOptionsDrawer" style="display: none;">
                    <select class="select-filter-dropdown" data-filter-department>
                        <option value="">All Departments</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo htmlspecialchars($dept); ?>"><?php echo htmlspecialchars($dept); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="date" class="date-filter-picker" data-filter-date value="<?php echo $today; ?>">
                </div>

                <!-- ATTENDANCE DATA TABLE -->
                <div class="custom-table-wrapper">
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th style="width: 100px;"># ID <i class="fa-solid fa-sort"></i></th>
                                <th>Name <i class="fa-solid fa-sort"></i></th>
                                <th>Department <i class="fa-solid fa-sort"></i></th>
                                <th>Time-in <i class="fa-solid fa-sort"></i></th>
                                <th>Break-in <i class="fa-solid fa-sort"></i></th>
                                <th>Break-out <i class="fa-solid fa-sort"></i></th>
                                <th>Time-out <i class="fa-solid fa-sort"></i></th>
                                <th>Status <i class="fa-solid fa-sort"></i></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="attendance-table-body">
                            <?php if (!empty($records)): ?>
                                <?php foreach ($records as $row): ?>
                                    <?php 
                                    $statusClass = strtolower($row['status']); 
                                    if (!in_array($statusClass, ['present', 'late', 'absent'], true)) { 
                                        $statusClass = 'present'; 
                                    } 
                                    ?>
                                    <tr>
                                        <td class="id-cell">#E<?php echo (int) $row['id']; ?></td>
                                        <td>
                                            <div class="profile-meta-cell">
                                                <div class="avatar-image"></div>
                                                <span><?php echo htmlspecialchars($row['employee_name']); ?></span>
                                            </div>
                                        </td>
                                        <td class="dept-cell"><?php echo htmlspecialchars($row['department']); ?></td>
                                        
                                        <!-- Time Logging Cells -->
                                        <td class="time-cell"><?php echo (!empty($row['time_in']) && $row['time_in'] !== '00:00:00') ? date('h:i A', strtotime($row['time_in'])) : '<span class="empty-log">--:--</span>'; ?></td>
                                        <td class="time-cell"><?php echo (!empty($row['break_in']) && $row['break_in'] !== '00:00:00') ? date('h:i A', strtotime($row['break_in'])) : '<span class="empty-log">--:--</span>'; ?></td>
                                        <td class="time-cell"><?php echo (!empty($row['break_out']) && $row['break_out'] !== '00:00:00') ? date('h:i A', strtotime($row['break_out'])) : '<span class="empty-log">--:--</span>'; ?></td>
                                        <td class="time-cell"><?php echo (!empty($row['time_out']) && $row['time_out'] !== '00:00:00') ? date('h:i A', strtotime($row['time_out'])) : '<span class="empty-log">--:--</span>'; ?></td>
                                        
                                        <td><span class="status-pill <?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                        <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="9" style="text-align: center; color: #94a3b8; padding: 30px;">No timekeeping records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Responsive Logic
            const toggleBtn = document.getElementById('toggleSidebarBtn');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    const isMinimized = document.documentElement.classList.toggle('sidebar-minimized');
                    localStorage.setItem('sidebarMinimized', isMinimized);
                });
            }

            // Filter Configuration Panel Toggle
            const filterToggle = document.getElementById('btnFilterToggle');
            const filterDrawer = document.getElementById('filterOptionsDrawer');
            if (filterToggle && filterDrawer) {
                filterToggle.addEventListener('click', function() {
                    if (filterDrawer.style.display === 'none') {
                        filterDrawer.style.display = 'flex';
                        filterToggle.classList.add('active');
                    } else {
                        filterDrawer.style.display = 'none';
                        filterToggle.classList.remove('active');
                    }
                });
            }

            const departmentFilter = document.querySelector('[data-filter-department]');
            const dateFilter = document.querySelector('[data-filter-date]');
            const searchInput = document.querySelector('[data-table-search]');

            // Dynamic AJAX Row Refresher Engine
            async function loadAttendance() {
                const payload = {
                    search: searchInput?.value.trim() || '',
                    department: departmentFilter?.value || '',
                    date: dateFilter?.value || '',
                };

                try {
                    if (typeof KiwiApp !== 'undefined' && KiwiApp.request) {
                        const response = await KiwiApp.request('attendance_list', payload, 'GET');
                        if (response.status !== 'success') return;

                        if (response.stats) KiwiApp.updateMetricCards(response.stats);

                        const tbody = document.getElementById('attendance-table-body');
                        if (!tbody) return;

                        if (!response.data || !response.data.length) {
                            tbody.innerHTML = '<tr><td colspan="9" style="text-align:center; color:#94a3b8; padding:30px;">No timekeeping records found.</td></tr>';
                            return;
                        }

                        tbody.innerHTML = response.data.map((row) => {
                            let statusClass = (row.status || 'present').toLowerCase();
                            if (!['present', 'late', 'absent'].includes(statusClass)) statusClass = 'present';
                            
                            const timeIn = row.time_in && row.time_in !== '00:00:00' ? KiwiApp.formatTime(row.time_in) : '<span class="empty-log">--:--</span>';
                            const breakIn = row.break_in && row.break_in !== '00:00:00' ? KiwiApp.formatTime(row.break_in) : '<span class="empty-log">--:--</span>';
                            const breakOut = row.break_out && row.break_out !== '00:00:00' ? KiwiApp.formatTime(row.break_out) : '<span class="empty-log">--:--</span>';
                            const timeOut = row.time_out && row.time_out !== '00:00:00' ? KiwiApp.formatTime(row.time_out) : '<span class="empty-log">--:--</span>';
                            
                            return `
                                <tr>
                                    <td class="id-cell">#E${row.id}</td>
                                    <td><div class="profile-meta-cell"><div class="avatar-image"></div><span>${KiwiApp.escapeHtml(row.employee_name)}</span></div></td>
                                    <td class="dept-cell">${KiwiApp.escapeHtml(row.department)}</td>
                                    <td class="time-cell">${timeIn}</td>
                                    <td class="time-cell">${breakIn}</td>
                                    <td class="time-cell">${breakOut}</td>
                                    <td class="time-cell">${timeOut}</td>
                                    <td><span class="status-pill ${statusClass}">${KiwiApp.escapeHtml(row.status)}</span></td>
                                    <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                                </tr>
                            `;
                        }).join('');
                    }
                } catch (error) {
                    console.error("Error loading attendance data:", error);
                }
            }

            // --- REAL-TIME REFRESH ENGINE ---
            // Set refresh interval to 500ms (0.5 seconds)[cite: 4]
            setInterval(loadAttendance, 500);

            // Input Debouncing Handlers
            if (typeof KiwiApp !== 'undefined' && KiwiApp.bindTableSearch) {
                KiwiApp.bindTableSearch(loadAttendance);
            } else if (searchInput) {
                let delayTimer;
                searchInput.addEventListener('input', function() {
                    clearTimeout(delayTimer);
                    delayTimer = setTimeout(loadAttendance, 300);
                });
            }

            departmentFilter?.addEventListener('change', loadAttendance);
            dateFilter?.addEventListener('change', loadAttendance);

            // Export Actions Mapping
            document.querySelectorAll('.dropdown-item.btn-export').forEach(button => {
                button.addEventListener('click', function() {
                    const format = this.getAttribute('data-export-format');
                    const type = this.getAttribute('data-export-type');
                    if (typeof KiwiApp !== 'undefined' && KiwiApp.handleExport) {
                        KiwiApp.handleExport(type, format);
                    } else {
                        console.log(`Exporting ${type} as ${format.toUpperCase()}`);
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php 
$conn->close(); 
?>