<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timekeeping - Kiwi Digital</title>
    
    <!-- Local Bootstrap File Link -->
    <link rel="stylesheet" href="bootstrap.min.css">

    <!-- Font Awesome 6 CDN Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

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

        /* Main Application Layout Wrapper */
        .app-container {
            display: flex;
            gap: 24px;
            height: calc(100vh - 40px);
            width: 100%;
        }

        /* ==========================================================================
           SIDEBAR STYLES (MATCHED EXACTLY TO PREVENT JUMPING)
           ========================================================================== */
        .sidebar {
            width: 280px;
            height: 100%;
            background-color: #dbdbdb; 
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            padding: 30px 0;
            flex-shrink: 0;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 40px;
            padding: 0 20px;
        }

        .logo-img {
            width: 65px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .nav-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 4px;
            width: 100%;
        }

        .nav-item {
            width: 100%;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            text-decoration: none;
            padding: 14px 0 14px 35px;
            color: #555555; 
            font-size: 16px;
            font-weight: 600;
            transition: all 0.15s ease;
        }

        .icon {
            font-size: 20px;
            width: 30px;
            margin-right: 14px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #555555;
        }

        /* Active styling applied to Timekeeping */
        .nav-item.active a {
            background-color: #ffffff;
            color: #444444;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
            margin-right: 25px; 
            padding-left: 35px; 
        }

        .nav-item.active .icon {
            color: #444444;
        }

        .nav-item:not(.active) a:hover {
            color: #222222;
            padding-left: 38px;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-left: 35px;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #555555;
            font-size: 13px;
            font-weight: 600;
        }

        .logout-btn:hover { color: #ff3333; }
        .logout-btn .icon { font-size: 20px; margin-right: 10px; color: #555555; }

        /* ==========================================================================
           MAIN CONTENT CONTAINER & METRICS ROW (MATCHED TO image_154e86.png)
           ========================================================================== */
        .main-content {
            flex-grow: 1;
            padding-top: 10px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
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
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f3f5;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 160px;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
        }

        .card-title {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
        }

        .card-value {
            font-size: 32px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 12px;
            line-height: 1;
        }

        .card-footer {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #6b7280;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

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

        .panel-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .panel-title-area {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .panel-title-area i {
            color: #2563eb;
            font-size: 18px;
        }

        .panel-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
        }

        .panel-menu-dot {
            color: #94a3b8;
            cursor: pointer;
        }

        /* Control Filter Actions Strip */
        .table-controls-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 16px;
        }

        .filter-group-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

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

        .action-group-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .table-search-box {
            position: relative;
            width: 260px;
        }

        .table-search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
        }

        .table-search-box input {
            width: 100%;
            padding: 10px 14px 10px 40px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 14px;
            outline: none;
            color: #334155;
        }

        .btn-download-data {
            background-color: #2563eb;
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
           ATTENDANCE DATA TABLE (MATCHED TO image_154e86.png)
           ========================================================================== */
        .custom-table-wrapper {
            overflow-x: auto;
            width: 100%;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .attendance-table th {
            color: #94a3b8;
            font-weight: 600;
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }

        .attendance-table th i {
            font-size: 11px;
            margin-left: 4px;
            color: #cbd5e1;
        }

        .attendance-table td {
            padding: 16px;
            color: #1e293b;
            vertical-align: middle;
            border-bottom: 1px solid #f8fafc;
        }

        .id-cell {
            color: #94a3b8 !important;
        }

        .profile-meta-cell {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #1e293b;
        }

        .avatar-image {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            background-color: #e2e8f0;
        }

        .dept-cell {
            color: #64748b;
        }

        .time-cell {
            font-weight: 600;
            color: #0f172a;
        }

        .empty-log {
            color: #94a3b8;
            font-weight: 500;
        }

        /* Match specific status badges */
        .status-pill {
            display: inline-flex;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pill.present {
            background-color: #f0fdf4;
            color: #16a34a;
        }

        .status-pill.late {
            background-color: #fff7ed;
            color: #ea580c;
        }

        .status-pill.absent {
            background-color: #fef2f2;
            color: #ef4444;
        }

        .action-dot-menu {
            color: #cbd5e1;
            cursor: pointer;
            font-size: 16px;
            text-align: right;
            width: 30px;
        }
    </style>
</head>
<body>

    <div class="app-container">
        
        <!-- SIDEBAR COMPONENT -->
        <nav class="sidebar">
            <div class="logo-container">
                <img src="img/kiwi.png" alt="Logo" class="logo-img">
            </div>

            <ul class="nav-links">
                <li class="nav-item">
                    <a href="index.php">
                        <i class="fa-solid fa-table-cells-large icon"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="employee.php">
                        <i class="fa-solid fa-users-rectangle icon"></i>
                        <span>Employee</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="biometric.php">
                        <i class="fa-solid fa-fingerprint icon"></i>
                        <span>Biometric Enrollment</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="timekeeping.php">
                        <i class="fa-solid fa-clipboard-user icon"></i>
                        <span>Timekeeping</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="shift.php">
                        <i class="fa-solid fa-right-left icon"></i>
                        <span>Shift Configuration</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="leave.php">
                        <i class="fa-solid fa-user-gear icon"></i>
                        <span>Leave Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="internship.php">
                        <i class="fa-solid fa-cubes icon"></i>
                        <span>Internship Registry</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="audit.php">
                        <i class="fa-solid fa-square-poll-horizontal icon"></i>
                        <span>System Audit</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <a href="#" class="logout-btn">
                    <i class="fa-solid fa-right-from-bracket icon"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>

        <!-- MAIN VIEW WRAPPER -->
        <main class="main-content">
            
            <!-- METRIC CARDS OVERVIEW -->
            <div class="metrics-straight-row">
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-circle-check" style="color: #2563eb;"></i>
                        <span class="card-title">Total Employees Present</span>
                    </div>
                    <div class="card-value">120</div>
                    <div class="card-footer">
                        <span class="badge positive"><i class="fa-solid fa-arrow-up"></i> 5%</span>
                        <span>from yesterday</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-user-clock" style="color: #2563eb;"></i>
                        <span class="card-title">Late Arrivals Today</span>
                    </div>
                    <div class="card-value">15</div>
                    <div class="card-footer">
                        <span class="badge positive"><i class="fa-solid fa-arrow-up"></i> 3 people</span>
                        <span>compared to last week</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-user-minus" style="color: #ef4444;"></i>
                        <span class="card-title">Employees Absent</span>
                    </div>
                    <div class="card-value">8</div>
                    <div class="card-footer">
                        <span class="badge negative"><i class="fa-solid fa-arrow-down"></i> 2 people</span>
                        <span>compared to last Monday</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-clock" style="color: #2563eb;"></i>
                        <span class="card-title">Average Check-In Time</span>
                    </div>
                    <div class="card-value">08:25 AM</div>
                    <div class="card-footer">
                        <span>Consistent with last week</span>
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
                        <select class="select-filter-dropdown">
                            <option>Select department</option>
                        </select>
                        <input type="text" class="date-filter-picker" value="10 Feb 2025" readonly>
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
                            <!-- Row 1 -->
                            <tr>
                                <td class="id-cell">#E120</td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <span>John Doe</span>
                                    </div>
                                </td>
                                <td class="dept-cell">Information Technology</td>
                                <td class="time-cell">08:15 AM</td>
                                <td class="time-cell">05:00 PM</td>
                                <td><span class="status-pill present">Present</span></td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                            <!-- Row 2 -->
                            <tr>
                                <td class="id-cell">#E119</td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <span>Sarah Lee</span>
                                    </div>
                                </td>
                                <td class="dept-cell">Human Resource</td>
                                <td class="time-cell">09:05 AM</td>
                                <td class="time-cell">05:00 PM</td>
                                <td><span class="status-pill late">Late</span></td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                            <!-- Row 3 -->
                            <tr>
                                <td class="id-cell">#E118</td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <span>Michael Tan</span>
                                    </div>
                                </td>
                                <td class="dept-cell">Marketing</td>
                                <td class="time-cell">08:50 AM</td>
                                <td class="time-cell">05:00 PM</td>
                                <td><span class="status-pill present">Present</span></td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                            <!-- Row 4 -->
                            <tr>
                                <td class="id-cell">#E117</td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <span>Alice Morgan</span>
                                    </div>
                                </td>
                                <td class="dept-cell">Finance</td>
                                <td class="time-cell">08:45 AM</td>
                                <td class="time-cell">05:00 PM</td>
                                <td><span class="status-pill present">Present</span></td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                            <!-- Row 5 -->
                            <tr>
                                <td class="id-cell">#E116</td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <span>James Carter</span>
                                    </div>
                                </td>
                                <td class="dept-cell">Information Technology</td>
                                <td class="empty-log">-</td>
                                <td class="empty-log">-</td>
                                <td><span class="status-pill absent">Absent</span></td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                            <!-- Row 6 -->
                            <tr>
                                <td class="id-cell">#E115</td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <span>Emma Brown</span>
                                    </div>
                                </td>
                                <td class="dept-cell">Marketing</td>
                                <td class="time-cell">08:30 AM</td>
                                <td class="time-cell">05:05 PM</td>
                                <td><span class="status-pill present">Present</span></td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                            <!-- Row 7 -->
                            <tr>
                                <td class="id-cell">#E114</td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <span>David Bowen</span>
                                    </div>
                                </td>
                                <td class="dept-cell">Human Resource</td>
                                <td class="empty-log">-</td>
                                <td class="empty-log">-</td>
                                <td><span class="status-pill absent">Absent</span></td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                            <!-- Row 8 -->
                            <tr>
                                <td class="id-cell">#E113</td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <span>Rachel Amanda</span>
                                    </div>
                                </td>
                                <td class="dept-cell">Marketing</td>
                                <td class="time-cell">08:45 AM</td>
                                <td class="time-cell">05:10 PM</td>
                                <td><span class="status-pill present">Present</span></td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                            <!-- Row 9 -->
                            <tr>
                                <td class="id-cell">#E112</td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <span>Chris Johnson</span>
                                    </div>
                                </td>
                                <td class="dept-cell">Operations</td>
                                <td class="time-cell">09:00 AM</td>
                                <td class="time-cell">05:30 PM</td>
                                <td><span class="status-pill present">Present</span></td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </main>

    </div>

</body>
</html><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timekeeping - Kiwi Digital</title>
    
    <!-- Local Bootstrap File Link -->
    <link rel="stylesheet" href="bootstrap.min.css">

    <!-- Font Awesome 6 CDN Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

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

        /* Main Application Layout Wrapper */
        .app-container {
            display: flex;
            gap: 24px;
            height: calc(100vh - 40px);
            width: 100%;
        }

        /* ==========================================================================
           SIDEBAR STYLES (MATCHED EXACTLY TO PREVENT JUMPING)
           ========================================================================== */
        .sidebar {
            width: 280px;
            height: 100%;
            background-color: #dbdbdb; 
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            padding: 30px 0;
            flex-shrink: 0;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 40px;
            padding: 0 20px;
        }

        .logo-img {
            width: 65px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .nav-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 4px;
            width: 100%;
        }

        .nav-item {
            width: 100%;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            text-decoration: none;
            padding: 14px 0 14px 35px;
            color: #555555; 
            font-size: 16px;
            font-weight: 600;
            transition: all 0.15s ease;
        }

        .icon {
            font-size: 20px;
            width: 30px;
            margin-right: 14px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #555555;
        }

        /* Active styling applied to Timekeeping */
        .nav-item.active a {
            background-color: #ffffff;
            color: #444444;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
            margin-right: 25px; 
            padding-left: 35px; 
        }

        .nav-item.active .icon {
            color: #444444;
        }

        .nav-item:not(.active) a:hover {
            color: #222222;
            padding-left: 38px;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-left: 35px;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #555555;
            font-size: 13px;
            font-weight: 600;
        }

        .logout-btn:hover { color: #ff3333; }
        .logout-btn .icon { font-size: 20px; margin-right: 10px; color: #555555; }

        /* ==========================================================================
           MAIN CONTENT CONTAINER & METRICS ROW (MATCHED TO image_154e86.png)
           ========================================================================== */
        .main-content {
            flex-grow: 1;
            padding-top: 10px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
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
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f3f5;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 160px;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
        }

        .card-title {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
        }

        .card-value {
            font-size: 32px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 12px;
            line-height: 1;
        }

        .card-footer {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #6b7280;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

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

        .panel-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .panel-title-area {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .panel-title-area i {
            color: #2563eb;
            font-size: 18px;
        }

        .panel-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
        }

        .panel-menu-dot {
            color: #94a3b8;
            cursor: pointer;
        }

        /* Control Filter Actions Strip */
        .table-controls-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 16px;
        }

        .filter-group-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

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

        .action-group-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .table-search-box {
            position: relative;
            width: 260px;
        }

        .table-search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
        }

        .table-search-box input {
            width: 100%;
            padding: 10px 14px 10px 40px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 14px;
            outline: none;
            color: #334155;
        }

        .btn-download-data {
            background-color: #2563eb;
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
           ATTENDANCE DATA TABLE (MATCHED TO image_154e86.png)
           ========================================================================== */
        .custom-table-wrapper {
            overflow-x: auto;
            width: 100%;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .attendance-table th {
            color: #94a3b8;
            font-weight: 600;
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }

        .attendance-table th i {
            font-size: 11px;
            margin-left: 4px;
            color: #cbd5e1;
        }

        .attendance-table td {
            padding: 16px;
            color: #1e293b;
            vertical-align: middle;
            border-bottom: 1px solid #f8fafc;
        }

        .id-cell {
            color: #94a3b8 !important;
        }

        .profile-meta-cell {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #1e293b;
        }

        .avatar-image {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            background-color: #e2e8f0;
        }

        .dept-cell {
            color: #64748b;
        }

        .time-cell {
            font-weight: 600;
            color: #0f172a;
        }

        .empty-log {
            color: #94a3b8;
            font-weight: 500;
        }

        /* Match specific status badges */
        .status-pill {
            display: inline-flex;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pill.present {
            background-color: #f0fdf4;
            color: #16a34a;
        }

        .status-pill.late {
            background-color: #fff7ed;
            color: #ea580c;
        }

        .status-pill.absent {
            background-color: #fef2f2;
            color: #ef4444;
        }

        .action-dot-menu {
            color: #cbd5e1;
            cursor: pointer;
            font-size: 16px;
            text-align: right;
            width: 30px;
        }
    </style>
</head>
<body>

    <div class="app-container">
        
        <!-- SIDEBAR COMPONENT -->
        <nav class="sidebar">
            <div class="logo-container">
                <img src="img/kiwi.png" alt="Logo" class="logo-img">
            </div>

            <ul class="nav-links">
                <li class="nav-item">
                    <a href="index.php">
                        <i class="fa-solid fa-table-cells-large icon"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="employee.php">
                        <i class="fa-solid fa-users-rectangle icon"></i>
                        <span>Employee</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="biometric.php">
                        <i class="fa-solid fa-fingerprint icon"></i>
                        <span>Biometric Enrollment</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="timekeeping.php">
                        <i class="fa-solid fa-clipboard-user icon"></i>
                        <span>Timekeeping</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="shift.php">
                        <i class="fa-solid fa-right-left icon"></i>
                        <span>Shift Configuration</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="leave.php">
                        <i class="fa-solid fa-user-gear icon"></i>
                        <span>Leave Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="internship.php">
                        <i class="fa-solid fa-cubes icon"></i>
                        <span>Internship Registry</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="audit.php">
                        <i class="fa-solid fa-square-poll-horizontal icon"></i>
                        <span>System Audit</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <a href="#" class="logout-btn">
                    <i class="fa-solid fa-right-from-bracket icon"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>

        <!-- MAIN VIEW WRAPPER -->
        <main class="main-content">
            
            <!-- METRIC CARDS OVERVIEW -->
            <div class="metrics-straight-row">
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-circle-check" style="color: #2563eb;"></i>
                        <span class="card-title">Total Employees Present</span>
                    </div>
                    <div class="card-value">120</div>
                    <div class="card-footer">
                        <span class="badge positive"><i class="fa-solid fa-arrow-up"></i> 5%</span>
                        <span>from yesterday</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-user-clock" style="color: #2563eb;"></i>
                        <span class="card-title">Late Arrivals Today</span>
                    </div>
                    <div class="card-value">15</div>
                    <div class="card-footer">
                        <span class="badge positive"><i class="fa-solid fa-arrow-up"></i> 3 people</span>
                        <span>compared to last week</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-user-minus" style="color: #ef4444;"></i>
                        <span class="card-title">Employees Absent</span>
                    </div>
                    <div class="card-value">8</div>
                    <div class="card-footer">
                        <span class="badge negative"><i class="fa-solid fa-arrow-down"></i> 2 people</span>
                        <span>compared to last Monday</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-clock" style="color: #2563eb;"></i>
                        <span class="card-title">Average Check-In Time</span>
                    </div>
                    <div class="card-value">08:25 AM</div>
                    <div class="card-footer">
                        <span>Consistent with last week</span>
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
                        <select class="select-filter-dropdown">
                            <option>Select department</option>
                        </select>
                        <input type="text" class="date-filter-picker" value="10 Feb 2025" readonly>
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
                            <!-- Row 1 -->
                            <tr>
                                <td class="id-cell">#E120</td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <span>John Doe</span>
                                    </div>
                                </td>
                                <td class="dept-cell">Information Technology</td>
                                <td class="time-cell">08:15 AM</td>
                                <td class="time-cell">05:00 PM</td>
                                <td><span class="status-pill present">Present</span></td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                            <!-- Row 2 -->
                            <tr>
                                <td class="id-cell">#E119</td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <span>Sarah Lee</span>
                                    </div>
                                </td>
                                <td class="dept-cell">Human Resource</td>
                                <td class="time-cell">09:05 AM</td>
                                <td class="time-cell">05:00 PM</td>
                                <td><span class="status-pill late">Late</span></td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                            <!-- Row 3 -->
                            <tr>
                                <td class="id-cell">#E118</td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <span>Michael Tan</span>
                                    </div>
                                </td>
                                <td class="dept-cell">Marketing</td>
                                <td class="time-cell">08:50 AM</td>
                                <td class="time-cell">05:00 PM</td>
                                <td><span class="status-pill present">Present</span></td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                            <!-- Row 4 -->
                            <tr>
                                <td class="id-cell">#E117</td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <span>Alice Morgan</span>
                                    </div>
                                </td>
                                <td class="dept-cell">Finance</td>
                                <td class="time-cell">08:45 AM</td>
                                <td class="time-cell">05:00 PM</td>
                                <td><span class="status-pill present">Present</span></td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                            <!-- Row 5 -->
                            <tr>
                                <td class="id-cell">#E116</td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <span>James Carter</span>
                                    </div>
                                </td>
                                <td class="dept-cell">Information Technology</td>
                                <td class="empty-log">-</td>
                                <td class="empty-log">-</td>
                                <td><span class="status-pill absent">Absent</span></td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                            <!-- Row 6 -->
                            <tr>
                                <td class="id-cell">#E115</td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <span>Emma Brown</span>
                                    </div>
                                </td>
                                <td class="dept-cell">Marketing</td>
                                <td class="time-cell">08:30 AM</td>
                                <td class="time-cell">05:05 PM</td>
                                <td><span class="status-pill present">Present</span></td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                            <!-- Row 7 -->
                            <tr>
                                <td class="id-cell">#E114</td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <span>David Bowen</span>
                                    </div>
                                </td>
                                <td class="dept-cell">Human Resource</td>
                                <td class="empty-log">-</td>
                                <td class="empty-log">-</td>
                                <td><span class="status-pill absent">Absent</span></td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                            <!-- Row 8 -->
                            <tr>
                                <td class="id-cell">#E113</td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <span>Rachel Amanda</span>
                                    </div>
                                </td>
                                <td class="dept-cell">Marketing</td>
                                <td class="time-cell">08:45 AM</td>
                                <td class="time-cell">05:10 PM</td>
                                <td><span class="status-pill present">Present</span></td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                            <!-- Row 9 -->
                            <tr>
                                <td class="id-cell">#E112</td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <span>Chris Johnson</span>
                                    </div>
                                </td>
                                <td class="dept-cell">Operations</td>
                                <td class="time-cell">09:00 AM</td>
                                <td class="time-cell">05:30 PM</td>
                                <td><span class="status-pill present">Present</span></td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </main>

    </div>

</body>
</html>