<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard - Kiwi Digital</title>
    
    <link rel="stylesheet" href="bootstrap.min.css">

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
           SIDEBAR STYLES (MATCHED EXACTLY TO THE REFERENCE DESIGNS)
           ========================================================================== */
        .sidebar {
            width: 260px;
            height: 100%;
            background-color: #dbdbdb;
            display: flex;
            flex-direction: column;
            padding: 24px 0;
            flex-shrink: 0;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 36px;
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
        }

        /* ==========================================================================
           FIXED ACTIVE NAV ITEM STATE (FLUSH TO THE RIGHT EDGE OF SIDEBAR)
           ========================================================================== */
        .nav-item.active {
            background-color: #ffffff;
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
            margin-left: 12px;
            width: calc(100% - 12px);
        }

        .nav-item.active > a {
            color: #333333;
            font-weight: 600;
            background-color: transparent;
            padding-left: 12px;
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
           MAIN DASHBOARD CONTENT
           ========================================================================== */
        .main-content {
            flex-grow: 1;
            padding: 32px 40px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Top Action / Header Area */
        .top-navbar-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dashboard-main-title {
            font-size: 28px;
            font-weight: 700;
            color: #111111;
        }

        .navbar-search-side {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .search-box-wrapper {
            position: relative;
            width: 240px;
        }

        .search-box-wrapper i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 13px;
        }

        .search-input-field {
            width: 100%;
            padding: 8px 36px 8px 32px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background-color: #f1f5f9;
            font-size: 13px;
            outline: none;
            color: #334155;
        }

        .shortcut-badge {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            color: #94a3b8;
            font-weight: 500;
        }

        .bell-notify-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #ffffff;
            color: #334155;
            position: relative;
            cursor: pointer;
        }

        .bell-notify-btn::after {
            content: '';
            position: absolute;
            top: 8px;
            right: 10px;
            width: 6px;
            height: 6px;
            background-color: #ef4444;
            border-radius: 50%;
        }

        /* 4 Metrics Cards Cluster */
        .summary-quad-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .metric-card-box {
            background: #ffffff;
            border: 1px solid #eef0f3;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        }

        .card-top-meta {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }

        .mini-icon-container {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background-color: #f0f0fe;
            color: #6366f1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .metric-card-box:nth-child(2) .mini-icon-container { background-color: #f0f7ff; color: #3b82f6; }
        .metric-card-box:nth-child(3) .mini-icon-container { background-color: #f0fdf4; color: #16a34a; }
        .metric-card-box:nth-child(4) .mini-icon-container { background-color: #fff5f5; color: #ef4444; }

        .meta-text-labels h3 {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .meta-text-labels p {
            font-size: 11px;
            color: #94a3b8;
        }

        .card-bottom-data {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .metric-large-number {
            font-size: 32px;
            font-weight: 700;
            color: #111111;
            line-height: 1;
            margin-bottom: 6px;
        }

        .comparison-status-badge {
            font-size: 11px;
            font-weight: 600;
            color: #16a34a;
            background-color: #f0fdf4;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-flex;
            gap: 2px;
        }

        .comparison-status-badge.negative { color: #ef4444; background-color: #fff5f5; }
        .comparison-status-badge span { color: #94a3b8; font-weight: 400; margin-left: 2px; }
        .details-arrow-link { font-size: 12px; font-weight: 500; color: #1e293b; text-decoration: none; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: 6px; background-color: #ffffff; }

        /* Analytics Split Grid Layout */
        .analytics-twins-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 16px;
        }

        .analytics-card-frame {
            background-color: #ffffff;
            border: 1px solid #eef0f3;
            border-radius: 12px;
            padding: 20px;
        }

        .analytics-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .analytics-header-labels h4 { font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 2px; }
        .analytics-header-labels p { font-size: 11px; color: #94a3b8; }
        .action-icon-trigger { color: #94a3b8; font-size: 13px; cursor: pointer; }
        .graph-numeric-strip { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
        .graph-large-readout { font-size: 28px; font-weight: 700; color: #111111; }
        .filter-dropdown-pill { background-color: #ffffff; border: 1px solid #e2e8f0; font-size: 11px; font-weight: 500; color: #334155; padding: 4px 10px; border-radius: 6px; outline: none; }

        /* Graph Box Display */
        .mock-area-graph-canvas {
            height: 160px;
            border-left: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            position: relative;
            background: linear-gradient(180deg, rgba(99, 102, 241, 0.06) 0%, rgba(255, 255, 255, 0) 100%);
            margin-top: 10px;
        }

        .mock-svg-line-path { width: 100%; height: 100%; stroke: #6366f1; stroke-width: 2; fill: none; }
        .avg-indicator-horizontal-line { position: absolute; top: 45%; left: 0; right: 0; border-top: 1px dashed #444444; display: flex; align-items: center; }
        .avg-floating-pill { background-color: #111111; color: #ffffff; font-size: 11px; font-weight: 600; padding: 2px 6px; border-radius: 4px; margin-left: 10px; transform: translateY(-50%); }
        .graph-axis-x-row { display: flex; justify-content: space-between; padding: 8px 16px 0 16px; font-size: 11px; color: #94a3b8; }

        /* Columns Setup */
        .twin-chart-legend-row { display: flex; gap: 16px; margin-bottom: 16px; font-size: 12px; font-weight: 500; }
        .legend-indicator { display: flex; align-items: center; gap: 6px; color: #334155; }
        .legend-indicator span { width: 8px; height: 8px; border-radius: 2px; }
        .twin-metrics-readout-row { display: flex; gap: 24px; margin-bottom: 16px; }
        .readout-unit-block h5 { font-size: 11px; color: #94a3b8; margin-bottom: 2px; font-weight: 400; }
        .readout-unit-block p { font-size: 18px; font-weight: 700; color: #111111; }
        .bars-container-flex { height: 140px; display: flex; justify-content: space-around; align-items: flex-end; padding-top: 10px; }
        .bars-twin-cluster { display: flex; align-items: flex-end; gap: 6px; height: 100%; }
        .bar-pill-element { width: 14px; border-radius: 3px; }
        .month-axis-label { text-align: center; font-size: 11px; color: #94a3b8; margin-top: 8px; }

        /* Matrix List Section */
        .employee-list-panel-card { background-color: #ffffff; border: 1px solid #eef0f3; border-radius: 12px; padding: 20px; }
        .panel-header-controls-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .panel-header-controls-row .analytics-header-labels { display: flex; align-items: center; gap: 10px; }
        .table-filters-right-side { display: flex; align-items: center; gap: 8px; }
        .filter-select-box { border: 1px solid #e2e8f0; background-color: #ffffff; font-size: 12px; font-weight: 500; color: #334155; padding: 6px 12px; border-radius: 6px; outline: none; }
        .icon-square-action-btn { width: 30px; height: 30px; border: 1px solid #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 12px; background-color: #ffffff; cursor: pointer; }

        .system-data-grid { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
        .system-data-grid th { color: #64748b; font-weight: 500; padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: 12px; }
        .system-data-grid th i { font-size: 10px; color: #cbd5e1; margin-left: 4px; }
        .system-data-grid td { padding: 16px; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .system-data-grid tbody tr:last-child td { border-bottom: none; }
        .grid-custom-checkbox { width: 15px; height: 15px; border-radius: 4px; border: 1px solid #cbd5e1; cursor: pointer; }
        
        .emp-id-txt { font-weight: 500; color: #334155; }
        .emp-name-txt { font-weight: 600; color: #111111; }
        .emp-email-txt { color: #64748b; }
        .emp-role-txt { color: #334155; }
        
        .dept-pill-label { display: inline-block; font-size: 11px; font-weight: 600; padding: 4px 8px; border-radius: 4px; }
        .dept-pill-label.design { background-color: #f0fdf4; color: #16a34a; }
        .dept-pill-label.marketing { background-color: #fff7ed; color: #ea580c; }
        .dept-pill-label.development { background-color: #f3e8ff; color: #9333ea; }

        .status-plain-text { font-weight: 500; color: #111111; }
        .grid-action-links { display: flex; align-items: center; justify-content: flex-end; gap: 16px; color: #94a3b8; }
        .grid-action-links i { cursor: pointer; }
    </style>
</head>
<body>

    <div class="app-container">
        
        <nav class="sidebar">
            <div class="logo-container">
                <img src="../img/kiwi.png" alt="Kiwi Digital Logo" class="logo-img">
            </div>

            <ul class="nav-links">
                <li class="nav-item active">
                    <a href="hr_dashboard.php">
                        <i class="fa-solid fa-table-cells-large icon"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="employee.php">
                        <i class="fa-solid fa-users icon"></i>
                        <span>Employee</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="leave_management.php">
                        <i class="fa-solid fa-user-plus icon"></i>
                        <span>Leave Requests</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="timekeeping.html">
                        <i class="fa-solid fa-business-time icon"></i>
                        <span>Timesheet</span>
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

        <main class="main-content">
            
            <div class="top-navbar-strip">
                <h1 class="dashboard-main-title">Dashboard</h1>
                <div class="navbar-search-side">
                    <div class="search-box-wrapper">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" class="search-input-field" placeholder="Search here">
                        <span class="shortcut-badge">⌘K</span>
                    </div>
                    <div class="bell-notify-btn">
                        <i class="fa-regular fa-bell"></i>
                    </div>
                </div>
            </div>

            <div class="summary-quad-row">
                <div class="metric-card-box">
                    <div class="card-top-meta">
                        <div class="mini-icon-container"><i class="fa-solid fa-users"></i></div>
                        <div class="meta-text-labels">
                            <h3>Total Employees</h3>
                            <p>Employee count includes all staff</p>
                        </div>
                    </div>
                    <div class="card-bottom-data">
                        <div>
                            <div class="metric-large-number">183</div>
                            <div class="comparison-status-badge">+10% <span>vs Last Year</span></div>
                        </div>
                        <a href="#" class="details-arrow-link">Details →</a>
                    </div>
                </div>

                <div class="metric-card-box">
                    <div class="card-top-meta">
                        <div class="mini-icon-container"><i class="fa-solid fa-sitemap"></i></div>
                        <div class="meta-text-labels">
                            <h3>Departments</h3>
                            <p>Total divisions in the company</p>
                        </div>
                    </div>
                    <div class="card-bottom-data">
                        <div>
                            <div class="metric-large-number">8</div>
                            <div class="comparison-status-badge">+15% <span>vs Last Year</span></div>
                        </div>
                        <a href="#" class="details-arrow-link">Details →</a>
                    </div>
                </div>

                <div class="metric-card-box">
                    <div class="card-top-meta">
                        <div class="mini-icon-container"><i class="fa-solid fa-user-check"></i></div>
                        <div class="meta-text-labels">
                            <h3>Today Presents</h3>
                            <p>Total present employees today</p>
                        </div>
                    </div>
                    <div class="card-bottom-data">
                        <div>
                            <div class="metric-large-number">178</div>
                            <div class="comparison-status-badge">+20% <span>vs Yesterday</span></div>
                        </div>
                        <a href="#" class="details-arrow-link">Details →</a>
                    </div>
                </div>

                <div class="metric-card-box">
                    <div class="card-top-meta">
                        <div class="mini-icon-container"><i class="fa-solid fa-user-minus"></i></div>
                        <div class="meta-text-labels">
                            <h3>Today Absents</h3>
                            <p>Total people absents today</p>
                        </div>
                    </div>
                    <div class="card-bottom-data">
                        <div>
                            <div class="metric-large-number">5</div>
                            <div class="comparison-status-badge negative">-20% <span>vs Yesterday</span></div>
                        </div>
                        <a href="#" class="details-arrow-link">Details →</a>
                    </div>
                </div>
            </div>

            <div class="analytics-twins-grid">
                
                <div class="analytics-card-frame">
                    <div class="analytics-card-header">
                        <div class="analytics-header-labels">
                            <div class="mini-icon-container" style="background-color: #f5f3ff;"><i class="fa-regular fa-clock" style="color: #8b5cf6;"></i></div>
                            <div>
                                <h4>Avg Work Hours</h4>
                                <p>Track the average hours worked across all employees.</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-expand action-icon-trigger"></i>
                    </div>

                    <div class="graph-numeric-strip">
                        <div class="graph-large-readout">7,8 hours</div>
                        <div class="comparison-status-badge">+10% <span>vs Last Month</span></div>
                        <div style="margin-left: auto;">
                            <select class="filter-dropdown-pill">
                                <option>Last 7 Days</option>
                            </select>
                        </div>
                    </div>

                    <div class="mock-area-graph-canvas">
                        <svg class="mock-svg-line-path" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <path d="M0,75 L15,75 L15,30 L30,30 L35,22 L55,22 L55,30 L75,30 L75,15 L85,15 L85,45 L100,45" stroke="#6366f1" stroke-width="2" fill="none"/>
                        </svg>
                        <div class="avg-indicator-horizontal-line">
                            <div class="avg-floating-pill">Avg 7,8 h</div>
                        </div>
                    </div>
                    <div class="graph-axis-x-row">
                        <span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span><span>7</span>
                    </div>
                </div>

                <div class="analytics-card-frame">
                    <div class="analytics-card-header">
                        <div class="analytics-header-labels">
                            <div class="mini-icon-container" style="background-color: #f0fdfa;"><i class="fa-solid fa-chart-simple" style="color: #0d9488;"></i></div>
                            <div>
                                <h4>Work Hours Per Month</h4>
                                <p>Total hours worked by all employees each month</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-expand action-icon-trigger"></i>
                    </div>

                    <div class="twin-chart-legend-row">
                        <div class="legend-indicator"><span style="background-color: #6366f1;"></span> Work-Time</div>
                        <div class="legend-indicator"><span style="background-color: #22d3ee;"></span> Overtime</div>
                    </div>

                    <div class="twin-metrics-readout-row">
                        <div class="readout-unit-block">
                            <h5>Work-Time</h5>
                            <p>159h 25m</p>
                        </div>
                        <div class="readout-unit-block">
                            <h5>Overtime</h5>
                            <p>27h 28m</p>
                        </div>
                    </div>

                    <div class="bars-container-flex">
                        <div>
                            <div class="bars-twin-cluster">
                                <div class="bar-pill-element" style="background-color: #6366f1; height: 100px;"></div>
                                <div class="bar-pill-element" style="background-color: #22d3ee; height: 35px;"></div>
                            </div>
                            <div class="month-axis-label">Jan</div>
                        </div>
                        <div>
                            <div class="bars-twin-cluster">
                                <div class="bar-pill-element" style="background-color: #6366f1; height: 100px;"></div>
                                <div class="bar-pill-element" style="background-color: #22d3ee; height: 20px;"></div>
                            </div>
                            <div class="month-axis-label">Feb</div>
                        </div>
                        <div>
                            <div class="bars-twin-cluster">
                                <div class="bar-pill-element" style="background-color: #6366f1; height: 100px;"></div>
                                <div class="bar-pill-element" style="background-color: #22d3ee; height: 65px;"></div>
                            </div>
                            <div class="month-axis-label">Mar</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="employee-list-panel-card">
                <div class="panel-header-controls-row">
                    <div class="analytics-header-labels">
                        <div class="mini-icon-container"><i class="fa-solid fa-address-book"></i></div>
                        <div>
                            <h4>Employee List</h4>
                            <p>Directory of all employees in the organization.</p>
                        </div>
                    </div>
                    
                    <div class="table-filters-right-side">
                        <select class="filter-select-box"><option>All Status</option></select>
                        <select class="filter-select-box"><option>All Department</option></select>
                        <select class="filter-select-box"><option>Last 14 Days</option></select>
                        <div class="icon-square-action-btn"><i class="fa-solid fa-expand"></i></div>
                        <div class="icon-square-action-btn"><i class="fa-solid fa-ellipsis"></i></div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="system-data-grid">
                        <thead>
                            <tr>
                                <th style="width: 40px;"><input type="checkbox" class="grid-custom-checkbox"></th>
                                <th>Employee ID <i class="fa-solid fa-chevron-down"></i></th>
                                <th>Name <i class="fa-solid fa-chevron-down"></i></th>
                                <th>Email <i class="fa-solid fa-chevron-down"></i></th>
                                <th>Role <i class="fa-solid fa-chevron-down"></i></th>
                                <th>Departments</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="checkbox" class="grid-custom-checkbox"></td>
                                <td class="emp-id-txt">Orac1823</td>
                                <td class="emp-name-txt">Andrew Rico</td>
                                <td class="emp-email-txt">andrew@gmail.com</td>
                                <td class="emp-role-txt">UI Designer</td>
                                <td><span class="dept-pill-label design">Design</span></td>
                                <td class="status-plain-text">Fulltime</td>
                                <td>
                                    <div class="grid-action-links">
                                        <i class="fa-regular fa-eye"></i>
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="grid-custom-checkbox"></td>
                                <td class="emp-id-txt">Aero2345</td>
                                <td class="emp-name-txt">Maria Lopez</td>
                                <td class="emp-email-txt">jessica@yahoo.com</td>
                                <td class="emp-role-txt">UX Researcher</td>
                                <td><span class="dept-pill-label design">Design</span></td>
                                <td class="status-plain-text">Parttime</td>
                                <td>
                                    <div class="grid-action-links">
                                        <i class="fa-regular fa-eye"></i>
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="grid-custom-checkbox"></td>
                                <td class="emp-id-txt">MediX500</td>
                                <td class="emp-name-txt">Devon Smith</td>
                                <td class="emp-email-txt">michael@hotmail.com</td>
                                <td class="emp-role-txt">Product Manager</td>
                                <td><span class="dept-pill-label marketing">Marketing</span></td>
                                <td class="status-plain-text">Internship</td>
                                <td>
                                    <div class="grid-action-links">
                                        <i class="fa-regular fa-eye"></i>
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="grid-custom-checkbox"></td>
                                <td class="emp-id-txt">TechNova1000</td>
                                <td class="emp-name-txt">Aisha Khan</td>
                                <td class="emp-email-txt">sarah@outlook.com</td>
                                <td class="emp-role-txt">Front-end Developer</td>
                                <td><span class="dept-pill-label development">Development</span></td>
                                <td class="status-plain-text">Contract</td>
                                <td>
                                    <div class="grid-action-links">
                                        <i class="fa-regular fa-eye"></i>
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </div>
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