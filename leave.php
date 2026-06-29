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
           SIDEBAR STYLES (MATCHED EXACTLY TO THE SYSTEM STANDARD)
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

        /* Highlighted style active state on Leave Management */
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

        /* ==========================================================================
           MAIN LEAVE LIST PANEL BLOCK (MATCHED TO Screenshot 2026-06-25 151121.png)
           ========================================================================== */
        .main-content {
            flex-grow: 1;
            padding-top: 10px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .leave-list-panel {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 24px;
            width: 100%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f3f5;
        }

        /* Top Bar Header Area inside card container */
        .panel-header-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .panel-title-side {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Purple layout visual block from screenshot */
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

        /* View Toggle Layout Component Buttons */
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

        /* ==========================================================================
           EMPLOYEES LEAVE LIST SYSTEM DATA TABLE
           ========================================================================== */
        .table-responsive-wrapper {
            overflow-x: auto;
            width: 100%;
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

        /* Profile metadata elements */
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

        /* ==========================================================================
           SPECIFIC INTERACTION BADGES & PILLS
           ========================================================================== */
        .action-flex-box {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Pending Option: Approve pill triggers */
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

        /* Permanent Badge Outcomes */
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
                <li class="nav-item">
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
                <li class="nav-item active">
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

        <!-- MAIN WINDOW DISPLAY VIEW -->
        <main class="main-content">
            
            <!-- CARD CONTAINER FRAME -->
            <div class="leave-list-panel">
                
                <!-- TOP HEADER CONTROL ACTIONS BAR -->
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

                <!-- MAIN SYSTEM DATA TABLE -->
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
                                style="text-align: center;"
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

</body>
</html>