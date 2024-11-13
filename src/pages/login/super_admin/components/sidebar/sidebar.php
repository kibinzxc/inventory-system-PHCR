<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="../Sidebar/Sidebar.css">

<div id="mySidebar" class="sidebar">
    <!-- Logo and Toggle Button -->
    <div class="logo-toggle-container">
        <div class="logo" id="sidebarLogo">
            <img src="../../assets/logo-white.png" alt="Logo">
        </div>
    </div>

    <!-- Sidebar Links -->
    <div class="bglinks">
        <a href="../dashboard/dashboard.php" data-tooltip="Dashboard" class="<?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>"><img src="../../assets/home.svg" alt=""><span class="links">Dashboard</span></a>
        <hr class="hr_style" />
        <a href="../inventory/items.php" data-tooltip="Inventory" class="<?= ($currentPage == 'items.php' || $currentPage == 'transfers-report.php') ? 'active' : '' ?>"><img src="../../assets/package.svg" alt=""><span class="links">Inventory</span></a>
        <a href="#" data-tooltip="Notifications" class="<?= ($currentPage == 'notifications.php') ? 'active' : '' ?>"><img src="../../assets/mail.svg" alt=""><span class="links">Notifications</span> <span class="notification-badge">3</span></a>

        <hr class="hr_style" />
        <a href="../accounts/accounts.php" data-tooltip="Accounts" class="<?= ($currentPage == 'accounts.php') ? 'active' : '' ?>"><img src="../../assets/user.svg" alt=""><span class="links">Accounts</span></a>
    </div>

    <!-- Logout Link -->
    <div class="bglink_footer">
        <a href="../../authentication/logout.php" data-tooltip="Logout" class="logout"><img src="../../assets/log-out.svg" class="logout-icon" alt=""><span class="links">Logout</span></a>
    </div>
</div>