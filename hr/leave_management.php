<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Management - Kiwi Digital</title>
    
    <!-- Local Bootstrap File Link -->
    <link rel="stylesheet" href="bootstrap.min.css">

    <!-- Font Awesome 6 CDN Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        /* Google Font matching clean modern corporate UI style */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #ffffff; /* Clear white workspace background from image_229862.png */
            font-family: 'Inter', sans-serif;
            height: 100vh;
            overflow: hidden;
        }

        /* Main Application Layout Wrapper */
        .app-container {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        /* ==========================================================================
           SIDEBAR STYLES (MATCHED EXACTLY TO image_229862.png)
           ========================================================================== */
        .sidebar {
            width: 260px;
            height: 100%;
            background-color: #dbdbdb; /* System standard light gray container */
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
            width: 100px; /* Aligned proportions for Kiwi Digital logo header */
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

        /* Highlighted style active state on Leave Requests from reference screenshot */
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

        /* Expanded Submenu Layout Structure */
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
            font-size: 12px;
            color: #777777;
            font-weight: 500;
            background: none !important;
        }

        .sub-menu-list li a:hover {
            color: #111111;
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
            gap: 28px;
        }

        /* Header Text Block Layout with Icon */
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

        /* Horizontal Menu Row (Dark Gray Tab Blocks) */
        .tabs-button-row {
            display: flex;
            gap: 16px;
            width: 100%;
        }

        .tab-block-btn {
            flex: 1;
            background-color: #6b6b6b; /* Muted corporate slate-gray styling */
            color: #ffffff;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: background-color 0.2s;
        }

        .tab-block-btn:hover {
            background-color: #555555;
        }

        /* ==========================================================================
           HERO CARD CONTAINER WITH BACKDROP LAYOUT (MATCHED TO image_229862.png)
           ========================================================================== */
        .leave-banner-card {
            background-color: #dbdbdb; /* Light gray base banner fill */
            border-radius: 20px;
            padding: 48px;
            position: relative;
            min-height: 280px;
            display: flex;
            align-items: center;
            overflow: hidden;
            width: 100%;
        }

        .banner-text-side {
            max-width: 60%;
            z-index: 2;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .banner-headline-title {
            font-size: 36px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .banner-headline-title span {
            color: #febb12; /* Golden yellow accent keyword color highlight */
        }

        .banner-paragraph-sub {
            font-size: 16px;
            color: #999999;
            font-weight: 400;
        }

        /* Layout positioning placeholder for illustration alignment */
        .banner-graphic-artwork-side {
            position: absolute;
            right: 40px;
            bottom: 0;
            top: 0;
            width: 35%;
            background-image: url('https://img.freepik.com/free-vector/meditation-concept-illustration_114360-3941.jpg'); /* Representative clean web artwork mirroring the placeholder */
            background-repeat: no-repeat;
            background-position: center bottom;
            background-size: contain;
            z-index: 1;
        }
    </style>
</head>
<body>

    <div class="app-container">
        
        <!-- SIDEBAR COMPONENT (EXACT image_229862.png SPECIFICATION) -->
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
                    <a href="leave.html">
                        <i class="fa-solid fa-user-plus icon"></i>
                        <span>Leave Requests</span>
                    </a>
                    <!-- Active Expanded Inner Tab Links Stack -->
                    <ul class="sub-menu-list">
                        <li><a href="#">Vacation Leave List</a></li>
                        <li><a href="#">Sick/Medical Leave</a></li>
                        <li><a href="#">Parental Leave List</a></li>
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

            <!-- TABS ACTIONS ROW -->
            <div class="tabs-button-row">
                <button class="tab-block-btn">Leave Settings</button>
                <button class="tab-block-btn">Leave Recall</button>
                <button class="tab-block-btn">Leave History</button>
                <button class="tab-block-btn">Relief Officers</button>
            </div>

            <!-- CARD BANNER CONTAINER FRAME -->
            <div class="leave-banner-card">
                <div class="banner-text-side">
                    <h2 class="banner-headline-title">Manage ALL <span>Leave Applications</span></h2>
                    <p class="banner-paragraph-sub">A relaxed employee is a performing employee.</p>
                </div>
                <!-- Aligned Graphic Space Layer -->
                <div class="banner-graphic-artwork-side"></div>
            </div>

        </main>
    </div>

</body>
</html>