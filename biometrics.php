<?php
$activePage = 'biometric';
$imgPrefix = './';
require_once 'auth.php';
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Biometric Enrollment</title>
    <link rel="stylesheet" href="bootstrap-5.3.5-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; padding: 20px; height: 100vh; overflow: hidden; }
        
        .app-container { 
            display: grid; 
            grid-template-columns: 310px 1fr; 
            gap: 30px; 
            height: calc(100vh - 40px); 
            width: 100%; 
            position: relative;
            transition: grid-template-columns 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-minimized .app-container { grid-template-columns: 85px 1fr; }
        
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
        
        .dashboard-row-layout { display: flex; flex-direction: column; gap: 16px; width: 100%; height: auto; }
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

        .bottom-content-area { display: flex; gap: 24px; width: 100%; flex-grow: 1; align-items: stretch; overflow: hidden; }
        
        .employee-panel { 
            background-color: #ffffff; 
            border-radius: 16px; 
            padding: 20px; 
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.04); 
            border: 1px solid #f1f3f5; 
            flex-grow: 1; 
            display: flex; 
            flex-direction: column; 
            gap: 16px; 
            height: 100%; 
            overflow: hidden; 
        }

        .custom-table-wrapper { width: 100%; flex-grow: 1; overflow-y: auto; border: 1px solid #f1f5f9; border-radius: 12px; }
        .employee-table { width: 100%; border-collapse: separate; border-spacing: 0; text-align: left; table-layout: fixed; }
        .employee-table th, .employee-table td { 
            padding: 12px 16px; 
            border-bottom: 1px solid #f1f5f9; 
            color: #334155; 
            font-size: 13px; 
            vertical-align: middle; 
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .employee-table th { background-color: #f8fafc; color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 10; }
        .employee-table tr:hover td { background-color: #f8fafc; }

        .sidebar { width: 100%; background-color: #dcdddf; border-radius: 36px; padding: 45px 0 35px 0; display: flex; flex-direction: column; position: relative; height: 100%; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar-header { display: flex; justify-content: center; align-items: center; margin-bottom: 35px; width: 100%; position: relative; padding: 0 20px; }
        .logo-container { display: flex; align-items: center; justify-content: center; width: 100%; text-align: center; margin-bottom: 0; }
        .logo-img { max-width: 140px; width: auto; height: auto; display: inline-block; margin: 0; transition: max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s ease; }
        .sidebar-toggle-btn { position: absolute; top: 10px; right: -13px; width: 26px; height: 26px; border-radius: 50%; background-color: #ffffff; border: none; box-shadow: 0 2px 6px rgba(0,0,0,0.12); display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 100; color: #52525b; }
        .nav-links { list-style: none; padding-left: 0 !important; margin-bottom: 0 !important; display: flex; flex-direction: column; gap: 6px; flex-grow: 1; width: 100%; }
        .nav-item { width: 100%; }
        .nav-item a { display: flex; align-items: center; gap: 20px; padding: 15px 35px !important; color: #434850; text-decoration: none !important; font-size: 16px; font-weight: 600; }
        .nav-item.active a { background-color: #ffffff; color: #11161e; border-top-right-radius: 18px; border-bottom-right-radius: 18px; margin-right: 20px; padding-left: 35px !important; }
        .nav-item a i.icon { font-size: 20px; width: 26px; text-align: center; color: #434850; }
        .sidebar-footer { margin-top: auto; padding-left: 0; }
        .logout-btn { display: flex; align-items: center; gap: 20px; padding: 15px 35px; color: #434850; text-decoration: none; font-size: 16px; font-weight: 600; }
        
        .sidebar-minimized .sidebar { padding: 45px 0 35px 0; }
        .sidebar-minimized .sidebar .logo-img { max-width: 40px; }
        .sidebar-minimized .sidebar .nav-item a span, .sidebar-minimized .sidebar .logout-btn span { display: none; }
        .sidebar-minimized .sidebar .nav-item a { justify-content: center; padding: 15px 0 !important; }
        .sidebar-minimized .sidebar .nav-item.active a { margin-right: 10px; border-radius: 0 16px 16px 0; }
        .sidebar-minimized .sidebar .logout-btn { justify-content: center; padding: 15px 0; }
        .sidebar-minimized .sidebar-toggle-btn i { transform: rotate(180deg); }
    </style>
</head>
<body>

<div class="app-container">
    <?php
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
  
    <nav class="sidebar" id="sidebarContainer">
        <div class="sidebar-header">
            <div class="logo-container">
                <img src="img/kiwi.png" alt="KIWI DIGITAL TECH INC." class="logo-img">
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
        </div>

        <div class="dashboard-header-bar"><h1 class="dashboard-title">Biometric Enrollment</h1></div>
        
        <div class="bottom-content-area">
            <div class="employee-panel">
                <div class="custom-table-wrapper">
                    <table class="employee-table">
                        <thead>
                            <tr><th>Name</th><th>Dept.</th><th>Rf_ID</th><th>Face</th><th>Finger</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT e.id, e.name, e.department, u.biometric_rfid, u.facial_map, u.fingerprint_template 
                                      FROM employees e LEFT JOIN users u ON e.id = u.name";
                            $res = $conn->query($query);
                            if ($res) {
                                while($row = $res->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['department']); ?></td>
                                        <td><?php echo htmlspecialchars($row['biometric_rfid'] ?? '--------'); ?></td>
                                        <td><?php echo $row['facial_map'] ? 'Registered' : 'Not enroll'; ?></td>
                                        <td><?php echo $row['fingerprint_template'] ? 'Registered' : 'Not enroll'; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#enrollModal" 
                                                    data-id="<?php echo htmlspecialchars($row['id']); ?>"
                                                    data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                                    data-dept="<?php echo htmlspecialchars($row['department']); ?>">
                                                Enroll
                                            </button>
                                        </td>
                                    </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Enrollment Modal -->
<div class="modal fade" id="enrollModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 20px; border: none; padding: 30px;">
            <div class="modal-header border-0 flex-column align-items-start p-0 mb-4">
                <h5 class="modal-title d-flex align-items-center" style="font-weight: 700; color: #1f2937;">
                    <i class="fa-solid fa-file-lines me-2"></i> BIOMETRICS ENROLLMENT
                </h5>
                <p class="text-muted mt-1" style="font-size: 14px;">Please provide personal details and biometric data for enrollment.</p>
            </div>
            
            <div class="modal-body p-0">
                <input type="hidden" id="hidden-emp-id"> 
                <!-- Hidden input field to capture physical RFID key strokes -->
                <input type="text" id="rfid-hardware-input" style="position: absolute; opacity: 0; pointer-events: none;">
                
                <div class="mb-3"><label class="small fw-bold mb-1">Name</label><input type="text" id="modal-name" class="form-control" style="border-radius: 10px;"></div>
                <div class="mb-3"><label class="small fw-bold mb-1">Job Title</label><input type="text" id="modal-job" class="form-control" style="border-radius: 10px;"></div>
                <div class="mb-3"><label class="small fw-bold mb-1">Department</label><input type="text" id="modal-dept" class="form-control" style="border-radius: 10px;"></div>
                <div class="mb-3"><label class="small fw-bold mb-1">Employment Type</label><input type="text" id="modal-type" class="form-control" style="border-radius: 10px;"></div>
                <div class="mb-4"><label class="small fw-bold mb-1">Status</label><input type="text" id="modal-status" class="form-control" style="border-radius: 10px;"></div>
                
                <label class="small fw-bold mb-2">Enrollment type</label>
                <div class="row g-3">
                    <!-- RF ID Card -->
                    <div class="col-md-4">
                        <div class="p-3 border rounded d-flex align-items-center" id="rf-card" style="background-color: #e5e5e5; height: 80px; position: relative;">
                            <input type="radio" name="enrollType" id="select-rfid" style="position: absolute; top: 10px; right: 10px;">
                            <i class="fa-solid fa-id-card me-3" style="font-size: 24px;"></i>
                            <div>
                                <div style="font-weight: 600; font-size: 14px;">RF ID</div>
                                <div id="rf-data" style="font-size: 14px;">------------</div>
                            </div>
                            <i id="rf-loading" class="fa-solid fa-spinner fa-spin ms-auto" style="display: none; font-size: 18px;"></i>
                            <i id="rf-success" class="fa-solid fa-circle-check ms-auto" style="display: none; font-size: 18px; color: #10b981;"></i>
                        </div>
                    </div>

                    <!-- Fingerprint Card -->
                    <div class="col-md-4">
                        <div class="p-3 border rounded d-flex align-items-center" style="background-color: #e5e5e5; height: 80px; position: relative;">
                            <input type="radio" name="enrollType" id="select-finger" style="position: absolute; top: 10px; right: 10px;">
                            <i class="fa-solid fa-fingerprint me-3" style="font-size: 24px;"></i>
                            <div style="font-weight: 600; font-size: 14px;">Fingerprint</div>
                        </div>
                    </div>

                    <!-- Face ID Card -->
                    <div class="col-md-4">
                        <div class="p-3 border rounded d-flex align-items-center" style="background-color: #e5e5e5; height: 80px; position: relative;">
                            <input type="radio" name="enrollType" id="select-face" style="position: absolute; top: 10px; right: 10px;">
                            <i class="fa-solid fa-face-smile me-3" style="font-size: 24px;"></i>
                            <div style="font-weight: 600; font-size: 14px;">Face ID</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="done-btn" class="btn btn-primary" style="background-color: #4f46e5; border: none;">Done!</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Sidebar Persistence
    const isMinimized = localStorage.getItem('sidebarMinimized') === 'true';
    if (isMinimized) {
        document.documentElement.classList.add('sidebar-minimized');
    }

    const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
    toggleSidebarBtn.addEventListener('click', function() {
        const minimized = document.documentElement.classList.toggle('sidebar-minimized');
        localStorage.setItem('sidebarMinimized', minimized);
    });

    // Live Time Card
    function updateLiveTimeCard() {
        const now = new Date();
        let hours = now.getHours(); 
        let minutes = now.getMinutes();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        document.getElementById('live-time-card').innerText = `${hours}:${minutes} ${ampm}`;
    }
    updateLiveTimeCard(); 
    setInterval(updateLiveTimeCard, 1000);
});

const enrollModal = document.getElementById('enrollModal');
const rfidHiddenInput = document.getElementById('rfid-hardware-input');
let currentScannedRfid = ""; 

// Dynamic modal assignment
enrollModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('modal-name').value = button.getAttribute('data-name');
    document.getElementById('modal-dept').value = button.getAttribute('data-dept');
    document.getElementById('hidden-emp-id').value = button.getAttribute('data-id'); 
    
    // Clear states
    currentScannedRfid = ""; 
    rfidHiddenInput.value = "";
    document.getElementById('select-rfid').checked = false;
    document.getElementById('rf-loading').style.display = 'none';
    document.getElementById('rf-success').style.display = 'none';
    document.getElementById('rf-data').innerText = '------------';
});

// Automatically trigger waiting state when radio option changes
document.getElementById('select-rfid').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('rf-loading').style.display = 'inline-block';
        document.getElementById('rf-success').style.display = 'none';
        document.getElementById('rf-data').innerText = 'Waiting to scan...';
        
        // Push user focus on the hidden text input to process physical card input stream
        rfidHiddenInput.focus();
    }
});

// Ensure the input field stays focused if the user accidentally clicks away during scanning
document.getElementById('rf-card').addEventListener('click', function() {
    if (document.getElementById('select-rfid').checked) {
        rfidHiddenInput.focus();
    }
});

// Capture real keyboard data transmitted by physical hardware scanner
rfidHiddenInput.addEventListener('keydown', function(event) {
    // Standard hardware devices simulate an 'Enter' key stroke down to end transmission sequence
    if (event.key === 'Enter') {
        event.preventDefault();
        
        if (this.value.trim() !== "") {
            currentScannedRfid = this.value.trim();
            document.getElementById('rf-data').innerText = currentScannedRfid;
            document.getElementById('rf-loading').style.display = 'none';
            document.getElementById('rf-success').style.display = 'inline-block';
        }
    }
});

// Done Button Submit Action
document.getElementById('done-btn').addEventListener('click', function() {
    const empId = document.getElementById('hidden-emp-id').value;
    
    if (!currentScannedRfid) {
        alert("Please tap your physical RF ID card first!");
        return;
    }

    fetch('enrolled_biometrics.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `emp_id=${encodeURIComponent(empId)}&rfid=${encodeURIComponent(currentScannedRfid)}`
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === "success") {
            alert("Enrollment successful!");
            location.reload(); 
        } else {
            alert("Error saving: " + data);
        }
    })
    .catch(err => {
        alert("An error occurred during network transmission.");
        console.error(err);
    });
});
</script>
</body>
</html>