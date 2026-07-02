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

    
    $dbConnection = new mysqli("localhost", "root", "", "attendance_kiwi");

    if ($dbConnection->connect_error) {
        die("Connection architecture failure: " . $dbConnection->connect_error);
    }

    
   $attendanceEngine = new Attendance($dbConnection);
    
    // 1. Query the 'employees' table instead of 'users'
    $sql = "SELECT name FROM employees WHERE biometric_rfid = ?";
    $stmt = $dbConnection->prepare($sql);
    $stmt->bind_param("s", $rfid_uid);
    $stmt->execute();
    $userProfile = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($userProfile) {
        $fullName = $userProfile['name'];

        // 2. Pass the retrieved name to your log function
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
        }
        /* Dynamic color themes mapping to operational feedback states */
        .info { background: #eef2f7; color: #34495e; border-left-color: #3498db; }
        .success { background: #e8f8f5; color: #117a65; border-left-color: #2ecc71; }
        .danger { background: #fdf2f2; color: #922b21; border-left-color: #e74c3c; }

        /* Secure input architecture: visually obfuscate focus field while sustaining element listeners */
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
    </style>
</head>
<body>


<!-- Inside your attendance_terminal.php form -->
<form id="attendanceForm" action="attendance_terminal.php" method="POST">
    <input type="text" id="rfid_uid" name="rfid_uid" autofocus autocomplete="off">
    
    <!-- Add a selection for manual override -->
    <div class="mt-3">
        <label>Action:</label>
        <select name="action_type" class="form-select">
            <option value="auto">Auto (Clock In/Out)</option>
            <option value="clock_out">Manual Clock Out</option>
        </select>
    </div>
</form>

<div class="scanner-card">
    <div class="clock-display" id="live-clock">00:00:00 AM</div>
    <h2>TERMINAL SCANNER</h2>
    <p>Please pass your company ID card cleanly over the RFID reader device</p>
    
    <div class="alert-box <?php echo $alert_class; ?>">
        <?php echo htmlspecialchars($msg); ?>
    </div>

    <form id="attendanceForm" action="attendance_terminal.php" method="POST">
        <input type="text" id="rfid_uid" name="rfid_uid" autofocus autocomplete="off">
    </form>
</div>

<script>
    const rfidInput = document.getElementById('rfid_uid');

    document.addEventListener('click', () => rfidInput.focus());
    window.onload = () => rfidInput.focus();


    function updateClock() {
        const now = new Date();
        let options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        document.getElementById('live-clock').innerText = now.toLocaleTimeString('en-US', options);
    }
    setInterval(updateClock, 1000);
    updateClock();

    if (window.location.search.includes('msg=')) {
        setTimeout(() => {
            window.location.href = 'attendance_terminal.php';
        }, 4000);
    }
</script>

</body>
</html>