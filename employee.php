<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Registry - Kiwi Digital</title>
    
    <!-- Local Bootstrap File Link -->
    <link rel="stylesheet" href="bootstrap.min.css">

    <!-- Font Awesome 6 CDN Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        /* Google Font matching the clean modern UI style */
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
           SIDEBAR STYLES (MATCHED EXACTLY TO THE DASHBOARD INDEX)
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

        /* Active shape logic */
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
            transition: color 0.2s ease;
        }

        .logout-btn:hover {
            color: #ff3333;
        }

        .logout-btn .icon {
            font-size: 20px;
            margin-right: 10px;
            color: #555555;
        }

        /* ==========================================================================
           MAIN CONTENT CONTAINER & METRICS (MATCHED TO image_146a00.png)
           ========================================================================== */
        .main-content {
            flex-grow: 1;
            padding-top: 10px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .dashboard-header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .dashboard-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
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

        .card-header i {
            font-size: 16px;
        }

        .card-title {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
            white-space: nowrap;
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
            white-space: nowrap;
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

        .badge.positive {
            background-color: #ecfdf5;
            color: #10b981;
        }

        .badge.negative {
            background-color: #fef2f2;
            color: #ef4444;
        }

        /* ==========================================================================
           EMPLOYEE PANEL & TABLE STYLES (MATCHED TO image_13f1a6.jpg)
           ========================================================================== */
        .employee-panel {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 24px;
            width: 100%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f3f5;
        }

        .table-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            gap: 16px;
        }

        .table-search-box {
            position: relative;
            width: 320px;
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

        .action-buttons-group {
            display: flex;
            gap: 12px;
        }

        .btn-filter, .btn-export {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .btn-add-employee {
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

        .custom-table-wrapper {
            overflow-x: auto;
            width: 100%;
        }

        .employee-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .employee-table th {
            color: #64748b;
            font-weight: 500;
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .employee-table td {
            padding: 14px 16px;
            color: #334155;
            vertical-align: middle;
        }

        .checkbox-cell {
            width: 40px;
        }

        .custom-checkbox {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            cursor: pointer;
        }

        .profile-meta-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            background-color: #e2e8f0;
        }

        .profile-identity-info {
            display: flex;
            flex-direction: column;
        }

        .profile-name-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            color: #1e293b;
        }

        .profile-name-row i {
            font-size: 12px;
            color: #94a3b8;
            cursor: pointer;
        }

        .emp-id-sub {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .status-pill {
            display: inline-flex;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pill.active {
            background-color: #f0fdf4;
            color: #16a34a;
        }

        .status-pill.inactive {
            background-color: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        .action-dot-menu {
            color: #94a3b8;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
        }

        .table-footer-pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
            font-size: 14px;
            color: #64748b;
        }

        .show-entries-dropdown {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .show-entries-dropdown select {
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            outline: none;
            color: #334155;
            background-color: #ffffff;
        }

        .pagination-pages-list {
            display: flex;
            align-items: center;
            gap: 6px;
            list-style: none;
        }

        .page-link-node {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            font-weight: 500;
            color: #64748b;
            transition: all 0.2s;
            text-decoration: none;
        }

        .page-link-node.active {
            background-color: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
        }

        .page-link-node:hover:not(.active) {
            background-color: #f1f5f9;
        }

        .page-link-node.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

    <div class="app-container">
        
        <!-- SIDEBAR COMPONENT (PERFECT STATIC MATCH) -->
        <nav class="sidebar">
            <div class="logo-container">
                <img src="img/kiwi.png" alt="KIWI DIGITAL TECH INC." class="logo-img">
            </div>

            <ul class="nav-links">
                <li class="nav-item">
                    <a href="index.php">
                        <i class="fa-solid fa-table-cells-large icon"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="employee.php">
                        <i class="fa-solid fa-users-rectangle icon"></i>
                        <span>Employee</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#">
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
                    <a href="#">
                        <i class="fa-solid fa-right-left icon"></i>
                        <span>Shift Configuration</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#">
                        <i class="fa-solid fa-user-gear icon"></i>
                        <span>Leave Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#">
                        <i class="fa-solid fa-cubes icon"></i>
                        <span>Internship Registry</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#">
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
            
            <div class="dashboard-header-bar">
                <h1 class="dashboard-title">Employee</h1>
            </div>
            
            <!-- METRIC CARDS OVERVIEW (MATCHED TO image_146a00.png) -->
            <div class="metrics-straight-row">
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-circle-check" style="color: #3b82f6;"></i>
                        <span class="card-title">Total Employees Present</span>
                    </div>
                    <div class="card-value">120</div>
                    <div class="card-footer">
                        <span class="badge positive">
                            <i class="fa-solid fa-arrow-up"></i> 5%
                        </span>
                        <span>from yesterday</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-user-clock" style="color: #3b82f6;"></i>
                        <span class="card-title">Late Arrivals Today</span>
                    </div>
                    <div class="card-value">15</div>
                    <div class="card-footer">
                        <span class="badge positive">
                            <i class="fa-solid fa-arrow-up"></i> 3 people
                        </span>
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
                        <span class="badge negative">
                            <i class="fa-solid fa-arrow-down"></i> 2 people
                        </span>
                        <span>compared to last Monday</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-clock" style="color: #3b82f6;"></i>
                        <span class="card-title">Average Check-In Time</span>
                    </div>
                    <div class="card-value">01:11 PM</div>
                    <div class="card-footer">
                        <span>Consistent with last week</span>
                    </div>
                </div>
            </div>
            
            <!-- REGISTRY TABLE DATA PANEL (MATCHED TO image_13f1a6.jpg) -->
            <div class="employee-panel">
                
                <div class="table-controls">
                    <div class="table-search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Search by name, role, or employee ID">
                    </div>
                    
                    <div class="action-buttons-group">
                        <button class="btn-filter"><i class="fa-solid fa-sliders"></i> Filter</button>
                        <button class="btn-export"><i class="fa-solid fa-download"></i> Export</button>
                        <button class="btn-add-employee"><i class="fa-solid fa-plus"></i> Add Employee</button>
                    </div>
                </div>

                <div class="custom-table-wrapper">
                    <table class="employee-table">
                        <thead>
                            <tr>
                                <th class="checkbox-cell"><input type="checkbox" class="custom-checkbox"></th>
                                <th>Name</th>
                                <th>Job Title</th>
                                <th>Department</th>
                                <th>Employment Type</th>
                                <th>Status</th>
                                <th>Join Date</th>
                                <th style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Row 1 -->
                            <tr>
                                <td><input type="checkbox" class="custom-checkbox"></td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <div class="profile-identity-info">
                                            <div class="profile-name-row">Andi Pratama <i class="fa-regular fa-copy"></i></div>
                                            <span class="emp-id-sub">EMP-001</span>
                                        </div>
                                    </div>
                                </td>
                                <td>Product Manager</td>
                                <td>Product</td>
                                <td>Full-time</td>
                                <td><span class="status-pill active">Active</span></td>
                                <td>Jan 10, 2022</td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis-vertical"></i></td>
                            </tr>
                            <!-- Row 2 -->
                            <tr>
                                <td><input type="checkbox" class="custom-checkbox"></td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <div class="profile-identity-info">
                                            <div class="profile-name-row">Siti Aulia <i class="fa-regular fa-copy"></i></div>
                                            <span class="emp-id-sub">EMP-002</span>
                                        </div>
                                    </div>
                                </td>
                                <td>UI/UX Designer</td>
                                <td>Design</td>
                                <td>Full-time</td>
                                <td><span class="status-pill active">Active</span></td>
                                <td>Mar 22, 2022</td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis-vertical"></i></td>
                            </tr>
                            <!-- Row 3 -->
                            <tr>
                                <td><input type="checkbox" class="custom-checkbox"></td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <div class="profile-identity-info">
                                            <div class="profile-name-row">Bima Nugraha <i class="fa-regular fa-copy"></i></div>
                                            <span class="emp-id-sub">EMP-003</span>
                                        </div>
                                    </div>
                                </td>
                                <td>Frontend Engineer</td>
                                <td>Engineering</td>
                                <td>Full-time</td>
                                <td><span class="status-pill inactive">Inactive</span></td>
                                <td>Jul 05, 2021</td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis-vertical"></i></td>
                            </tr>
                            <!-- Row 4 -->
                            <tr>
                                <td><input type="checkbox" class="custom-checkbox"></td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <div class="profile-identity-info">
                                            <div class="profile-name-row">Rina Putri <i class="fa-regular fa-copy"></i></div>
                                            <span class="emp-id-sub">EMP-004</span>
                                        </div>
                                    </div>
                                </td>
                                <td>HR Generalist</td>
                                <td>Human Resources</td>
                                <td>Full-time</td>
                                <td><span class="status-pill active">Active</span></td>
                                <td>Feb 14, 2023</td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis-vertical"></i></td>
                            </tr>
                            <!-- Row 5 -->
                            <tr>
                                <td><input type="checkbox" class="custom-checkbox"></td>
                                <td>
                                    <div class="profile-meta-cell">
                                        <div class="avatar-image"></div>
                                        <div class="profile-identity-info">
                                            <div class="profile-name-row">Dimas Saputra <i class="fa-regular fa-copy"></i></div>
                                            <span class="emp-id-sub">EMP-005</span>
                                        </div>
                                    </div>
                                </td>
                                <td>Backend Engineer</td>
                                <td>Engineering</td>
                                <td>Contract</td>
                                <td><span class="status-pill active">Active</span></td>
                                <td>Nov 01, 2020</td>
                                <td class="action-dot-menu"><i class="fa-solid fa-ellipsis-vertical"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- TABLE FOOTER & PAGINATION CONTROL -->
                <div class="table-footer-pagination">
                    <div class="show-entries-dropdown">
                        <span>Show</span>
                        <select>
                            <option>10</option>
                            <option>25</option>
                            <option>50</option>
                        </select>
                        <span>from 200 data</span>
                    </div>
                    
                    <ul class="pagination-pages-list">
                        <li><a class="page-link-node disabled" href="#"><i class="fa-solid fa-chevron-left"></i></a></li>
                        <li><a class="page-link-node active" href="#">1</a></li>
                        <li><a class="page-link-node" href="#">2</a></li>
                        <li><a class="page-link-node" href="#">3</a></li>
                        <li><a class="page-link-node" href="#">4</a></li>
                        <li style="padding: 0 4px;">...</li>
                        <li><a class="page-link-node" href="#"><i class="fa-solid fa-chevron-right"></i></a></li>
                    </ul>
                </div>

            </div>
        </main>

    </div>

</body>
</html>