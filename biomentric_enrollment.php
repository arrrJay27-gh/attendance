<?php
// 1. SET LOCAL TIMEZONE & INITIALIZE VARIABLES
date_default_timezone_set('Asia/Manila');
$msg = "Select an employee and tap a card to enroll.";
$alert_class = "info";

// Establish Database Connection (OOP Style)
$conn = new mysqli("localhost", "root", "", "attendance_kiwi");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. PROCESS ENROLLMENT IF FORM IS SUBMITTED
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['rfid_uid']) && !empty($_POST['user_id'])) {
    $rfid_uid = trim($_POST['rfid_uid']);
    $user_id = intval($_POST['user_id']);

    // Check if this RFID card is already assigned to someone else
    $check_sql = "SELECT id, first_name, last_name FROM users WHERE biometric_rfid = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $rfid_uid);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $existing_user = $check_result->fetch_assoc();
        $msg = "Error: This card is already assigned to " . $existing_user['first_name'] . " " . $existing_user['last_name'] . "!";
        $alert_class = "danger";
    } else {
        // Update the biometric_rfid column for the chosen user
        $update_sql = "UPDATE users SET biometric_rfid = ? WHERE id = ?";
        $up_stmt = $conn->prepare($update_sql);
        $up_stmt->bind_param("si", $rfid_uid, $user_id);

        if ($up_stmt->execute()) {
            $msg = "Success! RFID Card registered successfully.";
            $alert_class = "success";
        } else {
            $msg = "Database Error: Could not enroll card.";
            $alert_class = "danger";
        }
        $up_stmt->close();
    }
    $check_stmt->close();
    
    // Redirect to clear form POST data and show the alert cleanly
    header("Location: enrollment.php?msg=" . urlencode($msg) . "&class=" . $alert_class);
    exit();
}

// Capture redirect messages for the UI
if (isset($_GET['msg']) && isset($_GET['class'])) {
    $msg = $_GET['msg'];
    $alert_class = $_GET['class'];
}

// 3. FETCH UNENROLLED EMPLOYEES FOR THE DROPDOWN
// This searches for users where biometric_rfid is currently NULL
$query = "SELECT id, first_name, last_name, role FROM users WHERE biometric_rfid IS NULL";
$unregistered_users = $conn->query($query);
?>

<!-- 4. FRONTEND HTML INTERFACE -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFID Biometric Registration</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .enroll-card {
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            width: 420px;
        }
        h2 { color: #2c3e50; margin-bottom: 5px; text-align: center; }
        p { color: #7f8c8d; margin-bottom: 25px; font-size: 0.9rem; text-align: center; }
        
        label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
            font-weight: 600;
            color: #34495e;
        }
        select {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1rem;
            margin-bottom: 25px;
            background-color: #fff;
            box-sizing: border-box;
        }
        .alert-box {
            padding: 15px;
            border-radius: 6px;
            font-weight: 600;
            border-left: 5px solid;
            margin-bottom: 25px;
            text-align: center;
        }
        .info { background: #eef2f7; color: #34495e; border-left-color: #3498db; }
        .success { background: #e8f8f5; color: #117a65; border-left-color: #2ecc71; }
        .danger { background: #feadad; color: #900c3f; border-left-color: #c70039; }

        /* Keeps the scanner focus box invisible but highly active */
        #rfid_uid {
            position: absolute;
            opacity: 0;
            left: -9999px;
        }
        .scan-indicator {
            font-size: 0.85rem;
            color: #95a5a6;
            margin-top: 15px;
            font-style: italic;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="enroll-card">
    <h2>RFID Enrollment Station</h2>
    <p>Link a physical badge to an employee database profile</p>
    
    <div class="alert-box <?php echo $alert_class; ?>">
        <?php echo htmlspecialchars($msg); ?>
    </div>

    <form id="enrollmentForm" action="enrollment.php" method="POST">
        <label for="user_id">Select Employee Profile:</label>
        <select name="user_id" id="user_id" required>
            <option value="" disabled selected>-- Choose employee without RFID --</option>
            <?php 
            if ($unregistered_users && $unregistered_users->num_rows > 0) {
                while($row = $unregistered_users->fetch_assoc()) {
                    echo "<option value='". $row['id'] ."'>" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . " (" . ucfirst($row['role']) . ")</option>";
                }
            } else {
                echo "<option value='' disabled>All employees have registered RFIDs</option>";
            }
            ?>
        </select>

        <!-- Hidden input capturing hardware emulator string sequence -->
        <input type="text" id="rfid_uid" name="rfid_uid" autocomplete="off" required>
    </form>

    <div class="scan-indicator">
        *Cursor stays auto-locked. Choose a name, then scan card to save.*
    </div>
</div>

<script>
    const rfidInput = document.getElementById('rfid_uid');
    const userSelect = document.getElementById('user_id');

    // Lock cursor target onto hidden scanner input unless interacting with dropdown selection
    document.addEventListener('click', (e) => {
        if (e.target !== userSelect) {
            rfidInput.focus();
        }
    });

    // Ensure initial landing context sets form focus appropriately
    window.onload = () => rfidInput.focus();

    // Prevent submission attempts on mechanical card taps if user has not chosen an employee profile
    document.getElementById('enrollmentForm').addEventListener('submit', function(e) {
        if (userSelect.value === "") {
            e.preventDefault();
            alert("Please choose an employee from the dropdown list before tapping the card!");
            rfidInput.value = ""; // Clear string data
            rfidInput.focus();
        }
    });

    // Clear alert indicators automatically after 5 seconds to reset system layout
    if (window.location.search.includes('msg=')) {
        setTimeout(() => {
            window.location.href = 'enrollment.php';
        }, 5000);
    }
</script>

</body>
</html>
<?php $conn->close(); ?>