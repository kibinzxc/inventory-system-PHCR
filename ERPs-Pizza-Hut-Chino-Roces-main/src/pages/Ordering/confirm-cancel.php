<?php
session_start();
include 'connection/database-conn.php';
include 'connection/database-db.php';


if (isset($_SESSION['uid'])) {
    $loggedIn = true;
    $currentUserId = $_SESSION['uid'];
    // Database connection details

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

    $sql = "SELECT * FROM orders WHERE uid = $currentUserId";
    $resultz = $conn->query($sql);
    $row1 = $resultz->fetch_assoc();
    $orderStatus1 = $row1['status'];
    if ($orderStatus1 !== "placed" && $orderStatus1 !== "preparing" && $orderStatus1 !== "delivery") {
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
    $sql = "DELETE FROM orders WHERE uid = $currentUserId";
    if ($db->query($sql) === TRUE) {
        $_SESSION['success'] = "Order has been cancelled successfully";
        header("Location: menu.php");
    } else {
        echo "Error updating record: " . $db->error;
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
                        <a href="order.php?logout=1" class="item">
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

                <div class="row">
                    <div class="col-sm-12">
                        <div class="wrapper">
                            <h2><i class="fa-solid fa-utensils" style="margin-left:5px;"></i> My Orders</h2>
                            <div class="upper-buttons">
                                <a href="order-history.php" class="btn btn-primary" style="margin-top:10px; background-color: #a12c12;
  border-color: #a12c12;
  color: white;"><i
                                        class="fa-solid fa-file-invoice"></i> Order History</a>
                                <a href="menu.php" class="btn btn-primary" style="margin-top:10px; background-color: #a12c12;
  border-color: #a12c12;
  color: white;"><i
                                        class="fa-solid fa-bag-shopping"></i> My Bag</a>
                                <a href="messages.php" class="btn btn-primary" style="margin-top:10px; background-color: #a12c12;
  border-color: #a12c12;
  color: white;"><i
                                        class="fa-solid fa-bell"></i> Messages</a>
                            </div>
                            <hr>
                            <?php
                            // Assuming you have a database connection established as $db and $currentUserId is defined

                            $sql = "SELECT * FROM orders WHERE uid = $currentUserId";
                            $result = $db->query($sql);
                            $results = $db->query($sql);
                            $rowz = $results->fetch_assoc();

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
                            function getBadgeClass($status)
                            {
                                switch ($status) {
                                    case "placed":
                                        return "badge bg-secondary";
                                    case "preparing":
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
                            $html = "<p  >Order Status: <span class=\"$badgeClass\" style= 'font-size:1rem; color:white; '>" . ucfirst($orderStatus) . "</span></p>";
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
                                                        style="margin-bottom:30px; background-color: #a12c12;
  border-color: #a12c12;
  color: white;">
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
                                                            <th>Total Price</th>
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
                                                                    $qty = $item['qty'];
                                                                    $totalPrice = $item['totalPrice'];
                                                                    $totalOrderPrice += $totalPrice;
                                                                    echo ' <tr>
                                                        <td>' . $name . '</td>
                                                        <td>' . $size . '</td>
                                                        <td>₱ ' . $price . '</td>
                                                        <td>' . $qty . '</td>
                                                        <td>₱ ' . $totalPrice . '</td>
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

                                                    $deliveryFee = 50;
                                                    $totalAmount = $totalOrderPrice + $deliveryFee;

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
                                                            <td>Subtotal</td>
                                                            <td>₱ <?php echo $totalOrderPrice ?></td>
                                                        </tr>
                                                        <tr class="lasttotal">
                                                            <td>Delivery Fee</td>
                                                            <td>₱
                                                                <?php echo $deliveryFee ?></td>
                                                        </tr>
                                                        <tr class="total">
                                                            <td style="color:maroon;">Total</td>
                                                            <td style="color:maroon;">₱
                                                                <?php echo $totalAmount ?></td>
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
    <div class="mpopup1" id="mpopup1">
        <div class="modal-content1">
            <div class="modal-header1">
                <h4 style="text-align:center; font-size:2rem;">Cancel Order</h4>
            </div>
            <div class="modal-body1">
                <form method="POST" action="">
                    <p>Are you sure you want to cancel your order?</p>

            </div>
            <div class="modal-footer1">
                <input type="submit" class="confirmation" id="confirmation" value="Confirm" name="confirmation">
                <input type="button" class="cancellation" value="Cancel" name="cancel_btn" onclick="closeModal()">
            </div>

        </div>
    </div>
    </div>
    </form>
    <script>
        function closeModal() {
            window.location.href = 'order-placed.php'

        }
    </script>
    <script>
        setTimeout(function() {
            var messageBox = document.getElementById('message-box');
            if (messageBox) {
                messageBox.style.display = 'none';
            }
        }, 2000);
    </script>
    <?php
    session_start();

    // Check if user is logged in
    if (isset($_SESSION['uid'])) {
        $loggedIn = true;
        $currentUserId = $_SESSION['uid'];
        // Database connection details


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

        $conn->close();
    } else {
        header("Location: menu.php");
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


    function convertDateTimeFormat($dateTime)
    {
        // Convert date to 'F d, Y' format
        $date = date("F d, Y", strtotime($dateTime));

        // Convert time to 12-hour format with AM and PM
        $time = date("h:i A", strtotime($dateTime));

        // Combine date and time
        $convertedDateTime = $date . ' ' . $time;

        return $convertedDateTime;
    }

    if (isset($_GET['delete'])) {
        $notificationId = $_GET['delete'];
        $deleteQuery = "DELETE FROM msg_users WHERE msgID = $notificationId AND uid=" . $_SESSION['uid'];
        if ($db->query($deleteQuery) === TRUE) {
            $_SESSION['success2']  = "Message has been successfully deleted";
            header("Location:archives.php ");
            exit();
        } else {
        }
    }

    if (isset($_GET['inbox'])) {
        $notificationId = $_GET['archive'];
        $updateQuery = "UPDATE msg_users SET status = 'read' WHERE uid =" . $_SESSION['uid'];
        if ($db->query($updateQuery) === TRUE) {
            $_SESSION['success2']  = "Message has been successfully restored";
            header("Location:archives.php ");
            exit();
        } else {
        }
    }

    if (isset($_POST['mark_all_read'])) {
        $deleteQuery = "DELETE FROM msg_users WHERE uid =" . $_SESSION['uid'] . " AND status = 'archived'";

        if ($db->query($deleteQuery) === TRUE) {
            $_SESSION['success2']  = "Message has been successfully deleted";
        } else {
            // Handle deletion error
            echo "Error deleting record: " . $db->error;
        }
    }

    $queryz1 = "SELECT COUNT(*) as archived_count FROM msg_users WHERE status = 'archived' AND uid =" . $_SESSION['uid'];
    $result42 = $db->query($queryz1);

    if ($result42) {
        $row42 = $result42->fetch_assoc();
        $unreadArchivedCount = $row42['archived_count'];
    } else {
        $unreadArchivedCount  = 0; // Default to 0 if query fails
    }

    ?>


    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="../../assets/img/pizzahut-logo.png">
        <title>Archived Messages | Pizza Hut Chino Roces</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="../../../src/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="../../../src/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/messages.css">
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
                            <i class="fa-solid fa-receipt"></i>
                            <span>Records</span>
                        </a>
                        <a href="messages.php" class="item-last active" id="messagesLink">
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
                            <a href="confirm-cancel.php?logout=1" class="item">
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
                <div class="col-sm-11" style="background: white;">
                    <div class="row">
                        <?php
                        if (isset($_SESSION['success2']) && !empty($_SESSION['success2'])) {
                            echo '<div class="success" id="message-box">';
                            echo $_SESSION['success2'];
                            unset($_SESSION['success2']);
                            echo '</div>';
                        }
                        if (isset($_SESSION['error2']) && !empty($_SESSION['error2'])) {
                            echo '<div class="error" id="message-box">';
                            echo $_SESSION['error2'];
                            unset($_SESSION['error2']);
                            echo '</div>';
                        }
                        ?>

                        <?php

                        $sql = "SELECT * FROM msg_users WHERE uid=" . $_SESSION['uid'] . " AND status = 'archived'";
                        $result = $db->query($sql);
                        $result1 = $db->query($sql);
                        $newrow = mysqli_fetch_array($result1);


                        // Check if any notifications were found
                        if ($result->num_rows > 0) {
                            $notifications = array();
                            // Display notifications
                            while ($row = $result->fetch_assoc()) {
                                $notifications[] = $row;
                            }
                            $notifications = array_reverse($notifications);

                            foreach ($notifications as $row) {

                                $category = $row['category'];
                                $iconMapping = [
                                    "Order update" => "fa-solid fa-bell",
                                    "Promotion" => "fa-solid fa-bullhorn",

                                ];
                                $defaultIcon = "bi bi-question"; // Adjust the default icon class name

                                $icon = isset($iconMapping[$category]) ? $iconMapping[$category] : $defaultIcon;
                                // Define an array that maps categories to their corresponding icons


                                $dateString = $row['date_created']; // Your input date string
                                $inputFormat = "Y-m-d"; // Format of the input date string
                                $outputFormat = "F d, Y"; // Desired output format
                                $timestamp = strtotime($dateString);

                                // Format the timestamp into the desired output format
                                $outputDate = date($outputFormat, $timestamp);
                                $dateTime = $row['date_created'];
                                $convertedDateTime = convertDateTimeFormat($dateTime);

                                echo '<a class="notif" style="text-decoration:none; color:black;" href="view-archived.php?id=' . $row['msgID'] . '">
                            <div class="' . $row['status'] . '" style = "padding:20px 20px 5px 20px; width:100%; border-bottom:1px solid #B6B6B6; border-radius:5px; margin-bottom:10px;">
                                <div style = "float:left; margin-top:10px;"> 
                                    
                                    <i class="' . $icon . '" alt="Category Icon" style="font-size:35px; color:#605D5D;"></i>
                                </div>
                                <div style = "float:center; margin-left:60px;">
                                    <h5>' . $row['title'] . '</h5>
                                    <p style="overflow:hidden; white-space: nowrap; text-overflow: ellipsis;">' . $row['category'] . ': ' . $row['description'] . '</p>
                                    <p style = "margin-top:-15px;">' . $outputDate . '</p>
                                    </div>
                            </div>
                            </a>';
                            }
                        } else {
                            echo '<h4 style="text-align:center; margin-top:300px;">No Archived Message Yet</h4>';
                        }
                        ?>
                    </div>
                </div>
                <div class="col-md-7" style="">
                    <h4 style="text-align:center; margin-top:400px;">No Message Selected</h4>
                </div>
            </div>
        </div>
        </div>


        <!-- ENDING OF BODY -->

        <script>
            <?php if (!$loggedIn) : ?>
                document.getElementById('messagesLink').classList.add('disabled');
                document.getElementById('orderLink').classList.add('disabled');
            <?php endif; ?>
        </script>
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
</body>

</html>