<link rel="stylesheet" href="MainContent.css">
<div id="main-content">
    <div class="tooltip" id="tooltip" style="display: none;">Tooltip Text</div>

    <h1>Welcome to My Page</h1>
    <p>The sidebar shows both icons and texttest by default. Click the toggle button to collapse it and show only icons.</p>
    <h1>Dashboard</h1>
    <h2>Session ID: <?php echo session_id(); ?></h2>
    <h2>User Type: <?php echo   $_SESSION['userType']; ?></h2>
    <h2>User ID: <?php echo   $_SESSION['user_id']; ?></h2>
    <h2>Username
        <h2>Email: <?php echo   $_SESSION['email']; ?></h2>

</div>