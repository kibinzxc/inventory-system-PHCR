<?php
include '../../connection/database.php';
error_reporting(1);

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Create a DateTime object for the current date
$currentDate = new DateTime();

// Get Monday of the current week (start of the week)
$monday = clone $currentDate;
$monday->modify('Monday this week');

// Get Sunday of the current week (end of the week)
$sunday = clone $currentDate;
$sunday->modify('Sunday this week');

// Get the dates in Y-m-d format
$mondayDate = $monday->format('Y-m-d');
$sundayDate = $sunday->format('Y-m-d');

// Query to fetch the top 3 products for the current week from the usage_reports table
// Join with the products table to get the image
$query = "SELECT ur.name, ur.size, SUM(ur.quantity) AS orders, p.img
          FROM usage_reports ur
          JOIN products p ON ur.name = p.name
          WHERE DATE(ur.day_counted) BETWEEN ? AND ?
          GROUP BY ur.name, ur.size
          ORDER BY orders DESC
          LIMIT 3";

// Prepare and execute the query
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);  // Show error if prepare fails
}

// Bind the parameters for Monday and Sunday
$stmt->bind_param("ss", $mondayDate, $sundayDate);
$stmt->execute();
$result = $stmt->get_result();

// Check if no records are found
if ($result->num_rows === 0) {
    echo "<p style='text-align:center;'>No top products yet.</p>";
} else {
    // Display the results
    echo '<div class="usage-product-cards-container">'; // Container for horizontal card display
    while ($row = $result->fetch_assoc()) {
        $product_name = $row['name'];
        $size = $row['size'];
        $orders = $row['orders'];
        $image = $row['img'];

        // Inside your PHP loop for displaying the top 3 products
        echo '<a href="../orders/order-count.php" class="usage-product-card-link">';  // Add link
        echo '<div class="usage-product-card">';
        // Product Image
        echo '<div class="usage-product-image-container">';
        if (!empty($image)) {
            echo '<img src="../../assets/products/' . $image . '" alt="' . $product_name . '" class="usage-product-image">';
        } else {
            echo '<p>No image available</p>';
        }
        echo '</div>';

        // Product Name and Size
        echo '<div class="usage-product-name-size">';
        echo '<p><strong>' . $product_name . '</strong></p>';
        echo '<p class="usage-product-size">' . $size . '</p>';
        echo '</div>';

        // Order Count
        echo '<div class="usage-product-orders">';
        echo '<p> <strong>' . $orders . '</strong> ' . ($orders == 1 ? 'Order' : 'Orders') . '</p>';
        echo '</div>';
        echo '</div>';
        echo '</a>';  // Close the link

    }
    echo '</div>';  // End of usage-product-cards-container
}

// Close statement and connection
$stmt->close();
$conn->close();
