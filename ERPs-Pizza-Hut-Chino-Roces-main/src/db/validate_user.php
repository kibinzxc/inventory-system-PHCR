<?php
session_start();

function checkUserRole($allowedUserTypes) {
    if (isset($_SESSION['email']) && isset($_SESSION['user_type'])) {
        $currentUserType = $_SESSION['user_type'];

        if (in_array($currentUserType, $allowedUserTypes)) {
            // User has access to this page
        } else {
            header("Location: ../../../index.php"); // Redirect to login page if the user's user_type is not allowed
            exit();
        }
    } else {
        header("Location: ../../../index.php"); // Redirect to login page if the user is not logged in
        exit();
    }
}
?>
