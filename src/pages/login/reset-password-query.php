<?php
// Include database connection
include_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
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

    // If no errors, proceed to update password
    // Hash the new password
    $hashed_password = md5($new_password);

    // Prepare the SQL query
    $query = "UPDATE accounts SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $hashed_password, $email);

    if ($stmt->execute()) {
        header("Location: forgot-password.php?success=Password%20reset%20successfully");
    } else {
        header("Location: forgot-password.php?error=Failed%20to%20reset%20password");
    }

    $stmt->close();
}

$conn->close();
