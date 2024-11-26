<?php
include '../../connection/database.php';
include 'functions.php';  // Ensure the timeAgo function is included

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// SQL query to fetch the 3 most recent products based on `last_update`
$sql = "SELECT inventoryID, itemID, name, uom, beginning, deliveries, transfers_in, transfers_out, spoilage, ending, last_update, updated_by, usage_count, status
        FROM daily_inventory
        ORDER BY last_update DESC
        LIMIT 5";  // Only fetch the 3 most recent products

$result = $conn->query($sql);
?>

<link rel="stylesheet" href="itemsTable.css">

<table border="1">
    <thead>
        <tr>
            <th>Name</th>
            <th>Code</th>
            <th>Ending Inventory</th>
            <th>UoM</th>
            <th>Status</th>
            <th>Last Update</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Determine status class based on stock level
                $status = $row['status'];
                $status_class = "";
                $status_style = ""; // Initialize status style
                if ($status == "in stock") {
                    $status_class = "in-stock";
                    $status_style = ' font-weight: 600; padding: 5px 10px;';
                } elseif ($status == "low stock") {
                    $status_class = "low-stock";
                    $status_style = ' font-weight: 600;padding: 5px 10px;';
                } elseif ($status == "out of stock") {
                    $status_class = "out-of-stock";
                    $status_style = ' font-weight: 600;padding: 5px 10px;';
                }

                // Add emphasis to the Ending column if stock is low or out
                $ending_style = "";
                if ($status == "low stock") {
                    $ending_style = "color: #DB7600; font-weight: 700;";
                } elseif ($status == "out of stock") {
                    $ending_style = "color: #B70000; font-weight: 700;";
                } elseif ($status == "in stock") {
                    $ending_style = "color: #006D6D; font-weight: 700;";
                }

                // Format the last update using the timeAgo function
                $lastUpdateFormatted = timeAgo($row["last_update"]);

                // Render the row
                echo "<tr onclick='window.location.href=\"../inventory/items.php\";'>"; // Make row clickable
                echo "<td><strong>" . strtoupper(htmlspecialchars($row["name"])) . "</strong></td>";
                echo "<td>" . htmlspecialchars($row["itemID"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["ending"]) . "</td>";
                echo "<td>" . strtoupper(htmlspecialchars($row["uom"])) . "</td>";
                echo "<td class='$status_class' style='$status_style'>" . strtoupper(htmlspecialchars($row["status"])) . "</td>";
                echo "<td>" . $lastUpdateFormatted . "</td>";
                echo "</tr>";
            }
        } else {
            // No records found, update colspan to match the number of columns
            echo "<tr><td colspan='6'>No items found</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$conn->close();
?>