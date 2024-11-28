<?php
session_start();
error_reporting(0);

function isUserLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function isAdmin()
{
    return isset($_SESSION['userType']) && $_SESSION['userType'] === 'cashier';
}

if (isUserLoggedIn()) {
    // echo "User is logged in.<br>";

    if (isAdmin()) {
        // echo "User is an admin.";
    } else {
        // echo "User is not an admin.";
        header("Location:  ../../../../login.php?error=User%20not%20cashier");
        exit();
    }
} else {
    // echo "User is not logged in.";
    header("Location: ../../../../login.php?error=User%20not%20logged%20in");
    exit();
}
