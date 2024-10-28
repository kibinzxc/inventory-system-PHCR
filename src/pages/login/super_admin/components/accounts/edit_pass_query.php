<?php
include '../../connection/database.php';
session_start(); // Start the session to access session variables

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the current user's UID from session
    $uid = $_SESSION['user_id'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Basic validation for updating account
    if (!empty($uid)) {
        $sql = '';
        $params = [];
        $paramTypes = '';

        if (!empty($password) || !empty($confirmPassword)) {
            if (!empty($password) && !empty($confirmPassword)) {
                if ($password === $confirmPassword) {
                    if (strlen($password) < 8) {
                        header("Location: new-pass-container.php?action=error&reason=password_criteria&message=Password must be at least 8 characters long.");
                        exit();
                    } elseif (!preg_match('/[A-Z]/', $password)) {
                        header("Location: new-pass-container.php?action=error&reason=password_criteria&message=Password must contain at least one uppercase letter.");
                        exit();
                    } elseif (!preg_match('/[a-z]/', $password)) {
                        header("Location: new-pass-container.php?action=error&reason=password_criteria&message=Password must contain at least one lowercase letter.");
                        exit();
                    } elseif (!preg_match('/[0-9]/', $password)) {
                        header("Location: new-pass-container.php?action=error&reason=password_criteria&message=Password must contain at least one number.");
                        exit();
                    } elseif (!preg_match('/[\W_]/', $password)) {
                        header("Location: new-pass-container.php?action=error&reason=password_criteria&message=Password must contain at least one special character.");
                        exit();
                    }

                    $hashedPassword = md5($password);

                    $sql = "UPDATE accounts SET password = ? WHERE uid = ?";
                    $params = [$hashedPassword, $uid];
                    $paramTypes = 'si';
                } else {
                    header("Location: new-pass-container.php?action=error&reason=password_mismatch");
                    exit();
                }
            } else {
                header("Location: new-pass-container.php?action=error&reason=password_criteria&message=Please fill both password fields.");
                exit();
            }
        }

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param($paramTypes, ...$params);
            if ($stmt->execute()) {
                header("Location: accounts.php?action=newpass&reason=account_updated");
                exit();
            } else {
                header("Location: new-pass-container.php?action=error&reason=sql_failure");
                exit();
            }
            $stmt->close();
        } else {
            header("Location: new-pass-container.php?action=error&reason=sql_failure");
            exit();
        }
    } else {
        header("Location: new-pass-container.php?action=error&reason=uid_missing&messages=UID is missing.");
        exit();
    }
}
