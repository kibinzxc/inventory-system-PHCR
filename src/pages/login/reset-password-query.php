<?php
session_start();
include 'super_admin/connection/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs and redirect on error
    if (empty($new_password) || empty($confirm_password)) {
        header("Location: forgot-password.php?error=All%20fields%20are%20required");
        exit();
    } elseif ($new_password !== $confirm_password) {
        header("Location: forgot-password.php?error=Passwords%20do%20not%20match");
        exit();
    } elseif (strlen($new_password) < 8) {
        header("Location: forgot-password.php?error=Password%20must%20be%20at%20least%208%20characters%20long");
        exit();
    } elseif (!preg_match('/[A-Z]/', $new_password)) {
        header("Location: forgot-password.php?error=Password%20must%20include%20at%20least%20one%20uppercase%20letter");
        exit();
    } elseif (!preg_match('/[a-z]/', $new_password)) {
        header("Location: forgot-password.php?error=Password%20must%20include%20at%20least%20one%20lowercase%20letter");
        exit();
    } elseif (!preg_match('/[0-9]/', $new_password)) {
        header("Location: forgot-password.php?error=Password%20must%20include%20at%20least%20one%20number");
        exit();
    } elseif (!preg_match('/[\W]/', $new_password)) {
        header("Location: forgot-password.php?error=Password%20must%20include%20at%20least%20one%20special%20character");
        exit();
    }


    $sql = "SELECT * FROM accounts WHERE reset_token = ? AND reset_token_expiry > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token is valid, update the password

        $hashedPassword = md5($newPassword);
        $sql = "UPDATE accounts SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashedPassword, $token);
        $stmt->execute();

        header("Location: forgot-password.php?success=Password%20reset%20successfully");
        exit();
    } else {
        header("Location: forgot-password.php?error=Failed%20to%20reset%20password");
    }
}
