<?php
date_default_timezone_set('Asia/Manila');

if (!defined('GRACE_PERIOD_MINUTES')) {
    define('GRACE_PERIOD_MINUTES', 15);
}

require_once 'class/attendance.php';

$msg = "Waiting for card scan...";
$alert_class = "info";
$is_scanning = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['rfid_uid'])) {
    $rfid_uid = trim($_POST['rfid_uid']);
    $liveTimestamp = date('Y-m-d H:i:s');
    $display_time = date('h:i:s A');

    // Retrieve selected active toggle state from form submission
    $action_type = isset($_POST['action_type']) ? $_POST['action_type'] : 'auto';
    
    // SMART ROUTER MAP: Converts UI names into explicit backend tracking states
    $punchType = ($action_type === 'clock_out') ? 'break' : 'shift';

    // Establish DB connection
    $dbConnection = new mysqli("localhost", "root", "", "attendance_kiwi");

    if ($dbConnection->connect_error) {
        die("Connection architecture failure: " . $dbConnection->connect_error);
    }
    
    $attendanceEngine = new Attendance($dbConnection);
    
    // 1. First, try an exact match
    $sql = "SELECT id, name FROM users WHERE biometric_rfid = ?";
    $stmt = $dbConnection->prepare($sql);
    $stmt->bind_param("s", $rfid_uid);
    $stmt->execute();
    $userProfile = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // 2. Fallback
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
        
        // Log the punch using the mapped $punchType
        $punchResult = $attendanceEngine->logTimePunch($fullName, $liveTimestamp, $punchType);

        if ($punchResult['status'] === 'success') {
            $msg = "[{$punchResult['action']}] Successful! {$fullName} recorded at {$display_time}.";
            $alert_class = "success";
        } else {
            // The cooldown error message from the Attendance class appears here
            $msg = "Terminal Notice: " . $punchResult['message'];
            $alert_class = "danger";
        }
    } else {
        $msg = "Access Denied: Unregistered card credential mapped ({$rfid_uid}).";
        $alert_class = "danger";
    }

    $dbConnection->close();

    // Preserve action type selection on redirect
    header("Location: attendance_terminal.php?msg=" . urlencode($msg) . "&class=" . $alert_class . "&action_type=" . urlencode($action_type));
    exit();
}

if (isset($_GET['msg']) && isset($_GET['class'])) {
    $msg = $_GET['msg'];
    $alert_class = $_GET['class'];
    $is_scanning = false; // Turn off scanning dots when showing a static message result
}

$current_action = isset($_GET['action_type']) ? $_GET['action_type'] : 'auto';
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
            background-color: #e5e5e5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .scanner-card {
            text-align: center;
            background: #dcdcdc;
            padding: 40px 60px;
            border-radius: 40px;
            box-shadow: none;
            border: 1px solid #b5b5b5;
            width: 750px;
            min-height: 480px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
        }
        .logo-container img {
            max-height: 65px;
            width: auto;
            object-fit: contain;
        }
        .clock-display {
            font-size: 3.2rem;
            font-weight: 600;
            color: #000000;
            margin: 20px 0;
            letter-spacing: 0.5px;
        }
        p.instruction { 
            color: #333333; 
            margin: 0 0 25px 0; 
            font-size: 0.95rem; 
            font-weight: 500;
        }
        
        .status-container {
            width: 100%;
            max-width: 520px;
            text-align: left;
            margin-bottom: 25px;
        }
        .status-label {
            font-size: 1.1rem;
            color: #444444;
            font-weight: 500;
            margin-bottom: 6px;
            display: block;
        }
        
        .dots::after {
            content: '';
            animation: blink 1.5s infinite steps(4, start);
        }
        @keyframes blink {
            0% { content: ''; }
            25% { content: '.'; }
            50% { content: '..'; }
            75% { content: '...'; }
            100% { content: ''; }
        }

        .alert-box {
            width: 100%;
            background: #ffffff;
            padding: 24px;
            border-radius: 12px;
            font-size: 1.1rem;
            min-height: 30px;
            box-sizing: border-box;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);
            word-wrap: break-word;
        }
        .info { color: #555555; }
        .success { color: #27ae60; font-weight: 600; }
        .danger { color: #c0392b; font-weight: 600; }

        #rfid_uid {
            position: absolute;
            opacity: 0;
            left: -9999px;
        }
        
        .controls-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-top: 10px;
            width: 100%;
        }
        .btn {
            border: none;
            padding: 14px 32px;
            font-size: 1.15rem;
            font-weight: 600;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.2s ease;
            color: #ffffff;
        }
        .btn-active {
            background-color: #5e5ce6;
        }
        .btn-inactive {
            background-color: #98989d;
        }
        .camera-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 10px;
        }
        .camera-icon svg {
            width: 32px;
            height: 32px;
            fill: #000000;
        }
    </style>
</head>
<body>

<div class="scanner-card">
    <div class="logo-container">
        <img src="img/kiwi.png" alt="Kiwi Digital Tech Inc.">
    </div>

    <div class="clock-display" id="live-clock">00:00:00 AM</div>
    
    <p class="instruction">Please pass your company ID card cleanly over the RFID reader device</p>
    
    <div class="status-container">
        <span class="status-label" id="status-text">
            <?php if ($is_scanning): ?>
                scanning<span class="dots"></span>
            <?php else: ?>
                system message:
            <?php endif; ?>
        </span>
        <div class="alert-box <?php echo $alert_class; ?>">
            <?php echo htmlspecialchars($msg); ?>
        </div>
    </div>

    <form id="attendanceForm" action="attendance_terminal.php" method="POST" style="width: 100%;">
        <input type="text" id="rfid_uid" name="rfid_uid" autofocus autocomplete="off">
        <input type="hidden" name="action_type" id="action_type" value="<?php echo htmlspecialchars($current_action); ?>">
        
        <div class="controls-row">
            <button type="button" class="btn <?php echo $current_action === 'auto' ? 'btn-active' : 'btn-inactive'; ?>" onclick="setAction('auto')">Time in/out</button>
            <button type="button" class="btn <?php echo $current_action === 'clock_out' ? 'btn-active' : 'btn-inactive'; ?>" onclick="setAction('clock_out')">Break in/out</button>
            
            <div class="camera-icon" title="Camera Status">
                <svg viewBox="0 0 24 24">
                    <path d="M4 4h3l2-2h6l2 2h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2zm12 10a4 4 0 1 0-8 0 4 4 0 0 0 8 0zm-4 2.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/>
                </svg>
            </div>
        </div>
    </form>
</div>

<script>
    const rfidInput = document.getElementById('rfid_uid');
    const attendanceForm = document.getElementById('attendanceForm');
    const actionInput = document.getElementById('action_type');

    document.addEventListener('click', () => rfidInput.focus());
    window.onload = () => rfidInput.focus();

    function setAction(type) {
        actionInput.value = type;
        const buttons = document.querySelectorAll('.controls-row .btn');
        buttons.forEach(btn => {
            btn.classList.remove('btn-active');
            btn.classList.add('btn-inactive');
        });
        event.target.classList.remove('btn-inactive');
        event.target.classList.add('btn-active');
        rfidInput.focus();
    }

    if (window.location.search.includes('msg=')) {
        setTimeout(() => {
            window.location.href = 'attendance_terminal.php?action_type=' + encodeURIComponent(actionInput.value);
        }, 4000);
    }

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
</script>

</body>
</html>