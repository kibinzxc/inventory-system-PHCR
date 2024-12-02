<?php
session_start(); // Make sure to start the session
include 'src/db/config.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establish connection to your MySQL database

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $houseNo = $_POST['houseNo'];
    $street = $_POST['street'];
    $baranggay = $_POST['baranggay'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $zipCode = $_POST['zipCode'];
    $contactNum = $_POST['contactNum'];
    $email = $_POST['email'];
    //empty fields validation
    if (empty($firstName) || empty($lastName) || empty($houseNo) || empty($street) || empty($baranggay) || empty($city) || empty($province) || empty($zipCode) || empty($contactNum) || empty($email)) {
        $_SESSION['errorMessage1'] = "All fields are required";
    } //check name, enable space but only letters
    else if (!preg_match("/^[a-zA-Z ]*$/", $firstName) || !preg_match("/^[a-zA-Z ]*$/", $lastName)) {
        $_SESSION['errorMessage1'] = "Invalid name format";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['errorMessage1'] = "Invalid email format";
    } else if (!preg_match("/^[0-9]{11}+$/", $contactNum)) {
        $_SESSION['errorMessage1'] = "Invalid contact number format";
    } else if (!preg_match("/^[0-9]{4}+$/", $zipCode)) {
        $_SESSION['errorMessage1'] = "Invalid zip code format";
    } else {
        //combine the firstname and lastName
        $fullName = $lastName . ", " . $firstName;
        $fullName = strtoupper($fullName);
        //combine the address
        $fullAddress = $houseNo . ", " . $street . ", " . $baranggay . ", " . $city . ", " . $province . ", " . $zipCode;

        //put all the data into session
        $_SESSION['fullName'] = $fullName;
        $_SESSION['fullAddress'] = $fullAddress;
        $_SESSION['contactNum'] = $contactNum;
        $_SESSION['email'] = $email;


        header('location: register2.php');
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
    <link rel="stylesheet" href="src/pages/Ordering/css/register.css">
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
                        <h2 class="title">Create Account</h2><br><br><br><br>
                        <form action="" method="post">
                            <div class="box-wrapper" style="padding:0 20px 0 20px;">
                                <div class="box">
                                    <div class="box-content" style="margin:20px 0 50px 0;">

                                        <div class="row" style="margin-bottom:20px">
                                            <div class="col-sm-12">
                                                <label for="name">First Name</label>
                                                <input type="text1" id="name" name="firstName"
                                                    value=""
                                                    placeholder="Enter your first name">
                                            </div>
                                        </div>

                                        <div class="row" style="margin-bottom:20px">
                                            <div class="col-sm-12">
                                                <label for="name">Last Name</label>
                                                <input type="text1" id="name" name="lastName"
                                                    value=""
                                                    placeholder="Enter your last name">
                                            </div>

                                        </div>
                                        <div class="row" style="margin-bottom:20px">
                                            <div class="col-sm-12">
                                                <label for="contact">Contact Number</label>
                                                <input type="text1" id="contact" name="contactNum"
                                                    value="" placeholder="Enter your contact number">
                                            </div>

                                        </div>

                                        <?php
                                        if (isset($_SESSION['errorMessage1']) && !empty($_SESSION['errorMessage1'])) {
                                            echo '<div class="error" id="message-box">';
                                            echo $_SESSION['errorMessage1'];
                                            unset($_SESSION['errorMessage1']);
                                            echo '</div>';
                                        } ?>
                                        <div class="edit">
                                            <a href="profile.php" class="btn btn-primary" style="color:white;">Cancel</a>
                                            <button type="submit" class="btn btn-primary submit">Register</button>
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