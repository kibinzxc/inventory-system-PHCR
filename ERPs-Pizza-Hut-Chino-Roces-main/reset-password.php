<?php
session_start(); // Ensure session is started

include 'src/db/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Capture form inputs
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate inputs
    // Validate inputs
    if (empty($newPassword) || empty($confirmPassword)) {
        $_SESSION['errorMessage'] = "All fields are required.";
    } elseif ($newPassword !== $confirmPassword) {
        $_SESSION['errorMessage'] = "Passwords do not match.";
    } elseif (strlen($newPassword) < 8) {
        $_SESSION['errorMessage'] = "Password must be at least 8 characters long.";
    } elseif (!preg_match('/[A-Z]/', $newPassword)) {
        $_SESSION['errorMessage'] = "Password must include at least one uppercase letter.";
    } elseif (!preg_match('/[a-z]/', $newPassword)) {
        $_SESSION['errorMessage'] = "Password must include at least one lowercase letter.";
    } elseif (!preg_match('/[0-9]/', $newPassword)) {
        $_SESSION['errorMessage'] = "Password must include at least one number.";
    } elseif (!preg_match('/[\W]/', $newPassword)) {
        $_SESSION['errorMessage'] = "Password must include at least one special character.";
    } else {
        // Check if the token is valid and not expired
        $sql = "SELECT * FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Token is valid, update the password

            $hashedPassword = md5($newPassword);
            $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $hashedPassword, $token);
            $stmt->execute();

            $_SESSION['update'] = "Your password has been reset successfully.";

            // Redirect to the login page
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['errorMessage'] = "Invalid or expired token.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="icon" href="src/assets/img/pizzahut-logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="src/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="src/bootstrap/css/bootstrap.min.css">
    <script src="src/bootstrap/js/bootstrap.min.js"></script>
    <script src="src/bootstrap/js/bootstrap.js"></script>
    <script src="https://kit.fontawesome.com/0d118bca32.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="src/pages/Ordering/css/register.css">
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
                        <h2 class="title">Reset Password</h2><br><br><br>
                        <form action="" method="post">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                            <div class="box-wrapper" style="padding:0 20px 0 20px;">
                                <div class="box">
                                    <div class="box-content" style="margin:20px 0 50px 0;">
                                        <div class="row" style="margin-bottom:20px">
                                            <div class="col-sm-12">
                                                <label for="new_password">New Password</label>
                                                <input type="text" id="new_password" name="new_password" placeholder="Enter your new password">
                                            </div>
                                        </div>
                                        <div class="row" style="margin-bottom:20px">
                                            <div class="col-sm-12">
                                                <label for="confirm_password">Confirm Password</label>
                                                <input type="text" id="confirm_password" name="confirm_password" placeholder="Confirm your new password">
                                            </div>
                                        </div>

                                        <?php
                                        if (isset($_SESSION['errorMessage']) && !empty($_SESSION['errorMessage'])) {
                                            echo '<div class="error" id="message-box">';
                                            echo $_SESSION['errorMessage'];
                                            unset($_SESSION['errorMessage']);
                                            echo '</div>';
                                        }

                                        if (isset($_SESSION['successMessage']) && !empty($_SESSION['successMessage'])) {
                                            echo '<div class="success" id="message-box">';
                                            echo $_SESSION['successMessage'];
                                            unset($_SESSION['successMessage']);
                                            echo '</div>';
                                        }
                                        ?>

                                        <div class="edit">
                                            <button type="submit" class="btn btn-primary submit">Submit</button>
                                        </div>

                                        <div class="additional-links">
                                            <p>Remember your password?<a href="login.php" class="register-link">Login</a></p>
                                        </div>
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