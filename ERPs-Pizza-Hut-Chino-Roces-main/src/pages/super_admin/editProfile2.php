<?php
session_start();

// Check if user is logged in
if (isset($_SESSION['uid'])) {
    $loggedIn = true;
    $currentUserId = $_SESSION['uid'];

    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ph_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch user information from the database
    $userQuery = "SELECT uid, email, password FROM users WHERE uid = $currentUserId";
    $result = $conn->query($userQuery);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        // Handle the case when user data is not found
        header("Location: ../../../admin_login.php");
        exit();
    }

    // Close the database connection
    $conn->close();
} else {
    // Redirect to login page if not logged in
    header("Location: ../../../admin_login.php");
    exit();
}
if(isset($_POST['submitz'] )){
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ph_db";

    // Create connection
    $db = new mysqli($servername, $username, $password, $dbname);
    //get the old passsword
    $sql = "SELECT * FROM users WHERE uid = " . $_SESSION['uid'];
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    $password = $_POST['password'];
    
    if (empty($password)) { 
        $_SESSION['errorMessage'] = 'Password is required';
    } else if(strlen($password) < 8){
        $_SESSION['errorMessage'] = 'Password must be at least 8 characters';
    } else if(!preg_match("#[0-9]+#",$password)) {
        $_SESSION['errorMessage'] = 'Password must include at least one number!';
    } else if(!preg_match("#[A-Z]+#",$password)) {
        $_SESSION['errorMessage'] = 'Password must include at least one uppercase letter!';
    } else if(!preg_match("#[a-z]+#",$password)) {
        $_SESSION['errorMessage'] = 'Password must include at least one lowercase letter!';
    } else if(!preg_match('/[\'^£$%&*()}{@#~?>!<>,|=_+¬-]/', $password)) {
    $_SESSION['errorMessage'] = 'Password must include at least one special character!';
    }//else if password is the same as the old password
    else if (md5($password) == $row['password']) {
    $_SESSION['errorMessage'] = 'Password must be different from the old password';
    }
    else {
    $password1 = md5($password);
    mysqli_query($db,"UPDATE users SET password='$password1' WHERE uid=". $_SESSION['uid']);
    $_SESSION['update'] = "Password has been updated successfully";
    header('location: profile.php');

    exit();
    }
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ph_db";

// Create connection
$db= new mysqli($servername, $username, $password, $dbname);
$queryza = "SELECT COUNT(*) as unread_order FROM orders";
$result41a = $db->query($queryza);

if ($result41a) {
    $row41a = $result41a->fetch_assoc();
    $unreadNotificationCount4 = $row41a['unread_order'];
} else {
    $unreadNotificationCount4 = 0; // Default to 0 if query fails
}
if (isset($_GET['logout'])) {
    if (isset($_SESSION['uid'])) {

        session_destroy();
        unset($_SESSION['uid']);
    }
    header("Location:../../../admin_login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/img/pizzahut-logo.png">
    <title>Admin | Pizza Hut Chino Roces</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../../src/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../../src/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/profile.css">
    <script src="../../../src/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../../src/bootstrap/js/bootstrap.js"></script>
    <script src="https://kit.fontawesome.com/0d118bca32.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="js/menu.js"></script>
    <script src="js/search-index.js"></script>
</head>

<body>

    <div class="container-fluid" style="overflow:hidden;">
        <div class="row row-flex">
            <!-- Add the row-flex class -->
            <div class="col-sm-1 custom-width" style="height:100vh;">
                <!-- Add the custom-width class -->
                <div class="sidebar" style="height:100vh;">
                    <a href="dashboard.php" class="item1">
                        <img class="logo" src="../../assets/img/pizzahut-logo.png" alt="Pizza Hut Logo">
                    </a>
                    <a href="dashboard.php" class="item">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="items.php" class="item" id="orderLink">
                        <i class="fa-solid fa-utensils"></i>
                        <span>Items</span>
                    </a>
                    <a href="logs.php" class="item">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Accounts</span>
                    </a>
                    <a href="promotion.php" class="item-last" id="messagesLink">
                        <i class="fa-solid fa-file-pen"></i>
                        <span>Promotion</span>
                    </a>
                    <!-- Toggle Login/Logout link -->
                    <a href="profile.php" class="item active">
                        <i class="fa-solid fa-user"></i>
                        <span>Profile</span>
                    </a>
                    <a href="editProfile2.php?logout=1" class="item">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </a>


                </div>
            </div>
            <!-- BEGINNING OF BODY -->

            <div class="col-sm-11 wrap" style="padding:15px; height:100vh;">

                <div class="row">
                    <?php
                        if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
                            echo '<div class="success" id="message-box">';
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                            echo '</div>';
                        }
                        if (isset($_SESSION['errorMessage']) && !empty($_SESSION['errorMessage'])) {
                            echo '<div class="error" id="message-box">';
                            echo $_SESSION['errorMessage'];
                            unset($_SESSION['errorMessage']);
                            echo '</div>';
                        }
                        ?>

                    <?php 
//get the user details from table users
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ph_db";
$currentUserId = $_SESSION['uid'];
$conn = new mysqli($servername, $username, $password, $dbname);
$sql = "SELECT * FROM users WHERE uid = $currentUserId";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$conn->close();

                    ?>
                    <div class="col-sm-12">
                        <div class="wrapper">
                           <h2><i class="fa-solid fa-user" style="margin-left:5px;"></i> Profile</h2>
                            <hr>
                            <div class="box-wrapper" style="padding:0 20px 0 20px; ">
                                <div class="box" style="margin-bottom:20px;">
                                    <div class="box-content" style="margin:20px 0 20px 0;">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label for="contact">User ID</label>
                                                <input type="text" id="contact" name="contact"
                                                    value="<?php echo $row['uid']; ?>" disabled>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="address">Email</label>
                                                <input type="text" id="address" name="address"
                                                    value="<?php echo $row['email']; ?>" disabled>
                                            </div>



                                        </div>
                                    </div>

                                </div>
                               
                                <div class="box" style="margin-top:50px;">
                                    <h3>Account Information</h3>
                                    <hr>
                                    <div class="box-content">
                                        <div class="row" style="margin-top:20px; margin-bottom:20px;">
                                             <div class="col-sm-6">
                                                    <form action="" method="post">
                                                        <label for="password">Password</label>
                                                        <input type="password1" name="password" value=""
                                                            placeholder="Enter your new password">
                                                </div>
                                            <div class="col-sm-6">
                                                
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                                    <div class="edit">
                                                        <input type="submit" class="btn btn-primary" name="submitz"
                                                            value="Save"></button>
                                                        <a href="profile.php" class="btn btn-primary">Cancel</a>
                                                    </div>
                            </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ENDING OF BODY -->
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