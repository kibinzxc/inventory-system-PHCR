<?php
include '../../connection/database.php';

// Set the timezone
date_default_timezone_set('Asia/Manila');
// Get the current date in the format YYYY-MM-DD
$currentDate = date('Y-m-d');

// Select from usage_reports to get only the orders for today
$query = "SELECT * FROM usage_reports WHERE DATE(day_counted) = '$currentDate' ORDER BY day_counted DESC";
$result = $conn->query($query);

if (!$result) {
    die("Error executing query: " . $conn->error);
}
echo "  <link rel='stylesheet' href='recent-orders-card.css'>
        <div class='recent-orders-card'>";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Fetch the product image based on the product name
        $productName = $row['name'];
        $imageQuery = "SELECT img FROM products WHERE name = '$productName'";
        $imageResult = $conn->query($imageQuery);
        $image = '';
        if ($imageResult->num_rows > 0) {
            $imageRow = $imageResult->fetch_assoc();
            $image = "../../assets/products/" . $imageRow['img'];  // Prepending the path
        } else {
            $image = "../../assets/products/default.jpg"; // Default image if not found
        }

        // Get the transaction type based on invID from the invoice table
        $invID = $row['invID'];
        $transactionQuery = "SELECT order_type FROM invoice WHERE invID = '$invID'";
        $transactionResult = $conn->query($transactionQuery);
        $transactionType = '';
        if ($transactionResult->num_rows > 0) {
            $transactionRow = $transactionResult->fetch_assoc();
            $transactionType = $transactionRow['order_type'];  // Assuming 'order_type' column holds the type
        } else {
            $transactionType = "Unknown"; // Default value if no transaction type is found
        }

        // Prepare the content to be displayed
        $orderDate = $row['day_counted'];  // Date from the usage_reports table
        $orderCount = $row['quantity'];    // Quantity from the usage_reports table
        $size = $row['size'];              // Size from the usage_reports table


        // Calculate the time difference between the current date and the order date
        $orderTimestamp = strtotime($orderDate);
        $currentTimestamp = time();
        $timeDifference = $currentTimestamp - $orderTimestamp;

        if ($timeDifference < 60) {
            $formattedTime = "Just now";
        } elseif ($timeDifference < 3600) {
            $minutes = floor($timeDifference / 60);
            $formattedTime = "$minutes mins ago";
        } else {
            $hours = floor($timeDifference / 3600);
            $formattedTime = "$hours " . ($hours == 1 ? "hour" : "hours") . " ago";
        }
        // Display the recent order card
        echo "
      
            <div class='recent-orders-row'>
                <div class='recent-image'><img src='$image' alt='Order Image'></div>
                <div class='recent-details'>
                    <div class='recent-name'>
                        <span class='name'>$productName</span>
                        <span class='size'>($size)</span>
                    </div>
                    <span class='transaction-type'>Order ID#$invID - $transactionType</span>
                </div>
                <div class='recent-info'>
                    <div class='recent-count'>$orderCount " . ($orderCount == 1 ? "Order" : "Orders") . "</div>
                    <div class='recent-date'>$formattedTime</div>
                </div>
            </div>";
    }
} else {
    echo "<p style='text-align:center;'>No orders yet</p> $currentDate";
}
echo "</div>";
