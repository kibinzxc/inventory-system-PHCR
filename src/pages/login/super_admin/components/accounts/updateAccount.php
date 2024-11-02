<?php
include '../../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uid = $_POST['uid'];

    // Capitalize all characters in the name and make the email lowercase
    $name = strtoupper($_POST['name']);
    $email = strtolower($_POST['email']);
    $userType = trim($_POST['userType']); // Capture userType without converting to uppercase

    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Basic validation for updating account
    if (!empty($uid)) {
        if (trim($name) === '') {
            header("Location: accounts.php?action=error&reason=name_empty&messages=Name cannot be blank.");
            exit();
        }

        if (trim($email) === '') {
            header("Location: accounts.php?action=error&reason=email_empty&messages=Email cannot be blank.");
            exit();
        }

        if (trim($userType) === '') {
            header("Location: accounts.php?action=error&reason=userType_empty&messages=User type cannot be blank.");
            exit();
        }

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

                    $sql = "UPDATE accounts SET name = ?, email = ?, password = ?, userType = ? WHERE uid = ?";
                    $params = [$name, $email, $hashedPassword, $userType, $uid];
                    $paramTypes = 'ssssi';
                } else {
                    header("Location: accounts.php?action=error&reason=password_mismatch");
                    exit();
                }
            } else {
                header("Location: accounts.php?action=error&reason=password_criteria&messages=Please fill both password fields.");
                exit();
            }
        } else {
            $sql = "UPDATE accounts SET name = ?, email = ?, userType = ? WHERE uid = ?";
            $params = [$name, $email, $userType, $uid];
            $paramTypes = 'sssi';
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

if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];

    $sql = "DELETE FROM accounts WHERE uid = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $uid);
        if ($stmt->execute()) {
            header("Location: accounts.php?action=success&reason=account_deleted");
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

    $conn->close();
} else {
    header("Location: accounts.php?action=error&reason=no_account_specified");
    exit();
}
