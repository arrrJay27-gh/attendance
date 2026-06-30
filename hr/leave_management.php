<?php
// Simple filter handler for the employee leave list
$leave_type_filter = isset($_GET['type']) ? $_GET['type'] : 'all';

// Mock data array (In your actual code, replace this with your SQL SELECT query)
$all_leave_requests = [
    ['id' => 'EMP-0294', 'name' => 'Andrew Rico', 'email' => 'andrew@gmail.com', 'type' => 'vacation', 'label' => 'Vacation Leave', 'start' => '2026-07-05', 'end' => '2026-07-12', 'status' => 'Approved'],
    ['id' => 'EMP-1840', 'name' => 'Maria Lopez', 'email' => 'maria@yahoo.com', 'type' => 'sick', 'label' => 'Sick/Medical', 'start' => '2026-06-29', 'end' => '2026-06-30', 'status' => 'Pending'],
    ['id' => 'EMP-0582', 'name' => 'Devon Smith', 'email' => 'devon@hotmail.com', 'type' => 'parental', 'label' => 'Parental Leave', 'start' => '2026-07-10', 'end' => '2026-07-15', 'status' => 'Approved'],
    ['id' => 'EMP-2049', 'name' => 'Aisha Khan', 'email' => 'aisha@outlook.com', 'type' => 'vacation', 'label' => 'Vacation Leave', 'start' => '2026-08-01', 'end' => '2026-08-14', 'status' => 'Rejected']
];

// Filter the array depending on which sub-menu link was clicked
$filtered_requests = [];
foreach ($all_leave_requests as $request) {
    if ($leave_type_filter === 'all' || $request['type'] === $leave_type_filter) {
        $filtered_requests[] = $request;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Management - Kiwi Digital</title>
    
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
           SIDEBAR STYLES (MATCHED EXACTLY TO image_229862.png & image_040ae4.png)
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
            transition: all 0.15s ease;
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

        .sub-menu-list {
            list-style: none;
            padding-left: 62px;
            margin-top: 8px;
            margin-bottom: 8px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sub-menu-list li a {
            padding: 0;
            font-size: 13px;
            color: #777777;
            font-weight: 500;
            background: none !important;
            text-decoration: none;
        }

        .sub-menu-list li a:hover,
        .sub-menu-list li a.sub-active {
            color: #111111;
            font-weight: 600;
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
            gap: 24px;
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

        .tabs-button-row {
            display: flex;
            gap: 16px;
            width: 100%;
        }

        .tab-block-btn {
            flex: 1;
            background-color: #6b6b6b;
            color: #ffffff;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: background-color 0.2s;
        }

        .tab-block-btn:hover {
            background-color: #555555;
        }

        /* ==========================================================================
           LEAVE STATS CARDS GRID (MATCHED TO image_039dff.png)
           ========================================================================== */
        .leave-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            width: 100%;
        }

        .stat-card-canvas {
            background-color: #ffffff;
            border: 1px solid #eef0f3;
            border-radius: 16px;
            padding: 20px 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .stat-card-title-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
        }

        .stat-card-title-row.total i { color: #2563eb; }
        .stat-card-title-row.approved i { color: #16a34a; }
        .stat-card-title-row.pending i { color: #2563eb; }
        .stat-card-title-row.rejected i { color: #dc2626; }

        .stat-card-large-number {
            font-size: 34px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
        }

        .stat-card-badge-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #64748b;
        }

        .stat-pill-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .stat-pill-badge.green { color: #16a34a; background-color: #f0fdf4; }
        .stat-pill-badge.red { color: #dc2626; background-color: #fef2f2; }

        /* ==========================================================================
           EMPLOYEE LIST DYNAMIC PANEL LAYOUT STYLE 
           ========================================================================== */
        .employee-list-panel-card { 
            background-color: #ffffff; 
            border: 1px solid #eef0f3; 
            border-radius: 12px; 
            padding: 20px; 
        }
        
        .panel-header-controls-row { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px; 
        }

        .panel-header-controls-row h4 { 
            font-size: 15px; 
            font-weight: 600; 
            color: #1e293b; 
            margin-bottom: 2px; 
        }
        
        .panel-header-controls-row p { 
            font-size: 12px; 
            color: #94a3b8; 
            margin-bottom: 0; 
        }

        .system-data-grid { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
        .system-data-grid th { color: #64748b; font-weight: 500; padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: 12px; }
        .system-data-grid td { padding: 16px; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        
        .emp-name-txt { font-weight: 600; color: #111111; }
        
        .type-pill-label { display: inline-block; font-size: 11px; font-weight: 600; padding: 4px 8px; border-radius: 4px; }
        .type-pill-label.vacation { background-color: #e0f2fe; color: #0369a1; }
        .type-pill-label.sick { background-color: #fef3c7; color: #b45309; }
        .type-pill-label.parental { background-color: #f3e8ff; color: #6b21a8; }

        .status-badge { font-size: 11px; font-weight: 600; padding: 4px 8px; border-radius: 20px; display: inline-block; }
        .status-badge.Approved { background-color: #dcfce7; color: #15803d; }
        .status-badge.Pending { background-color: #fef9c3; color: #a16207; }
        .status-badge.Rejected { background-color: #fee2e2; color: #b91c1c; }
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
                    <a href="hr_dashboard.php">
                        <i class="fa-solid fa-table-cells-large icon"></i>
                        <span>Dashboard</span>
                    </a> 
                </li>
                <li class="nav-item">
                    <a href="employee.html">
                        <i class="fa-solid fa-users icon"></i>
                        <span>Employee</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="leave_management.php">
                        <i class="fa-solid fa-user-plus icon"></i>
                        <span>Leave Requests</span>
                    </a>
                    <!-- Active Expanded Inner Links (References image_040ae4.png style with added filtering) -->
                    <ul class="sub-menu-list">
                        <li><a href="leave_management.php?type=vacation" class="<?= $leave_type_filter === 'vacation' ? 'sub-active' : '' ?>">Vacation Leave List</a></li>
                        <li><a href="leave_management.php?type=sick" class="<?= $leave_type_filter === 'sick' ? 'sub-active' : '' ?>">Sick/Medical Leave</a></li>
                        <li><a href="leave_management.php?type=parental" class="<?= $leave_type_filter === 'parental' ? 'sub-active' : '' ?>">Parental Leave List</a></li>
                        <?php if($leave_type_filter !== 'all'): ?>
                            <li><a href="leave_management.php" style="color: #3b82f6;">Show All Requests</a></li>
                        <?php endif; ?>
                    </ul>
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

        <!-- MAIN DASHBOARD WINDOW VIEW -->
        <main class="main-content">
            
            <!-- HEADER LOG LAYOUT -->
            <div class="page-header-container">
                <i class="fa-solid fa-book-open page-header-icon"></i>
                <h1 class="page-title-text">Leave Management</h1>
            </div>
            <!-- LEAVE STATUS INDICATOR GRID (IMAGED REF: image_039dff.png) -->
            <div class="leave-stats-grid">
                <div class="stat-card-canvas">
                    <div class="stat-card-title-row total">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Total Requests</span>
                    </div>
                    <div class="stat-card-large-number">120</div>
                    <div class="stat-card-badge-row">
                        <span class="stat-pill-badge green"><i class="fa-solid fa-arrow-up"></i> 5%</span>
                        <span>from yesterday</span>
                    </div>
                </div>

                <div class="stat-card-canvas">
                    <div class="stat-card-title-row approved">
                        <i class="fa-solid fa-user-clock"></i>
                        <span>Approved Requests</span>
                    </div>
                    <div class="stat-card-large-number">15</div>
                    <div class="stat-card-badge-row">
                        <span class="stat-pill-badge green"><i class="fa-solid fa-arrow-up"></i> 3 people</span>
                        <span>compared to last week</span>
                    </div>
                </div>

                <div class="stat-card-canvas">
                    <div class="stat-card-title-row pending">
                        <i class="fa-solid fa-user-clock"></i>
                        <span>Pending Requests</span>
                    </div>
                    <div class="stat-card-large-number">8</div>
                    <div class="stat-card-badge-row">
                        <span class="stat-pill-badge red"><i class="fa-solid fa-arrow-down"></i> 2 people</span>
                        <span>compared to last Monday</span>
                    </div>
                </div>

                <div class="stat-card-canvas">
                    <div class="stat-card-title-row rejected">
                        <i class="fa-regular fa-clock"></i>
                        <span>Rejected Requests</span>
                    </div>
                    <div class="stat-card-large-number">3</div>
                    <div class="stat-card-badge-row">
                        <span>Consistent with last week</span>
                    </div>
                </div>
            </div>

            <!-- DYNAMIC EMPLOYEE LEAVE LIST PANEL -->
            <div class="employee-list-panel-card">
                <div class="panel-header-controls-row">
                    <div>
                        <h4>Leave Application Directory</h4>
                        <p>Showing: <strong><?= ucfirst($leave_type_filter) ?> Leave</strong> records</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="system-data-grid">
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($filtered_requests) > 0): ?>
                                <?php foreach ($filtered_requests as $row): ?>
                                    <tr>
                                        <td><strong><?= $row['id'] ?></strong></td>
                                        <td class="emp-name-txt"><?= $row['name'] ?></td>
                                        <td><?= $row['email'] ?></td>
                                        <td><span class="type-pill-label <?= $row['type'] ?>"><?= $row['label'] ?></span></td>
                                        <td><?= $row['start'] ?></td>
                                        <td><?= $row['end'] ?></td>
                                        <td><span class="status-badge <?= $row['status'] ?>"><?= $row['status'] ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted" style="padding: 24px;">No leave applications found for this type.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

</body>
</html>