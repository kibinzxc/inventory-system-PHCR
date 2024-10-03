<?php
session_start(); // Start the session

// Check if the user is logged in by checking session variable
if (isset($_SESSION['user_id'])) {
    // User is logged in, redirect to dashboard
    header('Location: ../components/Dashboard/Dashboard.php');
    exit();
}
