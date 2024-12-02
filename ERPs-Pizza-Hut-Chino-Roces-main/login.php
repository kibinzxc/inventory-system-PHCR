<?php
session_start(); // Make sure to start the session
include 'src/db/config.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if inputs are empty
    if (empty($email) || empty($password)) {
        $_SESSION['message'] = "Please enter both email and password.";
        header("Location: login.php");
        die();
    }


    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Use prepared statements to prevent SQL injection
    $sql = "SELECT * FROM users WHERE email=? AND password=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    // Get user input from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // SQL injection prevention
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    // Hash the password (you should use a more secure hashing algorithm in practice)
    $hashed_password = md5($password);

    // Search for user in the database
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$hashed_password'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();
        $uid = $user['uid'];
        // Check user_type
        $userTypeQuery = "SELECT user_type FROM users WHERE uid = $uid";
        $typeResult = $conn->query($userTypeQuery);

        if ($typeResult && $typeResult->num_rows > 0) {
            $typeRow = $typeResult->fetch_assoc();
            $userType = $typeRow['user_type'];

            if ($userType !== "customer") {
                // Set login failure message
                $_SESSION['message'] = "Login failed.";
            } else {
                // Store user ID in the session
                $_SESSION['uid'] = $uid;
                // Redirect to a logged-in page
                header("Location: index.php");
                die(); // Ensure script stops execution after redirection
            }
        }
    } else {
        // Set login failure message
        $_SESSION['message'] = "Login failed. Invalid username or password.";
    }

    // Redirect to login.php in both cases
    header("Location: login.php");
    die(); // Ensure script stops execution after redirection
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="src/assets/img/pizzahut-logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="src/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="src/bootstrap/css/bootstrap.min.css">
    <script src="src/bootstrap/js/bootstrap.min.js"></script>
    <script src="src/bootstrap/js/bootstrap.js"></script>
    <script src="https://kit.fontawesome.com/0d118bca32.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="src/pages/Ordering/css/login.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-sm-1" style="height:6vh; background:white;">
                <button type="button" class="back-btn" onclick="window.location.href='src/pages/Ordering/menu.php'">
                    <i class="fa-solid fa-arrow-left" style="margin-right:7px;"></i>BACK
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
                        <h1 class="title">Login</h1><br><br><br><br>
                        <form action="" method="post">
                            <div class="user-box">
                                <label>Email</label>
                                <input type="text" name="email" placeholder="username@email.com">
                            </div>
                            <div class="user-box" style="margin-bottom:50px;">
                                <label>Password</label>
                                <input type="password" name="password" placeholder="Password">
                            </div>
                            <?php
                            if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
                                echo '<div class="error" id="message-box">';
                                echo $_SESSION['message'];
                                unset($_SESSION['message']);
                                echo '</div>';
                            }
                            if (isset($_SESSION['update']) && !empty($_SESSION['update'])) {
                                echo '<div class="success" id="message-box">';
                                echo $_SESSION['update'];
                                unset($_SESSION['update']);
                                echo '</div>';
                            }
                            ?>
                            <input type="submit" value="Sign in" class="login-btn" name="login">
                            <div class="additional-links">
                                <p>Don't have an account yet?<a href="register.php" class="register-link">Register here</a></p>
                            </div>
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