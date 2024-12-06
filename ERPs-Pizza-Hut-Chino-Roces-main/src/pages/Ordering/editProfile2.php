<?php
session_start();
include 'connection/database-conn.php';
include 'connection/database-db.php';
// Check if user is logged in
if (isset($_SESSION['uid'])) {
    $loggedIn = true;
    $currentUserId = $_SESSION['uid'];

    $sql = "SELECT address FROM customerInfo WHERE uid = $currentUserId"; // Replace 'users' with your table name
    $result = $conn->query($sql);
    $hasActiveOrders = false;
    $orderStatuses = ["placed", "preparing", "ready for pickup", "delivery"];
    // Query the database to check for orders with specified statuses
    $checkOrdersSql = "SELECT COUNT(*) AS orderCount FROM orders WHERE uid = $currentUserId AND status IN ('" . implode("','", $orderStatuses) . "')";
    $resultOrders = $conn->query($checkOrdersSql);

    if ($resultOrders) {
        $rowOrders = $resultOrders->fetch_assoc();
        $hasActiveOrders = ($rowOrders['orderCount'] > 0);
    }
    // Retrieve the current user's ID from the session
    $currentUserId = $_SESSION['uid'];

    $sql = "SELECT address FROM customerInfo WHERE uid = $currentUserId"; // Replace 'users' with your table name
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userAddress = $row['address']; // Store the user's address in a variable
        $currentUserId = $currentUserId;
    } else {
        $userAddress = "House No, Street, City, Province"; // Set a default value if no address is found
    }
    $userTypeQuery = "SELECT user_type FROM users WHERE uid = $currentUserId";
    $result = $conn->query($userTypeQuery);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userType = $row['user_type'];

        // Check if user_type is "customer"
        if ($userType !== "customer") {
            header("Location: ../../../login.php");
            exit(); // Ensure script stops execution after redirection
        }
    }
    $conn->close();
} else {
    header("Location: ../../../login.php");
}


if (isset($_GET['logout'])) {
    if (isset($_SESSION['uid'])) {

        session_destroy();
        unset($_SESSION['uid']);
    }
    header("Location:../../../login.php");
    exit();
}


$queryz = "SELECT COUNT(*) as unread_count FROM msg_users WHERE status = 'unread' AND uid =" . $_SESSION['uid'];
$result41 = $db->query($queryz);

if ($result41) {
    $row41 = $result41->fetch_assoc();
    $unreadNotificationCount = $row41['unread_count'];
} else {
    $unreadNotificationCount = 0; // Default to 0 if query fails
}

if (isset($_POST['submitz'])) {
    //get the old passsword
    $sql = "SELECT * FROM users WHERE uid = " . $_SESSION['uid'];
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    $password = $_POST['password'];

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
    else if (md5($password) == $row['password']) {
        $_SESSION['errorMessage'] = 'Password must be different from the old password';
    } else {
        $password1 = md5($password);
        mysqli_query($db, "UPDATE users SET password='$password1' WHERE uid=" . $_SESSION['uid']);
        $_SESSION['update'] = "Password has been updated successfully";
        header('location: profile.php');

        exit();
    }
}
$isCartEmpty = true;

if ($loggedIn) {
    $sqlCartCheck = "SELECT * FROM cart WHERE uid = $currentUserId";
    $resultCartCheck = $db->query($sqlCartCheck);

    if ($resultCartCheck->num_rows > 0) {
        $isCartEmpty = false;
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/img/pizzahut-logo.png">
    <title>Edit Profile | Pizza Hut Chino Roces</title>
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
                    <a href="../../../index.php" class="item1">
                        <img class="logo" src="../../assets/img/pizzahut-logo.png" alt="Pizza Hut Logo">
                    </a>
                    <a href="menu.php" class="item">
                        <i class="fa-solid fa-utensils"></i>
                        <span>Menu</span>
                    </a>
                    <a href="order.php" class="item" id="orderLink">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Orders</span>
                    </a>
                    <a href="order-history.php" class="item">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Records</span>
                    </a>
                    <a href="messages.php" class="item-last" id="messagesLink">
                        <i class="fa-solid fa-envelope"></i>
                        <span>Messages</span>
                        <?php

                        $unreadNotificationCount = $unreadNotificationCount;

                        if ($unreadNotificationCount > 0) {
                            echo '<span class="notification-count">' . $unreadNotificationCount . '</span>';
                        }
                        ?>
                    </a>
                    <!-- Toggle Login/Logout link -->
                    <?php if ($loggedIn) : ?>
                        <a href="profile.php" class="item active">
                            <i class="fa-solid fa-user"></i>
                            <span>Profile</span>
                        </a>
                        <a href="editProfile2.php?logout=1" class="item">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Logout</span>
                        </a>
                    <?php else : ?><br><br>
                        <a href="../../../login.php" class="item-login">
                            <i class="fa-solid fa-user"></i>
                            <span>Login</span>
                        </a>
                    <?php endif; ?>

                </div>
            </div>
            <!-- BEGINNING OF BODY -->
            <?php
            //get the user's information
            $sql = "SELECT * FROM customerInfo WHERE uid = $currentUserId";
            $result = $db->query($sql);
            $row = $result->fetch_assoc();
            ?>
            <div class="col-sm-11 wrap" style="padding:15px; height:100vh;">
                <?php
                if (isset($_SESSION['update']) && !empty($_SESSION['update'])) {
                    echo '<div class="success" id="message-box">';
                    echo $_SESSION['update'];
                    unset($_SESSION['update']);
                    echo '</div>';
                }
                if (isset($_SESSION['errorMessage']) && !empty($_SESSION['errorMessage'])) {
                    echo '<div class="error" id="message-box">';
                    echo $_SESSION['errorMessage'];
                    unset($_SESSION['errorMessage']);
                    echo '</div>';
                }
                ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="wrapper">
                            <h2><i class="fa-solid fa-user" style="margin-left:5px;"></i> Profile</h2>
                            <hr>
                            <div class="box-wrapper" style="padding:0 20px 0 20px;">
                                <div class="box">
                                    <div class="box-content" style="margin:20px 0 50px 0;">
                                        <div class="row" style="margin-bottom:20px">
                                            <div class="col-sm-6">
                                                <label for="name">Name</label>
                                                <input type="text" id="name" name="name"
                                                    value="<?php echo $row['name']; ?>" disabled>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="email">Email</label>
                                                <input type="text" id="email" name="email"
                                                    value="<?php echo $row['email']; ?>" disabled>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label for="address">Address</label>
                                                <input type="text" id="address" name="address"
                                                    value="<?php echo $userAddress; ?>" disabled>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="contact">Contact Number</label>
                                                <input type="text" id="contact" name="contact"
                                                    value="<?php echo $row['contactNum']; ?>" disabled>
                                            </div>


                                        </div>
                                    </div>

                                </div>
                                <div class="box">

                                    <h3>Account Information</h3>
                                    <hr>
                                    <div class="box-content">
                                        <div class="row" style="margin-top:20px; margin-bottom:20px;">
                                            <div class="col-sm-6">
                                                <label for="username">User ID</label>
                                                <input type="text" id="username" name="username"
                                                    value="<?php echo $row['uid']; ?>" disabled>
                                            </div>

                                            <div class="col-sm-6">
                                                <form action="" method="post">
                                                    <label for="password">Password</label>
                                                    <input type="password1" name="password" value=""
                                                        placeholder="Enter your new password">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="edit">
                                                    <input type="submit" class="btn btn-primary" name="submitz"
                                                        value="Save"></button>
                                                    <a href="profile.php" class="btn btn-primary">Cancel</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ENDING OF BODY -->
        </div>
        <script>
            <?php if (!$loggedIn) : ?>
                document.getElementById('messagesLink').classList.add('disabled');
                document.getElementById('orderLink').classList.add('disabled');
            <?php endif; ?>
        </script>
        <script>
            setTimeout(function() {
                var messageBox = document.getElementById('message-box');
                if (messageBox) {
                    messageBox.style.display = 'none';
                }
            }, 2000);
        </script>
        <script>
            <?php if ($isCartEmpty && !$hasActiveOrders) : ?>
                document.getElementById('orderLink').classList.add('disabled');
            <?php endif; ?>
        </script>

</body>

</html>