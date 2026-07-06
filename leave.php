<?php
require_once 'auth.php';
require_once 'database.php';
require_once 'class/Leave.php';

$database = new Database();
$conn = $database->getConnection();
$leaveService = new Leave($conn);
$leaveRows = $leaveService->getAll('', '', 100, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees Leave List - Kiwi Digital</title>
    
    <!-- Local Bootstrap File Link -->
    <link rel="stylesheet" href="bootstrap-5.3.5-dist/css/bootstrap.min.css">

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
            margin-bottom: 20px;
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

        /* Filter Row Styles */
        .filter-row-container {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        .filter-search-input-wrap {
            position: relative;
            flex-grow: 1;
            min-width: 240px;
        }

        .filter-search-input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
        }

        .filter-search-field {
            width: 100%;
            padding: 8px 14px 8px 38px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            font-size: 13px;
            outline: none;
            color: #334155;
            height: 38px;
        }

        .filter-dropdown-select {
            padding: 8px 14px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            font-size: 13px;
            color: #475569;
            outline: none;
            min-width: 150px;
            height: 38px;
            cursor: pointer;
        }

        /* Unified Export Dropdown Styling matched to employee.php */
        .export-dropdown-wrapper {
            position: relative;
            display: inline-block;
        }

        .btn-export-trigger {
            background-color: #ffffff;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            height: 38px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-export-trigger:hover {
            background-color: #f8fafc;
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

    <div class="app-container" id="appContainer">
        
        <?php
        $activePage = 'leave';
        $imgPrefix = $imgPrefix ?? '';
        $navItems = [
            ['id' => 'dashboard',   'href' => 'index.php',       'icon' => 'fa-table-cells-large',       'label' => 'Dashboard'],
            ['id' => 'employee',    'href' => 'employee.php',    'icon' => 'fa-users-rectangle',         'label' => 'Employee'],
            ['id' => 'biometric',   'href' => '#',               'icon' => 'fa-fingerprint',             'label' => 'Biometric Enrollment'],
            ['id' => 'timekeeping', 'href' => 'timekeeping.php', 'icon' => 'fa-clipboard-user',          'label' => 'Timekeeping'],
            ['id' => 'shift',       'href' => 'shift_management.php',               'icon' => 'fa-right-left',              'label' => 'Shift Configuration'],
            ['id' => 'leave',       'href' => 'leave.php',       'icon' => 'fa-user-gear',               'label' => 'Leave Management'],
            ['id' => 'internship',  'href' => '#',               'icon' => 'fa-cubes',                   'label' => 'Internship Registry'],
            ['id' => 'audit',       'href' => '#',               'icon' => 'fa-square-poll-horizontal',  'label' => 'System Audit'],
        ];
        ?>

        <!-- SIDEBAR CONTAINER -->
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
                        <!-- Dropdown Setup -->
                        <div class="dropdown export-dropdown-wrapper">
                            <button class="btn btn-export-trigger dropdown-toggle" type="button" id="exportMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-download"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportMenuButton">
                                <li>
                                    <button class="dropdown-item btn-export" data-export-type="leave" data-export-format="csv">
                                        <i class="fa-solid fa-file-csv me-2 text-success"></i> Export as CSV
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item btn-export" data-export-type="leave" data-export-format="pdf">
                                        <i class="fa-solid fa-file-pdf me-2 text-danger"></i> Export as PDF
                                    </button>
                                </li>
                            </ul>
                        </div>
                        
                        <button class="btn-approve-all" id="approve-all-leave">Approve All</button>
                    </div>
                </div>

                <!-- SEARCH & FILTER INTERACTION PANEL -->
                <div class="filter-row-container">
                    <div class="filter-search-input-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="filter-search-name" class="filter-search-field" placeholder="Search employee by name...">
                    </div>
                    <select id="filter-leave-type" class="filter-dropdown-select">
                        <option value="">All Leave Types</option>
                        <option value="Sick Leave">Sick Leave</option>
                        <option value="Vacation Leave">Vacation Leave</option>
                        <option value="Emergency Leave">Emergency Leave</option>
                    </select>
                    <select id="filter-leave-status" class="filter-dropdown-select">
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
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
                        <tbody id="leave-table-body">
                            <?php if (!empty($leaveRows)): ?>
                                <?php foreach ($leaveRows as $leave): ?>
                                    <tr data-leave-id="<?php echo (int) $leave['id']; ?>" 
                                        data-emp-name="<?php echo htmlspecialchars(strtolower($leave['employee_name'])); ?>"
                                        data-type="<?php echo htmlspecialchars($leave['leave_type']); ?>"
                                        data-status="<?php echo htmlspecialchars($leave['status']); ?>">
                                        <td>
                                            <div class="emp-profile-block">
                                                <div class="emp-avatar"></div>
                                                <span><?php echo htmlspecialchars($leave['employee_name']); ?></span>
                                            </div>
                                        </td>
                                        <td class="designation-text"><?php echo htmlspecialchars($leave['designation']); ?></td>
                                        <td class="leave-type-text"><?php echo htmlspecialchars($leave['leave_type']); ?></td>
                                        <td class="reason-truncate-cell"><?php echo htmlspecialchars($leave['reason']); ?></td>
                                        <td class="date-log-text"><?php echo date('F d, Y', strtotime($leave['start_date'])); ?></td>
                                        <td class="date-log-text"><?php echo date('F d, Y', strtotime($leave['end_date'])); ?></td>
                                        <td class="days-count-badge"><?php echo (int) $leave['days']; ?></td>
                                        <td style="text-align: center;">
                                            <?php if ($leave['status'] === 'Pending'): ?>
                                                <div class="action-flex-box justify-content-center">
                                                    <button class="btn-action-approve leave-approve-btn" data-id="<?php echo (int) $leave['id']; ?>"><i class="fa-solid fa-check"></i> Approve</button>
                                                    <button class="btn-action-reject-cross leave-reject-btn" data-id="<?php echo (int) $leave['id']; ?>"><i class="fa-solid fa-xmark"></i></button>
                                                </div>
                                            <?php else: ?>
                                                <span class="status-outcome-badge <?php echo strtolower($leave['status']); ?>"><?php echo htmlspecialchars($leave['status']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="8" style="text-align:center; padding:30px; color:#64748b;">No leave requests found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap Core JS Bundle with Popper for Dropdowns -->
    <script src="bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. FIXED & ISOLATED SIDEBAR TOGGLE MECHANIC (Guaranteed to execute first)
            const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
            if (toggleSidebarBtn) {
                toggleSidebarBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const isMinimized = document.documentElement.classList.toggle('sidebar-minimized');
                    localStorage.setItem('sidebarMinimized', isMinimized);
                });
            }

            // 2. SEARCH & FILTERING CORE LOGIC
            const searchNameInput = document.getElementById('filter-search-name');
            const selectTypeDropdown = document.getElementById('filter-leave-type');
            const selectStatusDropdown = document.getElementById('filter-leave-status');

            function performRowFiltering() {
                if (!searchNameInput || !selectTypeDropdown || !selectStatusDropdown) return;
                
                const searchVal = searchNameInput.value.toLowerCase().trim();
                const typeVal = selectTypeDropdown.value;
                const statusVal = selectStatusDropdown.value;
                const rows = document.querySelectorAll('#leave-table-body tr[data-leave-id]');
                
                let visibleCount = 0;

                rows.forEach(row => {
                    const rowName = row.getAttribute('data-emp-name') || '';
                    const rowType = row.getAttribute('data-type') || '';
                    const rowStatus = row.getAttribute('data-status') || '';

                    const matchesName = !searchVal || rowName.includes(searchVal);
                    const matchesType = !typeVal || rowType === typeVal;
                    const matchesStatus = !statusVal || rowStatus === statusVal;

                    if (matchesName && matchesType && matchesStatus) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                let emptyRow = document.getElementById('filter-empty-fallback-row');
                if (visibleCount === 0) {
                    if (!emptyRow) {
                        emptyRow = document.createElement('tr');
                        emptyRow.id = 'filter-empty-fallback-row';
                        emptyRow.innerHTML = '<td colspan="8" style="text-align:center; padding:30px; color:#64748b;">No matching leave records found.</td>';
                        document.getElementById('leave-table-body').appendChild(emptyRow);
                    }
                } else if (emptyRow) {
                    emptyRow.remove();
                }
            }

            if (searchNameInput) searchNameInput.addEventListener('input', performRowFiltering);
            if (selectTypeDropdown) selectTypeDropdown.addEventListener('change', performRowFiltering);
            if (selectStatusDropdown) selectStatusDropdown.addEventListener('change', performRowFiltering);

            // 3. ASYNCHRONOUS DATA LAYERING SAFELY WRAPPED IN TRY/CATCH
            async function loadLeaveRows() {
                try {
                    if (typeof KiwiApp === 'undefined' || !KiwiApp.request) return;
                    
                    const response = await KiwiApp.request('leave_list', {}, 'GET');
                    if (!response || response.status !== 'success') return;

                    const tbody = document.getElementById('leave-table-body');
                    if (!tbody) return;

                    if (!response.data || !response.data.length) {
                        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; padding:30px; color:#64748b;">No leave requests found.</td></tr>';
                        return;
                    }

                    tbody.innerHTML = response.data.map((leave) => {
                        const startDate = new Date(leave.start_date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
                        const endDate = new Date(leave.end_date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
                        const actionCell = leave.status === 'Pending'
                            ? `<div class="action-flex-box justify-content-center">
                                   <button class="btn-action-approve leave-approve-btn" data-id="${leave.id}"><i class="fa-solid fa-check"></i> Approve</button>
                                   <button class="btn-action-reject-cross leave-reject-btn" data-id="${leave.id}"><i class="fa-solid fa-xmark"></i></button>
                               </div>`
                            : `<span class="status-outcome-badge ${leave.status.toLowerCase()}">${typeof KiwiApp.escapeHtml === 'function' ? KiwiApp.escapeHtml(leave.status) : leave.status}</span>`;

                        const cleanName = leave.employee_name ? leave.employee_name.toLowerCase() : '';
                        const escName = typeof KiwiApp.escapeHtml === 'function' ? KiwiApp.escapeHtml(leave.employee_name) : leave.employee_name;
                        const escDesignation = typeof KiwiApp.escapeHtml === 'function' ? KiwiApp.escapeHtml(leave.designation) : leave.designation;
                        const escType = typeof KiwiApp.escapeHtml === 'function' ? KiwiApp.escapeHtml(leave.leave_type) : leave.leave_type;
                        const escReason = typeof KiwiApp.escapeHtml === 'function' ? KiwiApp.escapeHtml(leave.reason) : leave.reason;

                        return `
                            <tr data-leave-id="${leave.id}" 
                                data-emp-name="${cleanName}"
                                data-type="${escType}"
                                data-status="${leave.status}">
                                <td><div class="emp-profile-block"><div class="emp-avatar"></div><span>${escName}</span></div></td>
                                <td class="designation-text">${escDesignation}</td>
                                <td class="leave-type-text">${escType}</td>
                                <td class="reason-truncate-cell">${escReason}</td>
                                <td class="date-log-text">${startDate}</td>
                                <td class="date-log-text">${endDate}</td>
                                <td class="days-count-badge">${leave.days}</td>
                                <td style="text-align:center;">${actionCell}</td>
                            </tr>
                        `;
                    }).join('');

                    bindLeaveActions();
                    performRowFiltering();
                } catch (error) {
                    console.error("Dynamic table render skipped or blocked:", error);
                }
            }

            async function updateLeaveStatus(id, status) {
                try {
                    const response = await KiwiApp.request('leave_update_status', { id, status });
                    if (response && response.status === 'success') {
                        if (typeof KiwiApp.showToast === 'function') KiwiApp.showToast(`Leave ${status.toLowerCase()}.`);
                        loadLeaveRows();
                    } else {
                        if (typeof KiwiApp.showToast === 'function') KiwiApp.showToast(response.message || 'Update failed.', true);
                    }
                } catch (err) {
                    console.error(err);
                }
            }

            function bindLeaveActions() {
                document.querySelectorAll('.leave-approve-btn').forEach((btn) => {
                    btn.replaceWith(btn.cloneNode(true)); // Clear tracking duplicates safely
                });
                document.querySelectorAll('.leave-reject-btn').forEach((btn) => {
                    btn.replaceWith(btn.cloneNode(true));
                });
                
                document.querySelectorAll('.leave-approve-btn').forEach((btn) => {
                    btn.addEventListener('click', () => updateLeaveStatus(Number(btn.dataset.id), 'Approved'));
                });
                document.querySelectorAll('.leave-reject-btn').forEach((btn) => {
                    btn.addEventListener('click', () => updateLeaveStatus(Number(btn.dataset.id), 'Rejected'));
                });
            }

            document.getElementById('approve-all-leave')?.addEventListener('click', async () => {
                try {
                    const response = await KiwiApp.request('leave_approve_all', {});
                    if (response && response.status === 'success') {
                        if (typeof KiwiApp.showToast === 'function') KiwiApp.showToast('All pending leave requests approved.');
                        loadLeaveRows();
                    } else {
                        if (typeof KiwiApp.showToast === 'function') KiwiApp.showToast(response.message || 'Approve all failed.', true);
                    }
                } catch(e) {}
            });

            document.querySelectorAll('.btn-export').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault(); 
                    const type = this.getAttribute('data-export-type');
                    const format = this.getAttribute('data-export-format');
                    
                    if (typeof KiwiApp !== 'undefined' && typeof KiwiApp.exportData === 'function') {
                        KiwiApp.exportData(type, format);
                    } else {
                        if (typeof KiwiApp.showToast === 'function') KiwiApp.showToast(`Exporting ${type} report to ${format.toUpperCase()}...`);
                    }
                });
            });

            // Initialize actions for static server rows right away
            bindLeaveActions();
            // Fire API loader gracefully without halting core window layout buttons
            loadLeaveRows();
        });
    </script>
</body>
</html>