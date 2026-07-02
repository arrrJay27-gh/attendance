<?php
// Define sidebar links and configurations
$imgPrefix = $imgPrefix ?? '';
$activePage = $activePage ?? 'dashboard';

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

<!-- Reusable Styles Unique to the Sidebar & Layout Structure -->
<style>
    /* Layout Grid System Toggle Rule */
    .app-container { 
        display: grid; 
        grid-template-columns: 310px 1fr; 
        gap: 30px; 
        height: calc(100vh - 40px); 
        width: 100%; 
        position: relative;
        transition: grid-template-columns 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .sidebar-minimized .app-container {
        grid-template-columns: 85px 1fr;
    }

    /* Sidebar Base Styling matching Screenshot 2026-07-02 111211_2.png */
    .sidebar {
        width: 100%;
        background-color: #dcdddf; 
        border-radius: 36px;       
        padding: 45px 0 35px 0;
        display: flex;
        flex-direction: column;
        position: relative;
        height: 100%;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .sidebar-header {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 35px;
        width: 100%;
        position: relative;
        padding: 0 20px;
    }
    
    .logo-container {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
    }
    
    .logo-img {
        max-width: 140px;
        height: auto;
        transition: max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s ease;
    }
    
    .sidebar-toggle-btn {
        position: absolute;
        top: 10px;
        right: -13px;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background-color: #ffffff;
        border: none;
        box-shadow: 0 2px 6px rgba(0,0,0,0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 100;
        color: #52525b;
    }
    
    .sidebar-toggle-btn i {
        font-size: 11px;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .nav-links {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 6px;
        flex-grow: 1;
    }
    
    .nav-item {
        width: 100%;
    }
    
    .nav-item a {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 15px 35px;
        color: #434850; 
        text-decoration: none;
        font-size: 16px;
        font-weight: 600;
        transition: color 0.2s;
    }
    
    /* Flush Inset Active Styling Layout */
    .nav-item.active a {
        background-color: #ffffff;
        color: #11161e;
        border-top-right-radius: 18px;
        border-bottom-right-radius: 18px;
        margin-right: 20px;
        padding-left: 35px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }
    
    .nav-item a i.icon {
        font-size: 20px;
        width: 26px;
        text-align: center;
        color: #434850;
    }
    
    .nav-item.active a i.icon {
        color: #11161e;
    }
    
    .sidebar-footer {
        margin-top: auto;
    }
    
    .logout-btn {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 15px 35px;
        color: #434850;
        text-decoration: none;
        font-size: 16px;
        font-weight: 600;
    }
    
    /* Responsive State Adaptive Reductions */
    .sidebar-minimized .sidebar {
        padding: 45px 0 35px 0;
    }
    
    .sidebar-minimized .sidebar .logo-img {
        max-width: 40px; 
        transform: scale(1);
    }
    
    .sidebar-minimized .sidebar .nav-item a span,
    .sidebar-minimized .sidebar .logout-btn span {
        display: none;
    }
    
    .sidebar-minimized .sidebar .nav-item a {
        justify-content: center;
        padding: 15px 0;
    }
    
    .sidebar-minimized .sidebar .nav-item.active a {
        margin-right: 10px;
        padding-left: 0;
        border-radius: 0 16px 16px 0;
    }
    
    .sidebar-minimized .sidebar .logout-btn {
        justify-content: center;
        padding: 15px 0;
    }
    
    .sidebar-minimized .sidebar-toggle-btn i {
        transform: rotate(180deg);
    }
</style>

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

<!-- Toggle Logic Controller -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
        if(toggleSidebarBtn) {
            toggleSidebarBtn.addEventListener('click', function() {
                const isMinimized = document.documentElement.classList.toggle('sidebar-minimized');
                localStorage.setItem('sidebarMinimized', isMinimized);
            });
        }
    });
</script>