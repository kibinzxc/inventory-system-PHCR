<?php
session_start();

function isUserLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function isAdmin()
{
    return isset($_SESSION['userType']) && $_SESSION['userType'] === 'admin';
}

if (isUserLoggedIn()) {
    // echo "User is logged in.<br>";

    if (isAdmin()) {
        // echo "User is an admin.";
    } else {
        // echo "User is not an admin.";
        header("Location: ../../pages/login.php?error=User%20not%20admin");
        exit();
    }
} else {
    // echo "User is not logged in.";
    header("Location: ../../pages/login.php?error=User%20not%20logged%20in");
    exit();
}
