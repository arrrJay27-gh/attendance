<?php 

require_once 'auth.php';
$activePage = 'dashboard'; 

// Use shared Database class for connection
require_once 'database.php';
$database = new Database();
$conn = $database->getConnection();

// 1. Live Count: Total Employees Present Today
$present_q = "SELECT COUNT(DISTINCT name) as total FROM attendance WHERE status='Present' AND DATE(date) = CURDATE()";
$present_res = $conn->query($present_q);
$total_present = ($present_res && $row = $present_res->fetch_assoc()) ? $row['total'] : 0;

// 2. Live Count: Late Arrivals Today
$late_q = "SELECT COUNT(DISTINCT name) as total FROM attendance WHERE status='Late' AND DATE(date) = CURDATE()";
$late_res = $conn->query($late_q);
$total_late = ($late_res && $row = $late_res->fetch_assoc()) ? $row['total'] : 0;

// 3. Live Count: Employees Absent Today
$absent_q = "SELECT COUNT(DISTINCT name) as total FROM attendance WHERE status='Absent' AND DATE(date) = CURDATE()";
$absent_res = $conn->query($absent_q);
$total_absent = ($absent_res && $row = $absent_res->fetch_assoc()) ? $row['total'] : 0;


// ==========================================================================
// REAL-TIME DYNAMIC GRAPH ANALYTICS CALCULATIONS
// ==========================================================================
$graph_months = [];
$graph_counts = [];

for ($i = 5; $i >= 0; $i--) {
    $target_month_label = date('M', strtotime("-$i months"));
    $target_month_num = date('m', strtotime("-$i months"));
    $target_year = date('Y', strtotime("-$i months"));
    
    $analytics_q = "SELECT COUNT(*) as total FROM attendance WHERE MONTH(date) = '$target_month_num' AND YEAR(date) = '$target_year'";
    $analytics_res = $conn->query($analytics_q);
    $month_count = ($analytics_res && $row = $analytics_res->fetch_assoc()) ? $row['total'] : 0;
    
    $graph_months[] = $target_month_label;
    $graph_counts[] = $month_count;
}

$max_recorded_value = max($graph_counts) > 0 ? max($graph_counts) : 10;
$y_positions = [];
foreach ($graph_counts as $count) {
    $y_positions[] = 180 - (($count / $max_recorded_value) * 120); 
}

$svg_points_path = "M 25,{$y_positions[0]} ";
$svg_points_path .= "C 70,".($y_positions[0]-20)." 70,".($y_positions[1]+5)." 115,{$y_positions[1]} ";
$svg_points_path .= "C 160,".($y_positions[1]-5)." 160,".($y_positions[2]+5)." 205,{$y_positions[2]} ";
$svg_points_path .= "C 250,".($y_positions[2]-5)." 250,".($y_positions[3]+5)." 295,{$y_positions[3]} ";
$svg_points_path .= "C 340,".($y_positions[3]-5)." 340,".($y_positions[4]+5)." 385,{$y_positions[4]} ";
$svg_points_path .= "C 430,".($y_positions[4]-5)." 430,".($y_positions[5]+5)." 475,{$y_positions[5]}";

$svg_area_fill_path = $svg_points_path . " L 475,180 L 25,180 Z";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiwi Digital Dashboard</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>try{if(localStorage.getItem('sidebarMinimized')==='true'){document.documentElement.classList.add('sidebar-minimized');}}catch(e){}</script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; padding: 20px; height: 100vh; overflow: hidden; }
        
        /* Layout Grid System */
        .app-container { 
            display: grid; 
            grid-template-columns: 310px 1fr; 
            gap: 30px; 
            height: calc(100vh - 40px); 
            width: 100%; 
            position: relative;
            transition: grid-template-columns 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Handle minimized state grid column switch */
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
            overflow: hidden; 
        }

        .dashboard-header-bar { display: flex; justify-content: space-between; align-items: center; width: 100%; height: 40px; }
        .dashboard-title { font-size: 24px; font-weight: 700; color: #0f172a; }
        .search-container { position: relative; width: 320px; }
        .search-container i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }
        .search-input { width: 100%; padding: 10px 14px 10px 40px; border-radius: 50px; border: 1px solid #e2e8f0; background-color: #ffffff; font-size: 13px; outline: none; color: #334155; }
        
        .dashboard-row-layout { display: flex; flex-direction: column; gap: 16px; width: 100%; height: calc(100% - 56px); }
        .metrics-straight-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; width: 100%; }
        
        .card { background-color: #ffffff; border-radius: 16px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.04); border: 1px solid #f1f3f5; display: flex; flex-direction: column; justify-content: space-between; height: 125px; }
        .card-header { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
        .card-header i { font-size: 14px; color: #3b82f6; }
        .card-title { font-size: 13px; font-weight: 600; color: #1f2937; white-space: nowrap; }
        .card-value { font-size: 28px; font-weight: 700; color: #111827; line-height: 1.1; }
        .card-footer { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #6b7280; white-space: nowrap; }
        
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 6px; border-radius: 6px; font-size: 11px; font-weight: 600; }
        .badge.positive { background-color: #ecfdf5; color: #10b981; }
        .badge.negative { background-color: #fef2f2; color: #ef4444; }

        .bottom-content-area { display: flex; gap: 24px; width: 100%; height: calc(100% - 141px); align-items: stretch; }
        
        .analytics-card { background-color: #ffffff; border-radius: 16px; padding: 25px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.04); border: 1px solid #f1f3f5; flex-grow: 1; display: flex; flex-direction: column; gap: -12px; height: 95%; overflow: hidden; }
        .analytics-header { display: flex; justify-content: space-between; align-items: center; }
        .analytics-title { font-size: 16px; font-weight: 600; color: #1f2937; }
        .analytics-dropdown { background-color: #f8f9fa; border: 1px solid #e5e7eb; padding: 4px 10px; border-radius: 8px; font-size: 12px; color: #4b5563; cursor: pointer; outline: none; }
        .chart-container { width: 100%; flex-grow: 1; position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden; }

        .right-sidebar-column { display: flex; flex-direction: column; gap: 16px; width: 340px; min-width: 340px; height: 100%; }
        .calendar-card { background-color: #f1f5f7; border-radius: 20px; padding: 16px 20px; width: 100%; box-shadow: inset 0 0 1px rgba(0,0,0,0.05); flex-shrink: 0; }
        .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .calendar-month-title { font-size: 20px; font-weight: 600; color: #111827; }
        .calendar-nav-buttons { display: flex; gap: 6px; }
        .cal-btn { background: #ffffff; border: 1px solid #e5e7eb; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #374151; font-size: 11px; transition: all 0.2s ease; user-select: none; }
        .cal-btn:hover { background-color: #e5e7eb; }
        .calendar-week-strip { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; text-align: center; }
        .day-column { display: flex; flex-direction: column; align-items: center; gap: 8px; cursor: pointer; }
        .day-label { font-size: 12px; font-weight: 500; color: #6b7280; }
        .day-number { font-size: 14px; font-weight: 600; color: #1f2937; width: 100%; padding: 6px 0; border-radius: 8px; transition: all 0.2s ease; }
        .day-column.active .day-number { background-color: #004d4d; color: #ffffff; border-radius: 8px; }
        .day-column.active .day-label { color: #004d4d; font-weight: 600; }
        
        .activity-log-card { background-color: #f1f5f7; border-radius: 20px; padding: 16px 20px; width: 100%; box-shadow: inset 0 0 1px rgba(0,0,0,0.05); flex-grow: 1; display: flex; flex-direction: column; overflow: hidden; }
        .activity-log-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-shrink: 0; }
        .activity-log-title { font-size: 15px; font-weight: 600; color: #111827; }
        .view-all-link { font-size: 12px; font-weight: 500; color: #3b82f6; text-decoration: none; }
        .activity-list { display: flex; flex-direction: column; gap: 12px; overflow-y: auto; flex-grow: 1; padding-right: 4px; }
        .activity-item { display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
        .activity-user-info { display: flex; align-items: center; gap: 10px; }
        .activity-avatar { width: 32px; height: 32px; border-radius: 50%; background-color: #e2e8f0; object-fit: cover; }
        .activity-details { display: flex; flex-direction: column; }
        .activity-username { font-size: 12px; font-weight: 600; color: #1f2937; line-height: 1.2; }
        .activity-action { font-size: 11px; color: #6b7280; }
        .activity-time { font-size: 11px; color: #94a3b8; white-space: nowrap; }

        /* ==================================================================
           SIDEBAR STYLES (MATCHING SCREENSHOT WITH DYNAMIC LOGO RESIZING)
           ================================================================== */
        .sidebar {
            width: 100%;
            background-color: #dcdddf; /* Grey tone capsule container background */
            border-radius: 36px;       /* Pill shape corner flow configuration */
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
        
        /* Smooth scale layout setup for the core corporate logo image */
        .logo-img {
            max-width: 140px;
            height: auto;
            transition: max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s ease;
        }
        
        /* Floating Round Sidebar Switcher Button Layout Profile */
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
        
        /* White Active Element Tab Inset Configuration */
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
           MINIMIZED STATE TRANSITIONS & AUTOMATIC LOGO RESIZING
           ================================================================== */
        .sidebar-minimized .sidebar {
            padding: 45px 0 35px 0;
        }
        
        /* Resize the logo container and image smoothly when minimized */
        .sidebar-minimized .sidebar .logo-img {
            max-width: 40px; /* Adjusts width down to a small icon size */
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
    </style>
</head>
<body>

    <div class="app-container">
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

        <main class="main-content">
            <div class="dashboard-header-bar">
                <h1 class="dashboard-title"></h1>
                <div class="search-container">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" class="search-input" placeholder="Search employees, attendance, reports...">
                </div>
            </div>

            <div class="dashboard-row-layout">
                <div class="metrics-straight-row">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-circle-check"></i>
                            <span class="card-title">Total Employees Present</span>
                        </div>
                        <div class="card-value"><?php echo $total_present; ?></div>
                        <div class="card-footer">
                            <span class="badge positive"><i class="fa-solid fa-arrow-up"></i> Live</span>
                            <span>from database</span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-user-clock"></i>
                            <span class="card-title">Late Arrivals Today</span>
                        </div>
                        <div class="card-value"><?php echo $total_late; ?></div>
                        <div class="card-footer">
                            <span class="badge positive"><i class="fa-solid fa-arrow-up"></i> Live</span>
                            <span>updates tracked</span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-user-minus" style="color: #ef4444;"></i>
                            <span class="card-title">Employees Absent</span>
                        </div>
                        <div class="card-value"><?php echo $total_absent; ?></div>
                        <div class="card-footer">
                            <span class="badge negative"><i class="fa-solid fa-arrow-down"></i> Live</span>
                            <span>missing checks</span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-clock"></i>
                            <span class="card-title">Average Check-In Time</span>
                        </div>
                        <div class="card-value" id="live-time-card">--:-- --</div>
                        <div class="card-footer">
                            <span>System active</span>
                        </div>
                    </div>
                </div>

                <div class="bottom-content-area">
                    <div class="analytics-card">
                        <div class="analytics-header">
                            <span class="analytics-title">Attendance Overview</span>
                            <select class="analytics-dropdown">
                                <option>Last 6 months</option>
                            </select>
                        </div>
                        <div class="chart-container">
                            <svg viewBox="0 0 500 200" width="100%" height="100%" style="overflow: visible; max-height: 100%;">
                                <defs>
                                    <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#a855f7" stop-opacity="0.4"/>
                                        <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0"/>
                                    </linearGradient>
                                </defs>

                                <line x1="0" y1="20" x2="500" y2="20" stroke="#f1f5f9" stroke-width="1" />
                                <line x1="0" y1="60" x2="500" y2="60" stroke="#f1f5f9" stroke-width="1" />
                                <line x1="0" y1="100" x2="500" y2="100" stroke="#f1f5f9" stroke-width="1" />
                                <line x1="0" y1="140" x2="500" y2="140" stroke="#f1f5f9" stroke-width="1" />
                                <line x1="0" y1="180" x2="500" y2="180" stroke="#f1f5f9" stroke-width="1" />

                                <line x1="25" y1="20" x2="25" y2="180" stroke="#f1f5f9" stroke-width="1" />
                                <line x1="115" y1="20" x2="115" y2="180" stroke="#f1f5f9" stroke-width="1" />
                                <line x1="205" y1="20" x2="205" y2="180" stroke="#f1f5f9" stroke-width="1" />
                                <line x1="295" y1="20" x2="295" y2="180" stroke="#f1f5f9" stroke-width="1" />
                                <line x1="385" y1="20" x2="385" y2="180" stroke="#f1f5f9" stroke-width="1" />
                                <line x1="475" y1="20" x2="475" y2="180" stroke="#f1f5f9" stroke-width="1" />

                                <path d="<?php echo $svg_area_fill_path; ?>" fill="url(#chartGradient)" />
                                <path d="<?php echo $svg_points_path; ?>" fill="none" stroke="url(#chartGradient)" stroke-width="3" />

                                <circle cx="475" cy="<?php echo $y_positions[5]; ?>" r="5" fill="#3b82f6" stroke="#ffffff" stroke-width="2" />
                                <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
                                    <feDropShadow dx="0" dy="2" stdDeviation="2" flood-opacity="0.1" />
                                </filter>
                                
                                <rect x="445" y="<?php echo ($y_positions[5] - 35); ?>" width="50" height="24" rx="12" fill="#ffffff" filter="url(#shadow)" />
                                <text x="470" y="<?php echo ($y_positions[5] - 19); ?>" font-size="11" font-weight="600" fill="#3b82f6" text-anchor="middle"><?php echo $graph_counts[5]; ?></text>

                                <text x="-15" y="24" font-size="11" fill="#94a3b8" text-anchor="end"><?php echo $max_recorded_value; ?></text>
                                <text x="-15" y="104" font-size="11" fill="#94a3b8" text-anchor="end"><?php echo round($max_recorded_value / 2); ?></text>
                                <text x="-15" y="184" font-size="11" fill="#94a3b8" text-anchor="end">0</text>

                                <text x="25" y="202" font-size="12" fill="#94a3b8" text-anchor="middle"><?php echo $graph_months[0]; ?></text>
                                <text x="115" y="202" font-size="12" fill="#94a3b8" text-anchor="middle"><?php echo $graph_months[1]; ?></text>
                                <text x="205" y="202" font-size="12" fill="#94a3b8" text-anchor="middle"><?php echo $graph_months[2]; ?></text>
                                <text x="295" y="202" font-size="12" fill="#94a3b8" text-anchor="middle"><?php echo $graph_months[3]; ?></text>
                                <text x="385" y="202" font-size="12" fill="#94a3b8" text-anchor="middle"><?php echo $graph_months[4]; ?></text>
                                <text x="475" y="202" font-size="12" fill="#94a3b8" text-anchor="middle"><?php echo $graph_months[5]; ?></text>
                            </svg>
                        </div>
                    </div>

                    <div class="right-sidebar-column">
                        <div class="calendar-card">
                            <div class="calendar-header">
                                <div class="calendar-month-title" id="calendar-title">-- --</div>
                                <div class="calendar-nav-buttons">
                                    <button class="cal-btn" id="prev-btn"><i class="fa-solid fa-chevron-left"></i></button>
                                    <button class="cal-btn" id="next-btn"><i class="fa-solid fa-chevron-right"></i></button>
                                </div>
                            </div>
                            <div class="calendar-week-strip" id="calendar-strip"></div>
                        </div>

                        <div class="activity-log-card">
                            <div class="activity-log-header">
                                <span class="activity-log-title">Activity log</span>
                                <a href="#" class="view-all-link">view all</a>
                            </div>
                            <div class="activity-list">
                                <?php
                                $log_q = "SELECT name, status, time FROM attendance ORDER BY id DESC LIMIT 4";
                                $log_res = $conn->query($log_q);

                                if ($log_res && $log_res->num_rows > 0) {
                                    while($log = $log_res->fetch_assoc()) {
                                        echo '
                                        <div class="activity-item">
                                            <div class="activity-user-info">
                                                <img src="https://ui-avatars.com/api/?name='.urlencode($log['name']).'&background=cbd5e1&color=334155" class="activity-avatar" alt="">
                                                <div class="activity-details">
                                                    <span class="activity-username">'.htmlspecialchars($log['name']).'</span>
                                                    <span class="activity-action">'.htmlspecialchars($log['status']).'</span>
                                                </div>
                                            </div>
                                            <span class="activity-time">'.date("h:i A", strtotime($log['time'])).'</span>
                                        </div>';
                                    }
                                } else {
                                    echo '<span class="activity-action" style="text-align:center; padding:20px; display:block;">No log tracks found</span>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div> 
            </div> 
        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Sidebar minimization logic
            const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
            
            toggleSidebarBtn.addEventListener('click', function() {
                const isMinimized = document.documentElement.classList.toggle('sidebar-minimized');
                localStorage.setItem('sidebarMinimized', isMinimized);
            });

            // Running metrics logic loop
            function updateLiveTimeCard() {
                const now = new Date();
                let hours = now.getHours(); let minutes = now.getMinutes();
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12; hours = hours ? hours : 12; 
                minutes = minutes < 10 ? '0' + minutes : minutes;
                hours = hours < 10 ? '0' + hours : hours;
                document.getElementById('live-time-card').innerText = `${hours}:${minutes} ${ampm}`;
            }
            updateLiveTimeCard(); setInterval(updateLiveTimeCard, 1000);

            // Calendar ribbon view builder logic
            const realToday = new Date(); let currentSelectedDate = new Date(realToday); let dayOffsetValue = 0; 
            function renderCalendarView() {
                const dayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                const monthLabels = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                const calendarStrip = document.getElementById('calendar-strip'); const calendarTitle = document.getElementById('calendar-title');
                const currentViewDate = new Date(realToday); currentViewDate.setDate(realToday.getDate() + dayOffsetValue);
                const currentDayOfWeekIndex = currentViewDate.getDay(); const startOfWeekSunday = new Date(currentViewDate);
                startOfWeekSunday.setDate(currentViewDate.getDate() - currentDayOfWeekIndex);
                calendarTitle.innerText = `${monthLabels[currentSelectedDate.getMonth()]} ${currentSelectedDate.getDate()}`;
                calendarStrip.innerHTML = '';
                for (let i = 0; i < 7; i++) {
                    const loopsDay = new Date(startOfWeekSunday); loopsDay.setDate(startOfWeekSunday.getDate() + i);
                    const dayName = dayLabels[i]; const dayNumber = loopsDay.getDate();
                    const dayColumn = document.createElement('div'); dayColumn.classList.add('day-column');
                    if (loopsDay.getDate() === currentSelectedDate.getDate() && loopsDay.getMonth() === currentSelectedDate.getMonth() && loopsDay.getFullYear() === currentSelectedDate.getFullYear()) { dayColumn.classList.add('active'); }
                    dayColumn.innerHTML = `<span class="day-label">${dayName}</span><span class="day-number">${dayNumber}</span>`;
                    dayColumn.addEventListener('click', function() { currentSelectedDate = loopsDay; renderCalendarView(); });
                    calendarStrip.appendChild(dayColumn);
                }
            }
            document.getElementById('prev-btn').addEventListener('click', function() { dayOffsetValue -= 7; renderCalendarView(); });
            document.getElementById('next-btn').addEventListener('click', function() { dayOffsetValue += 7; renderCalendarView(); });
            renderCalendarView();
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>