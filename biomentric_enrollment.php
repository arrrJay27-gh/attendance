<?php 
$activePage = 'biometric'; 
require_once 'database.php';

$database = new Database();
$conn = $database->getConnection();

$msg = "";
$alert_class = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emp_id']) && !empty($_POST['rfid_uid'])) {

    $empId = trim($_POST['emp_id']); 
    $rfidUid = trim($_POST['rfid_uid']);


    $checkQ = "SELECT employee_id, name FROM employees WHERE biometric_rfid = ?";
    $checkStmt = $conn->prepare($checkQ);
    
    if (!$checkStmt) {
        die("<div class='alert alert-danger m-4'><strong>Database Query Error:</strong> " . $conn->error . "<br><em>Make sure you ran the ALTER TABLE command to add the 'biometric_rfid' column!</em></div>");
    }

    $checkStmt->bind_param("s", $rfidUid);
    $checkStmt->execute();
    $res = $checkStmt->get_result()->fetch_assoc();
    $checkStmt->close();

    if ($res) {
        $msg = "Error: Card payload standard already registered to " . htmlspecialchars($res['name']);
        $alert_class = "alert-danger";
    } else {

        $updateQ = "UPDATE employees SET biometric_rfid = ? WHERE employee_id = ?";
        $updateStmt = $conn->prepare($updateQ);
        
        if (!$updateStmt) {
            die("<div class='alert alert-danger m-4'><strong>Update Preparation Failed:</strong> " . $conn->error . "</div>");
        }
        
        $updateStmt->bind_param("ss", $rfidUid, $empId);
        
        if ($updateStmt->execute()) {
            if ($conn->affected_rows > 0) {
                $msg = "Success: Card bound cleanly to employee account record.";
                $alert_class = "alert-success";
            } else {
                $msg = "Warning: Query ran but no rows were updated. Check if the employee code is valid.";
                $alert_class = "alert-warning";
            }
        } else {
            $msg = "Error executing update: " . $updateStmt->error;
            $alert_class = "alert-danger";
        }
        $updateStmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Biometric Registration Gateway</title>
    <link rel="stylesheet" href="bootstrap-5.3.5-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; padding: 40px; }
        .enroll-card { background: #ffffff; border-radius: 16px; padding: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #f1f3f5; }
        #rfid_uid_display { font-family: monospace; font-size: 1.2rem; font-weight: bold; color: #2563eb; letter-spacing: 1px; }
        .scanner-wait-box { background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 8px; text-align: center; padding: 20px; position: relative; }
        #rfid_capture_input { position: absolute; opacity: 0; top:0; left:0; width:100%; height:100%; cursor: pointer; }
    </style>
</head>
<body>

<div class="container" style="max-width: 800px;">
    <div class="mb-4">
        <a href="employee.php" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-arrow-left me-2"></i>Back to Dashboard</a>
    </div>

    <div class="enroll-card">
        <h3 class="mb-2" style="font-weight:700; color:#0f172a;">RFID Credential Enrollment</h3>
        <p class="text-muted mb-4 small">Link unassigned physical smart badges to identity entries inside your relational profile ledger matrix.</p>

        <?php if (!empty($msg)): ?>
            <div class="alert <?php echo $alert_class; ?> py-2 px-3 mb-4 rounded-3 small"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form action="biometric_enrollment.php" method="POST" id="enrollForm">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label font-weight-bold small text-secondary">1. Target Employee Profile</label>
                    <select class="form-select" name="emp_id" required>
                        <option value="">-- Choose Profile Entry --</option>
                        <?php
                        // Populates using the unique string 'employee_id' as the option value attribute
                        $empQ = "SELECT employee_id, name FROM employees WHERE biometric_rfid IS NULL OR biometric_rfid = '' ORDER BY name ASC";
                        $empRes = $conn->query($empQ);

                        if (!$empRes) {
                            echo '<option value="" disabled class="text-danger">SQL Error: ' . htmlspecialchars($conn->error) . '</option>';
                        } elseif ($empRes->num_rows > 0) {
                            while($e = $empRes->fetch_assoc()) {
                                echo '<option value="'.htmlspecialchars($e['employee_id']).'">'.htmlspecialchars($e['name']).' ('.htmlspecialchars($e['employee_id']).')</option>';
                            }
                        } else {
                            echo '<option value="" disabled>No unassigned employees found</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label font-weight-bold small text-secondary">2. Scan Physical RFID Badge</label>
                    <div class="scanner-wait-box" id="scannerZone">
                        <span id="scanPromptText"><i class="fa-solid fa-fingerprint fa-bounce me-2 text-primary"></i>Click here & Tap Badge</span>
                        <span id="rfid_uid_display" class="d-none"></span>
                        
                        <input type="text" id="rfid_capture_input" name="rfid_uid" autocomplete="off">
                    </div>
                </div>

                <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4" style="background-color:#2563eb; border:none; border-radius:8px; font-weight:500;">Assign Credential Map</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const hiddenInput = document.getElementById('rfid_capture_input');
    const zoneBox = document.getElementById('scannerZone');
    const promptText = document.getElementById('scanPromptText');
    const displayVal = document.getElementById('rfid_uid_display');

    hiddenInput.focus();
    document.addEventListener('click', () => hiddenInput.focus());

    hiddenInput.addEventListener('input', function() {
        if(this.value.trim().length > 2) {
            promptText.classList.add('d-none');
            displayVal.innerText = this.value.toUpperCase();
            displayVal.classList.remove('d-none');
            zoneBox.style.borderStyle = 'solid';
            zoneBox.style.borderColor = '#2563eb';
            zoneBox.style.background = '#eff6ff';
        }
    });
});
</script>

</body>
</html>
<?php $conn->close(); ?>