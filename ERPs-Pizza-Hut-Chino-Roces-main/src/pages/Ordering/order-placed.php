<?php
session_start();
include 'connection/database-conn.php';
include 'connection/database-db.php';
// Check if user is logged in
if (isset($_SESSION['uid'])) {
    $loggedIn = true;
    $currentUserId = $_SESSION['uid'];
    // Database connection details

    $sql = "SELECT * FROM orders WHERE uid = $currentUserId";
    $resultz = $conn->query($sql);
    $row1 = $resultz->fetch_assoc();
    $orderStatus1 = $row1['status'];
    if ($orderStatus1 !== "placed" && $orderStatus1 !== "preparing" && $orderStatus1 !== "ready for pickup" && $orderStatus1 !== "delivery") {
        // Redirect to the specific page
        header("Location: order.php"); // Replace "/specific-page.php" with the actual page URL
        exit(); // Ensure that no further code is executed after the redirection
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
    $isCartEmpty = true;
    $sqlCartCheck = "SELECT * FROM cart WHERE uid = $currentUserId";
    $resultCartCheck = $conn->query($sqlCartCheck);


    $sqlactiveOrder = "SELECT * FROM cart WHERE uid = $currentUserId";
    $resultactiveOrder = $conn->query($sqlactiveOrder);
    $orderActive = false;

    if ($resultCartCheck->num_rows > 0) {
        $isCartEmpty = false;
    }
    if ($resultactiveOrder->num_rows > 0) {
        $orderActive = true;
    }
    if ($orderActive && $isCartEmpty) {
        header("Location: menu.php");
        exit();
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //if submit button pressed direct to confirm-cancel.php
    header("Location: confirm-cancel.php");
    exit();
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
                    <a href="order.php" class="item active" id="orderLink">
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
                        <a href="profile.php" class="item">
                            <i class="fa-solid fa-user"></i>
                            <span>Profile</span>
                        </a>
                        <a href="order-placed.php?logout=1" class="item">
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
            <div class="col-sm-11 wrap" style="padding:15px; height:100vh;">
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
                            <h2><i class="fa-solid fa-utensils" style="margin-left:5px;"></i> My Orders</h2>
                            <div class="upper-buttons">
                                <a href="order-history.php" class="btn btn-primary" style="margin-top:10px;"><i
                                        class="fa-solid fa-file-invoice"></i> Order History</a>
                                <a href="menu.php" class="btn btn-primary" style="margin-top:10px;"><i
                                        class="fa-solid fa-bag-shopping"></i> My Bag</a>
                                <a href="messages.php" class="btn btn-primary" style="margin-top:10px;"><i
                                        class="fa-solid fa-bell"></i> Messages</a>
                            </div>
                            <hr>
                            <?php
                            // Assuming you have a database connection established as $db and $currentUserId is defined

                            $sql = "SELECT * FROM orders WHERE uid = $currentUserId AND status != 'cancelled' ORDER BY orderID DESC LIMIT 1";
                            $result = $db->query($sql);
                            $results = $db->query($sql);
                            $rowz = $results->fetch_assoc();

                            $totalPrice = $rowz['totalPrice'];
                            $orderPlaced = $rowz['orderPlaced'];
                            // Create a DateTime object from the input data
                            $dateTime = new DateTime($orderPlaced);
                            // Format date as M-D-Y
                            $date = $dateTime->format("M-d-Y");

                            // Format time with AM/PM
                            $time = $dateTime->format("h:i A");

                            $orderDelivered = $rowz['orderDelivered'];
                            $dateTime1 = new DateTime($orderDelivered);
                            $date1 = $dateTime1->format("M-d-Y");
                            $time1 = $dateTime1->format("h:i A");

                            $orderStatus = $rowz['status'];

                            //if ready for pickup make it 'waiting for delivery' 
                            if ($orderStatus === "ready for pickup") {
                                $orderStatus = "waiting for delivery";
                            }
                            function getBadgeClass($status)
                            {
                                switch ($status) {
                                    case "placed":
                                        return "badge bg-secondary";
                                    case "preparing":
                                        return "badge custom-warning";
                                    case "waiting for delivery":
                                        return "badge custom-warning";
                                    case "delivery":
                                        return "badge custom1-warning";
                                    case "delivered":
                                        return "badge bg-success";
                                    default:
                                        return "badge bg-secondary"; // Default to secondary if status is not recognized
                                }
                            }
                            // Generate HTML based on the order status
                            $badgeClass = getBadgeClass($orderStatus);
                            $html = "<p  >Order Status: <span class=\"$badgeClass\" style= 'font-size:1rem; color:white'>" . ucfirst($orderStatus) . "</span></p>";
                            function shouldDisplayDeliveryDetails($status)
                            {
                                return $status === "delivered";
                            }
                            $deliveryDate = $date1;
                            $deliveryTime = $time1;

                            $badgeClass = getBadgeClass($orderStatus);
                            $displayDeliveryDetails = shouldDisplayDeliveryDetails($orderStatus);
                            $displayCancelButton = ($orderStatus === "placed");
                            ?>

                            <div class="col-sm-12 cart"
                                style="padding:20px 350px 20px 350px; position:relative; height:85vh; overflow:auto;">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Order #<?php echo $rowz['orderID'] ?></h4>
                                        <div class="edit2">
                                            <form action="" method="post">
                                                <?php if ($displayCancelButton): ?>
                                                    <button type="submit" class="btn btn-primary"
                                                        style="margin-bottom:30px;">
                                                        <i class="fa-solid fa-x"></i> Cancel Order
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <h5>Order Details</h5>
                                                <div style="padding:10px 0 0 30px">
                                                    <p>Order Date: <?php echo $date ?></p>
                                                    <p>Order Time: <?php echo $time ?></p>
                                                    <?php echo $html ?>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <h5>Delivery Address</h5>
                                                <div style="padding:10px 0 0 30px">
                                                    <p>Address: <?php echo $rowz['address'] ?></p>
                                                    <p>Contact Number: <?php
                                                                        $uid = $rowz['uid'];
                                                                        $sql2 = "SELECT * FROM customerinfo where uid = $uid";
                                                                        $resultz = $db->query($sql2);
                                                                        $rows2 = $resultz->fetch_assoc();
                                                                        echo $rows2['contactNum'];
                                                                        ?></p>
                                                    <?php if ($displayDeliveryDetails): ?>
                                                        <p>Delivery Date: <?php echo $deliveryDate; ?></p>
                                                        <p>Delivery Time: <?php echo $deliveryTime; ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12" style="margin-top:30px;">
                                                <h5>Order Items</h5>
                                                <table class="table" style="text-align:center;">
                                                    <thead>
                                                        <tr>
                                                            <th>Item Name</th>
                                                            <th>Size</th>
                                                            <th>Price</th>
                                                            <th>Quantity</th>
                                                        </tr>
                                                    </thead>
                                                    <?php $totalOrderPrice = 0;
                                                    if ($result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            // Check if the 'items' column is not null
                                                            if ($row['items'] !== null) {
                                                                $items_data = json_decode($row['items'], true);

                                                                // Check if json_decode was successful
                                                                foreach ($items_data as $item) {
                                                                    $name = $item['name'];
                                                                    $size = $item['size'];
                                                                    $price = $item['price'];
                                                                    $qty = $item['quantity'];
                                                                    echo ' <tr>
                                                        <td>' . $name . '</td>
                                                        <td>' . $size . '</td>
                                                        <td>₱ ' . $price . '</td>
                                                        <td>' . $qty . '</td>
                                                    </tr>';
                                                                }
                                                            } else {
                                                                // Handle case where 'items' column is null
                                                                echo "'items' column is null";
                                                            }
                                                        }
                                                    } else {
                                                        echo "0 results";
                                                    }

                                                    $vat = $totalPrice * 0.12;
                                                    $vatable = $totalPrice - $vat;
                                                    $deliveryFee = 65;
                                                    $totalAmount = $totalPrice + $deliveryFee;

                                                    ?>

                                                </table>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-12" style="margin-top:20px;">
                                                <h5>Order Summary</h5>
                                                <table class="table" style="text-align:left; margin-top:10px;">
                                                    <tbody>
                                                        <tr class="subtotal">
                                                            <td>Mode of Payment</td>
                                                            <td>Cash on Delivery</td>
                                                        </tr>
                                                        <tr class="subtotal">
                                                            <td>Vatable</td>
                                                            <td>₱ <?php echo number_format($vatable, 2) ?></td>
                                                        </tr>
                                                        <tr class="vat">
                                                            <td>Vat (12%)</td>
                                                            <td>₱ <?php echo number_format($vat, 2) ?></td>
                                                        </tr>
                                                        <tr class="subtotal">
                                                            <td>Subtotal</td>
                                                            <td>₱ <?php echo number_format($totalPrice, 2) ?></td>
                                                        </tr>
                                                        <tr class="lasttotal">
                                                            <td>Delivery Fee</td>
                                                            <td>₱
                                                                <?php echo number_format($deliveryFee, 2) ?></td>
                                                        </tr>
                                                        <tr class="total">
                                                            <td style="color:maroon;">Total</td>
                                                            <td style="color:maroon;">₱
                                                                <?php echo number_format($totalAmount, 2) ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>
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
    <script>
        <?php if ($isCartEmpty && !$hasActiveOrders) : ?>
            document.getElementById('orderLink').classList.add('disabled');
        <?php endif; ?>
    </script>
</body>

</html>