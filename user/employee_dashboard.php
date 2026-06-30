<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Leave Applications - Kiwi Digital</title>
    
    <!-- Bootstrap Link -->
    <link rel="stylesheet" href="bootstrap.min.css">

    <!-- Font Awesome 6 CDN Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #fcfcfd;
            font-family: 'Inter', sans-serif;
            height: 100vh;
            overflow: hidden;
        }

        .app-container {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        /* ==========================================================================
           SIDEBAR STYLES
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
            margin-bottom: 36px;
            padding: 0 16px;
        }

        .logo-img {
            width: 100px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .nav-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .nav-item {
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            text-decoration: none;
            padding: 12px 24px;
            color: #666666; 
            font-size: 15px;
            font-weight: 500;
        }

        .icon {
            font-size: 18px;
            width: 28px;
            margin-right: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #666666;
        }

        .nav-item.active > a {
            background-color: #ffffff;
            color: #333333;
            border-top-left-radius: 6px;
            border-bottom-left-radius: 6px;
            margin-left: 10px;
            font-weight: 600;
        }

        .nav-item.active > a .icon {
            color: #333333;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 0 24px;
        }

        .divider-dots {
            color: #999999;
            font-size: 14px;
            margin-bottom: 12px;
            padding-left: 4px;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #555555;
            font-size: 13px;
            font-weight: 500;
        }

        /* ==========================================================================
           MAIN CONTENT WORKSPACE STYLES
           ========================================================================== */
        .main-content {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .page-header-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-header-icon {
            font-size: 22px;
            color: #000000;
        }

        .page-title-text {
            font-size: 20px;
            font-weight: 700;
            color: #000000;
        }

        /* ==========================================================================
           LEAVE CARD GRID STRUCTURE (MATCHED TO image_06359a.png)
           ========================================================================== */
        .leave-type-row-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            width: 100%;
        }

        .credit-allocation-card {
            background-color: #4b5563; /* Alternating modern container gray bases */
            border-radius: 20px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            position: relative;
        }

        /* Card color variation alignments */
        .credit-allocation-card.dark-slate { background-color: #555555; }
        .credit-allocation-card.medium-gray { background-color: #777777; }
        .credit-allocation-card.light-silver { background-color: #dbdbdb; color: #333333; }

        .circle-score-wrapper {
            background-color: #ffffff;
            width: 84px;
            height: 84px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            color: #2563eb; /* Primary indicator link tone */
            flex-shrink: 0;
        }

        .credit-allocation-card.light-silver .circle-score-wrapper {
            color: #1e3a8a;
            border: 1px solid #cbd5e1;
        }

        .card-details-side {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .card-details-side h3 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        /* Rounded Apply Button matching exact styling dimensions */
        .btn-apply-credit-pill {
            background-color: #febb12; /* Golden yellow accent keyword highlight */
            color: #111111;
            font-size: 12px;
            font-weight: 700;
            border: none;
            padding: 6px 32px;
            border-radius: 20px;
            cursor: pointer;
            text-align: center;
            transition: opacity 0.2s;
        }

        .btn-apply-credit-pill:hover {
            opacity: 0.9;
        }

        /* ==========================================================================
           LEAVE HISTORY TABLES SECTION
           ========================================================================== */
        .history-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .history-section-header h2 {
            font-size: 18px;
            font-weight: 700;
            color: #000000;
        }

        .control-actions-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .icon-filter-trigger {
            font-size: 16px;
            color: #64748b;
            cursor: pointer;
        }

        .btn-export-dropdown {
            background-color: #16a34a; /* Classic functional utility button */
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Custom Structured Table from Reference image_06359a.png */
        .history-data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px; /* Dynamic padding split layout space rows */
            margin-top: -8px;
        }

        .history-data-table th {
            font-size: 13px;
            font-weight: 700;
            color: #000000;
            padding: 12px 16px;
            text-align: left;
        }

        .history-data-table tbody tr {
            background-color: #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
            transition: transform 0.15s;
        }

        /* Soft zebra tint row alternative */
        .history-data-table tbody tr:nth-child(even) {
            background-color: #f1f5f9;
        }

        .history-data-table td {
            padding: 14px 16px;
            font-size: 13px;
            color: #334155;
            vertical-align: middle;
        }

        .history-data-table tbody tr td:first-child {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
            font-weight: 500;
        }

        .history-data-table tbody tr td:last-child {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        /* Action item selector styles */
        .btn-table-action-menu {
            background-color: #1e3a8a;
            color: #ffffff;
            font-size: 12px;
            font-weight: 500;
            padding: 6px 14px;
            border-radius: 4px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>

    <div class="app-container">
        
        <!-- SIDEBAR COMPONENT -->
        <nav class="sidebar">
            <div class="logo-container">
                <img src="../img/kiwi.png" alt="Kiwi Digital Logo" class="logo-img">
            </div>

            <ul class="nav-links">
                <li class="nav-item">
                    <a href="employee_dashboard.php">
                        <i class="fa-solid fa-table-cells-large icon"></i>
                        <span>Dashboard</span>
                    </a> 
                </li>
                <li class="nav-item active">
                    <a href="leave_employee.php">
                        <i class="fa-solid fa-user-plus icon"></i>
                        <span>Apply for Leave</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="my_timesheet.php">
                        <i class="fa-solid fa-business-time icon"></i>
                        <span>My Timesheet</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <div class="divider-dots">...</div>
                <a href="#" class="logout-btn">
                    <i class="fa-solid fa-right-from-bracket icon"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>

        <!-- MAIN DASHBOARD CONTENT AREA -->
        <main class="main-content">
            
            <!-- APPLICATION TITLE -->
            <div class="page-header-container">
                <i class="fa-solid fa-book open page-header-icon fa-book"></i>
                <h1 class="page-title-text">Leave Application</h1>
            </div>

            <!-- TOP CARDS: CREDITS METRICS (EXACT PLACEMENT FROM image_06359a.png) -->
            <div class="leave-type-row-grid">
                
                <!-- Card 1: Annual Leave -->
                <div class="credit-allocation-card dark-slate">
                    <div class="circle-score-wrapper">60</div>
                    <div class="card-details-side">
                        <h3>Annual Leave</h3>
                        <button class="btn-apply-credit-pill">Apply</button>
                    </div>
                </div>

                <!-- Card 2: Sick Leave -->
                <div class="credit-allocation-card medium-gray">
                    <div class="circle-score-wrapper">20</div>
                    <div class="card-details-side">
                        <h3>Sick Leave</h3>
                        <button class="btn-apply-credit-pill">Apply</button>
                    </div>
                </div>

                <!-- Card 3: Maternity Leave -->
                <div class="credit-allocation-card light-silver">
                    <div class="circle-score-wrapper">60</div>
                    <div class="card-details-side">
                        <h3 style="color: #64748b;">Maternity Leave</h3>
                        <button class="btn-apply-credit-pill">Apply</button>
                    </div>
                </div>

            </div>

            <!-- HISTORY TABLE BLOCK CONTROLLER -->
            <div class="history-section-header">
                <h2>Leave History</h2>
                <div class="control-actions-right">
                    <i class="fa-solid fa-filter icon-filter-trigger"></i>
                    <button class="btn-export-dropdown">Export <i class="fa-solid fa-chevron-down"></i></button>
                </div>
            </div>

            <!-- HISTORY LOG TABLE LIST -->
            <div class="table-responsive" style="overflow: visible;">
                <table class="history-data-table">
                    <thead>
                        <tr>
                            <th>Name(s)</th>
                            <th>Duration(s)</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Type</th>
                            <th>Reason(s)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Abenezer kebede</td>
                            <td>5</td>
                            <td>22/04/2022</td>
                            <td>28/04/2022</td>
                            <td>Sick</td>
                            <td>Personal</td>
                            <td><button class="btn-table-action-menu">Actions <i class="fa-solid fa-chevron-down"></i></button></td>
                        </tr>
                        <tr>
                            <td>Abenezer kebede</td>
                            <td>7</td>
                            <td>22/04/2022</td>
                            <td>30/04/2022</td>
                            <td>Exam</td>
                            <td>Examination</td>
                            <td><button class="btn-table-action-menu">Actions <i class="fa-solid fa-chevron-down"></i></button></td>
                        </tr>
                        <tr>
                            <td>Abenezer kebede</td>
                            <td>120</td>
                            <td>22/04/2022</td>
                            <td>28/06/2022</td>
                            <td>Maternity</td>
                            <td>Child Care</td>
                            <td><button class="btn-table-action-menu">Actions <i class="fa-solid fa-chevron-down"></i></button></td>
                        </tr>
                        <tr>
                            <td>Abenezer kebede</td>
                            <td>5</td>
                            <td>22/04/2022</td>
                            <td>28/04/2022</td>
                            <td>Sick</td>
                            <td>Personal</td>
                            <td><button class="btn-table-action-menu">Actions <i class="fa-solid fa-chevron-down"></i></button></td>
                        </tr>
                        <tr>
                            <td>Abenezer kebede</td>
                            <td>5</td>
                            <td>22/04/2022</td>
                            <td>28/04/2022</td>
                            <td>Sick</td>
                            <td>Personal</td>
                            <td><button class="btn-table-action-menu">Actions <i class="fa-solid fa-chevron-down"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </main>
    </div>

</body>
</html>