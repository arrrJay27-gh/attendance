<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiwi Digital Dashboard</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Google Font matching the clean modern UI style */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
            padding: 20px;
            height: 100vh;
            overflow: hidden;
        }

        /* Main Application Layout Wrapper */
        .app-container {
            display: flex;
            gap: 24px;
            height: calc(100vh - 40px);
            width: 100%;
        }

        /* ==========================================================================
           SIDEBAR STYLES (MATCHED EXACTLY TO image_06c33d.png)
           ========================================================================== */
        .sidebar {
            width: 280px;
            height: 100%;
            background-color: #dbdbdb; 
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            padding: 30px 0;
            flex-shrink: 0;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 40px;
            padding: 0 20px;
        }

        .logo-img {
            width: 65px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .nav-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 4px;
            width: 100%;
        }

        .nav-item {
            width: 100%;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            text-decoration: none;
            padding: 14px 0 14px 35px;
            color: #555555; 
            font-size: 16px;
            font-weight: 600;
            transition: all 0.15s ease;
        }

        .icon {
            font-size: 20px;
            width: 30px;
            margin-right: 14px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #555555;
        }

        /* Active shape logic matching image_06c33d.png */
        .nav-item.active a {
            background-color: #ffffff;
            color: #444444;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
            margin-right: 25px; 
            padding-left: 35px; 
        }

        .nav-item.active .icon {
            color: #444444;
        }

        .nav-item:not(.active) a:hover {
            color: #222222;
            padding-left: 38px;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-left: 35px;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #555555;
            font-size: 13px;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .logout-btn:hover {
            color: #ff3333;
        }

        .logout-btn .icon {
            font-size: 20px;
            margin-right: 10px;
            color: #555555;
        }

        /* ==========================================================================
           DASHBOARD MAIN LAYOUT & GRID
           ========================================================================== */
        .main-content {
            flex-grow: 1;
            padding-top: 10px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .dashboard-header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .dashboard-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
        }

        .search-container {
            position: relative;
            width: 320px;
        }

        .search-container i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
        }

        .search-input {
            width: 100%;
            padding: 10px 14px 10px 40px;
            border-radius: 50px;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            font-size: 13px;
            outline: none;
            color: #334155;
        }

        .dashboard-row-layout {
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 100%;
        }

        .metrics-straight-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            width: 100%;
        }

        .card {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f3f5;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 160px;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
        }

        .card-header i {
            font-size: 16px;
            color: #3b82f6; 
        }

        .card-title {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
            white-space: nowrap;
        }

        .card-value {
            font-size: 32px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 12px;
            line-height: 1;
        }

        .card-footer {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #6b7280;
            white-space: nowrap;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge.positive {
            background-color: #ecfdf5;
            color: #10b981;
        }

        .badge.negative {
            background-color: #fef2f2;
            color: #ef4444;
        }

        .bottom-content-area {
            display: flex;
            justify-content: flex-end; 
            width: 100%;
        }

        /* ==========================================================================
           CALENDAR COMPONENT WITH UNMOVABLE HEADER AND INDEPENDENT NAVIGATION
           ========================================================================== */
        .calendar-card {
            background-color: #f1f5f7; 
            border-radius: 20px;
            padding: 20px;
            width: 340px; 
            box-shadow: inset 0 0 1px rgba(0,0,0,0.05);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-month-title {
            font-size: 22px;
            font-weight: 600;
            color: #111827;
        }

        .calendar-nav-buttons {
            display: flex;
            gap: 8px;
        }

        .cal-btn {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #374151;
            font-size: 12px;
            transition: all 0.2s ease;
            user-select: none;
        }

        .cal-btn:hover {
            background-color: #e5e7eb;
        }

        .calendar-week-strip {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
            text-align: center;
        }

        .day-column {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .day-label {
            font-size: 13px;
            font-weight: 500;
            color: #6b7280;
        }

        .day-number {
            font-size: 15px;
            font-weight: 600;
            color: #1f2937;
            width: 100%;
            padding: 10px 0;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .day-column.active .day-number {
            background-color: #004d4d; 
            color: #ffffff;
            font-weight: 600;
            border-radius: 10px;
        }

        .day-column.active .day-label {
            color: #004d4d;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="app-container">
        
        <!-- SIDEBAR COMPONENT MATCHED EXACTLY TO image_06c33d.png -->
        <nav class="sidebar">
            <div class="logo-container">
                <img src="img/kiwi.png" alt="KIWI DIGITAL TECH INC." class="logo-img">
            </div>

            <ul class="nav-links">
                <li class="nav-item active">
                    <a href="index.php">
                        <i class="fa-solid fa-table-cells-large icon"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="employee.php">
                        <i class="fa-solid fa-users-rectangle icon"></i>
                        <span>Employee</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#">
                        <i class="fa-solid fa-fingerprint icon"></i>
                        <span>Biometric Enrollment</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="timekeeping.php">
                        <i class="fa-solid fa-clipboard-user icon"></i>
                        <span>Timekeeping</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#">
                        <i class="fa-solid fa-right-left icon"></i>
                        <span>Shift Configuration</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="leave.php">
                        <i class="fa-solid fa-user-gear icon"></i>
                        <span>Leave Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#">
                        <i class="fa-solid fa-cubes icon"></i>
                        <span>Internship Registry</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#">
                        <i class="fa-solid fa-square-poll-horizontal icon"></i>
                        <span>System Audit</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <a href="#" class="logout-btn">
                    <i class="fa-solid fa-right-from-bracket icon"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>

        <!-- MAIN VIEW WRAPPER -->
        <main class="main-content">
            
            <div class="dashboard-header-bar">
                <h1 class="dashboard-title">Dashboard</h1>
                <div class="search-container">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" class="search-input" placeholder="Search employees, attendance, reports...">
                </div>
            </div>

            <div class="dashboard-row-layout">
                
                <!-- METRIC CARDS OVERVIEW -->
                <div class="metrics-straight-row">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-circle-check" style="color: #3b82f6;"></i>
                            <span class="card-title">Total Employees Present</span>
                        </div>
                        <div class="card-value">120</div>
                        <div class="card-footer">
                            <span class="badge positive">
                                <i class="fa-solid fa-arrow-up"></i> 5%
                            </span>
                            <span>from yesterday</span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-user-clock" style="color: #3b82f6;"></i>
                            <span class="card-title">Late Arrivals Today</span>
                        </div>
                        <div class="card-value">15</div>
                        <div class="card-footer">
                            <span class="badge positive">
                                <i class="fa-solid fa-arrow-up"></i> 3 people
                            </span>
                            <span>compared to last week</span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-user-minus" style="color: #ef4444;"></i>
                            <span class="card-title">Employees Absent</span>
                        </div>
                        <div class="card-value">8</div>
                        <div class="card-footer">
                            <span class="badge negative">
                                <i class="fa-solid fa-arrow-down"></i> 2 people
                            </span>
                            <span>compared to last Monday</span>
                        </div>
                    </div>

                    <!-- REAL TIME CARD -->
                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-clock" style="color: #3b82f6;"></i>
                            <span class="card-title">Average Check-In Time</span>
                        </div>
                        <div class="card-value" id="live-time-card">--:-- --</div>
                        <div class="card-footer">
                            <span>Consistent with last week</span>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM AREA -->
                <div class="bottom-content-area">
                    
                    <!-- CALENDAR COMPONENT -->
                    <div class="calendar-card">
                        <div class="calendar-header">
                            <div class="calendar-month-title" id="calendar-title">June 25</div>
                            <div class="calendar-nav-buttons">
                                <button class="cal-btn" id="prev-btn"><i class="fa-solid fa-chevron-left"></i></button>
                                <button class="cal-btn" id="next-btn"><i class="fa-solid fa-chevron-right"></i></button>
                            </div>
                        </div>
                        
                        <div class="calendar-week-strip" id="calendar-strip">
                            <!-- Injected by JavaScript dynamically -->
                        </div>
                    </div>
                    
                </div> 

            </div> 
        </main>

    </div>

    <!-- ==========================================================================
       JAVASCRIPT FOR RUNTIMES (CLOCK ENGINE & INDEPENDENT CALENDAR DECOUPLE)
       ========================================================================== -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            
            // ==========================================
            // REAL-TIME CLOCK ENGINE
            // ==========================================
            function updateLiveTimeCard() {
                const now = new Date();
                let hours = now.getHours();
                let minutes = now.getMinutes();
                const ampm = hours >= 12 ? 'PM' : 'AM';
                
                hours = hours % 12;
                hours = hours ? hours : 12; 
                minutes = minutes < 10 ? '0' + minutes : minutes;
                hours = hours < 10 ? '0' + hours : hours;

                document.getElementById('live-time-card').innerText = `${hours}:${minutes} ${ampm}`;
            }
            updateLiveTimeCard();
            setInterval(updateLiveTimeCard, 1000);


            // ==========================================
            // DECOUPLED TRACKING MATRIX ROUTINE
            // ==========================================
            const realToday = new Date(2026, 5, 25); 
            let dayOffsetValue = 0; 

            function renderCalendarView() {
                const dayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                const calendarStrip = document.getElementById('calendar-strip');

                const currentViewDate = new Date(realToday);
                currentViewDate.setDate(realToday.getDate() + dayOffsetValue);

                const currentDayOfWeekIndex = currentViewDate.getDay(); 
                const startOfWeekSunday = new Date(currentViewDate);
                startOfWeekSunday.setDate(currentViewDate.getDate() - currentDayOfWeekIndex);

                calendarStrip.innerHTML = '';

                for (let i = 0; i < 7; i++) {
                    const loopsDay = new Date(startOfWeekSunday);
                    loopsDay.setDate(startOfWeekSunday.getDate() + i);

                    const dayName = dayLabels[i]; 
                    const dayNumber = loopsDay.getDate();

                    const dayColumn = document.createElement('div');
                    dayColumn.classList.add('day-column');

                    if (loopsDay.getDate() === realToday.getDate() && 
                        loopsDay.getMonth() === realToday.getMonth() && 
                        loopsDay.getFullYear() === realToday.getFullYear()) {
                        dayColumn.classList.add('active');
                    }

                    dayColumn.innerHTML = `
                        <span class="day-label">${dayName}</span>
                        <span class="day-number">${dayNumber}</span>
                    `;

                    calendarStrip.appendChild(dayColumn);
                }
            }

            document.getElementById('prev-btn').addEventListener('click', function() {
                dayOffsetValue -= 1; 
                renderCalendarView();
            });

            document.getElementById('next-btn').addEventListener('click', function() {
                dayOffsetValue += 1; 
                renderCalendarView();
            });

            renderCalendarView();
        });
    </script>
</body>
</html>