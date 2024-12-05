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

// Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>Error: User not logged in.</p>";
    exit;
}

// Check if the current user already has a pending order in float_orders
$orderQuery = "SELECT * FROM float_orders WHERE cashier = ?";
$stmtOrder = $conn->prepare($orderQuery);
$stmtOrder->bind_param("s", $currentUsername);
$stmtOrder->execute();
$orderResult = $stmtOrder->get_result();

if ($orderResult->num_rows < 1) {
    // Redirect to accepted-orders.php if an order is found
    header("Location: online-orders.php");
    exit;
}


// Handle the "Accept" button action
// Handle the "Not Delivered" button action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['not-yet'])) {
    $orderID = $_POST['orderID'];

    $conn->begin_transaction();

    try {
        // Update the status in `orders` table
        $updateOrdersSql = "UPDATE orders SET status = 'not delivered' WHERE orderID = ?";
        $stmtUpdateOrders = $conn->prepare($updateOrdersSql);
        $stmtUpdateOrders->bind_param("i", $orderID);
        $stmtUpdateOrders->execute();

        if ($stmtUpdateOrders->affected_rows === 0) {
            throw new Exception('Order not found or already updated.');
        }

        $deleteFloatOrdersSql = "DELETE FROM float_orders WHERE orderID = ?";
        $stmtDeleteFloatOrders = $conn->prepare($deleteFloatOrdersSql);
        $stmtDeleteFloatOrders->bind_param("i", $orderID);
        $stmtDeleteFloatOrders->execute();



        // Get the UID of the user who placed the order
        $getUidSql = "SELECT uid FROM orders WHERE orderID = ?";
        $stmtGetUid = $conn->prepare($getUidSql);
        $stmtGetUid->bind_param("i", $orderID);
        $stmtGetUid->execute();
        $uidResult = $stmtGetUid->get_result();
        $uidRow = $uidResult->fetch_assoc();
        $uid = $uidRow['uid'];

        $title = "Order ID#$orderID Status Update";
        $category = "Order status";
        $description = "We regret to inform you that the delivery of your order with ID#$orderID could not be completed because our delivery rider was unable to reach you. Please ensure that someone is available to receive the order at the delivery address, or kindly provide an alternate contact number or instructions. We apologize for the inconvenience and appreciate your prompt attention to this matter. If you need any assistance, please do not hesitate to contact us.";
        $image = "not-delivered.png";
        $status = "unread";

        // Insert notification
        $sql3 = "INSERT INTO msg_users (uid, title, category, description, image, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("isssss", $uid, $title, $category, $description, $image, $status);
        $stmt3->execute();
        $conn->commit();

        //insert all the data of orders with same orderID from float_orders to success_orders table, orderID uid name address items(orders) totalPrice payment, del instruct, orderplaced, status, orderdelivered
        $query = "SELECT * FROM orders WHERE orderID = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param('i', $orderID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        $orderID = $row['orderID'];
        $uid = $row['uid'];
        $name = $row['name'];
        $address = $row['address'];
        $items = $row['items'];
        $totalPrice = $row['totalPrice'];
        $payment = $row['payment'];
        $del_instruct = $row['del_instruct'];
        $orderPlaced = $row['orderPlaced'];
        $status = $row['status'];
        $query = "INSERT INTO success_orders (orderID, uid, name, address, items, totalPrice, payment, del_instruct, orderPlaced, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param('isssssssss', $orderID, $uid, $name, $address, $items, $totalPrice, $payment, $del_instruct, $orderPlaced, $status);
        if ($stmt->execute()) {
            //delete float 
            $removeOrder = "DELETE FROM orders WHERE orderID = ?";
            $stmtRemoveOrder = $conn->prepare($removeOrder);
            $stmtRemoveOrder->bind_param("i", $orderID);
            $stmtRemoveOrder->execute();
            $stmtRemoveOrder->close();


            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            die("Error executing stmt3: " . $stmt3->error);  // More detailed error if insert fails
        }
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
    echo '<label for="image-' . $orderID . '" style="font-size: 1.2rem; color: #343434; display: block; margin-bottom: 10px;">Proof of Delivery</label>';
    echo '<input type="file" name="image" id="image-' . $orderID . '" accept="image/*" required onchange="handleFileChange(event, \'' . $orderID . '\')">';
    echo '<br>';
    echo '<img id="image-preview-' . $orderID . '" src="#" alt="Image Preview" style="display:none; margin-top: 15px; max-width: 100%; max-height: 200px; border: 1px solid #ccc; border-radius: 5px;">';
    echo '<input type="hidden" name="orderID" value="' . htmlspecialchars($orderID) . '">';
    echo '<div class="button-group">';
    echo '<button type="button" class="btn btn-secondary" id="not-delivered-btn-' . $orderID . '" style="display: none;" onclick="openModal(\'not-delivered-modal-' . $orderID . '\')">Not Delivered</button>';
    echo '<button type="button" class="btn btn-done" id="delivered-btn-' . $orderID . '" style="display: none;" onclick="openModal(\'delivered-modal-' . $orderID . '\')">Mark as Delivered</button>';
    echo '</div>';
    echo '</form>';

    // Not Delivered Modal
    echo '<div id="not-delivered-modal-' . $orderID . '" class="modal">';
    echo '<div class="modal-content">';
    echo '<p>Are you sure you want to mark this order as "Not Delivered"?</p>';
    echo '<div class="modal-buttons">';
    echo '<button onclick="closeModal(\'not-delivered-modal-' . $orderID . '\')" class="btn btn-cancel2">Cancel</button>';
    echo '<form method="POST" style="display: inline;">';
    echo '<input type="hidden" name="orderID" value="' . htmlspecialchars($orderID) . '">';
    echo '<button type="submit" name="not-yet" class="btn btn-secondary">Confirm</button>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Delivered Modal
    echo '<div id="delivered-modal-' . $orderID . '" class="modal">';
    echo '<div class="modal-content">';
    echo '<p>Are you sure you want to mark this order as "Delivered"?</p>';
    echo '<div class="modal-buttons">';
    echo '<button onclick="closeModal(\'delivered-modal-' . $orderID . '\')" class="btn btn-cancel2">Cancel</button>';
    echo '<form method="POST" style="display: inline;">';
    echo '<input type="hidden" name="orderID" value="' . htmlspecialchars($orderID) . '">';
    echo '<button type="submit" name="Done" class="btn btn-done">Confirm</button>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}



$stmt->close();
$conn->close();
