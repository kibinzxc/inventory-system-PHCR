<?php
session_start(); // Start the session

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Set a flag to indicate that we want to clear localStorage and sessionStorage
echo "<script>
    // Clear both sessionStorage and localStorage
    sessionStorage.clear();  // Clear sessionStorage
    localStorage.clear();    // Clear localStorage

    // Redirect to the login page
    window.location.href = '../../login.php';
</script>";

exit();
