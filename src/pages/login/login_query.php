<?php
session_start();
include 'super_admin/connection/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT uid, password, userType FROM accounts WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($uid, $hashed_password, $userType);
        $stmt->fetch();

        // Hash the entered password using MD5
        $hashed_input_password = md5($password);

        // Verify the password
        if ($hashed_input_password === $hashed_password) {
            // Set session variables
            $_SESSION['user_id'] = $uid;
            $_SESSION['email'] = $email;
            $_SESSION['userType'] = $userType;

            // Redirect based on userType
            if ($userType === 'admin') {
                header("Location: admin/components/dashboard/Dashboard.php");
            } elseif ($userType === 'super_admin') {
                header("Location: super_admin/components/dashboard/Dashboard.php");
            } elseif ($userType === 'stockman') {
                header("Location: stockman/components/dashboard/Dashboard.php");
            } elseif ($userType === 'rider') {
                header("Location: delivery-rider/components/dashboard/Dashboard.php");
            } elseif ($userType === 'cashier') {
                header("Location: cashier/components/orders/orders.php");
            } else {
                header("Location: pages/login.php?error=Unauthorized%20access");
            }
            exit();
        } else {
            // Invalid password
            header("Location: login.php?error=Invalid%20email%20or%20password");
            exit();
        }
    } else {
        // User does not exist
        header("Location: login.php?error=Account%20does%20not%20exist");
        exit();
    }

    $stmt->close();
}

$conn->close();
