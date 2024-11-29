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
       $userTypeQuery = "SELECT user_type FROM users WHERE uid = $currentUserId";
    $result = $conn->query($userTypeQuery);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userType = $row['user_type'];

        // Check if user_type is "customer"
        if ($userType !== "admin") {
            header("Location: ../../../admin_login.php");
            exit(); // Ensure script stops execution after redirection
        }
    }
    $conn->close();
} else {
 $loggedIn = false;
        
}


if (isset($_GET['logout'])) {
    if (isset($_SESSION['uid'])) {

        session_destroy();
        unset($_SESSION['uid']);
    }
    header("Location:../../../admin_login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ph_db";

// Create connection
$db= new mysqli($servername, $username, $password, $dbname);

$queryz = "SELECT COUNT(*) as unread_order FROM orders WHERE status = 'preparing'";
$result41 = $db->query($queryz);

if ($result41) {
    $row41 = $result41->fetch_assoc();
    $unreadNotificationCount2 = $row41['unread_order'];
} else {
    $unreadNotificationCount2 = 0; // Default to 0 if query fails
}

$queryz1 = "SELECT COUNT(*) as unread_order FROM orders WHERE status = 'delivery'";
$result411 = $db->query($queryz1);

if ($result411) {
    $row411 = $result411->fetch_assoc();
    $unreadNotificationCount3 = $row411['unread_order'];
} else {
    $unreadNotificationCount3 = 0; // Default to 0 if query fails
}

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
    <link rel="stylesheet" href="css/logs.css">
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
                    <a href="logs.php" class="item active">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Logs</span>
                        <?php
                            
                            $unreadNotificationCount4 = $unreadNotificationCount4; 
                            
                            if ($unreadNotificationCount4 > 0) {
                                echo '<span class="notification-count4">' . $unreadNotificationCount4 . '</span>';
                            }
                        ?>
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
                    <a href="logs.php?logout=1" class="item">
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
                        if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
                            echo '<div class="error" id="message-box">';
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            echo '</div>';
                        }
                        ?>
                    <div class="col-sm-12">
                        <div class="wrapper">
                            <h2><i class="fa-solid fa-file" style="margin-left:5px;"></i> New Orders</h2>
                            <div class="upper-buttons">
                                <?php
                            
                            $unreadNotificationCount2 = $unreadNotificationCount2; 
                            
                            if ($unreadNotificationCount2 > 0) {
                                echo '<span class="notification-count">' . $unreadNotificationCount2 . '</span>';
                            }
                            $unreadNotificationCount3 = $unreadNotificationCount3; 
                            
                            if ($unreadNotificationCount3 > 0) {
                                echo '<span class="notification-count3">' . $unreadNotificationCount3 . '</span>';
                            }
                        ?>
                                 <a href="order-preparing.php" class="btn btn-primary" style="margin-top:10px;"><i class="fa-solid fa-gears"></i> Preparing </a>
                                 <a href="order-delivery.php" class="btn btn-primary" style="margin-top:10px;"><i class="fa-solid fa-truck-fast"></i> Delivery</a>
                                <a href="orders.php" class="btn btn-primary" style="margin-top:10px;"><i class="fa-solid fa-check"></i> Success Orders</a>
                            </div>
                            <hr>
                            <div class = "wrapper2" style="height:80vh; overflow:auto; display: flex; flex-wrap: wrap; padding-left:20px; padding-top:20px;">
                            <?php
                            $servername = "localhost";
                            $username = "root";
                            $password = "";
                            $dbname = "ph_db";  
                            $conn = new mysqli($servername, $username, $password, $dbname);     
                            $sql = "SELECT * FROM orders WHERE status = 'placed'";      
                            $result = $conn->query($sql);   
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    
                             $orderPlaced = $row['orderPlaced'];
                            // Create a DateTime object from the input data
                            $dateTime = new DateTime($orderPlaced);
                            // Format date as M-D-Y
                            $date = $dateTime->format("F j, Y");
                            
                            // Format time with AM/PM
                            $time = $dateTime->format("h:i A");
                                    echo "<div class='card'>";
                                    echo "<div class='card-body'>";
                                    echo "<h5 class='card-title'>Order ID: " . $row["orderID"]. "</h5>";
                                    echo "<p class='card-text'>Order Date: " . $date. "</p>";
                                    echo "<p class='card-text'>Order Time: " . $time. "</p>";
                                    echo "<p class='card-text'>Order Status: " . $row["status"]. "</p>";
                                    echo "<div style='text-align:center;'><a href='order_details.php?order_id=" . $row["orderID"]. "' class='btn btn-primary'>View Order</a></div>";
                                    echo "</div>";
                                    echo "</div>";
                                }
                            } else {
                                echo "<h4 style='margin-left:700px; margin-top:200px;'>No Orders Found</h4>";
                            }
                            $conn->close();
                            ?>
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