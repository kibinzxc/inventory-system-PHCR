<?php
session_start();

include 'connection/database-conn.php';
include 'connection/database-db.php';

// Generate a new form token on every request
if (!isset($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}

$formToken = $_SESSION['form_token']; // Store token in a variable to use in HTML

// Check if user is logged in
if (isset($_SESSION['uid'])) {
    $loggedIn = true;
    $currentUserId = $_SESSION['uid'];

    $sql = "SELECT address FROM customerInfo WHERE uid = $currentUserId"; // Replace 'users' with your table name
    $result = $conn->query($sql);
    $hasActiveOrders = false;
    $orderStatuses = ["placed", "preparing", "ready for pickup", "delivery"];
    //get the selected address from the session
    if (isset($_SESSION['selectedAddress'])) {
        $selectedAddress = $_SESSION['selectedAddress'];
    } else {
        $selectedAddress = "";
    }

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
            if ($order_status == 'placed' || $order_status == 'delivery' || $order_status == 'ready for pickup' || $order_status == 'preparing') {
                // Redirect to another page
                header("Location: order-placed.php");
                exit();
            }
        }
    }
    $isCartEmpty = true;
    $sqlCartCheck = "SELECT * FROM cart WHERE uid = $currentUserId";
    $resultCartCheck = $conn->query($sqlCartCheck);


    $sqlactiveOrder = "SELECT * FROM orders WHERE uid = $currentUserId AND status IN ('placed', 'ready for pickup', 'preparing', 'delivery')";
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
    $payment = $_POST['flexRadioDefault'];

    // Check for a session token to prevent double submission
    if (!isset($_SESSION['form_token'])) {
        echo "Invalid or duplicate form submission.";
        exit;
    }

    // Invalidate the token after the form is processed to prevent reuse
    unset($_SESSION['form_token']);

    // Redirect to `order.php` if the payment method is "credit-card"
    if ($payment === 'credit-card') {
        header("Location: order-payment.php");
        exit;
    }

    // If the payment method is "COD," proceed with the order placement process
    if ($payment === 'COD') {
        // Fetch cart details
        $sqlCart = "SELECT * FROM cart WHERE uid = $currentUserId";
        $resultCart = $db->query($sqlCart);

        $del_instruct = $_POST['del_instruct'];

        if ($resultCart) {
            // Fetch user details
            $sqlUser = "SELECT * FROM customerInfo WHERE uid = $currentUserId";
            $resultUser = $db->query($sqlUser);
            $userDetails = mysqli_fetch_assoc($resultUser);

            // Initialize variables
            $totalPrice = 0;
            $deliveryFee = 65;
            $cartItems = [];

            // Iterate through cart items to calculate total price
            while ($row = mysqli_fetch_assoc($resultCart)) {
                $cartItems[] = [
                    'name' => $row['name'],
                    'size' => $row['size'],
                    'quantity' => $row['qty'],
                    'price' => $row['price'],
                ];
            }

            // Calculate total price
            foreach ($cartItems as $item) {
                $totalPrice += $item['price'] * $item['quantity'];
            }
            $totalPrice += $deliveryFee;

            // Convert cart items to JSON
            $cartItemsJSON = json_encode($cartItems);

            // Start database transaction
            $db->begin_transaction();

            try {
                // Check if there's already an unprocessed order for the user
                $checkOrderSql = "SELECT uid FROM orders WHERE uid = $currentUserId AND status = 'placed'";
                $existingOrder = $db->query($checkOrderSql);
                if ($existingOrder && $existingOrder->num_rows > 0) {
                    throw new Exception("You already have a pending order.");
                }

                // Step 1: Insert the order into the 'orders' table
                $insertOrderSql = "INSERT INTO `orders` (uid, name, address, items, totalPrice, payment, del_instruct, status)
                                   VALUES ('$currentUserId', '{$userDetails['name']}', '$selectedAddress', '$cartItemsJSON', '$totalPrice', '$payment', '$del_instruct', 'placed')";
                if (!$db->query($insertOrderSql)) {
                    throw new Exception("Error inserting order: " . $db->error);
                }

                // Step 2: Retrieve the 'orderID' of the newly inserted order
                $orderID = $db->insert_id;
                if (!$orderID) {
                    throw new Exception("Error retrieving order ID");
                }

                // Step 3: Insert into the 'float_orders' table
                $insertFloatOrderSql = "INSERT INTO `float_orders` (orderID, orders, total_amount, amount_received, amount_change, order_type, mop, status)
                                        VALUES ('$orderID', '$cartItemsJSON', '$totalPrice', '$totalPrice', 0, 'online', '$payment', 'placed')";
                if (!$db->query($insertFloatOrderSql)) {
                    throw new Exception("Error inserting into float_orders: " . $db->error);
                }

                // Step 4: Delete the cart items
                $deleteCartSql = "DELETE FROM cart WHERE uid = $currentUserId";
                if (!$db->query($deleteCartSql)) {
                    throw new Exception("Error deleting cart items: " . $db->error);
                }

                // Commit transaction
                $db->commit();

                // Redirect to success page
                header("Location: order-placed.php");
                exit;
            } catch (Exception $e) {
                // Rollback transaction on error
                $db->rollback();
                echo "Failed to place order: " . $e->getMessage();
                header("location: menu.php");
                exit;
            }
        } else {
            echo "Error fetching cart: " . $db->error;
            exit;
        }
    } else {
        echo "Invalid payment method selected.";
        exit;
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
                                            style="margin:0 0 0 0; padding:0; height:35vh; overflow-y: scroll; overflow:auto; border-radius:25px; ">
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
                                                        <p style="font-weight:550">Vatable Sales</p>
                                                        <p style="font-weight:550">Vat (12%)</p>
                                                        <p style="font-weight:550">Subtotal</p>
                                                    </div>
                                                    <div class="col-sm-6" style="padding:0 0 0 80px; margin:0;">
                                                        <p id="vatable" style="margin-left: 30px; font-weight:bold;"></p>
                                                        <p id="vat" style="margin-left:30px; font-weight:bold;"></p>
                                                        <p id="sub_total" style="margin-left:30px; font-weight:bold;"></p>


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
                                                        <p style="font-weight:550">Delivery Fee</p>
                                                        <p style="font-weight:550">Total Amount</p>
                                                    </div>
                                                    <div class="col-sm-6" style="padding:0 0 0 80px; margin:0;">
                                                        <p id="delivery_fee"
                                                            style="margin-left:30px; font-weight:bold;"></p>
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

                                        }
                                        ?><form action="" method="post">
                                            <div class="col-sm-12">
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col-sm-3"
                                                            style="padding:0 0 0 20px; margin:0 0 20px 0;">
                                                            <p style="font-weight:550; margin-bottom:30px">Name:</p>
                                                            <p style="font-weight:550; margin-bottom:60px;">Address:</p>
                                                            <p style="font-weight:550; margin-bottom:30px;">Contact
                                                                Number:
                                                            </p>
                                                        </div>
                                                        <div class="col-sm-9" style="padding:0 0 0 20px; margin:0;">
                                                            <p id="name"
                                                                style=" margin-left: 30px; margin-bottom:30px;">
                                                                <?php echo $fullName ?></p>
                                                            </p>
                                                            <p id="address"
                                                                style="margin-left: 30px; margin-bottom:60px;">
                                                                <?php echo $selectedAddress; ?>
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
                                                                <label class="form-check label" for="flexRadioDefault1">
                                                                    Cash on Delivery
                                                                </label>
                                                                <input class="form-check-input" type="radio"
                                                                    name="flexRadioDefault" id="flexRadioDefault2"
                                                                    style="margin-left: 5px" value="credit-card">
                                                                <label class="form-check label" for="flexRadioDefault2">
                                                                    Credit Card </label>
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