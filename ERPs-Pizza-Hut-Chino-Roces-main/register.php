<?php
session_start(); // Ensure session is started

include 'src/db/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Capture form inputs
    $firstName = strtolower($_POST['firstName']);
    $lastName = strtolower($_POST['lastName']);
    $contactNum = $_POST['contactNum'];
    $terms = isset($_POST['terms']) ? $_POST['terms'] : null;

    // Empty fields validation
    if (empty($firstName)) {
        $_SESSION['errorMessage1'] = "First name is required";
    } else if (empty($lastName)) {
        $_SESSION['errorMessage1'] = "Last name is required";
    } else if (empty($contactNum)) {
        $_SESSION['errorMessage1'] = "Contact number is required";
    } else if (!preg_match("/^[a-zA-Z ]*$/", $firstName)) {
        $_SESSION['errorMessage1'] = "Invalid first name format";
    } else if (!preg_match("/^[a-zA-Z ]*$/", $lastName)) {
        $_SESSION['errorMessage1'] = "Invalid last name format";
    } else if (!preg_match("/^\+63[0-9]{10}$/", "+63" . $contactNum)) { // Ensure +63 is added before validation
        $_SESSION['errorMessage1'] = "Invalid contact number format";
    } else if (empty($terms)) {
        $_SESSION['errorMessage1'] = "You must agree to the Terms and Conditions";
    } else {
        // Add +63 to the contact number
        $contactNum = '+63' . ltrim($contactNum, '0'); // Ensure no leading zeros

        // Store necessary data in session
        $_SESSION['firstName'] = $firstName;
        $_SESSION['lastName'] = $lastName;
        $_SESSION['contactNum'] = $contactNum;

        // Redirect to the next page
        header('location: register2.php');
        exit();
    }
} else {
    // Check if there are any values stored in session for pre-filled inputs
    $firstName = isset($_SESSION['firstName']) ? $_SESSION['firstName'] : '';
    $lastName = isset($_SESSION['lastName']) ? $_SESSION['lastName'] : '';
    $contactNum = isset($_SESSION['contactNum']) ? $_SESSION['contactNum'] : '';
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
                        <h2 class="title">Create Account</h2><br><br><br>
                        <form action="" method="post">
                            <div class="box-wrapper" style="padding:0 20px 0 20px;">
                                <div class="box">
                                    <div class="box-content" style="margin:20px 0 50px 0;">
                                        <div class="row" style="margin-bottom:20px">
                                            <div class="col-sm-12">
                                                <label for="name">First Name</label>
                                                <input type="text" id="name" name="firstName"
                                                    value="<?php echo htmlspecialchars($firstName); ?>"
                                                    placeholder="Enter your first name">
                                            </div>
                                        </div>

                                        <div class="row" style="margin-bottom:20px">
                                            <div class="col-sm-12">
                                                <label for="name">Last Name</label>
                                                <input type="text" id="name" name="lastName"
                                                    value="<?php echo htmlspecialchars($lastName); ?>"
                                                    placeholder="Enter your last name">
                                            </div>
                                        </div>
                                        <div class="row" style="margin-bottom:20px">
                                            <div class="col-sm-12">
                                                <label for="contact">Contact Number</label>
                                                <div class="input-group">
                                                    <!-- Readonly field for the +63 prefix -->
                                                    <span class="input-group-text" id="contact-prefix">+63</span>
                                                    <!-- Input field for the rest of the contact number -->
                                                    <input type="text" id="contact" name="contactNum"
                                                        value="<?php echo isset($_SESSION['contactNum']) ? substr($_SESSION['contactNum'], 3) : ''; ?>"
                                                        placeholder="Mobile number" maxlength="10" oninput="removeLeadingZero(this)">
                                                </div>
                                            </div>
                                        </div>


                                        <div class=" row" style="margin-bottom:20px">
                                            <div class="col-sm-12">
                                                <div class="checkbox-container">
                                                    <input type="checkbox" id="terms" name="terms" value="terms">
                                                    <label for="terms">
                                                        I agree to the <a href="terms-of-use.php" class="register-link">Terms of use</a> and the Pizza Hut Chino Roces <a href="privacy-policy.php">Privacy Policy</a>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <?php
                                        if (isset($_SESSION['errorMessage1']) && !empty($_SESSION['errorMessage1'])) {
                                            echo '<div class="error" id="message-box">';
                                            echo $_SESSION['errorMessage1'];
                                            unset($_SESSION['errorMessage1']);
                                            echo '</div>';
                                        }
                                        ?>

                                        <div class="edit">
                                            <button type="submit" class="btn btn-primary submit">Continue</button>
                                        </div>

                                        <div class="additional-links">
                                            <p>Already have an account?<a href="login.php" class="register-link">Login</a></p>
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

        function removeLeadingZero(input) {
            // Check if the value starts with "0"
            if (input.value.startsWith('0')) {
                // Remove the leading "0"
                input.value = input.value.substring(1);
            }
        }
    </script>
</body>

</html>