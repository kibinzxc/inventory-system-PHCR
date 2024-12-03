<?php
session_start(); // Make sure to start the session
include 'src/db/config.php';
if (!isset($_SESSION['firstName']) || !isset($_SESSION['lastName']) || !isset($_SESSION['contactNum'])) {
    // If not, redirect the user back to register.php
    header('Location: register.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Input sanitization
    $email = strtolower(trim(preg_replace('/\s+/', '', $_POST['email'])));
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // Validation flag
    $isValid = true;

    // Validation for required fields
    if (empty($email)) {
        $_SESSION['errorMessage'] = 'Email is required.';
        $isValid = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['errorMessage'] = 'Invalid email format.';
        $isValid = false;
    } elseif (empty($password)) {
        $_SESSION['errorMessage'] = 'Password is required.';
        $isValid = false;
    } elseif (strlen($password) < 8) {
        $_SESSION['errorMessage'] = 'Password must be at least 8 characters.';
        $isValid = false;
    } elseif (!preg_match("#[0-9]+#", $password)) {
        $_SESSION['errorMessage'] = 'Password must include at least one number.';
        $isValid = false;
    } elseif (!preg_match("#[A-Z]+#", $password)) {
        $_SESSION['errorMessage'] = 'Password must include at least one uppercase letter.';
        $isValid = false;
    } elseif (!preg_match("#[a-z]+#", $password)) {
        $_SESSION['errorMessage'] = 'Password must include at least one lowercase letter.';
        $isValid = false;
    } elseif (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬\-!]/', $password)) {
        $_SESSION['errorMessage'] = 'Password must include at least one special character.';
        $isValid = false;
    } elseif ($password !== $confirmPassword) {
        $_SESSION['errorMessage'] = 'Passwords do not match.';
        $isValid = false;
    }

    if (!$isValid) {
        $_SESSION['email'] = $email;
        $_SESSION['password'] = $password; // Optional: only if pre-filling passwords is acceptable
        $_SESSION['confirmPassword'] = $confirmPassword;
        header('Location: register2.php');
        exit();
    }

    // Check for duplicate email
    $checkEmailQuery = "SELECT * FROM users WHERE email = '$email'";
    $checkEmailResult = $conn->query($checkEmailQuery);

    if ($checkEmailResult && $checkEmailResult->num_rows > 0) {
        $_SESSION['errorMessage'] = 'Email is already in use';
        header('Location: register2.php');
        exit();
    }
    // Password hashing
    $hashed_password = md5($password);

    // Insert into users table
    $sql = "INSERT INTO users (email, user_type, password) VALUES ('$email', 'customer', '$hashed_password')";
    $userResult = $conn->query($sql);

    if ($userResult) {
        $sql = "SELECT uid FROM users WHERE email = '$email'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $uid = $row['uid'];

        $fullName = ucwords(strtolower($_SESSION['firstName'] . " " . $_SESSION['lastName']));
        $contactNum = $_SESSION['contactNum'];

        $sql2 = "INSERT INTO customerInfo (uid, name, email, contactNum) VALUES ('$uid', '$fullName', '$email', '$contactNum')";
        $result2 = $conn->query($sql2);

        $_SESSION['update'] = "You have successfully registered!";
        header('location: login.php');
        exit();
    } else {
        $_SESSION['errorMessage'] = 'An error occurred. Please try again.';
        header('Location: register2.php');
        exit();
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="icon" href="src/assets/img/pizzahut-logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="src/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="src/bootstrap/css/bootstrap.min.css">
    <script src="src/bootstrap/js/bootstrap.min.js"></script>
    <script src="src/bootstrap/js/bootstrap.js"></script>
    <script src="https://kit.fontawesome.com/0d118bca32.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="src/pages/Ordering/css/register2.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-sm-1" style="height:6vh; background:white;">
                <button type="button" class="back-btn" onclick="window.location.href='src/pages/Ordering/menu.php'">
                    <i class=" fa-solid fa-arrow-left" style="margin-right:7px;"></i>BACK
                </button>
            </div>

            <div class="col-sm-11" style="height:6vh; background:white;">
                <div class="topnav">
                    <a href="index.php">
                        <img class="logo" src="src/assets/img/pizza_hut_horizontal_logo.png">
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 no-padding">

                <div class="backdrop">
                    <img class="full-screen" src="src/assets/img/blurred-login-backdrop.png">
                </div>
                <div class="wrapper">
                    <div class="login-wrapper">
                        <form action="" method="post">
                            <div class="user-box" style="margin-bottom:20px;">
                                <label>Email</label>
                                <input type="text" name="email" placeholder="username@email.com"
                                    value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>">
                            </div>
                            <div class="user-box" style="margin-bottom:20px;">
                                <label>Password</label>
                                <input type="password" name="password" placeholder="Password"
                                    value="<?php echo isset($_SESSION['password']) ? htmlspecialchars($_SESSION['password']) : ''; ?>">
                            </div>
                            <div class="user-box" style="margin-bottom:30px;">
                                <label>Confirm Password</label>
                                <input type="password" name="confirmPassword" placeholder="Re-enter Password"
                                    value="<?php echo isset($_SESSION['confirmPassword']) ? htmlspecialchars($_SESSION['confirmPassword']) : ''; ?>">
                            </div>

                            <?php
                            if (isset($_SESSION['errorMessage']) && !empty($_SESSION['errorMessage'])) {
                                echo '<div class="error" id="message-box">';
                                echo $_SESSION['errorMessage'];
                                unset($_SESSION['errorMessage']);
                                echo '</div>';
                            }
                            ?>
                            <input type="submit" value="Create Account" class="login-btn" name="login">

                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script>
        setTimeout(function() {
            var messageBox = document.getElementById('message-box');
            if (messageBox) {
                messageBox.style.display = 'none';
            }
        }, 2000);
    </script>
</body>

</html>