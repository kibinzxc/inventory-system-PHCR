<?php
session_start();
include '../../connection/database.php';


// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute the SQL statement
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

        // Debugging output
        error_log("Entered Password Hash: " . $hashed_input_password);
        error_log("Stored Password Hash: " . $hashed_password);

        // Verify the password using MD5
        if ($hashed_input_password === $hashed_password) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $uid;
            $_SESSION['email'] = $email;
            $_SESSION['userType'] = $userType;

            // Redirect to the dashboard or another page
            header("Location: ../../components/Dashboard/Dashboard.php");
            exit();
        } else {
            // Password is incorrect
            header("Location: ../../pages/login.php?error=Invalid%20email%20or%20password");
            exit();
        }
    } else {
        // User does not exist
        header("Location:  ../../pages/login.php?error=Invalid%20email%20or%20password");
        exit();
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
