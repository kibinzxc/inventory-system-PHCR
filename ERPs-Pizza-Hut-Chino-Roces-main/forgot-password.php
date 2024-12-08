<?php
session_start(); // Ensure session is started

include 'src/db/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

//set timezone
date_default_timezone_set('Asia/Manila');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Capture form inputs
    $email = strtolower($_POST['email']);

    // Empty fields validation
    if (empty($email)) {
        $_SESSION['errorMessage1'] = "Email is required";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['errorMessage1'] = "Invalid email format";
    } else {
        // Check if email exists in the database
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Email exists, proceed with password reset process
            // Generate a unique token
            $token = bin2hex(random_bytes(50));


            // Store the token in the database with an expiration time
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));
            $sql = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $token, $expiry, $email);
            $stmt->execute();

            // Send reset email (you need to implement the sendResetEmail function)
            sendResetEmail($email, $token);

            $_SESSION['update'] = "A password reset link has been sent to your email.";
            //after sending the email, redirect to the login page
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['errorMessage1'] = "Email not found";
        }
    }
}

function sendResetEmail($email, $token)
{
    // Include PHPMailer autoload if not already included
    // require 'path_to_PHPMailer/PHPMailerAutoload.php'; 

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'phcr.inventory@gmail.com'; // Use your own SMTP username
        $mail->Password = 'qumsybajaxdmuyqv'; // Use a secure app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('phcr.inventory@gmail.com', 'PHCR Notifications');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';

        // Constructing the message with a clickable reset link
        $resetLink = "https://ordering.pizzahut-chinoroces.com/reset-password.php?token=" . $token;
        $message = '<p>Dear User,</p>';
        $message .= '<p>Click the following link to reset your password:</p>';
        $message .= '<p><a href="' . $resetLink . '" target="_blank">Reset your password</a></p>';
        $message .= '<p>If you did not request this, please ignore this email.</p>';
        $message .= '<br><p>Best regards,</p>';
        $message .= '<p>PHCR Inventory System</p>';

        $mail->Body = $message;

        $mail->send();
    } catch (Exception $e) {
        // Optionally log or handle the error if email fails to send
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="icon" href="src/assets/img/pizzahut-logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="src/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="src/bootstrap/css/bootstrap.min.css">
    <script src="src/bootstrap/js/bootstrap.min.js"></script>
    <script src="src/bootstrap/js/bootstrap.js"></script>
    <script src="https://kit.fontawesome.com/0d118bca32.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="src/pages/Ordering/css/register4.css">
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
                        <h2 class="title">Forgot Password</h2><br><br><br>
                        <form action="" method="post">
                            <div class="box-wrapper" style="padding:0 20px 0 20px;">
                                <div class="box">
                                    <div class="box-content" style="margin:20px 0 50px 0;">
                                        <div class="row" style="margin-bottom:20px">
                                            <div class="col-sm-12">
                                                <label for="email">Email</label>
                                                <input type="text" id="email" name="email"
                                                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                                    placeholder="Enter your email">
                                            </div>
                                        </div>

                                        <?php
                                        if (isset($_SESSION['errorMessage1']) && !empty($_SESSION['errorMessage1'])) {
                                            echo '<div class="error" id="message-box">';
                                            echo $_SESSION['errorMessage1'];
                                            unset($_SESSION['errorMessage1']);
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