<?php
session_start();
include 'src/db/config.php';

// Check if user is logged in
if (isset($_SESSION['uid'])) {
    $loggedIn = true;
    $currentUserId = $_SESSION['uid'];

    // Retrieve the current user's ID from the session
    $currentUserId = $_SESSION['uid'];


    header("Location: src/pages/Ordering/menu.php");
    $conn->close();
} else {
    $currentUserId = 123; // or any default value
    $loggedIn = false;
    $userAddress = "";
    header("Location: src/pages/Ordering/menu.php");
}


if (isset($_GET['logout'])) {
    if (isset($_SESSION['uid'])) {

        session_destroy();
        unset($_SESSION['uid']);
    }
    header("Location: login.php");
    exit();
}
