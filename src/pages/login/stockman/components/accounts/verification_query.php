<?php
session_start(); // Start the session
include '../../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the uid from the session
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../../../login.php?error=User%20not%20logged%20in.");
        exit();
    }

    $uid = $_SESSION['user_id']; // Get the current user's UID from session
    $currentPassword = $_POST['password'];

    // Check if the password field is empty
    if (empty($currentPassword)) {
        header("Location: accounts.php?action=error&reason=password_empty&message=Please enter your current password to proceed.");
        exit();
    }

    // Hash the inputted password using MD5
    $hashedInputPassword = md5($currentPassword);

    // Fetch the user's current hashed password from the database
    $sql = "SELECT password FROM accounts WHERE uid = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $stmt->bind_result($hashedPassword);
        $stmt->store_result(); // Store the result to check the number of rows
        $stmt->fetch();

        // Debugging: Log the number of rows and uid
        error_log("User ID: " . $uid);
        error_log("Number of rows: " . $stmt->num_rows); // Check if the row is found

        // Compare the hashed input password with the hashed password in the database
        if ($hashedInputPassword === $hashedPassword) {
            // Password is correct; redirect to edit_password.php
            header("Location: new-pass-container.php?uid=" . $uid);
            exit();
        } else {
            // Incorrect password message with retrieved password
            header("Location: accounts.php?action=error&reason=incorrect_password&message=Incorrect Password.%20");
            exit();
        }
    } else {
        // SQL preparation failed
        header("Location: accounts.php?action=error&reason=sql_failure&message=An error occurred while processing your request.");
        exit();
    }

    $conn->close();
} else {
    header("Location: accounts.php?action=error&reason=invalid_request&message=Invalid request method.");
    exit();
}
