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
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check for a session token to prevent double submission
    if (!isset($_SESSION['form_token'])) {
        echo "Invalid or duplicate form submission.";
        exit;
    }

    // Invalidate the token after the form is processed to prevent reuse
    unset($_SESSION['form_token']);

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
                                   VALUES ('$currentUserId', '{$userDetails['name']}', '$selectedAddress', '$cartItemsJSON', '$totalPrice', 'credit card', '$del_instruct', 'placed')";
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
                                        VALUES ('$orderID', '$cartItemsJSON', '$totalPrice', '$totalPrice', 0, 'online', 'credit card', 'placed')";
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
}
