<?php
include '../../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uid = $_POST['uid'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Basic validation for updating account
    if (!empty($uid)) {
        // Check if name is blank
        if (trim($name) === '') {
            header("Location: accounts.php?action=error&reason=name_empty&messages=Name cannot be blank.");
            exit();
        }

        // Check if email is blank
        if (trim($email) === '') {
            header("Location: accounts.php?action=error&reason=email_empty&messages=Email cannot be blank.");
            exit();
        }

        // Check if password fields are provided
        if (!empty($password) || !empty($confirmPassword)) {
            // Check if both password fields are filled
            if (!empty($password) && !empty($confirmPassword)) {
                if ($password === $confirmPassword) {
                    // Password criteria validation
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

                    // Hash the password (consider using password_hash() for better security)
                    $hashedPassword = md5($password);

                    // SQL statement with password
                    $sql = "UPDATE accounts 
                            SET name = ?, email = ?, password = ? 
                            WHERE uid = ?";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('sssi', $name, $email, $hashedPassword, $uid);
                } else {
                    // Redirect with specific error message if passwords don't match
                    header("Location: accounts.php?action=error&reason=password_mismatch");
                    exit();
                }
            } else {
                // If only one password field is filled, redirect with a criteria error
                header("Location: accounts.php?action=error&reason=password_criteria&messages=Please fill both password fields.");
                exit();
            }
        }

        // If password fields are empty, proceed to update without changing the password
        $sql = "UPDATE accounts 
                SET name = ?, email = ?
                WHERE uid = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $name, $email, $uid);

        // Execute the query
        if ($stmt->execute()) {
            header("Location: accounts.php?action=success&reason=account_updated");
            exit();
        } else {
            // Redirect with specific SQL error
            header("Location: accounts.php?action=error&reason=sql_failure");
            exit();
        }

        $stmt->close();
    } else {
        // Redirect with a missing UID error
        header("Location: accounts.php?action=error&reason=uid_missing&messages=UID is missing.");
        exit();
    }
}

// Handling account deletion
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
