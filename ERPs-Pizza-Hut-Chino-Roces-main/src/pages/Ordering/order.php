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
    // Retrieve the current user's ID from the session
    $sql = "SELECT status FROM orders WHERE uid = $currentUserId";
    $result = $conn->query($sql);

    // Check if there is a result
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $order_status = $row['status'];

            // Check if the order status is 'placed' or 'delivery'
            if ($order_status == 'placed' || $order_status == 'delivery' || $order_status == 'preparing') {
                // Redirect to another page
                header("Location: order-placed.php");
                exit();
            }
        }
    }
    $isCartEmpty = true;
    $sqlCartCheck = "SELECT * FROM cart WHERE uid = $currentUserId";
    $resultCartCheck = $conn->query($sqlCartCheck);


    $sqlactiveOrder = "SELECT * FROM cart WHERE uid = $currentUserId";
    $resultactiveOrder = $conn->query($sqlactiveOrder);
    $orderActive = true;

    if ($resultCartCheck->num_rows > 0) {
        $isCartEmpty = false;
    }
    if ($resultactiveOrder->num_rows > 0) {
        $orderActive = false;
    }
    if ($orderActive && $isCartEmpty) {
        header("Location: menu.php");
        exit();
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

// Create connection

$queryz = "SELECT COUNT(*) as unread_count FROM msg_users WHERE status = 'unread' AND uid =" . $_SESSION['uid'];
$result41 = $db->query($queryz);

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
            $sqlCategory = "SELECT category FROM products WHERE dish_id =" . $row['dish_id'];
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
        $insertOrderSql = "INSERT INTO `orders` (uid, name, address, items, totalPrice, payment, del_instruct, status)
                           VALUES ('$currentUserId', '{$userDetails['name']}', '{$userDetails['address']}', '$cartItemsJSON', '$totalPrice', '$payment', '$del_instruct', 'preparing')";
        $db->query($insertOrderSql);
        $deleteCartSql = "DELETE FROM cart WHERE uid = $currentUserId";
        $db->query($deleteCartSql);

        $_SESSION['success'] = "Order has been placed successfully";
        header("Location: order-placed.php");
        exit();
    } else {
        // Handle query error for cart
        echo "Error fetching cart: " . $db->error;
    }
} else {
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
                            <div class="row">
                                <div class="col-sm-5" style="height:85vh; padding:20px 30px 20px 30px;">
                                    <div class="cart-summary" style="height:80vh; width:100%;">
                                        <div class="col-sm-12">
                                            <h3>Order Summary</h3>
                                            <hr>
                                        </div>
                                        <div class="col-sm-12 cart"
                                            style="margin:0 0 0 0; padding:0; height:45vh; overflow-y: scroll; overflow:auto; border-radius:25px; ">
                                            <?php
                                            if ($loggedIn) {
                                                $sql = "SELECT * FROM cart WHERE uid = $currentUserId";
                                                $result = $db->query($sql);
                                                $result1 = $db->query($sql);
                                                $newrow = mysqli_fetch_array($result1);
                                                if ($result->num_rows > 0) {
                                                    $cart = array();
                                                    // Display events
                                                    while ($row = $result->fetch_assoc()) {
                                                        $cart[] = $row;
                                                    }
                                                    $cart = array_reverse($cart);
                                                    foreach ($cart as $row) {
                                                        echo '
                                                            <div class="box" style="padding: 10px;border-radius:10px; margin: 10px 10px 10px 5px; position:relative; margin-left:10px;">
                                                                <div class="container" style="margin:0; padding:0;">
                                                                    <div class="row">
                                                                        <div class="col-sm-4">
                                                                            <div class="image" style="height:100%; width:100%">
                                                                                <img src="../../../src/assets/img/menu/' . $row['img'] . '" alt="notif pic" style="width:100%; max-width:100%; min-width:100px; height:auto; overflow:hidden; border-radius:10px;">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <div class="caption">
                                                                                <p>' . $row['size'] . ' ' . $row['name'] . '</p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-2 bottom-footer">
                                                                            <div class="price">
                                                                                <p><span class="price-display" data-id="' . $row['cart_id'] . '">₱' . $row['price'] . '</span></p>
                                                                                <input type="hidden" class="price" name="price" data-id="' . $row['cart_id'] . '" value="' . $row['price'] . '">
                                                                                <div class="quantity1">
                                                                                    <select class="quantity" name="quantity" data-id="' . $row['cart_id'] . '" disabled>';
                                                        $sizes = explode(',', $row['qty']);

                                                        // Iterate over the 'size' data and create an option for each size
                                                        foreach ($sizes as $size) {
                                                            echo '<option value="' . $size . '">' . ucfirst($size) . '</option>';
                                                        }
                                                        echo '</select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            ';
                                                    }
                                                } else {
                                                    echo '<p style="text-align:center; margin-top:50px;">Add Items to your Bag</p> ';
                                                }
                                            } else {
                                                echo '<p style="text-align:center; margin-top:50px;">Please Login to Continue</p> ';
                                            }
                                            ?>
                                        </div>
                                        <div class="col-sm-12" style="margin: 30px 0 0 0;">
                                            <div class="linebreak" style="margin:0 15px 0 5px;">
                                                <hr style="height:2px;">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-sm-6" style="padding:0 0 0 80px; margin:0;">
                                                        <p style="font-weight:550">Sub Total</p>
                                                        <p style="font-weight:550">Delivery Fee</p>
                                                    </div>
                                                    <div class="col-sm-6" style="padding:0 0 0 80px; margin:0;">
                                                        <p id="subtotal" style="margin-left: 30px; font-weight:bold;">
                                                        </p>
                                                        <p id="delivery_fee"
                                                            style="margin-left:30px; font-weight:bold;"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="linebreak" style="margin:0 15px 0 5px;">
                                                <hr style="height:2px;">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-sm-6" style="padding:0 0 0 80px; margin:0;">
                                                        <p style="font-weight:550">Total Amount</p>
                                                    </div>
                                                    <div class="col-sm-6" style="padding:0 0 0 80px; margin:0;">
                                                        <p id="total_amount"
                                                            style="margin-left:30px; font-weight:bold; color: #a12c12; font-size:1.3rem;">
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-7" style=" height:85vh; padding:20px 30px 20px 30px;">
                                    <div class="deliveryInfo" style="height:80vh; width:100%;">
                                        <div class="col-sm-12">
                                            <h3>Delivery Information</h3>
                                            <hr>
                                        </div>
                                        <?php
                                        $sql = "SELECT * FROM customerInfo WHERE uid = $currentUserId";
                                        $result = $db->query($sql);

                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $fullName = $row['name'];
                                            $fullAddress = $row['address'];
                                            // Explode the full name using the comma as a delimiter
                                            $nameParts = explode(", ", $fullName);
                                            // Extract the first and last names
                                            $lastName = $nameParts[0];
                                            $firstName = $nameParts[1];
                                        }
                                        ?><form action="" method="post">
                                            <div class="col-sm-12">
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col-sm-3"
                                                            style="padding:0 0 0 20px; margin:0 0 20px 0;">
                                                            <p style="font-weight:550; margin-bottom:30px">Name:</p>
                                                            <p style="font-weight:550; margin-bottom:30px;">Address:</p>
                                                            <p style="font-weight:550; margin-bottom:30px;">Contact
                                                                Number:
                                                            </p>
                                                        </div>
                                                        <div class="col-sm-9" style="padding:0 0 0 20px; margin:0;">
                                                            <p id="name"
                                                                style=" margin-left: 30px; margin-bottom:30px;">
                                                                <?php echo $firstName . " " . $lastName; ?></p>
                                                            </p>
                                                            <p id="address"
                                                                style="margin-left: 30px; margin-bottom:30px;">
                                                                <?php echo $fullAddress; ?>
                                                            </p>
                                                            <p id="contact_number"
                                                                style="margin-left: 30px;margin-bottom:30px;">
                                                                <?php echo $row['contactNum']; ?>
                                                            </p>
                                                        </div>
                                                        <div class="col-sm-3" style="padding:0 0 0 20px; margin:0;">
                                                            <p style="font-weight:550; margin-bottom:30px">Mode of
                                                                Payment:
                                                            </p>
                                                        </div>
                                                        <div class="col-sm-9"
                                                            style="padding:0 0 0 20px; margin:0 0 50px 0;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio"
                                                                    name="flexRadioDefault" id="flexRadioDefault1"
                                                                    style="margin-left: 5px" value="COD" checked>
                                                                <label class="form-check label" for="flexRadioDefault1"
                                                                    style="">
                                                                    Cash on Delivery
                                                                </label>
                                                                <input class="form-check-input" type="radio"
                                                                    name="flexRadioDefault" id="flexRadioDefault2"
                                                                    style="margin-left: 5px" disabled>
                                                                <label class="form-check label" for="flexRadioDefault2"
                                                                    style="">
                                                                    GCash (Not Available)
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" style="padding:0 0 0 20px; margin:0;">
                                                            <p style="font-weight:550; margin-bottom:30px">Delivery
                                                                Instructions:</p>
                                                        </div>
                                                        <div class="col-sm-9" style="padding:0 60px 0 20px; margin:0;">
                                                            <textarea class="form-control"
                                                                id="exampleFormControlTextarea1" rows="6"
                                                                style="margin-left: 30px; margin-bottom:30px;"
                                                                name="del_instruct"></textarea>
                                                            <div class="edit">
                                                                <button type="submit" class="btn btn-primary"
                                                                    style="margin-left: 30px; margin-bottom:30px;">
                                                                    <i class="fas fa-solid fa-check"></i> Confirm Order
                                                                </button>
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
            </div>
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
    <script>
        <?php if ($isCartEmpty && !$hasActiveOrders) : ?>
            document.getElementById('orderLink').classList.add('disabled');
        <?php endif; ?>
    </script>
</body>

</html>