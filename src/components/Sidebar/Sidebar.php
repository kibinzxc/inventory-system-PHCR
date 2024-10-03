<link rel="stylesheet" href="../Sidebar/Sidebar.css">

<div id="mySidebar" class="sidebar">
    <!-- Logo and Toggle Button -->
    <div class="logo-toggle-container">
        <div class="logo" id="sidebarLogo">
            <img src="../../assets/logo-white.png" alt="Logo">
        </div>
        <button class="toggle-btn" onclick="toggleSidebar()" id="toggleSidebar" data-tooltip="Expand">
            <span class="arrow" id="toggleArrow"><img src="../../assets/sidebar.svg" alt=""></span>
        </button>
    </div>

    <!-- Sidebar Links -->
    <div class="bglinks">
        <a href="#" data-tooltip="Dashboard"><img src="../../assets/home.svg" alt=""><span class="links">Dashboard</span></a>
        <hr class="hr_style" />
        <a href="../../components/inventory.php" data-tooltip="Inventory"><img src="../../assets/package.svg" alt=""><span class="links">Inventory</span></a>
        <a href="../../components/products.php" data-tooltip="Products"><img src="../../assets/archive.svg" alt=""><span class="links">Products</span></a>
        <hr class="hr_style" />
        <a href="../Accounts/Accounts.php" data-tooltip="Accounts"><img src="../../assets/user.svg" alt=""><span class="links">Accounts</span></a>
        <a href="#" data-tooltip="Notifications"><img src="../../assets/mail.svg" alt=""><span class="links">Notifications</span> <span class="notification-badge">3</span></a>
    </div>

    <!-- Logout Link -->
    <div class="bglink_footer">
        <a href="../../authentication/logout.php" data-tooltip="Logout" class="logout"><img src="../../assets/log-out.svg" class="logout-icon" alt=""><span class=" links">Logout</span></a>
    </div>
</div>