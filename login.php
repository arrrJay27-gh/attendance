<?php
// Prevent browser caching of this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If the user is already logged in, skip straight to the dashboard.
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require_once 'database.php';

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Please enter both email and password.";
    } else {
        $database = new Database();
        $conn = $database->getConnection();

        $sql = "SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result ? $result->fetch_assoc() : null;
            $stmt->close();

            if ($user) {
                // FIXED PASSWORD CHECK: Checks for secure PHP hash OR matching plaintext password
                $isPasswordCorrect = password_verify($password, $user['password']) || ($password === $user['password']);

                if ($isPasswordCorrect) {
                    session_regenerate_id(true);

                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['full_name'] = $user['name']; 
                    $_SESSION['email']     = $user['email'];

                    header('Location: index.php');
                    exit();
                }
            }
        }

        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kiwi Digital</title>
    
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
            background-color: #e2e2e2;
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            padding: 40px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.02);
            text-align: center;
        }

        .logo-img {
            width: 110px;
            height: auto;
            margin-bottom: 28px;
        }

        .login-title {
            font-size: 26px;
            font-weight: 700;
            color: #111111;
            margin-bottom: 8px;
        }

        .login-subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 32px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #334155;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            width: 100%;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            color: #1e293b;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            border-color: #6366f1; 
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
            font-size: 16px;
        }

        .meta-links-row {
            display: flex;
            justify-content: flex-end;
            margin-top: 8px;
            margin-bottom: 24px;
        }

        .forgot-password-link {
            font-size: 13px;
            color: #64748b;
            text-decoration: none;
            font-weight: 400;
        }

        .forgot-password-link:hover {
            color: #6366f1;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #6366f1; 
            border: none;
            border-radius: 8px;
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .submit-btn:hover {
            background-color: #4f46e5;
        }

        .alert-danger-custom {
            background-color: #fff5f5;
            color: #ef4444;
            border: 1px solid #fecaca;
            padding: 10px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 16px;
            text-align: left;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <img src="img/kiwi.png" alt="Kiwi Digital Logo" class="logo-img">
        
        <h2 class="login-title">Kiwi Digital</h2>
        <p class="login-subtitle">We suggest using the email address you use at work.</p>

        <?php if(!empty($error)): ?>
            <div class="alert-danger-custom">
                <i class="fa-solid fa-circle-exclamation me-1"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" autocomplete="off">
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" placeholder="arnold@kiwidigital.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="passwordField" name="password" class="form-input" placeholder="kiwipass123" required>
                    <i class="fa-regular fa-eye-slash password-toggle" id="togglePasswordIcon" onclick="togglePasswordVisibility()"></i>
                </div>
                <div class="meta-links-row">
                    <a href="#" class="forgot-password-link">Forgot password</a>
                </div>
            </div>

            <button type="submit" class="submit-btn">Login</button>
        </form>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById("passwordField");
            const toggleIcon = document.getElementById("togglePasswordIcon");
            
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.remove("fa-eye-slash");
                toggleIcon.classList.add("fa-eye");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.remove("fa-eye");
                toggleIcon.classList.add("fa-eye-slash");
            }
        }
    </script>
</body>
</html>