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
$query = "SELECT orderID, name, address, items, totalPrice, payment, del_instruct, orderPlaced, status FROM orders WHERE status = 'delivery' ORDER BY orderPlaced DESC";
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
    echo '<form method="POST" enctype="multipart/form-data" class="order-actions">';
    echo '<br>';
    echo '<form method="POST" enctype="multipart/form-data" class="order-actions">';
    echo '<label for="image" style="font-size: 1.2rem; color: #343434; display: block; margin-bottom: 10px;">Proof of Delivery</label>';
    echo '<input type="file" name="image" id="image" accept="image/*" required onchange="previewImage(event)">';
    echo '<br>';
    echo '<img id="image-preview" src="#" alt="Image Preview" style="display:none; margin-top: 15px; max-width: 100%; max-height: 200px; border: 1px solid #ccc; border-radius: 5px;">';
    echo '<input type="hidden" name="orderID" value="' . htmlspecialchars($orderID) . '">';
    echo '<button type="submit" name="Done" class="btn btn-done">Mark as Delivered</button>';
    echo '</form>';
    echo '</div>';
}



$stmt->close();
$conn->close();
