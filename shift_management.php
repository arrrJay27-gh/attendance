<?php
require_once 'auth.php';
$activePage = 'shift'; // Dynamically highlights 'Shift Configuration' in the sidebar[cite: 1]

require_once 'database.php';
require_once 'class/Dashboard.php';
require_once 'class/Attendance.php';

$database = new Database();
$conn = $database->getConnection();

// ==========================================================================
// BACKEND AJAX PROCESSOR: Handle New Shift Creation (OOP MySQLi)
// ==========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_shift') {
    header('Content-Type: application/json');
    
    $empId      = intval($_POST['employee_id']);
    $startTime12= $conn->real_escape_string($_POST['start_time']); // e.g., "08:00 AM"
    $endTime12  = $conn->real_escape_string($_POST['end_time']);   // e.g., "05:00 PM"
    $role       = $conn->real_escape_string($_POST['role']);
    
    // Check if user selected dates from pills, otherwise fall back to the active main date input
    $targetDates = !empty($_POST['selected_dates']) ? explode(',', $_POST['selected_dates']) : [$_POST['shift_date']];

    // Convert 12-hour AM/PM inputs into clean 24-hour fragments for standard database building
    $startTime24 = date("H:i:00", strtotime($startTime12));
    $endTime24   = date("H:i:00", strtotime($endTime12));

    // Auto-assign colors based on role selection
    $colorClass = 'bg-blue';
    if (strtolower($role) === 'chief') $colorClass = 'bg-yellow';
    elseif (strtolower($role) === 'barista') $colorClass = 'bg-green';
    elseif (strtolower($role) === 'waiter') $colorClass = 'bg-purple';

    $errors = [];
    foreach ($targetDates as $shiftDate) {
        $shiftDate = $conn->real_escape_string(trim($shiftDate));
        if (empty($shiftDate)) continue;

        $fullStart = $shiftDate . ' ' . $startTime24;
        $fullEnd   = $shiftDate . ' ' . $endTime24;

        // Cleaned Query: Removed break_time and notes to match database schema
        $insertQuery = "INSERT INTO employee_shifts (employee_id, shift_date, start_time, end_time, role, color_class) 
                        VALUES ($empId, '$shiftDate', '$fullStart', '$fullEnd', '$role', '$colorClass')";
        
        if (!$conn->query($insertQuery)) {
            $errors[] = "Date $shiftDate: " . $conn->error;
        }
    }

    if (empty($errors)) {
        echo json_encode(['success' => true, 'message' => 'Shifts assigned successfully!']);
    } else {
        echo json_encode(['success' => false, 'error' => implode(', ', $errors)]);
    }
    exit;
}

$dashboard = new Dashboard($conn);
$attendanceService = new Attendance($conn);

$today = date('Y-m-d');
$stats = $dashboard->getTodayStats($today);
$presentCount = $stats['present'];
$lateCount = $stats['late'];
$absentCount = $stats['absent'];
$avgTimeValue = $stats['avg_check_in'];
$departments = $attendanceService->getDepartments();

// ==========================================================================
// WEEKLY LOGIC: Generate date keys for the current week row (Mon-Sun)
// ==========================================================================
$currentDate = new DateTime('2026-07-06'); 
$monday = clone $currentDate;
if ($monday->format('N') != 1) {
    $monday->modify('last Monday');
}

$weekDays = [];
for ($i = 0; $i < 7; $i++) {
    $day = clone $monday;
    $day->modify("+$i days");
    $weekDays[$day->format('Y-m-d')] = [
        'day_name' => $day->format('D'),
        'day_num'  => $day->format('j'),
        'full_date'=> $day->format('l, j F Y')
    ];
}

$startOfWeek = key($weekDays);
end($weekDays);
$endOfWeek = key($weekDays);

// Fetch distinct employees to populate grid
$employees = [];
$empQuery = "SELECT id, name, department FROM employees ORDER BY name ASC";
$empResult = $conn->query($empQuery);
if ($empResult && $empResult->num_rows > 0) {
    while ($row = $empResult->fetch_assoc()) {
        $employees[$row['id']] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'department' => $row['department'],
            'total_hours' => 0,
            'shifts' => array_fill_keys(array_keys($weekDays), null)
        ];
    }
}

// Query current shift logs
$shiftQuery = "SELECT employee_id, shift_date, start_time, end_time, role, color_class FROM employee_shifts WHERE shift_date BETWEEN '$startOfWeek' AND '$endOfWeek'";
$shiftResult = $conn->query($shiftQuery);

if ($shiftResult && $shiftResult->num_rows > 0) {
    while ($row = $shiftResult->fetch_assoc()) {
        $empId = $row['employee_id'];
        $dateKey = $row['shift_date'];
        
        if (isset($employees[$empId])) {
            $start = new DateTime($row['start_time']);
            $end = new DateTime($row['end_time']);
            $diff = $start->diff($end);
            $hours = $diff->h;
            
            $employees[$empId]['total_hours'] += $hours;
            $employees[$empId]['shifts'][$dateKey] = [
                'time_range'  => date('g:i A', strtotime($row['start_time'])) . ' - ' . date('g:i A', strtotime($row['end_time'])),
                'hours_label' => $hours . 'h',
                'role'        => $row['role'],
                'color_class' => !empty($row['color_class']) ? $row['color_class'] : 'bg-blue'
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Configuration - Kiwi Digital</title>
    
    <link rel="stylesheet" href="bootstrap-5.3.5-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <script>try{if(localStorage.getItem('sidebarMinimized')==='true'){document.documentElement.classList.add('sidebar-minimized');}}catch(e){}</script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght=400;500;600;700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; padding: 20px; height: 100vh; overflow: hidden; }
        
        /* Layout Grid System[cite: 1] */
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

        /* Sidebar Styles[cite: 1] */
        .sidebar { width: 100%; background-color: #dcdddf; border-radius: 36px; padding: 45px 0 35px 0; display: flex; flex-direction: column; position: relative; height: 100%; transition: all 0.3s ease; }
        .sidebar-header { display: flex; justify-content: center; align-items: center; margin-bottom: 35px; width: 100%; position: relative; padding: 0 20px; }
        .logo-container { display: flex; align-items: center; justify-content: center; width: 100%; }
        .logo-img { max-width: 140px; height: auto; transition: max-width 0.3s ease; }
        .sidebar-toggle-btn { position: absolute; top: 10px; right: -13px; width: 26px; height: 26px; border-radius: 50%; background-color: #ffffff; border: none; box-shadow: 0 2px 6px rgba(0,0,0,0.12); display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 100; color: #52525b; }
        .nav-links { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 6px; flex-grow: 1; }
        .nav-item a { display: flex; align-items: center; gap: 20px; padding: 15px 35px; color: #434850; text-decoration: none; font-size: 16px; font-weight: 600; }
        .nav-item.active a { background-color: #ffffff; color: #11161e; border-top-right-radius: 18px; border-bottom-right-radius: 18px; margin-right: 20px; padding-left: 35px; }
        .nav-item a i.icon { font-size: 20px; width: 26px; text-align: center; color: #434850; }
        .nav-item.active a i.icon { color: #11161e; }
        .sidebar-footer { margin-top: auto; }
        .logout-btn { display: flex; align-items: center; gap: 20px; padding: 15px 35px; color: #434850; text-decoration: none; font-size: 16px; font-weight: 600; }
        
        .sidebar-minimized .sidebar { padding: 45px 0 35px 0; }
        .sidebar-minimized .sidebar .logo-img { max-width: 40px; }
        .sidebar-minimized .sidebar .nav-item a span, .sidebar-minimized .sidebar .logout-btn span { display: none; }
        .sidebar-minimized .sidebar .nav-item a { justify-content: center; padding: 15px 0; }
        .sidebar-minimized .sidebar .nav-item.active a { margin-right: 10px; padding-left: 0; border-radius: 0 16px 16px 0; }
        .sidebar-minimized .sidebar-toggle-btn i { transform: rotate(180deg); }

        /* Metric Rows[cite: 1] */
        .metrics-straight-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; width: 100%; }
        .card { background-color: #ffffff; border-radius: 16px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); border: 1px solid #f1f3f5; display: flex; flex-direction: column; justify-content: space-between; height: 125px; }
        .card-header { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; border: none; background: transparent; padding: 0; }
        .card-title { font-size: 13px; font-weight: 600; color: #1f2937; white-space: nowrap; }
        .card-value { font-size: 28px; font-weight: 700; color: #111827; line-height: 1.1; }
        .card-footer { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #6b7280; border: none; background: transparent; padding: 0; }

        /* Attendance Core Block System[cite: 1] */
        .attendance-panel { background-color: #ffffff; border-radius: 16px; padding: 24px; width: 100%; box-shadow: 0 1px 3px rgba(0,0,0,0.02); border: 1px solid #f1f3f5; }
        .panel-header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .panel-title { font-size: 18px; font-weight: 700; color: #1e293b; }
        .table-controls-strip { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 16px; width: 100%; }
        .filter-group-left { flex-grow: 1; max-width: 420px; }
        .table-search-box { position: relative; width: 100%; }
        .table-search-box i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
        .table-search-box input { width: 100%; padding: 11px 16px 11px 48px; border-radius: 50px; border: 1px solid #e2e8f0; font-size: 14px; outline: none; }
        .action-group-right { display: flex; align-items: center; gap: 12px; }
        .btn-action-outline { background-color: #ffffff; color: #475569; border: 1px solid #e2e8f0; padding: 10px 20px; border-radius: 12px; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }

        /* Schedule Timeline Structural Frame[cite: 1] */
        .custom-table-wrapper { overflow-x: auto; width: 100%; }
        .attendance-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; table-layout: fixed; }
        .attendance-table th, .attendance-table td { border: 1px solid #f1f5f9; padding: 12px; vertical-align: middle; position: relative; }
        .attendance-table th:first-child, .attendance-table td:first-child { width: 250px; background-color: #ffffff; position: sticky; left: 0; z-index: 2; box-shadow: 2px 0 5px rgba(0,0,0,0.02); }
        .schedule-day-head { text-align: center; color: #94a3b8; font-weight: 600; font-size: 12px; }
        .day-num-lbl { display: block; font-size: 16px; font-weight: 700; color: #1e293b; margin-top: 2px; }
        .profile-meta-cell { display: flex; align-items: center; justify-content: space-between; width: 100%; }
        .avatar-image { width: 32px; height: 32px; border-radius: 50%; background-color: #e2e8f0; }
        .emp-name-title { font-weight: 600; color: #1e293b; font-size: 14px; }
        .dept-sub-lbl { color: #94a3b8; font-size: 12px; }
        .emp-hrs-counter { font-size: 12px; color: #16a34a; font-weight: 600; }

        /* Dynamic Badges styling details */
        .shift-card-block { border-radius: 8px; padding: 8px 10px; font-size: 11px; display: flex; flex-direction: column; gap: 3px; font-weight: 600; }
        .shift-time-hdr { display: flex; justify-content: space-between; font-weight: 700; font-size: 11px; }
        .shift-role-title { font-weight: 500; opacity: 0.9; }

        .bg-blue { background-color: #e0f2fe; color: #0369a1; border-left: 3px solid #0284c7; }
        .bg-green { background-color: #dcfce7; color: #15803d; border-left: 3px solid #16a34a; }
        .bg-purple { background-color: #f3e8ff; color: #6b21a8; border-left: 3px solid #9333ea; }
        .bg-yellow { background-color: #fef9c3; color: #a16207; border-left: 3px solid #ca8a04; }

        .empty-grid-block { min-height: 45px; width: 100%; display: flex; align-items: center; justify-content: center; }
        
        /* Plus icon styled within an adaptive static circle button */
        .btn-add-slot { border: 1px solid #cbd5e1; background-color: #ffffff; color: #3b82f6; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease-in-out; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .btn-add-slot:hover { background-color: #3b82f6; color: #ffffff; border-color: #3b82f6; transform: scale(1.1); }

        /* Modal Dialog Custom Layouts */
        .modal-custom-shift .modal-content { border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 10px; }
        .modal-custom-shift .modal-header { border-bottom: none; padding: 16px 20px 8px 20px; }
        .modal-custom-shift .modal-title { font-size: 18px; font-weight: 600; color: #1e293b; }
        .modal-custom-shift .modal-body { padding: 10px 24px; display: flex; flex-direction: column; gap: 20px; }
        .modal-custom-shift .modal-footer { border-top: 1px solid #f1f5f9; padding: 16px 24px; }
        
        .shift-row-item { display: flex; align-items: center; gap: 16px; color: #64748b; }
        .shift-row-item i { font-size: 16px; width: 20px; text-align: center; }
        .shift-time-input-group { display: flex; align-items: center; gap: 12px; flex-grow: 1; }
        
        .shift-time-select-custom { border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 10px; font-weight: 500; color: #1e293b; font-size: 13px; background-color: #ffffff; outline: none; }
        .shift-duration-lbl { margin-left: auto; font-size: 13px; color: #64748b; }
        .shift-label-text { color: #334155; font-weight: 500; }
        
        .week-days-badge-row { display: flex; gap: 6px; margin-top: 5px; flex-wrap: wrap; align-items: center; }
        .day-badge-pill { width: 36px; height: 36px; border-radius: 50%; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #64748b; font-weight: 600; background-color: #ffffff; cursor: pointer; user-select: none; }
        .day-badge-pill.active { background-color: #3b82f6; color: #ffffff; border-color: #3b82f6; }
        .day-badge-pill-all { padding: 0 12px; height: 36px; border-radius: 20px; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: #475569; background-color: #f1f5f9; cursor: pointer; user-select: none; }
        .day-badge-pill-all.active { background-color: #1e293b; color: #ffffff; border-color: #1e293b; }

        .btn-modal-cancel { background: none; border: none; color: #64748b; font-weight: 600; font-size: 14px; }
        .btn-modal-save { background-color: #3b82f6; color: #ffffff; border: none; border-radius: 6px; padding: 8px 20px; font-weight: 600; font-size: 14px; }
        .role-select-input { border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 10px; font-size: 14px; color: #334155; outline: none; }
        
        /* Fully styled inline editable input picker */
        .modal-date-picker { border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 12px; color: #334155; font-weight: 500; font-size: 13px; outline: none; background: #ffffff; cursor: pointer; width: 170px; transition: border-color 0.15s ease-in-out; }
        .modal-date-picker:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.15); }
    </style>
</head>
<body>

    <div class="app-container">
        <?php
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


        <!-- SIDEBAR COMPONENT[cite: 1] -->
        <nav class="sidebar" id="sidebarContainer">
            <div class="sidebar-header">
                <div class="logo-container"><img src="img/kiwi.png" alt="KIWI DIGITAL" class="logo-img"></div>
                <button type="button" class="sidebar-toggle-btn" id="toggleSidebarBtn"><i class="fa-solid fa-chevron-left"></i></button>
            </div>
            <ul class="nav-links">
                <li class="nav-item"><a href="index.php"><i class="fa-solid fa-table-cells-large icon"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="employee.php"><i class="fa-solid fa-users-rectangle icon"></i><span>Employee</span></a></li>
                <li class="nav-item active"><a href="shift_management.php"><i class="fa-solid fa-right-left icon"></i><span>Shift Configuration</span></a></li>
            </ul>
            <div class="sidebar-footer"><a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket icon"></i><span>Logout</span></a></div>
        </nav>

        <!-- MAIN VIEW WRAPPER[cite: 1] -->
        <main class="main-content">
            
            <div class="metrics-straight-row">
                <div class="card">
                    <div class="card-header"><i class="fa-solid fa-circle-check" style="color: #3b82f6;"></i><span class="card-title">Total Employees Present</span></div>
                    <div class="card-value"><?php echo $presentCount; ?></div>
                    <div class="card-footer"><span>Recorded today</span></div>
                </div>
                <div class="card">
                    <div class="card-header"><i class="fa-solid fa-user-clock" style="color: #3b82f6;"></i><span class="card-title">Late Arrivals Today</span></div>
                    <div class="card-value"><?php echo $lateCount; ?></div>
                    <div class="card-footer"><span>Recorded today</span></div>
                </div>
                <div class="card">
                    <div class="card-header"><i class="fa-solid fa-user-minus" style="color: #ef4444;"></i><span class="card-title">Employees Absent</span></div>
                    <div class="card-value"><?php echo $absentCount; ?></div>
                    <div class="card-footer"><span>Recorded today</span></div>
                </div>
                <div class="card">
                    <div class="card-header"><i class="fa-solid fa-clock" style="color: #3b82f6;"></i><span class="card-title">Average Check-In Time</span></div>
                    <div class="card-value"><?php echo htmlspecialchars($avgTimeValue); ?></div>
                    <div class="card-footer"><span>Active logs</span></div>
                </div>
            </div>
            
            <div class="attendance-panel">
                <div class="panel-header-row">
                    <div class="panel-title-area"><i class="fa-solid fa-list-check" style="color: #3b82f6;"></i> <span class="panel-title">Weekly Shift Overview</span></div>
                </div>

                <div class="table-controls-strip">
                    <div class="filter-group-left"><div class="table-search-box"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search operational schedules..."></div></div>
                    <div class="action-group-right"><button type="button" class="btn-action-outline"><i class="fa-solid fa-sliders"></i> Filter</button></div>
                </div>

                <div class="custom-table-wrapper">
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th>Employee / Hours</th>
                                <?php foreach ($weekDays as $dateStr => $dayInfo): ?>
                                    <th class="schedule-day-head">
                                        <?php echo $dayInfo['day_name']; ?><span class="day-num-lbl"><?php echo $dayInfo['day_num']; ?></span>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employees as $empId => $empData): ?>
                                <tr>
                                    <td>
                                        <div class="profile-meta-cell">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-image"></div>
                                                <div class="emp-detail-txt">
                                                    <span class="emp-name-title"><?php echo htmlspecialchars($empData['name']); ?></span>
                                                    <span class="dept-sub-lbl"><?php echo htmlspecialchars($empData['department']); ?></span>
                                                </div>
                                            </div>
                                            <span class="emp-hrs-counter"><?php echo $empData['total_hours']; ?>h</span>
                                        </div>
                                    </td>
                                    <?php foreach ($weekDays as $dateStr => $dayInfo): ?>
                                        <td>
                                            <?php if (isset($empData['shifts'][$dateStr]) && $empData['shifts'][$dateStr] !== null): ?>
                                                <?php $shift = $empData['shifts'][$dateStr]; ?>
                                                <div class="shift-card-block <?php echo htmlspecialchars($shift['color_class']); ?>">
                                                    <div class="shift-time-hdr"><span><?php echo htmlspecialchars($shift['time_range']); ?></span></div>
                                                    <span class="shift-role-title"><?php echo htmlspecialchars($shift['role']); ?></span>
                                                </div>
                                            <?php else: ?>
                                                <div class="empty-grid-block">
                                                    <button type="button" class="btn-add-slot" data-bs-toggle="modal" data-bs-target="#createShiftModal"
                                                            data-emp-id="<?php echo $empData['id']; ?>" data-emp-name="<?php echo htmlspecialchars($empData['name']); ?>"
                                                            data-shift-date="<?php echo $dateStr; ?>" data-day-name="<?php echo htmlspecialchars($dayInfo['day_name']); ?>">
                                                        <i class="fa-solid fa-plus"></i>
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- CREATE SHIFT MODAL DIALOG COMPONENT -->
    <div class="modal fade modal-custom-shift" id="createShiftModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="shiftCreationForm">
                    <input type="hidden" name="action" value="create_shift">
                    <input type="hidden" name="employee_id" id="formEmpId">
                    <input type="hidden" name="shift_date" id="formShiftDate">
                    <input type="hidden" name="selected_dates" id="formSelectedDates">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEmpName">Employee Name</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        
                        <!-- EDITABLE ASSIGNED DATE SELECTOR ROW -->
                        <div class="shift-row-item">
                            <i class="fa-regular fa-calendar"></i>
                            <span class="shift-label-text" style="font-size: 13px; margin-right: 4px;">Assign Date:</span>
                            <input type="date" id="modalDatePicker" class="modal-date-picker">
                        </div>

                        <!-- 12-HOUR TIME SELECT STRIPS -->
                        <div class="shift-row-item">
                            <i class="fa-regular fa-clock"></i>
                            <div class="shift-time-input-group">
                                <select name="start_time" id="formStartTime" class="shift-time-select-custom">
                                    <?php for($h=1; $h<=12; $h++): foreach(['AM','PM'] as $p): $ts = sprintf("%02d:00 %s", $h, $p); ?>
                                        <option value="<?php echo $ts; ?>" <?php echo $ts=='08:00 AM'?'selected':''; ?>><?php echo $ts; ?></option>
                                    <?php endforeach; endfor; ?>
                                </select>
                                <i class="fa-solid fa-arrow-right" style="font-size: 11px; width: auto; color: #94a3b8;"></i>
                                <select name="end_time" id="formEndTime" class="shift-time-select-custom">
                                    <?php for($h=1; $h<=12; $h++): foreach(['AM','PM'] as $p): $ts = sprintf("%02d:00 %s", $h, $p); ?>
                                        <option value="<?php echo $ts; ?>" <?php echo $ts=='05:00 PM'?'selected':''; ?>><?php echo $ts; ?></option>
                                    <?php endforeach; endfor; ?>
                                </select>
                                <span class="shift-duration-lbl" id="formDurationLbl">Duration: 9h</span>
                            </div>
                        </div>

                        <!-- ROLE SELECTION -->
                        <div class="shift-row-item">
                            <i class="fa-regular fa-id-badge"></i>
                            <span class="shift-label-text d-flex align-items-center gap-2">
                                <select name="role" id="formRole" class="role-select-input">
                                    <option value="Chief">Chief</option>
                                    <option value="Waiter">Waiter</option>
                                    <option value="Barista">Barista</option>
                                </select>
                            </span>
                        </div>

                        <hr style="border-color: #f1f5f9; margin: 4px 0;">

                        <!-- WEEKDAYS MATRIX EXTRA DAY BADGES -->
                        <div class="shift-row-item align-items-start flex-column gap-2">
                            <span class="shift-label-text" style="font-size: 13px; color: #64748b;">Assign targets to multiple days:</span>
                            <div class="week-days-badge-row">
                                <span class="day-badge-pill-all" id="btnSelectAllDays">All</span>
                                <span class="day-badge-pill" data-day="Mon">Mon</span>
                                <span class="day-badge-pill" data-day="Tue">Tue</span>
                                <span class="day-badge-pill" data-day="Wed">Wed</span>
                                <span class="day-badge-pill" data-day="Thu">Thu</span>
                                <span class="day-badge-pill" data-day="Fri">Fri</span>
                                <span class="day-badge-pill" data-day="Sat">Sat</span>
                                <span class="day-badge-pill" data-day="Sun">Sun</span>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-modal-save">Save Shifts</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Layout Control Interaction Trigger[cite: 1]
            const toggleBtn = document.getElementById('toggleSidebarBtn');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    const isMin = document.documentElement.classList.toggle('sidebar-minimized');
                    localStorage.setItem('sidebarMinimized', isMin);
                });
            }

            const weekDatesMap = {
                'Mon': '2026-07-06', 'Tue': '2026-07-07', 'Wed': '2026-07-08',
                'Thu': '2026-07-09', 'Fri': '2026-07-10', 'Sat': '2026-07-11', 'Sun': '2026-07-12'
            };

            const startTimeSelect = document.getElementById('formStartTime');
            const endTimeSelect = document.getElementById('formEndTime');
            const durationLabel = document.getElementById('formDurationLbl');
            const modalDatePicker = document.getElementById('modalDatePicker');
            const hiddenShiftDate = document.getElementById('formShiftDate');
            const hiddenSelectedDates = document.getElementById('formSelectedDates');
            const btnSelectAllDays = document.getElementById('btnSelectAllDays');

            function calculate12HrDuration() {
                const parseTime = (val) => {
                    const [time, ampm] = val.split(' ');
                    let [hrs, mins] = time.split(':').map(Number);
                    if (ampm === 'PM' && hrs < 12) hrs += 12;
                    if (ampm === 'AM' && hrs === 12) hrs = 0;
                    return hrs * 60 + mins;
                };

                const startTotal = parseTime(startTimeSelect.value);
                const endTotal = parseTime(endTimeSelect.value);
                let diff = endTotal - startTotal;
                if (diff < 0) diff += 1440;

                const totalHrs = Math.round((diff / 60) * 10) / 10;
                durationLabel.textContent = `Duration: ${totalHrs}h`;
            }

            startTimeSelect.addEventListener('change', calculate12HrDuration);
            endTimeSelect.addEventListener('change', calculate12HrDuration);

            // Gathers active states from pills into hidden forms
            function compileSelectedDays() {
                const activeDates = [];
                document.querySelectorAll('.day-badge-pill.active').forEach(pill => {
                    const d = pill.getAttribute('data-day');
                    if (weekDatesMap[d]) activeDates.push(weekDatesMap[d]);
                });
                
                // Fallback to the explicit calendar layout picker field if no pills remain activated
                if (activeDates.length === 0 && modalDatePicker.value) {
                    activeDates.push(modalDatePicker.value);
                }
                
                hiddenSelectedDates.value = activeDates.join(',');

                const totalPills = document.querySelectorAll('.day-badge-pill').length;
                const activePills = document.querySelectorAll('.day-badge-pill.active').length;
                btnSelectAllDays.classList.toggle('active', totalPills === activePills && totalPills > 0);
            }

            // Updates badges when the custom inline date input changes
            modalDatePicker.addEventListener('change', function() {
                hiddenShiftDate.value = this.value;
                
                // Highlight corresponding day pill matching selected calendar input timestamp
                const dateObj = new Date(this.value + 'T00:00:00');
                const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                const curDayName = days[dateObj.getDay()];

                document.querySelectorAll('.day-badge-pill').forEach(pill => {
                    pill.classList.toggle('active', pill.getAttribute('data-day') === curDayName);
                });
                compileSelectedDays();
            });

            // Individual pill day selections
            document.querySelectorAll('.day-badge-pill').forEach(pill => {
                pill.addEventListener('click', function() {
                    this.classList.toggle('active');
                    
                    // If precisely 1 pill is selected, mirror its date directly inside the explicit date box
                    const activePills = document.querySelectorAll('.day-badge-pill.active');
                    if (activePills.length === 1) {
                        const singleDay = activePills[0].getAttribute('data-day');
                        if (weekDatesMap[singleDay]) {
                            modalDatePicker.value = weekDatesMap[singleDay];
                            hiddenShiftDate.value = weekDatesMap[singleDay];
                        }
                    }
                    compileSelectedDays();
                });
            });

            // Multi select macro logic link
            btnSelectAllDays.addEventListener('click', function() {
                const isCurrentlyAllActive = this.classList.contains('active');
                document.querySelectorAll('.day-badge-pill').forEach(pill => {
                    pill.classList.toggle('active', !isCurrentlyAllActive);
                });
                this.classList.toggle('active', !isCurrentlyAllActive);
                compileSelectedDays();
            });

            const createShiftModal = document.getElementById('createShiftModal');
            if (createShiftModal) {
                createShiftModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const shiftDate = button.getAttribute('data-shift-date');
                    const dayName = button.getAttribute('data-day-name');
                    
                    document.getElementById('formEmpId').value = button.getAttribute('data-emp-id');
                    hiddenShiftDate.value = shiftDate;
                    modalDatePicker.value = shiftDate;
                    document.getElementById('modalEmpName').textContent = button.getAttribute('data-emp-name');

                    document.querySelectorAll('.day-badge-pill').forEach(pill => {
                        pill.classList.toggle('active', pill.getAttribute('data-day') === dayName);
                    });
                    
                    compileSelectedDays();
                    calculate12HrDuration();
                });
            }

            const form = document.getElementById('shiftCreationForm');
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!hiddenSelectedDates.value && !modalDatePicker.value) {
                    alert('Please select or specify a valid shift configuration date.');
                    return;
                }

                fetch('shift_management.php', {
                    method: 'POST',
                    body: new FormData(form)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(createShiftModal).hide();
                        window.location.reload();
                    } else {
                        alert('System Process Error: ' + data.error);
                    }
                })
                .catch(err => alert('Network processing channel failed context log parsing.'));
            });
        });
    </script>
</body>
</html>