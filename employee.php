<?php 
$activePage = 'employee'; 
require_once 'database.php';

$database = new Database();
$conn = $database->getConnection();

$present_q = "SELECT COUNT(DISTINCT name) as total FROM attendance WHERE status='Present' AND DATE(date) = CURDATE()";
$present_res = $conn->query($present_q);
$total_present = ($present_res && $row = $present_res->fetch_assoc()) ? $row['total'] : 0;

$late_q = "SELECT COUNT(DISTINCT name) as total FROM attendance WHERE status='Late' AND DATE(date) = CURDATE()";
$late_res = $conn->query($late_q);
$total_late = ($late_res && $row = $late_res->fetch_assoc()) ? $row['total'] : 0;

$absent_q = "SELECT COUNT(DISTINCT name) as total FROM attendance WHERE status='Absent' AND DATE(date) = CURDATE()";
$absent_res = $conn->query($absent_q);
$total_absent = ($absent_res && $row = $absent_res->fetch_assoc()) ? $row['total'] : 0;

$total_emp_q = "SELECT COUNT(*) as total FROM employees";
$total_emp_res = $conn->query($total_emp_q);
$total_employees_count = ($total_emp_res && $row = $total_emp_res->fetch_assoc()) ? $row['total'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiwi Digital Dashboard</title>
    <link rel="stylesheet" href="bootstrap-5.3.5-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>try{if(localStorage.getItem('sidebarMinimized')==='true'){document.documentElement.classList.add('sidebar-minimized');}}catch(e){}</script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; padding: 20px; height: 100vh; overflow: hidden; }
        
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

        .dashboard-header-bar { display: flex; justify-content: space-between; align-items: center; width: 100%; height: 40px; flex-shrink: 0; }
        .dashboard-title { font-size: 24px; font-weight: 700; color: #0f172a; }
        
        .dashboard-row-layout { display: flex; flex-direction: column; gap: 16px; width: 100%; height: calc(100% - 56px); }
        .metrics-straight-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; width: 100%; flex-shrink: 0; }
        
        .card { background-color: #ffffff; border-radius: 16px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.04); border: 1px solid #f1f3f5; display: flex; flex-direction: column; justify-content: space-between; height: 125px; }
        .card-header { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; background: transparent; border: none; padding: 0; }
        .card-header i { font-size: 14px; color: #3b82f6; }
        .card-title { font-size: 13px; font-weight: 600; color: #1f2937; white-space: nowrap; }
        .card-value { font-size: 28px; font-weight: 700; color: #111827; line-height: 1.1; }
        .card-footer { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #6b7280; white-space: nowrap; background: transparent; border: none; padding: 0; }
        
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 6px; border-radius: 6px; font-size: 11px; font-weight: 600; }
        .badge.positive { background-color: #ecfdf5; color: #10b981; }
        .badge.negative { background-color: #fef2f2; color: #ef4444; }

        .bottom-content-area { display: flex; gap: 24px; width: 100%; height: calc(100% - 141px); align-items: stretch; overflow: hidden; }
        
        .employee-panel { background-color: #ffffff; border-radius: 16px; padding: 20px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.04); border: 1px solid #f1f3f5; flex-grow: 1; display: flex; flex-direction: column; gap: 16px; height: 95%; overflow: hidden; }
        
        .table-controls { display: flex; justify-content: space-between; align-items: center; width: 100%; flex-shrink: 0; }
        .table-search-box { position: relative; width: 320px; }
        .table-search-box i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }
        .table-search-box input { width: 100%; padding: 10px 14px 10px 40px; border-radius: 50px; border: 1px solid #e2e8f0; background-color: #ffffff; font-size: 13px; outline: none; color: #334155; }
        
        .action-buttons-group { display: flex; gap: 10px; align-items: center; }
        .btn-filter, .btn-export { background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 14px; font-size: 13px; font-weight: 500; color: #475569; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.2s; }
        .btn-filter:hover, .btn-export:hover { background-color: #f8f9fa; border-color: #cbd5e1; }
        .btn-add-employee { background-color: #2563eb; color: #ffffff; border: none; border-radius: 8px; padding: 8px 14px; font-size: 13px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: background 0.2s; }
        .btn-add-employee:hover { background-color: #1d4ed8; }

        .custom-table-wrapper { width: 100%; flex-grow: 1; overflow-y: auto; border: 1px solid #f1f5f9; border-radius: 12px; }
        .employee-table { width: 100%; border-collapse: separate; border-spacing: 0; text-align: left; }
        .employee-table th { background-color: #f8fafc; color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 10; }
        .employee-table td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 13px; vertical-align: middle; }
        .employee-table tr:last-child td { border-bottom: none; }
        .employee-table tr:hover td { background-color: #f8fafc; }

        .profile-meta-cell { display: flex; align-items: center; gap: 12px; }
        .avatar-image { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; background-color: #e2e8f0; }
        .profile-identity-info { display: flex; flex-direction: column; }
        .profile-name-row { font-size: 13px; font-weight: 600; color: #0f172a; display: flex; align-items: center; gap: 6px; }
        .profile-name-row i { color: #94a3b8; font-size: 11px; cursor: pointer; }
        .profile-name-row i:hover { color: #64748b; }
        .emp-id-sub { font-size: 11px; color: #64748b; font-weight: 500; }

        .status-pill { display: inline-flex; align-items: center; padding: 4px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: capitalize; }
        .status-pill.active { background-color: #ecfdf5; color: #059669; }
        .status-pill.inactive { background-color: #f1f5f9; color: #64748b; }

        .action-dot-menu .btn { background: none; border: none; padding: 4px 8px; color: #94a3b8; font-size: 14px; border-radius: 4px; }
        .action-dot-menu .btn:after { display: none; }
        .action-dot-menu .btn:hover { color: #475569; background-color: #f1f5f9; }
        .dropdown-menu { border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; padding: 4px 0; }
        .dropdown-item { font-size: 13px; padding: 8px 16px; color: #334155; }
        .dropdown-item i { width: 16px; }

        .table-footer-pagination { display: flex; justify-content: space-between; align-items: center; width: 100%; flex-shrink: 0; margin-top: 4px; }
        .show-entries-dropdown { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #64748b; }
        .show-entries-dropdown select { padding: 4px 8px; border-radius: 6px; border: 1px solid #e2e8f0; outline: none; background-color: #ffffff; color: #334155; }
        
        .pagination-pages-list { display: flex; align-items: center; gap: 4px; list-style: none; margin: 0; padding: 0; }
        .page-link-node { display: flex; align-items: center; justify-content: center; min-width: 28px; height: 28px; padding: 0 6px; border-radius: 6px; font-size: 12px; font-weight: 500; color: #64748b; text-decoration: none; border: 1px solid transparent; transition: all 0.2s; }
        .page-link-node:hover:not(.disabled):not(.active) { background-color: #f1f5f9; color: #334155; }
        .page-link-node.active { background-color: #0f172a; color: #ffffff; font-weight: 600; }
        .page-link-node.disabled { color: #cbd5e1; cursor: not-allowed; pointer-events: none; }

        /* Sidebar Styles Exactly Matched */
        .sidebar { width: 100%; background-color: #dcdddf; border-radius: 36px; padding: 45px 0 35px 0; display: flex; flex-direction: column; position: relative; height: 100%; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar * { box-sizing: border-box !important; }
        .sidebar-header { display: flex; justify-content: center; align-items: center; margin-bottom: 35px; width: 100%; position: relative; padding: 0 20px; }
        .logo-container { display: flex; align-items: center; justify-content: center; width: 100%; text-align: center; margin-bottom: 0; }
        .logo-img { max-width: 140px; width: auto; height: auto; display: inline-block; margin: 0; transition: max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s ease; }
        .sidebar-toggle-btn { position: absolute; top: 10px; right: -13px; width: 26px; height: 26px; border-radius: 50%; background-color: #ffffff; border: none; box-shadow: 0 2px 6px rgba(0,0,0,0.12); display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 100; color: #52525b; }
        .sidebar-toggle-btn i { font-size: 11px; transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .nav-links { list-style: none; padding-left: 0 !important; margin-bottom: 0 !important; display: flex; flex-direction: column; gap: 6px; flex-grow: 1; width: 100%; }
        .nav-item { width: 100%; }
        .nav-item a { display: flex; align-items: center; gap: 20px; padding: 15px 35px !important; color: #434850; text-decoration: none !important; font-size: 16px; font-weight: 600; transition: color 0.2s; }
        .nav-item.active a { background-color: #ffffff; color: #11161e; border-top-right-radius: 18px; border-bottom-right-radius: 18px; margin-right: 20px; padding-left: 35px !important; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02); }
        .nav-item a i.icon { font-size: 20px; width: 26px; margin-right: 0; text-align: center; color: #434850; }
        .nav-item.active a i.icon { color: #11161e; }
        .sidebar-footer { margin-top: auto; padding-left: 0; }
        .logout-btn { display: flex; align-items: center; gap: 20px; padding: 15px 35px; color: #434850; text-decoration: none; font-size: 16px; font-weight: 600; }
        .sidebar-minimized .sidebar { padding: 45px 0 35px 0; }
        .sidebar-minimized .sidebar .logo-img { max-width: 40px; transform: scale(1); }
        .sidebar-minimized .sidebar .nav-item a span, .sidebar-minimized .sidebar .logout-btn span { display: none; }
        .sidebar-minimized .sidebar .nav-item a { justify-content: center; padding: 15px 0 !important; }
        .sidebar-minimized .sidebar .nav-item.active a { margin-right: 10px; padding-left: 0 !important; border-radius: 0 16px 16px 0; }
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
            ['id' => 'biometric',   'href' => 'biometrics.php',               'icon' => 'fa-fingerprint',             'label' => 'Biometric Enrollment'],
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
                <h1 class="dashboard-title">Employee</h1>
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
                    <div class="employee-panel">
                        <div class="table-controls">
                            <div class="table-search-box">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" placeholder="Search by name, role, or employee ID">
                            </div>
                            
                            <div class="action-buttons-group">
                                <button class="btn-filter"><i class="fa-solid fa-sliders"></i> Filter</button>
                                <button class="btn-export"><i class="fa-solid fa-download"></i> Export</button>
                                
                                <button type="button" class="btn-add-employee" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                                    <i class="fa-solid fa-plus"></i> Add Employee
                                </button>
                            </div>
                        </div>

                        <div class="custom-table-wrapper">
                            <table class="employee-table">
                                <thead>
                                    <tr>
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
                                    <?php
                                    $emp_list_q = "SELECT * FROM employees ORDER BY id DESC";
                                    $emp_list_res = $conn->query($emp_list_q);

                                    if ($emp_list_res && $emp_list_res->num_rows > 0) {
                                        while($emp = $emp_list_res->fetch_assoc()) {
                                            // Adjusted column array lookups to match Screenshot 2026-07-02 125134.png
                                            $job_title = !empty($emp['position']) ? htmlspecialchars($emp['position']) : 'Staff';
                                            $department = !empty($emp['department']) ? htmlspecialchars($emp['department']) : 'General';
                                            $emp_type = !empty($emp['employment_type']) ? htmlspecialchars($emp['employment_type']) : 'Full-time';
                                            $status = (isset($emp['status']) && strtolower($emp['status']) === 'inactive') ? 'inactive' : 'active';
                                            $join_date = !empty($emp['created_at']) ? date("M d, Y", strtotime($emp['created_at'])) : date("M d, Y");
                                            $emp_id = !empty($emp['employee_id']) ? htmlspecialchars($emp['employee_id']) : 'EMP-' . str_pad($emp['id'], 3, '0', STR_PAD_LEFT);
                                            
                                            echo '
                                            <tr>
                                                <td>
                                                    <div class="profile-meta-cell">
                                                        <img src="https://ui-avatars.com/api/?name='.urlencode($emp['name']).'&background=cbd5e1&color=334155" class="avatar-image" alt="">
                                                        <div class="profile-identity-info">
                                                            <div class="profile-name-row">'.htmlspecialchars($emp['name']).' <i class="fa-regular fa-copy"></i></div>
                                                            <span class="emp-id-sub">'.$emp_id.'</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>'.$job_title.'</td>
                                                <td>'.$department.'</td>
                                                <td>'.$emp_type.'</td>
                                                <td><span class="status-pill '.$status.'">'.ucfirst($status).'</span></td>
                                                <td>'.$join_date.'</td>
                                                <td class="action-dot-menu">
                                                    <div class="dropdown action-dropdown">
                                                        <button class="btn dropdown-toggle" type="button" id="actionMenu'.$emp['id'].'" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionMenu'.$emp['id'].'">
                                                            <li><a class="dropdown-item edit-employee-btn" href="#" data-bs-toggle="modal" data-bs-target="#editEmployeeModal" data-id="'.$emp['id'].'" data-employee_id="'.htmlspecialchars($emp['employee_id']).'" data-name="'.htmlspecialchars($emp['name']).'" data-position="'.htmlspecialchars($job_title).'" data-department="'.htmlspecialchars($department).'" data-status="'.ucfirst($status).'"><i class="fa-solid fa-pen me-2"></i>Edit</a></li>
                                                            <li><a class="dropdown-item text-danger delete-employee-btn" href="#" data-id="'.$emp['id'].'"><i class="fa-solid fa-trash me-2"></i>Delete</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="7" style="text-align:center; padding: 30px; color:#64748b;">No employees registered in the system.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="table-footer-pagination">
                            <div class="show-entries-dropdown">
                                <span>Show</span>
                                <select>
                                    <option>10</option>
                                    <option>25</option>
                                    <option>50</option>
                                </select>
                                <span>from <?php echo $total_employees_count; ?> entries</span>
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
                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmployeeModalLabel" style="font-weight:600; color:#0f172a;">Add New Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="add_employee_process.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="emp_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="emp_name" name="name" required placeholder="e.g. John Doe">
                        </div>
                        <div class="mb-3">
                            <label for="emp_id" class="form-label">Employee ID</label>
                            <input type="text" class="form-control" id="emp_id" name="employee_id" placeholder="e.g. EMP-005 (Leave blank to auto-generate)">
                        </div>
                        <div class="mb-3">
                            <label for="emp_title" class="form-label">Job Title / Position</label>
                            <input type="text" class="form-control" id="emp_title" name="position" required placeholder="e.g. Web Developer">
                        </div>
                        <div class="mb-3">
                            <label for="emp_dept" class="form-label">Department</label>
                            <select class="form-select" id="emp_dept" name="department" required>
                                <option value="IT Department">IT Department</option>
                                <option value="Human Resources">Human Resources</option>
                                <option value="Creative Production">Creative Production</option>
                                <option value="Marketing Team">Marketing Team</option>
                                <option value="Finance">Finance</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="emp_type" class="form-label">Employment Type</label>
                            <select class="form-select" id="emp_type" name="employment_type" required>
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Contractual">Contractual</option>
                                <option value="Internship">Internship</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:8px; font-size:14px;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="background-color:#2563eb; border:none; border-radius:8px; font-size:14px; font-weight:500; padding:10px 20px;">Save Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmployeeModalLabel" style="font-weight:600; color:#0f172a;">Edit Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="edit_employee_process.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="mb-3">
                            <label for="edit_emp_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="edit_emp_name" name="name" required placeholder="e.g. John Doe">
                        </div>
                        <div class="mb-3">
                            <label for="edit_emp_id" class="form-label">Employee ID</label>
                            <input type="text" class="form-control" id="edit_emp_id" name="employee_id" placeholder="e.g. EMP-005">
                        </div>
                        <div class="mb-3">
                            <label for="edit_emp_title" class="form-label">Job Title / Position</label>
                            <input type="text" class="form-control" id="edit_emp_title" name="position" required placeholder="e.g. Web Developer">
                        </div>
                        <div class="mb-3">
                            <label for="edit_emp_dept" class="form-label">Department</label>
                            <select class="form-select" id="edit_emp_dept" name="department" required>
                                <option value="IT Department">IT Department</option>
                                <option value="Human Resources">Human Resources</option>
                                <option value="Creative Production">Creative Production</option>
                                <option value="Marketing Team">Marketing Team</option>
                                <option value="Finance">Finance</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_emp_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_emp_status" name="status">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:8px; font-size:14px;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="background-color:#2563eb; border:none; border-radius:8px; font-size:14px; font-weight:500; padding:10px 20px;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
            toggleSidebarBtn.addEventListener('click', function() {
                const isMinimized = document.documentElement.classList.toggle('sidebar-minimized');
                localStorage.setItem('sidebarMinimized', isMinimized);
            });

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
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var editModalEl = document.getElementById('editEmployeeModal');
        if (editModalEl) {
            editModalEl.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                if (!button) return;
                var id = button.getAttribute('data-id') || '';
                var employee_id = button.getAttribute('data-employee_id') || '';
                var name = button.getAttribute('data-name') || '';
                var position = button.getAttribute('data-position') || '';
                var department = button.getAttribute('data-department') || '';
                var status = button.getAttribute('data-status') || 'Active';

                document.getElementById('edit_id').value = id;
                document.getElementById('edit_emp_id').value = employee_id;
                document.getElementById('edit_emp_name').value = name;
                document.getElementById('edit_emp_title').value = position;
                document.getElementById('edit_emp_dept').value = department;
                document.getElementById('edit_emp_status').value = status;
            });
        }

        document.querySelectorAll('.delete-employee-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var id = this.getAttribute('data-id');
                if (!id) return;
                if (confirm('Delete this employee? This cannot be undone.')) {
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'delete_employee_process.php';
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'id';
                    input.value = id;
                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
    </script>
</body>
</html> 
<?php $conn->close(); ?>    