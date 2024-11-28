<?php
include '../../connection/database.php';
error_reporting(1);

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Query to fetch order data
$query = "SELECT orderID, name, address, items, totalPrice, payment, del_instruct, orderPlaced, status FROM orders where status = 'preparing' ORDER BY orderPlaced DESC";

// Prepare and execute the query
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();

// Check if no records are found
if ($result->num_rows === 0) {
    echo "<p style='text-align:center;'>No records found.</p>";
} else {
    // Display the results
    while ($row = $result->fetch_assoc()) {
        $orderID = $row['orderID'];
        $name = $row['name'];
        $address = $row['address'];
        $items = json_decode($row['items'], true);
        $totalPrice = $row['totalPrice'];
        $payment = $row['payment'];
        $del_instruct = $row['del_instruct'];
        $orderPlaced = $row['orderPlaced'];
        $status = $row['status'];

        // Format the orderPlaced datetime
        $orderPlacedDateTime = new DateTime($orderPlaced);
        $formattedOrderPlaced = $orderPlacedDateTime->format('F d, Y g:i A');

        // Display order details
        echo '<link rel="stylesheet" href="archive.css">';
        echo '<div class="card-container">';
        echo '<div class="card">';
        echo '<div class="card-body">';

        echo '<div class="order-id">';
        echo '<p><strong>Order ID:</strong> ' . $orderID . '</p>';
        echo '</div>';

        echo '<div class="order-info">';
        echo '<p><strong>Name:</strong> ' . htmlspecialchars($name) . '</p>';
        echo '<p><strong>Address:</strong> ' . htmlspecialchars($address) . '</p>';
        echo '<p><strong>Total Price:</strong> ₱' . number_format($totalPrice, 2) . '</p>';
        echo '</div>';

        if (!empty($items)) {
            echo '<div class="order-items">';
            echo '<strong>Order Details:</strong>';
            echo '<ul>';
            foreach ($items as $item) {
                echo '<li>' . htmlspecialchars($item['name']) . ' (Size: ' . htmlspecialchars($item['size']) . ') - ₱' . number_format($item['price'], 2) . ' x ' . htmlspecialchars($item['qty']) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        echo '<div class="order-actions">';
        echo '<form action="done-preparing.php" method="POST">';
        echo '<input type="hidden" name="orderID" value="' . $orderID . '">';
        echo '<button type="submit" class="btn btn-done">Done Preparing</button>';
        echo '</form>';
        echo '</div>';

        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}

// Close statement and connection
$stmt->close();
$conn->close();
