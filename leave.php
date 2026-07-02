<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees Leave List - Kiwi Digital</title>
    
    <!-- Local Bootstrap File Link -->
    <link rel="stylesheet" href="bootstrap.min.css">

    <!-- Font Awesome 6 CDN Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Inline page flashing guard matching index.php -->
    <script>try{if(localStorage.getItem('sidebarMinimized')==='true'){document.documentElement.classList.add('sidebar-minimized');}}catch(e){}</script>

    <style>
        /* Google Font matching clean modern UI style */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
            padding: 20px;
            height: 100vh;
            overflow: hidden;
        }

        /* Layout Grid System (EXACTLY MATCHED TO INDEX.PHP) */
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

        /* ==================================================================
           SIDEBAR STYLES (MATCHED EXACTLY TO INDEX.PHP)
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

        /* ==========================================================================
           MAIN LEAVE LIST PANEL BLOCK
           ========================================================================== */
        .leave-list-panel {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 24px;
            width: 100%;
            height: 100%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f3f5;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .panel-header-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-shrink: 0;
        }

        .panel-title-side {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .purple-indicator {
            width: 6px;
            height: 24px;
            background-color: #6366f1;
            border-radius: 4px;
        }

        .panel-heading-text {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
        }

        .panel-actions-side {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .view-toggle-btn {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
        }

        .view-toggle-btn.active {
            background-color: #6366f1;
            color: #ffffff;
            border-color: #6366f1;
        }

        .btn-approve-all {
            background-color: #6366f1;
            color: #ffffff;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            height: 38px;
            display: flex;
            align-items: center;
        }

        .btn-approve-all:hover {
            background-color: #4f46e5;
        }

        .table-responsive-wrapper {
            overflow-y: auto;
            width: 100%;
            flex-grow: 1;
        }

        .leave-data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .leave-data-table th {
            color: #94a3b8;
            font-weight: 500;
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            background-color: #f8fafc;
            font-size: 13px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .leave-data-table th i {
            font-size: 11px;
            margin-left: 4px;
            color: #cbd5e1;
        }

        .leave-data-table td {
            padding: 16px;
            color: #334155;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .emp-profile-block {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            color: #1e293b;
        }

        .emp-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            background-color: #e2e8f0;
        }

        .designation-text {
            color: #475569;
            font-weight: 500;
        }

        .leave-type-text {
            color: #475569;
            font-weight: 500;
        }

        .reason-truncate-cell {
            color: #64748b;
            max-width: 220px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .date-log-text {
            color: #475569;
            white-space: nowrap;
        }

        .days-count-badge {
            font-weight: 500;
            color: #1e293b;
            padding-left: 8px;
        }

        .action-flex-box {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-action-approve {
            background-color: #6366f1;
            color: #ffffff;
            border: none;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
        }

        .btn-action-reject-cross {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            border: 1px solid #fee2e2;
            background-color: #fff5f5;
            color: #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            cursor: pointer;
        }

        .status-outcome-badge {
            display: inline-flex;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            min-width: 85px;
            justify-content: center;
        }

        .status-outcome-badge.approved {
            background-color: #f0fdf4;
            color: #16a34a;
        }

        .status-outcome-badge.rejected {
            background-color: #fef2f2;
            color: #ef4444;
        }
    </style>
</head>
<body>

    <div class="app-container">
        
        <?php
        $activePage = 'leave';
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

        <!-- EXACT INDEX SIDEBAR EMBED -->
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

        <!-- MAIN WINDOW DISPLAY VIEW -->
        <main class="main-content">
            <div class="leave-list-panel">
                <div class="panel-header-strip">
                    <div class="panel-title-side">
                        <div class="purple-indicator"></div>
                        <h1 class="panel-heading-text">Employees Leave List</h1>
                    </div>
                    <div class="panel-actions-side">
                        <button class="view-toggle-btn active"><i class="fa-solid fa-list"></i></button>
                        <button class="view-toggle-btn"><i class="fa-solid fa-grip"></i></button>
                        <button class="btn-approve-all">Approve All</button>
                    </div>
                </div>

                <div class="table-responsive-wrapper">
                    <table class="leave-data-table">
                        <thead>
                            <tr>
                                <th>Employee <i class="fa-solid fa-sort"></i></th>
                                <th>Designation <i class="fa-solid fa-sort"></i></th>
                                <th>Leave Type <i class="fa-solid fa-sort"></i></th>
                                <th>Reason <i class="fa-solid fa-sort"></i></th>
                                <th>Start Date <i class="fa-solid fa-sort"></i></th>
                                <th>End Date <i class="fa-solid fa-sort"></i></th>
                                <th>Days <i class="fa-solid fa-sort"></i></th>
                                <th style="text-align: center;">Action <i class="fa-solid fa-sort"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Row 1: Pending -->
                            <tr>
                                <td>
                                    <div class="emp-profile-block">
                                        <div class="emp-avatar"></div>
                                        <span>Samantha Paul</span>
                                    </div>
                                </td>
                                <td class="designation-text">Sr. UI Developer</td>
                                <td class="leave-type-text">Sick Leave</td>
                                <td class="reason-truncate-cell">To support my spouse and care...</td>
                                <td class="date-log-text">July 10,2025</td>
                                <td class="date-log-text">July 12,2025</td>
                                <td class="days-count-badge">2</td>
                                <td>
                                    <div class="action-flex-box justify-content-center">
                                        <button class="btn-action-approve"><i class="fa-solid fa-check"></i> Approve</button>
                                        <button class="btn-action-reject-cross"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Row 2: Approved -->
                            <tr>
                                <td>
                                    <div class="emp-profile-block">
                                        <div class="emp-avatar"></div>
                                        <span>Gray Noal</span>
                                    </div>
                                </td>
                                <td class="designation-text">React Developer</td>
                                <td class="leave-type-text">Casual Leave</td>
                                <td class="reason-truncate-cell">Attending a family function out...</td>
                                <td class="date-log-text">July 14,2025</td>
                                <td class="date-log-text">July 30,2025</td>
                                <td class="days-count-badge">15</td>
                                <td style="text-align: center;">
                                    <span class="status-outcome-badge approved">Approved</span>
                                </td>
                            </tr>
                            <!-- Row 3: Rejected -->
                            <tr>
                                <td>
                                    <div class="emp-profile-block">
                                        <div class="emp-avatar"></div>
                                        <span>Cameron Williamson</span>
                                    </div>
                                </td>
                                <td class="designation-text">Team Lead</td>
                                <td class="leave-type-text">Personal Leave</td>
                                <td class="reason-truncate-cell">Need time off to manage some...</td>
                                <td class="date-log-text">July 06,2025</td>
                                <td class="date-log-text">July 16,2025</td>
                                <td class="days-count-badge">10</td>
                                <td style="text-align: center;">
                                    <span class="status-outcome-badge rejected">Rejected</span>
                                </td>
                            </tr>
                            <!-- Row 4: Rejected -->
                            <tr>
                                <td>
                                    <div class="emp-profile-block">
                                        <div class="emp-avatar"></div>
                                        <span>Ralph Edwards</span>
                                    </div>
                                </td>
                                <td class="designation-text">Full Stack Developer</td>
                                <td class="leave-type-text">Maternity Leave</td>
                                <td class="reason-truncate-cell">Starting maternity leave as per...</td>
                                <td class="date-log-text">July 02,2025</td>
                                <td class="date-log-text">July 06,2025</td>
                                <td class="days-count-badge">4</td>
                                <td style="text-align: center;">
                                    <span class="status-outcome-badge rejected">Rejected</span>
                                </td>
                            </tr>
                            <!-- Row 5: Approved -->
                            <tr>
                                <td>
                                    <div class="emp-profile-block">
                                        <div class="emp-avatar"></div>
                                        <span>Annette Black</span>
                                    </div>
                                </td>
                                <td class="designation-text">Jr. Java Developer</td>
                                <td class="leave-type-text">Gifted Leave</td>
                                <td class="reason-truncate-cell">Team leave gifted by managem...</td>
                                <td class="date-log-text">August 26,2025</td>
                                <td class="date-log-text">August 30,2025</td>
                                <td class="days-count-badge">4</td>
                                <td style="text-align: center;">
                                    <span class="status-outcome-badge approved">Approved</span>
                                </td>
                            </tr>
                            <!-- Row 6: Pending -->
                            <tr>
                                <td>
                                    <div class="emp-profile-block">
                                        <div class="emp-avatar"></div>
                                        <span>Marvin McKinney</span>
                                    </div>
                                </td>
                                <td class="designation-text">Sr. UI Developer</td>
                                <td class="leave-type-text">Sick Leave</td>
                                <td class="reason-truncate-cell">Welcoming our second child an...</td>
                                <td class="date-log-text">August 05,2025</td>
                                <td class="date-log-text">August 06,2025</td>
                                <td class="days-count-badge">1</td>
                                <td>
                                    <div class="action-flex-box justify-content-center">
                                        <button class="btn-action-approve"><i class="fa-solid fa-check"></i> Approve</button>
                                        <button class="btn-action-reject-cross"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Row 7: Pending -->
                            <tr>
                                <td>
                                    <div class="emp-profile-block">
                                        <div class="emp-avatar"></div>
                                        <span>Theresa Webb</span>
                                    </div>
                                </td>
                                <td class="designation-text">React Developer</td>
                                <td class="leave-type-text">Casual Leave</td>
                                <td class="reason-truncate-cell">Traveling for a friend's wedding.</td>
                                <td class="date-log-text">August 14,2025</td>
                                <td class="date-log-text">August 16,2025</td>
                                <td class="days-count-badge">2</td>
                                <td>
                                    <div class="action-flex-box justify-content-center">
                                        <button class="btn-action-approve"><i class="fa-solid fa-check"></i> Approve</button>
                                        <button class="btn-action-reject-cross"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Row 8: Approved -->
                            <tr>
                                <td>
                                    <div class="emp-profile-block">
                                        <div class="emp-avatar"></div>
                                        <span>Arlene McCoy</span>
                                    </div>
                                </td>
                                <td class="designation-text">Business Analyst</td>
                                <td class="leave-type-text">Personal Leave</td>
                                <td class="reason-truncate-cell">Taking a day off to accompany...</td>
                                <td class="date-log-text">August 02,2025</td>
                                <td class="date-log-text">August 12,2025</td>
                                <td class="days-count-badge">10</td>
                                <td style="text-align: center;">
                                    <span class="status-outcome-badge approved">Approved</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Toggle Script handler matching index.php -->
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