<?php
include '../../connection/database.php';

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Get the start and end dates for last week (Monday to Sunday)
$startOfLastWeek = date('Y-m-d H:i:s', strtotime('monday last week'));  // Start of last week
$endOfLastWeek = date('Y-m-d 23:59:59', strtotime('sunday last week')); // End of last week


// Query to fetch product details for fast-moving products from last week along with category
$sql = "SELECT ur.name, SUM(ur.quantity) AS total_quantity, ur.price, p.category
        FROM usage_reports ur
        JOIN products p ON ur.name = p.name
        WHERE ur.day_counted BETWEEN ? AND ?
        GROUP BY ur.name, ur.price, p.category
        ORDER BY total_quantity ASC
        LIMIT 5";  // Fetch the top 5 fast-moving products and sum quantities

$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $startOfLastWeek, $endOfLastWeek);  // Bind the start and end of last week
$stmt->execute();
$result = $stmt->get_result();
?>

<link rel="stylesheet" href="itemsTable.css">

<table border="1" class="table-mobile">
    <thead>
        <tr>
            <th>#</th> <!-- Added Number column -->
            <th style='text-align: left;'>Name</th>
            <th>Category</th> <!-- Added Category column -->
            <th>Price</th>
            <th>Orders</th> <!-- Renamed 'Quantity' to 'Orders' -->
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            $counter = 1;
            while ($row = $result->fetch_assoc()) {
                // Render the row with product name, category, total quantity (sum), price
                echo "<tr>";
                echo "<td>" . $counter++ . "</td>"; // Display the number
                echo "<td style='text-align: left;'><strong>" . strtoupper(htmlspecialchars($row["name"])) . "</strong></td>";
                echo "<td>" . strtoupper(htmlspecialchars($row["category"])) . "</td>";  // Display the category
                echo "<td>₱" . number_format($row["price"], 2) . "</td>";
                echo "<td><strong>" . intval($row["total_quantity"]) . "</strong></td>";  // Display the summed quantity
                echo "</tr>";
            }
        } else {
            // No records found
            echo "<tr><td colspan='5'>No fast-moving products found for last week</td></tr>";
        }
        ?>
    </tbody>
</table>