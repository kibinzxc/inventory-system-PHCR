<?php
include '../../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uid = $_POST['uid'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $position = $_POST['position'];
    $userType = $_POST['userType'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Basic validation
    if (!empty($uid) && !empty($name) && !empty($email) && !empty($position) && !empty($userType)) {
        // Check if password fields are provided
        if (!empty($password) || !empty($confirmPassword)) {
            // Check if both password fields are filled
            if (!empty($password) && !empty($confirmPassword)) {
                if ($password === $confirmPassword) {
                    // Password criteria validation
                    if (strlen($password) < 8) {
                        header("Location: accounts.php?update=error&reason=password_criteria&messages=Password must be at least 8 characters long.");
                        exit();
                    } elseif (!preg_match('/[A-Z]/', $password)) {
                        header("Location: accounts.php?update=error&reason=password_criteria&messages=Password must contain at least one uppercase letter.");
                        exit();
                    } elseif (!preg_match('/[a-z]/', $password)) {
                        header("Location: accounts.php?update=error&reason=password_criteria&messages=Password must contain at least one lowercase letter.");
                        exit();
                    } elseif (!preg_match('/[0-9]/', $password)) {
                        header("Location: accounts.php?update=error&reason=password_criteria&messages=Password must contain at least one number.");
                        exit();
                    } elseif (!preg_match('/[\W_]/', $password)) {
                        header("Location: accounts.php?update=error&reason=password_criteria&messages=Password must contain at least one special character.");
                        exit();
                    }

                    // Hash the password (consider using password_hash() for better security)
                    $hashedPassword = md5($password);

                    // SQL statement with password
                    $sql = "UPDATE accounts 
                            SET name = ?, email = ?, position = ?, userType = ?, password = ? 
                            WHERE uid = ?";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('sssssi', $name, $email, $position, $userType, $hashedPassword, $uid);
                } else {
                    // Redirect with specific error message if passwords don't match
                    header("Location: accounts.php?update=error&reason=password_mismatch");
                    exit();
                }
            } else {
                // If only one password field is filled, redirect with a criteria error
                header("Location: accounts.php?update=error&reason=password_criteria&messages=Please fill both password fields.");
                exit();
            }
        }

        // If password fields are empty, proceed to update without changing the password
        $sql = "UPDATE accounts 
                SET name = ?, email = ?, position = ?, userType = ? 
                WHERE uid = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssi', $name, $email, $position, $userType, $uid);

        // Execute the query
        if ($stmt->execute()) {
            header("Location: Accounts.php?update=success&reason=account_updated");
            exit();
        } else {
            // Redirect with specific SQL error
            header("Location: Accounts.php?update=error&reason=sql_failure");
            exit();
        }

        $stmt->close();
    } else {
        // Redirect with an empty fields error
        header("Location: Accounts.php?update=error&reason=empty_fields");
        exit();
    }
}

$conn->close();
