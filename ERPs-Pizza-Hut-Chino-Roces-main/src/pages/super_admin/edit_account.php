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
//submit the form to the database self

if (isset($_POST['createPromotion'])) {
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ph_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Create connection
    $accountID = $_GET['item_id'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];
    $hashed_password = md5($password);
    //check password first if it is valid make sure atleast 8 characters, capital letters, small letters, special cahracters, numbers
    if (!preg_match('/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[a-zA-Z]).{8,}$/', $password)) {
        $_SESSION['error'] = "Invalid Password";
        header("Location: edit_account.php?item_id=$accountID");
        exit();
    }
    //update
    $sql = "UPDATE users SET email = '$email', password = '$hashed_password', user_type = '$user_type' WHERE uid = $accountID";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['update'] = "Account updated successfully";
        header("Location: logs.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $sql . "<br>" . $conn->error;
        header("Location: logs.php");
        exit();
    }
    $conn->close();

    
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
    <link rel="stylesheet" href="css/promotion.css">
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
                    <a href="profile.php" class="item">
                        <i class="fa-solid fa-user"></i>
                        <span>Profile</span>
                    </a>
                    <a href="edit_account.php?logout=1" class="item">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </a>


                </div>
            </div>
            <!-- BEGINNING OF BODY -->

            <div class="col-sm-11 wrap" style="padding:15px; height:100vh;">
                <div class="row">
                    <?php
                        if (isset($_SESSION['update']) && !empty($_SESSION['update'])) {
                            echo '<div class="success" id="message-box">';
                            echo $_SESSION['update'];
                            unset($_SESSION['update']);
                            echo '</div>';
                        }
                        if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
                            echo '<div class="error" id="message-box">';
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            echo '</div>';
                        }
                        //access the email and password of the user from get id 
                       
                            $id = $_GET['item_id'];
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
                            $userQuery = "SELECT uid, email, password FROM users WHERE uid = $id";
                            $result = $conn->query($userQuery);

                            if ($result && $result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                            }
                        
                        
                        ?>


                    <div class="col-sm-12">
                        <div class="wrapper">
                           <h2><i class="fa-solid fa-pen-to-square" style="margin-left:5px;"></i> Edit Account</h2>
                            <div class="upper-buttons">
                                 <a href="logs.php" class="btn btn-primary" style="margin-top:10px;"><i class="fa-solid fa-arrow-left"></i> Back</a>
                            </div>
                            <hr>
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="form-group row" style="padding:50px 400px 20px 400px;">
                                    <label for="title" class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-10" style="margin-bottom:20px;">
                                        <input type="text1" class="form-control" id="title" name="email" value = "<?php echo $row['email'] ?>" required>
                                    </div>
                                                                       <label for="title" class="col-sm-2 col-form-label">User Type</label>
                                    <div class="col-sm-10" style="margin-bottom:20px;">

                                        <select class="form-select" aria-label="Default select example" name="user_type">
                                            <option value="customer">Customer</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                    </div>
                                    <label for="title" class="col-sm-2 col-form-label">Password</label>
                                    <div class="col-sm-10" style="margin-bottom:20px;">
                                        <input type="text1" class="form-control" id="title" name="password" required>
                                    </div>
                                    
                                        
                                        <div class = "col-sm-2"></div>
                                        <div class="col-sm-10">
                                            
                                            <button type="submit" class="btn btn-primary" name="createPromotion">Edit Account</button>
                                            
                                        </div>
                                    </div>
                                    
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