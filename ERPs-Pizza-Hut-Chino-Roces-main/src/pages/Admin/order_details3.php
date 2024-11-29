<?php
session_start();
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ph_db";
$db = new mysqli($servername, $username, $password, $dbname);
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id= $_GET['order_id'];
    $newStatus = "delivered";
    //retrieve the details of order
    $retrieveOrder = "SELECT * FROM orders where orderID = $order_id";
    $resultc = $db->query($retrieveOrder);
    $rowc = $resultc->fetch_assoc();
    $orderidQ = $rowc['orderID'];
    $uidq = $rowc['uid'];
    $nameq = $rowc['name'];
    $addressq = $rowc['address'];
    $itemsq = $rowc['items'];
    $totalPriceq = $rowc['totalPrice'];
    $paymentq = $rowc['payment'];
    $del_instructq = $rowc['del_instruct'];
    $orderPlacedq = $rowc['orderPlaced'];
    $statusq = 'delivered';
    
    
    

    
    $sql = "UPDATE orders SET status='$newStatus' WHERE orderID = $order_id";
        if ($db->query($sql) === TRUE) {
            $_SESSION['success'] = "Order status updated successfully!";
            

    
                 $sqlx = "SELECT * FROM orders where orderID = $order_id";
    $resultx = $db->query($sqlx);
    $resultsx = $db->query($sqlx);
    $rowzx = $resultsx->fetch_assoc();
    $uidx = $rowzx['uid'];
    $sql2x = "SELECT * FROM customerinfo where uid = $uidx";
    $resultzx = $db->query($sql2x);
    $rows2x = $resultzx->fetch_assoc();
    //data are uid, title, category, description, image, status
    $titlex = "Order ID#$order_id Status Update";
    $categoryx = "Order status";
    $descriptionx = 
"Your order is now delivered. We hope you enjoy your delicious meal! If you have any questions or need assistance, feel free to reach out. Thank you for choosing our food delivery service, and bon appétit!";
    $imagex = "delivered.png";
    $statusx = "unread";
    $sql3x = "INSERT INTO msg_users (uid, title, category, description, image, status) VALUES ('$uidx', '$titlex', '$categoryx', '$descriptionx', '$imagex', '$statusx')";
    $result3x = $db->query($sql3x);

            $moveOrder = "INSERT INTO success_orders (orderID ,uid, name, address, items, totalPrice, payment, del_instruct, orderPlaced, status) VALUES ('$orderidQ','$uidq', '$nameq', '$addressq', '$itemsq', '$totalPriceq', '$paymentq','$del_instructq', '$orderPlacedq', '$statusq')";
        $result = $db->query($moveOrder);
        $deleteOrder = "DELETE FROM orders WHERE orderID = $orderidQ";
        $result = $db->query($deleteOrder);
            header("Location: order-delivery.php");
        } else {
            echo "Error updating record: " . $db->error;
        }
    exit();
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
    <link rel="stylesheet" href="css/logs2.css">
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
                    <a href="order_details3.php?logout=1" class="item">
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
                            <h2><i class="fa-solid fa-file" style="margin-left:5px;"></i> Order Details</h2>
                            <div class="upper-buttons">
                                 <a href="order-delivery.php" class="btn btn-primary" style="margin-top:10px;"><i class="fa-solid fa-arrow-left"></i>  Back</a>
                                
                            </div>
                            <hr>
                            <?php
                            // Assuming you have a database connection established as $db and $currentUserId is defined
                            $order_id= $_GET['order_id'];
                            $sql = "SELECT * FROM orders where orderID = $order_id";
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
        
                            $orderDelivered= $rowz['orderDelivered'];
                            $dateTime1 = new DateTime($orderDelivered); 
                            $date1 = $dateTime1->format("M-d-Y");
                            $time1 = $dateTime1->format("h:i A");
    
                            $orderStatus = $rowz['status'];
                            function getBadgeClass($status) {
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
                                $html = "<p  >Order Status: <span class=\"$badgeClass\" style= 'font-size:1rem; color:white'>" . ucfirst($orderStatus) . "</span></p>";
                                function shouldDisplayDeliveryDetails($status) {
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
                                        <h4>Order #<?php echo $rowz['orderID']?></h4>
                                        <div class="edit2">
                                            <form action="" method="post">
                                                <button type="submit" class="btn btn-primary"
                                                    style="margin-bottom:30px;">
                                                    <i class="fa-solid fa-check"></i> Order Delivered
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <h5>Order Details</h5>
                                                <div style="padding:10px 0 0 30px">
                                                    <p>Order Date: <?php echo $date?></p>
                                                    <p>Order Time: <?php echo $time?></p>
                                                    <?php echo $html?>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <h5>Delivery Address</h5>
                                                <div style="padding:10px 0 0 30px">
                                                    <p>Name: <?php echo $rowz['name']?></p> 
                                                    <p>Address: <?php echo $rowz['address']?></p>
                                                    <?php if ($displayDeliveryDetails): ?>
                                                    <p>Delivery Date: <?php echo $deliveryDate; ?></p>
                                                    <p>Delivery Time: <?php echo $deliveryTime; ?></p>
                                                    <?php endif; ?>
                                                    <p>Contact Number: <?php 
                                                        $uid = $rowz['uid'];
                                                     $sql2 = "SELECT * FROM customerinfo where uid = $uid";
                                                    $resultz = $db->query($sql2);
                                                    $rows2 = $resultz->fetch_assoc();     
                                                    echo $rows2['contactNum'];
                                                     ?></p>
                                                    <p>Delivery Instructions: <?php echo $rowz['del_instruct']?></p>
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
                                                   echo' <tr>
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
                                                            <td>₱ <?php echo $totalOrderPrice?></td>
                                                        </tr>
                                                        <tr class="lasttotal">
                                                            <td>Delivery Fee</td>
                                                            <td>₱
                                                                <?php echo $deliveryFee?></td>
                                                        </tr>
                                                        <tr class="total">
                                                            <td style="color:maroon;">Total</td>
                                                            <td style="color:maroon;">₱
                                                                <?php echo $totalAmount?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
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