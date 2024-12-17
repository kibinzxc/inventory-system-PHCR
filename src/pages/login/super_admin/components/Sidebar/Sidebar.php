<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="../Sidebar/Sidebar.css">

<!-- Sidebar (existing code) -->
<div id="mySidebar" class="sidebar">
    <div class="logo-toggle-container">
        <div class="logo" id="sidebarLogo">
            <img src="../../assets/logo-white.png" alt="Logo">
        </div>
    </div>

    <div class="bglinks">
        <a href="../dashboard/Dashboard.php" data-tooltip="Dashboard" class="<?= ($currentPage == 'Dashboard.php') ? 'active' : '' ?>">
            <img src="../../assets/home.svg" alt=""><span class="links">Dashboard</span>
        </a>
        <a href="../reports/reports.php" data-tooltip="Reports" class="<?= ($currentPage == 'sales.php' || $currentPage == 'reports.php') ? 'active' : '' ?>">
            <img src="../../assets/bar-chart-2.svg" alt=""><span class="links">Reports</span>
        </a>
        <hr class="hr_style" />
        <a href="../inventory/items.php" data-tooltip="Inventory" class="<?= ($currentPage == 'items.php') ? 'active' : '' ?>">
            <img src="../../assets/package.svg" alt=""><span class="links">Inventory</span>
        </a>
        <a href="../orders/orders.php" data-tooltip="Orders" class="<?= ($currentPage == 'orders.php') ? 'active' : '' ?>">
            <img src="../../assets/file-plus.svg" alt=""><span class="links">Orders</span>
        </a>
        <hr class="hr_style" />
        <a href="../accounts/accounts.php" data-tooltip="Accounts" class="<?= ($currentPage == 'accounts.php') ? 'active' : '' ?>">
            <img src="../../assets/user.svg" alt=""><span class="links">Accounts</span>
        </a>
    </div>

    <div class="bglink_footer">
        <a href="../../authentication/logout.php" data-tooltip="Logout" class="logout">
            <img src="../../assets/log-out.svg" class="logout-icon" alt=""><span class="links">Logout</span>
        </a>
    </div>
</div>

<!-- Bottom Navigation for Mobile View -->
<div class="bottom-nav">

    <a id="bottom-nav-reports" href="../reports/reports.php" class="<?= ($currentPage == 'reports.php') ? 'active' : '' ?>">
        <img src="../../assets/bar-chart-2.svg" alt="Reports">
    </a>
    <a id="bottom-nav-inventory" href="../inventory/items.php" class="<?= ($currentPage == 'items.php') ? 'active' : '' ?>">
        <img src="../../assets/package.svg" alt="Inventory">
    </a>

    <a id="bottom-nav-dashboard" href="../dashboard/Dashboard.php" class="<?= ($currentPage == 'Dashboard.php') ? 'active' : '' ?>">
        <img src="../../assets/home.svg" alt="Dashboard">
    </a>
    <a id="bottom-nav-orders" href="../orders/orders.php" class="<?= ($currentPage == 'orders.php') ? 'active' : '' ?>">
        <img src="../../assets/file-plus.svg" alt="Orders">
    </a>
    <a id="bottom-nav-accounts" href="../accounts/accounts.php" class="<?= ($currentPage == 'accounts.php') ? 'active' : '' ?>">
        <img src="../../assets/user.svg" alt="Accounts">
    </a>
    <a id="bottom-nav-logout" href="../../authentication/logout.php">
        <img src="../../assets/log-out.svg" class="logout-icon" alt="">
    </a>
</div>