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
    $orderStatuses = ["placed", "preparing", "delivery"];
    // Query the database to check for orders with specified statuses
    $checkOrdersSql = "SELECT COUNT(*) AS orderCount FROM orders WHERE uid = $currentUserId AND status IN ('" . implode("','", $orderStatuses) . "')";
    $resultOrders = $conn->query($checkOrdersSql);

    if ($resultOrders) {
        $rowOrders = $resultOrders->fetch_assoc();
        $hasActiveOrders = ($rowOrders['orderCount'] > 0);
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

$isCartEmpty = true;


$sqlCartCheck = "SELECT * FROM cart WHERE uid = $currentUserId";
$resultCartCheck = $db->query($sqlCartCheck);

if ($resultCartCheck->num_rows > 0) {
    $isCartEmpty = false;
}
if ($result41) {
    $row41 = $result41->fetch_assoc();
    $unreadNotificationCount = $row41['unread_count'];
} else {
    $unreadNotificationCount = 0; // Default to 0 if query fails
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the details from the cart
    $sqlCart = "SELECT * FROM cart WHERE uid = $currentUserId";
    $resultCart = $db->query($sqlCart);
    $payment = $_POST['flexRadioDefault'];
    $del_instruct = $_POST['del_instruct'];

    if ($resultCart) {
        // Fetch user details
        $sqlUser = "SELECT * FROM customerInfo WHERE uid = $currentUserId";
        $resultUser = $db->query($sqlUser);
        $userDetails = mysqli_fetch_assoc($resultUser);

        // Initialize variables for total price and delivery fee
        $totalPrice = 0;
        $deliveryFee = 50;

        // Initialize an array to store cart items
        $cartItems = array();

        // Iterate through cart items to calculate total price and store item details
        while ($row = mysqli_fetch_assoc($resultCart)) {
            //get the category id from dishes table
            $sqlCategory = "SELECT categoryID FROM dishes WHERE dish_id =" . $row['dish_id'];
            $resultCategory = $db->query($sqlCategory);
            $category = mysqli_fetch_assoc($resultCategory);

            // Check if the categoryID is '1'
            if ($category['categoryID'] == '1') {
                // Append additional text to the size for categoryID '1'
                $size = $row['size'] . " Regular Pan Pizza";
            } else {
                // Use the original size if categoryID is not '1'
                $size = $row['size'];
            }

            // Assuming you have columns 'name', 'size', and 'price' in your cart table
            $cartItems[] = array(
                'name' => $row['name'],
                'size' => $size,
                'price' => $row['price'],
                'qty' => $row['qty'],
                'totalPrice' => $row['totalprice'],
            );

            $totalPrice += $row['totalprice'];
        }

        // Add delivery fee to total price
        $totalPrice += $deliveryFee;

        // Convert cart items array to JSON for storage in the database
        $cartItemsJSON = json_encode($cartItems);

        // Insert order details into the 'test' table
        $insertOrderSql = "INSERT INTO `test` (uid, name, address, items, totalPrice, payment, del_instruct, status)
                           VALUES ('$currentUserId', '{$userDetails['name']}', '{$userDetails['address']}', '$cartItemsJSON', '$totalPrice', '$payment', '$del_instruct', 'placed')";
        $db->query($insertOrderSql);

        $deleteCartSql = "DELETE FROM cart WHERE uid = $currentUserId";
        $db->query($deleteCartSql);

        $_SESSION['success'] = "Order has been placed successfully";
        header("Location: order-placed.php");
    } else {
        // Handle query error for cart
        echo "Error fetching cart: " . $db->error;
    }
} else {
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
    <title>Orders | Pizza Hut Chino Roces</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../../src/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../../src/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/order.css">
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
                    <a href="order-history.php" class="item active">
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
                        <a href="profile.php" class="item">
                            <i class="fa-solid fa-user"></i>
                            <span>Profile</span>
                        </a>
                        <a href="order-history.php?logout=1" class="item">
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
            <div class="col-sm-11 wrap" style="padding:15px; height:100vh; ">
                <?php
                if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
                    echo '<div class="success" id="message-box">';
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    echo '</div>';
                }





                ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="wrapper">
                            <h2><i class="fa-solid fa-file-invoice" style="margin-left:5px;"></i> Order History</h2>
                            <div class="upper-buttons">

                                <a href="menu.php" class="btn btn-primary" style="margin-top:10px;"><i
                                        class="fa-solid fa-bag-shopping"></i> My Bag</a>
                                <a href="messages.php" class="btn btn-primary" style="margin-top:10px;"><i
                                        class="fa-solid fa-bell"></i> Messages</a>
                            </div>
                            <hr>
                            <div class="row" style="padding:20px 400px 0 400px;">
                                <div class="col-sm-12 cart" style="overflow-y: auto; height: 82vh; margin:0; padding:10px 50px 0 50px;">
                                    <?php
                                    $sql = "SELECT * FROM success_orders where uid = $currentUserId ORDER BY orderDelivered desc ";
                                    $result = $db->query($sql);
                                    $result1 = $db->query($sql);
                                    $newrow = mysqli_fetch_array($result1);



                                    if ($result->num_rows > 0) {
                                        $appointment = array();


                                        while ($row = $result->fetch_assoc()) {

                                            $orderPlaced = $row['orderDelivered'];
                                            // Create a DateTime object from the input data
                                            $dateTime = new DateTime($orderPlaced);
                                            // Format date as M-D-Y
                                            $date = $dateTime->format("M-d-Y");

                                            // Format time with AM/PM
                                            $time = $dateTime->format("h:i A");

                                            echo '
                        <div class = "row">
                            <div class="col-md-12" style=";border-radius:10px; background:#E7E7E7; margin-bottom:30px; height:10vh; padding:0;">
                                <div class="row">
                                <div class = "col-sm-3">
                                    <div style = "background-color:#a12c12; border-top-left-radius: 10px;border-bottom-left-radius: 10px; height:10vh; width:75%; padding-top:15px;">
                                        <div style="margin-left:50px;">
                                            <h3 style="color:white;">' . $row['orderID'] . '</h3>
                                        </div>
                                            <p style="margin-left:30px; color:white;">Order ID</p>
                                    </div>
                                </div>
                                <div class = "col-sm-3" style="padding-top:10px;">
                                    <p>Date: ' . $date . '</p>
                                    <p>Time: ' . $time . '</p>
                                </div>
                                <div class = "col-sm-3" style="padding-top:10px;">
                                    <p>Payment: ' . $row['payment'] . '</p>
                                    <p>Total Amount: ₱ ' . $row['totalPrice'] . '</p>
                                </div>
                                <div class = "col-sm-3" style="padding-top:35px;">
                                    <a href="order-details.php?order_id=' . $row['orderID'] . '" style="margin-left:20px; text-decoration:none;">
                                        View Details
                                     <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                                </div>
                            </div>
                        </div>
  

                            

                ';
                                        }
                                    } else {

                                        echo '<p style="text-align:center; margin-top:50px; font-size: 1.5rem;">No Orders Yet</p> ';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            <?php if ($isCartEmpty && !$hasActiveOrders) : ?>
                document.getElementById('orderLink').classList.add('disabled');
            <?php endif; ?>
        </script>
</body>

</html>