<?php
date_default_timezone_set('Asia/Manila');

if (!defined('GRACE_PERIOD_MINUTES')) {
    define('GRACE_PERIOD_MINUTES', 15);
}

require_once 'class/attendance.php';

$msg = "System Online. Waiting for card scan...";
$alert_class = "info";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['rfid_uid'])) {
    $rfid_uid = trim($_POST['rfid_uid']);
    $liveTimestamp = date('Y-m-d H:i:s');
    $display_time = date('h:i:s A');

    // Establish DB connection
    $dbConnection = new mysqli("localhost", "root", "", "attendance_kiwi");

    if ($dbConnection->connect_error) {
        die("Connection architecture failure: " . $dbConnection->connect_error);
    }

   
    
    $attendanceEngine = new Attendance($dbConnection);
    
    // 1. First, try an exact match (handles text/varchar columns with leading zeros)
    $sql = "SELECT name FROM users WHERE biometric_rfid = ?";
    $stmt = $dbConnection->prepare($sql);
    $stmt->bind_param("s", $rfid_uid);
    $stmt->execute();
    $userProfile = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // 2. Fallback: If not found, trim leading zeros (handles numerical INT columns)
    if (!$userProfile) {
        $cleaned_rfid = ltrim($rfid_uid, '0');
        if (!empty($cleaned_rfid)) {
            $sql = "SELECT name FROM employees WHERE biometric_rfid = ?";
            $stmt = $dbConnection->prepare($sql);
            $stmt->bind_param("s", $cleaned_rfid);
            $stmt->execute();
            $userProfile = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        }
    }

    if ($userProfile) {
        $fullName = $userProfile['name'];
        // ... (rest of your matching log punch code remains exactly the same)
        // Pass the employee's name to the timekeeping logger engine
        $punchResult = $attendanceEngine->logTimePunch($fullName, $liveTimestamp);

        if ($punchResult['status'] === 'success') {
            $msg = "[{$punchResult['action']}] Successful! {$fullName} recorded at {$display_time}.";
            $alert_class = "success";
        } else {
            $msg = "Terminal Notice: " . $punchResult['message'];
            $alert_class = "danger";
        }
    } else {
        $msg = "Access Denied: Unregistered card credential mapped ({$rfid_uid}).";
        $alert_class = "danger";
    }

    $dbConnection->close();

    header("Location: attendance_terminal.php?msg=" . urlencode($msg) . "&class=" . $alert_class);
    exit();
}

if (isset($_GET['msg']) && isset($_GET['class'])) {
    $msg = $_GET['msg'];
    $alert_class = $_GET['class'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFID Automated Timekeeping Gateway</title>
    <style>
        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Arial, sans-serif;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .scanner-card {
            text-align: center;
            background: #ffffff;
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            width: 440px;
        }
        h2 { color: #2c3e50; margin: 0 0 8px 0; font-size: 1.6rem; letter-spacing: 0.5px; }
        p { color: #7f8c8d; margin: 0 0 35px 0; font-size: 0.95rem; }
        
        .alert-box {
            padding: 20px;
            border-radius: 8px;
            font-weight: 600;
            border-left: 6px solid;
            font-size: 1.05rem;
            line-height: 1.5;
            text-align: left;
            margin-bottom: 20px;
        }
        .info { background: #eef2f7; color: #34495e; border-left-color: #3498db; }
        .success { background: #e8f8f5; color: #117a65; border-left-color: #2ecc71; }
        .danger { background: #fdf2f2; color: #922b21; border-left-color: #e74c3c; }

        #rfid_uid {
            position: absolute;
            opacity: 0;
            left: -9999px;
        }
        
        .clock-display {
            font-size: 2.2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
            font-family: monospace;
            background: #eef2f7;
            padding: 10px 0;
            border-radius: 6px;
        }
        .form-select {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-top: 5px;
        }
        .action-container {
            text-align: left;
            margin-top: 15px;
            color: #434850;
            font-size: 0.9rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="scanner-card">
    <div class="clock-display" id="live-clock">00:00:00 AM</div>
    <h2>TERMINAL SCANNER</h2>
    <p>Please pass your company ID card cleanly over the RFID reader device</p>
    
    <div class="alert-box <?php echo $alert_class; ?>">
        <?php echo htmlspecialchars($msg); ?>
    </div>

    <!-- Unified Single Form Structure -->
    <form id="attendanceForm" action="attendance_terminal.php" method="POST">
        <input type="text" id="rfid_uid" name="rfid_uid" autofocus autocomplete="off">
        
        <div class="action-container">
            <label for="action_type">Scan Action Override Type:</label>
            <select name="action_type" id="action_type" class="form-select">
                <option value="auto">Auto Check-In / Check-Out</option>
                <option value="clock_out">Forced Manual Clock Out</option>
            </select>
        </div>
    </form>
</div>

<script>
    const rfidInput = document.getElementById('rfid_uid');
    const attendanceForm = document.getElementById('attendanceForm');

    // Keep hidden input field continually targeted by key input listeners
    document.addEventListener('click', () => rfidInput.focus());
    window.onload = () => rfidInput.focus();

    // Automatically submit form layout when data injection finishes
    rfidInput.addEventListener('input', function() {
        if(this.value.trim().length >= 8) { 
            setTimeout(() => { attendanceForm.submit(); }, 300);
        }
    });

    function updateClock() {
        const now = new Date();
        let options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        document.getElementById('live-clock').innerText = now.toLocaleTimeString('en-US', options);
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Redirect timeout to clear alert messages from layout
    if (window.location.search.includes('msg=')) {
        setTimeout(() => {
            window.location.href = 'attendance_terminal.php';
        }, 4000);
    }
</script>

</body>
</html>