<?php
include '../../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $userType = $_POST['userType'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Check if the name and email are empty
    if (empty($name) && empty($email)) {
        header("Location: accounts.php?action=error&reason=empty_fields&message=Name and email cannot be empty.&name=$name&email=$email&userType=$userType");
        exit();
    } elseif (empty($name)) {
        header("Location: accounts.php?action=error&reason=name_empty&message=Name cannot be empty.&email=$email&userType=$userType");
        exit();
    } elseif (empty($email)) {
        header("Location: accounts.php?action=error&reason=email_empty&message=Email cannot be empty.&name=$name&userType=$userType");
        exit();
    } elseif (empty($userType)) {
        header("Location: accounts.php?action=error&reason=userType_empty&message=User type cannot be empty.&name=$name&email=$email");
        exit();
    } elseif (empty($password) || empty($confirmPassword)) {
        // Check if both password fields are filled
        header("Location: accounts.php?action=error&reason=password_empty&message=Both password fields must be filled.&name=$name&email=$email&userType=$userType");
        exit();
    }

    // Continue with further validation if all fields are filled
    if ($password === $confirmPassword) {
        // Password criteria validation
        if (strlen($password) < 8) {
            header("Location: accounts.php?action=error&reason=password_criteria&message=Password must be at least 8 characters long.&name=$name&email=$email&userType=$userType");
            exit();
        } elseif (!preg_match('/[A-Z]/', $password)) {
            header("Location: accounts.php?action=error&reason=password_criteria&message=Password must contain at least one uppercase letter.&name=$name&email=$email&userType=$userType");
            exit();
        } elseif (!preg_match('/[a-z]/', $password)) {
            header("Location: accounts.php?action=error&reason=password_criteria&message=Password must contain at least one lowercase letter.&name=$name&email=$email&userType=$userType");
            exit();
        } elseif (!preg_match('/[0-9]/', $password)) {
            header("Location: accounts.php?action=error&reason=password_criteria&message=Password must contain at least one number.&name=$name&email=$email&userType=$userType");
            exit();
        } elseif (!preg_match('/[\W_]/', $password)) {
            header("Location: accounts.php?action=error&reason=password_criteria&message=Password must contain at least one special character.&name=$name&email=$email&userType=$userType");
            exit();
        }

        // Hash the password (consider using password_hash() for better security)
        $hashedPassword = md5($password);

        // SQL statement for adding a new account
        $sql = "INSERT INTO accounts (name, email, userType, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $name, $email, $userType, $hashedPassword);

        // Execute the query
        if ($stmt->execute()) {
            header("Location: accounts.php?action=add&message=User successfully added.");
            exit();
        } else {
            // Redirect with specific SQL error
            header("Location: accounts.php?action=error&reason=sql_failure&name=$name&email=$email&userType=$userType");
            exit();
        }

        $stmt->close();
    } else {
        // Redirect with a password mismatch error
        header("Location: accounts.php?action=error&reason=password_mismatch&message=Passwords do not match.&name=$name&email=$email&userType=$userType");
        exit();
    }
}

$conn->close();
