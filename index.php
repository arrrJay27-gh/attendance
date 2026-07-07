<?php

require_once 'auth.php';
$activePage = 'dashboard';

require_once 'database.php';
require_once 'class/Dashboard.php';

$database = new Database();
$conn = $database->getConnection();
$dashboard = new Dashboard($conn);

$stats = $dashboard->getTodayStats();
$total_present = $stats['present'];
$total_late = $stats['late'];
$total_absent = $stats['absent'];
$avg_check_in = $stats['avg_check_in'];
$activity = $dashboard->getRecentActivity(4);

// Pull attendance values to power the monthly chart bar heights dynamically
$analytics = $dashboard->getMonthlyAnalytics(6);
$graph_months = $analytics['labels']; // e.g., ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
$graph_counts = $analytics['counts']; // Actual attendance counts from DB
$max_recorded_value = $analytics['max'] > 0 ? $analytics['max'] : 1; 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiwi Digital Dashboard</title>
    <link class="styles" rel="stylesheet" href="bootstrap-5.3.5-dist/css/bootstrap.min.css">
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
        .card-header { 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            margin-bottom: 4px; 
            border: none; 
            background: transparent; 
            padding: 0; 
        }
        .card-header i { font-size: 14px; color: #3b82f6; }
        .card-title { font-size: 13px; font-weight: 600; color: #1f2937; white-space: nowrap; }
        .card-value { font-size: 28px; font-weight: 700; color: #111827; line-height: 1.1; }
        .card-footer { 
            display: flex; 
            align-items: center; 
            gap: 6px; 
            font-size: 12px; 
            color: #6b7280; 
            white-space: nowrap; 
            border: none; 
            background: transparent; 
            padding: 0; 
        }

        .bottom-content-area { display: flex; gap: 24px; width: 100%; height: calc(100% - 141px); align-items: stretch; }
        
        /* ==================================================================
           ANALYTICS CARD & PROGRESS COMPONENT STYLES
           ================================================================== */
        .analytics-container-column { display: flex; flex-direction: column; gap: 16px; flex-grow: 1; height: 95%; }
        
        .kpi-card { background-color: #ffffff; border-radius: 16px; padding: 20px 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.04); border: 1px solid #f1f3f5; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .kpi-header { display: flex; justify-content: space-between; align-items: center; }
        .kpi-title-block { display: flex; align-items: center; gap: 8px; font-size: 15px; font-weight: 600; color: #1e293b; }
        .kpi-title-block i { color: #64748b; font-size: 14px; }
        
        /* UPDATED: Structured padding and design style for the select filter element */
        .analytics-dropdown { 
            background-color: #ffffff; 
            border: 1px solid #e2e8f0; 
            border-radius: 8px; 
            padding: 6px 36px 6px 12px; 
            font-size: 13px; 
            font-weight: 500; 
            color: #334155; 
            outline: none; 
            cursor: pointer; 
            transition: all 0.2s ease; 
            appearance: none; 
            -webkit-appearance: none; 
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e"); 
            background-repeat: no-repeat; 
            background-position: right 12px center; 
            background-size: 14px; 
        }
        .analytics-dropdown:hover { border-color: #cbd5e1; background-color: #f8fafc; }
        .analytics-dropdown:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1); }

        .kpi-value-block { margin-top: 12px; }
        .kpi-percentage { font-size: 26px; font-weight: 700; color: #0f172a; }
        .kpi-trend { font-size: 13px; font-weight: 600; color: #22c55e; margin-top: 2px; }
        .kpi-trend span { color: #64748b; font-weight: 400; }

        .kpi-bar-chart { display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px; align-items: flex-end; height: 110px; margin-top: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
        .bar-wrapper { display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end; position: relative; }
        .bar-column-fill { width: 100%; background: linear-gradient(to top, rgba(99, 102, 241, 0.05), rgba(99, 102, 241, 0.4)); border-top: 2px solid #4f46e5; border-radius: 2px 2px 0 0; min-height: 5px; transition: height 0.5s ease; }
        .bar-label { font-size: 11px; color: #94a3b8; margin-top: 6px; font-weight: 500; }

        .recent-activity-timeline-card { background-color: #ffffff; border-radius: 16px; padding: 20px 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.04); border: 1px solid #f1f3f5; height: 140px; display: flex; flex-direction: column; justify-content: space-between; }
        .timeline-header { display: flex; justify-content: space-between; align-items: center; }
        .timeline-title { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; color: #1e293b; }
        .timeline-title i { color: #64748b; }
        .timeline-menu-btn { background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 14px; }
        
        .total-worked-label { font-size: 15px; font-weight: 600; color: #334155; margin-top: 6px; }
        .total-worked-duration { font-size: 16px; font-weight: 400; color: #64748b; margin-bottom: 10px; }
        .total-worked-duration strong { color: #0f172a; font-weight: 700; font-size: 18px; }

        .stacked-progress-bar { display: flex; width: 100%; height: 14px; border-radius: 4px; overflow: hidden; background-color: #f1f5f9; }
        .progress-slice { height: 100%; transition: width 0.3s ease; }
        .slice-pause { background-color: #f59e0b; }
        .slice-active { background-color: #38bdf8; }
        .slice-extra { background-color: #8b5cf6; }

        .progress-legends { display: flex; gap: 16px; margin-top: 10px; }
        .legend-item { display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 600; color: #1e293b; }
        .legend-dot { width: 8px; height: 8px; border-radius: 50%; }

        /* Sidebar Elements */
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
        .activity-avatar { width: 32px; height: 32px; border-radius: 50%; background-color: #cbd5e1; object-fit: cover; }
        .activity-details { display: flex; flex-direction: column; }
        .activity-username { font-size: 12px; font-weight: 600; color: #1f2937; line-height: 1.2; }
        .activity-action { font-size: 11px; color: #6b7280; }
        .activity-time { font-size: 11px; color: #94a3b8; white-space: nowrap; }

        .sidebar { width: 100%; background-color: #dcdddf; border-radius: 36px; padding: 45px 0 35px 0; display: flex; flex-direction: column; position: relative; height: 100%; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar-header { display: flex; justify-content: center; align-items: center; margin-bottom: 35px; width: 100%; position: relative; padding: 0 20px; }
        .logo-container { display: flex; align-items: center; justify-content: center; width: 100%; }
        .logo-img { max-width: 140px; height: auto; transition: max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s ease; }
        .sidebar-toggle-btn { position: absolute; top: 10px; right: -13px; width: 26px; height: 26px; border-radius: 50%; background-color: #ffffff; border: none; box-shadow: 0 2px 6px rgba(0,0,0,0.12); display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 100; color: #52525b; }
        .sidebar-toggle-btn i { font-size: 11px; transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .nav-links { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 6px; flex-grow: 1; }
        .nav-item { width: 100%; }
        .nav-item a { display: flex; align-items: center; gap: 20px; padding: 15px 35px; color: #434850; text-decoration: none; font-size: 16px; font-weight: 600; transition: color 0.2s; }
        .nav-item.active a { background-color: #ffffff; color: #11161e; border-top-right-radius: 18px; border-bottom-right-radius: 18px; margin-right: 20px; padding-left: 35px; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02); }
        .nav-item a i.icon { font-size: 20px; width: 26px; text-align: center; color: #434850; }
        .nav-item.active a i.icon { color: #11161e; }
        .sidebar-footer { margin-top: auto; }
        .logout-btn { display: flex; align-items: center; gap: 20px; padding: 15px 35px; color: #434850; text-decoration: none; font-size: 16px; font-weight: 600; }
        
        .sidebar-minimized .sidebar { padding: 45px 0 35px 0; }
        .sidebar-minimized .sidebar .logo-img { max-width: 40px; transform: scale(1); }
        .sidebar-minimized .sidebar .nav-item a span, .sidebar-minimized .sidebar .logout-btn span { display: none; }
        .sidebar-minimized .sidebar .nav-item a { justify-content: center; padding: 15px 0; }
        .sidebar-minimized .sidebar .nav-item.active a { margin-right: 10px; padding-left: 0; border-radius: 0 16px 16px 0; }
        .sidebar-minimized .sidebar .logout-btn { justify-content: center; padding: 15px 0; }
        .sidebar-minimized .sidebar-toggle-btn i { transform: rotate(180deg); }
    </style>
</head>
<body>

    <div class="app-container">
        <?php
        $imgPrefix = $imgPrefix ?? '';
        $navItems = [
            ['id' => 'dashboard',   'href' => 'index.php',       'icon' => 'fa-table-cells-large',       'label' => 'Dashboard'],
            ['id' => 'employee',    'href' => 'employee.php',    'icon' => 'fa-users-rectangle',         'label' => 'Employee'],
            ['id' => 'biometric',   'href' => 'biometrics.php',   'icon' => 'fa-fingerprint',             'label' => 'Biometric Enrollment'],
            ['id' => 'timekeeping', 'href' => 'timekeeping.php', 'icon' => 'fa-clipboard-user',          'label' => 'Timekeeping'],
            ['id' => 'shift',       'href' => 'shift_management.php', 'icon' => 'fa-right-left',         'label' => 'Shift Configuration'],
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
                        <div class="card-value" data-stat-present><?php echo $total_present; ?></div>
                        <div class="card-footer">
                            <span>Recorded today</span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-user-clock"></i>
                            <span class="card-title">Late Arrivals Today</span>
                        </div>
                        <div class="card-value" data-stat-late><?php echo $total_late; ?></div>
                        <div class="card-footer">
                            <span>Recorded today</span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-user-minus" style="color: #ef4444;"></i>
                            <span class="card-title">Employees Absent</span>
                        </div>
                        <div class="card-value" data-stat-absent><?php echo $total_absent; ?></div>
                        <div class="card-footer">
                            <span>Recorded today</span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-clock"></i>
                            <span class="card-title">Average Check-In Time</span>
                        </div>
                        <div class="card-value" data-stat-avg-checkin><?php echo htmlspecialchars($avg_check_in); ?></div>
                        <div class="card-footer">
                            <span>Based on active entries</span>
                        </div>
                    </div>
                </div>

                <div class="bottom-content-area">
                    <div class="analytics-container-column">
                        
                        <!-- UPPER CARD: KPI PERFORMANCE -->
                        <div class="kpi-card">
                            <div class="kpi-header">
                                <div class="kpi-title-block">
                                    <i class="fa-solid fa-chart-line"></i>
                                    <span>Attendance performance</span>
                                </div>
                                <!-- UPDATED: Clean interactive select class config with standard dropdown action handles -->
                                <select class="analytics-dropdown" id="analyticsPeriodSelector">
                                    <option value="6" selected>Last 6 months</option>
                                    <option value="3">Last 3 months</option>
                                    <option value="12">Last 12 months</option>
                                </select>
                            </div>
                            
                            <div class="kpi-value-block">
                                <div class="kpi-percentage">90.75%</div>
                                <div class="kpi-trend"><i class="fa-solid fa-arrow-up"></i> +20% <span>vs last month</span></div>
                            </div>

                            <div class="kpi-bar-chart">
                                <?php for($i = 0; $i < 6; $i++): 
                                    $monthName = isset($graph_months[$i]) ? $graph_months[$i] : '--';
                                    $attendanceCount = isset($graph_counts[$i]) ? $graph_counts[$i] : 0;
                                    // Map row heights cleanly against the max recorded count
                                    $percentageHeight = ($attendanceCount / $max_recorded_value) * 100;
                                    if($percentageHeight < 10 && $attendanceCount > 0) $percentageHeight = 15; 
                                ?>
                                    <div class="bar-wrapper">
                                        <div class="bar-column-fill" style="height: <?php echo $percentageHeight; ?>%;" title="Total: <?php echo $attendanceCount; ?>"></div>
                                        <span class="bar-label"><?php echo $monthName; ?></span>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- LOWER CARD: RECENT ACTIVITY (TIME WORKED SLIDER) -->
                        <div class="recent-activity-timeline-card">
                            <div class="timeline-header">
                                <div class="timeline-title">
                                    <i class="fa-regular fa-clock"></i>
                                    <span>Recent activity</span>
                                </div>
                                <button type="button" class="timeline-menu-btn"><i class="fa-solid fa-ellipsis"></i></button>
                            </div>

                            <div class="total-worked-label">Total time worked</div>
                            <div class="total-worked-duration"><strong>12</strong> hours <strong>27</strong> minutes</div>

                            <!-- Configurable Stacked Tracking Row CSS -->
                            <div class="stacked-progress-bar">
                                <div class="progress-slice slice-pause" style="width: 15%;" title="Pause Time"></div>
                                <div class="progress-slice slice-active" style="width: 75%;" title="Active Time"></div>
                                <div class="progress-slice slice-extra" style="width: 10%;" title="Extra Time"></div>
                            </div>

                            <div class="progress-legends">
                                <div class="legend-item"><div class="legend-dot" style="background-color: #f59e0b;"></div>Pause Time</div>
                                <div class="legend-item"><div class="legend-dot" style="background-color: #38bdf8;"></div>Active Time</div>
                                <div class="legend-item"><div class="legend-dot" style="background-color: #8b5cf6;"></div>Extra Time</div>
                            </div>
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
                            <div class="activity-list" id="activity-log-list">
                                <?php if (!empty($activity)): ?>
                                    <?php foreach ($activity as $log): ?>
                                        <div class="activity-item">
                                            <div class="activity-user-info">
                                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($log['name']); ?>&background=cbd5e1&color=334155" class="activity-avatar" alt="">
                                                <div class="activity-details">
                                                    <span class="activity-username"><?php echo htmlspecialchars($log['name']); ?></span>
                                                    <span class="activity-action"><?php echo htmlspecialchars($log['status']); ?></span>
                                                </div>
                                            </div>
                                            <span class="activity-time"><?php echo !empty($log['time_in']) ? date('h:i A', strtotime($log['time_in'])) : '--:--'; ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="activity-action" style="text-align:center; padding:20px; display:block;">No log tracks found</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div> 
            </div> 
        </main>
    </div>

    <script src="assets/js/app.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
            if (toggleSidebarBtn) {
                toggleSidebarBtn.addEventListener('click', function() {
                    const isMinimized = document.documentElement.classList.toggle('sidebar-minimized');
                    localStorage.setItem('sidebarMinimized', isMinimized);
                });
            }

            // ADDED: Added functional change listener targeting metric filtering actions
            const periodSelector = document.getElementById('analyticsPeriodSelector');
            if (periodSelector) {
                periodSelector.addEventListener('change', function() {
                    const selectedMonths = this.value;
                    console.log("Filtering attendance timeline view to the last " + selectedMonths + " months.");
                    // Dynamic analytical URL updates or API requests can follow here
                });
            }

            async function refreshDashboardStats() {
                try {
                    const response = await KiwiApp.request('dashboard_stats', {}, 'GET');
                    if (response.status !== 'success') return;
                    KiwiApp.updateMetricCards(response.data);

                    const list = document.getElementById('activity-log-list');
                    if (list && Array.isArray(response.activity)) {
                        if (!response.activity.length) {
                            list.innerHTML = '<span class="activity-action" style="text-align:center; padding:20px; display:block;">No log tracks found</span>';
                            return;
                        }
                        list.innerHTML = response.activity.map((log) => `
                            <div class="activity-item">
                                <div class="activity-user-info">
                                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(log.name)}&background=cbd5e1&color=334155" class="activity-avatar" alt="">
                                    <div class="activity-details">
                                        <span class="activity-username">${KiwiApp.escapeHtml(log.name)}</span>
                                        <span class="activity-action">${KiwiApp.escapeHtml(log.status)}</span>
                                    </div>
                                </div>
                                <span class="activity-time">${log.time_in ? KiwiApp.formatTime(log.time_in) : '--:--'}</span>
                            </div>
                        `).join('');
                    }
                } catch (error) {
                    console.error(error);
                }
            }

            refreshDashboardStats();
            setInterval(refreshDashboardStats, 30000);

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