<?php
include '../../connection/database.php';
error_reporting(1);

// Start session to store user-specific data
session_start();

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>Error: User not logged in.</p>";
    exit;
}

$currentUid = $_SESSION['user_id']; // Current logged-in user's UID

// Get the name of the current user based on UID
$userQuery = "SELECT name FROM accounts WHERE uid = ?";
$stmtUser = $conn->prepare($userQuery);
$stmtUser->bind_param("i", $currentUid);
$stmtUser->execute();
$userResult = $stmtUser->get_result();

if ($userResult->num_rows === 0) {
    echo "<p>Error: Current user not found in accounts table.</p>";
    exit;
}

$user = $userResult->fetch_assoc();
$currentUsername = $user['name'];

// Check if the current user already has a pending order in float_orders
$orderQuery = "SELECT * FROM float_orders WHERE cashier = ?";
$stmtOrder = $conn->prepare($orderQuery);
$stmtOrder->bind_param("s", $currentUsername);
$stmtOrder->execute();
$orderResult = $stmtOrder->get_result();

if ($orderResult->num_rows > 0) {
    // Redirect to accepted-orders.php if an order is found
    header("Location: accepted-orders.php");
    exit;
}

// Handle the "Accept" button action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept'])) {
    $orderID = $_POST['orderID'];

    $conn->begin_transaction();

    try {
        // Update the status in `orders` table
        $updateOrdersSql = "UPDATE orders SET status = 'delivery' WHERE orderID = ?";
        $stmtUpdateOrders = $conn->prepare($updateOrdersSql);
        $stmtUpdateOrders->bind_param("i", $orderID);
        $stmtUpdateOrders->execute();

        if ($stmtUpdateOrders->affected_rows === 0) {
            throw new Exception('Order not found or already updated.');
        }

        // Update the status in `float_orders` table and set the cashier
        $updateFloatOrdersSql = "UPDATE float_orders SET status = 'delivery', cashier = ? WHERE orderID = ?";
        $stmtUpdateFloatOrders = $conn->prepare($updateFloatOrdersSql);
        $stmtUpdateFloatOrders->bind_param("si", $currentUsername, $orderID);
        $stmtUpdateFloatOrders->execute();

        if ($stmtUpdateFloatOrders->affected_rows === 0) {
            throw new Exception('Float order not found or already updated.');
        }

        //get the uid of the user who placed the order
        $query = "SELECT uid FROM orders WHERE orderID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $orderID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $uid = $row['uid'];

        $title = "Order ID#$orderID Status Update";
        $category = "Order status";
        $description = "Your order with ID#$orderID is out for delivery and will be 
        arriving shortly. Thank you for choosing Pizza Hut Chino Roces! We are committed to delivering your order quickly and safely, and we appreciate your business. Should you have any questions or need assistance, feel free to contact us.";
        $image = "delivery.png";
        $status = "unread";

        // Insert notification
        $sql3 = "INSERT INTO msg_users (uid, title, category, description, image, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("isssss", $uid, $title, $category, $description, $image, $status);
        $stmt3->execute();


        // Commit the transaction
        $conn->commit();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}

// Fetch and display orders with 'ready for pickup' status
$query = "SELECT orderID, name, address, items, totalPrice, payment, del_instruct, orderPlaced, status FROM orders WHERE status = 'ready for pickup' ORDER BY orderPlaced DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $orderID = $row['orderID'];
    $name = $row['name'];
    $address = $row['address'];
    $items = json_decode($row['items'], true);
    $totalPrice = $row['totalPrice'];
    $orderPlaced = $row['orderPlaced'];

    echo '<div class="order-card">';
    echo '<div class="order-header">';
    echo '<h2>Order #' . htmlspecialchars($orderID) . '</h2>';
    echo '<p><strong>Placed on:</strong> ' . date('F j, Y g:i A', strtotime($orderPlaced)) . '</p>';
    echo '</div>';

    if (!empty($items)) {
        echo '<div class="order-items">';
        echo '<strong>Order Details:</strong>';
        echo '<ul>';
        foreach ($items as $item) {
            echo '<li>' . htmlspecialchars($item['quantity']) . 'x - ' . htmlspecialchars($item['name']) . ' (Size: ' . htmlspecialchars($item['size']) . ') - ₱' . number_format($item['price'], 2) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }

    echo '<div class="order-body">';
    echo '<p><strong>Name:</strong> ' . htmlspecialchars($name) . '</p>';
    echo '<p><strong>Address:</strong> ' . htmlspecialchars($address) . '</p>';
    echo '<p><strong>Total Price:</strong> ₱' . number_format($totalPrice, 2) . '</p>';
    echo '</div>';

    echo '<form method="POST" class="order-actions">';
    echo '<input type="hidden" name="orderID" value="' . htmlspecialchars($orderID) . '">';
    echo '<button type="submit" name="accept" class="btn btn-done">Accept</button>';
    echo '</form>';
    echo '</div>';
}

$stmt->close();
$conn->close();
