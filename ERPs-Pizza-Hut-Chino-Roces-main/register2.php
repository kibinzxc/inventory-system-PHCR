<?php
session_start(); // Make sure to start the session
include 'src/db/config.php';

$email = $_SESSION['email'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_SESSION['email'];
    //make sure fullName is capitalized
    $fullName = ucwords($_SESSION['fullName']);
    $fullAddress = $_SESSION['fullAddress'];
    $contactNum = $_SESSION['contactNum'];

    //get the password from the form
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $hashed_password = md5($password);
    $hashed_confirmPassword = md5($confirmPassword);

    if ($password != $confirmPassword) {
        $_SESSION['errorMessage'] = "Password does not match";
    }
    if (empty($password)) {
        $_SESSION['errorMessage'] = 'Password is required';
    } else if (strlen($password) < 8) {
        $_SESSION['errorMessage'] = 'Password must be at least 8 characters';
    } else if (!preg_match("#[0-9]+#", $password)) {
        $_SESSION['errorMessage'] = 'Password must include at least one number!';
    } else if (!preg_match("#[A-Z]+#", $password)) {
        $_SESSION['errorMessage'] = 'Password must include at least one uppercase letter!';
    } else if (!preg_match("#[a-z]+#", $password)) {
        $_SESSION['errorMessage'] = 'Password must include at least one lowercase letter!';
    } else if (!preg_match('/[\'^£$%&*()}{@#~?>!<>,|=_+¬-]/', $password)) {
        $_SESSION['errorMessage'] = 'Password must include at least one special character!';
    } //else if password is the same as the old password
    //insert the info of email, user_type = "customer" and password into users
    $sql = "INSERT INTO users (email, user_type, password) VALUES ('$email', 'customer', '$hashed_password')";
    $userResult = $conn->query($sql);
    //if the user is successfully inserted into the database then insert the rest of the info
    if ($userResult) {
        //retrieve the uid from the users with the same email that have sent
        $sql = "SELECT uid FROM users WHERE email = '$email'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $uid = $row['uid'];

        $sql2 = "INSERT INTO customerinfo (uid, name, email, contactNum, address) VALUES ('$uid', '$fullName','$email',  '$contactNum' ,'$fullAddress')";
        $result2 = $conn->query($sql2);
        //message for success
        $_SESSION['update'] = "You have successfully registered!";
        header('location: login.php');
    }

    header('location: login.php');

    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
                <button type="button" class="back-btn" onclick="window.history.back()">
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
                        <h1 class="title">Create Account</h1><br><br><br><br>
                        <form action="" method="post">
                            <div class="user-box" style="margin-bottom:20px;">
                                <label>Email</label>
                                <input type="text" name="email" placeholder="username@email.com" value="<?php echo $email; ?>
                                                                                                                ">
                            </div>
                            <div class="user-box" style="margin-bottom:20px;">
                                <label>Password</label>
                                <input type="password" name="password" placeholder="Password">
                            </div>
                            <div class="user-box" style="margin-bottom:30px;">
                                <label>Confirm Password</label>
                                <input type="password" name="confirmPassword" placeholder="Re-enter Password">
                            </div>
                            <?php
                            if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
                                echo '<div class="error" id="message-box">';
                                echo $_SESSION['message'];
                                unset($_SESSION['message']);
                                echo '</div>';
                            }
                            ?>
                            <input type="submit" value="Sign in" class="login-btn" name="login">

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