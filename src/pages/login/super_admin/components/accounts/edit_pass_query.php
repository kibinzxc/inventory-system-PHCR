<?php
include '../../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uid = $_POST['uid'];
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
                        header("Location: accounts.php?action=error&reason=password_criteria&messages=Password must be at least 8 characters long.");
                        exit();
                    } elseif (!preg_match('/[A-Z]/', $password)) {
                        header("Location: accounts.php?action=error&reason=password_criteria&messages=Password must contain at least one uppercase letter.");
                        exit();
                    } elseif (!preg_match('/[a-z]/', $password)) {
                        header("Location: accounts.php?action=error&reason=password_criteria&messages=Password must contain at least one lowercase letter.");
                        exit();
                    } elseif (!preg_match('/[0-9]/', $password)) {
                        header("Location: accounts.php?action=error&reason=password_criteria&messages=Password must contain at least one number.");
                        exit();
                    } elseif (!preg_match('/[\W_]/', $password)) {
                        header("Location: accounts.php?action=error&reason=password_criteria&messages=Password must contain at least one special character.");
                        exit();
                    }

                    $hashedPassword = md5($password);

                    $sql = "UPDATE accounts SET password = ? WHERE uid = ?";
                    $params = [$hashedPassword, $uid];
                    $paramTypes = 'si';
                } else {
                    header("Location: accounts.php?action=error&reason=password_mismatch");
                    exit();
                }
            } else {
                header("Location: accounts.php?action=error&reason=password_criteria&messages=Please fill both password fields.");
                exit();
            }
        }

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param($paramTypes, ...$params);
            if ($stmt->execute()) {
                header("Location: accounts.php?action=update&reason=account_updated");
                exit();
            } else {
                header("Location: accounts.php?action=error&reason=sql_failure");
                exit();
            }
            $stmt->close();
        } else {
            header("Location: accounts.php?action=error&reason=sql_failure");
            exit();
        }
    } else {
        header("Location: accounts.php?action=error&reason=uid_missing&messages=UID is missing.");
        exit();
    }
}
