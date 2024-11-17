<?php
include '../../connection/database.php';

// Handle search, sort, and order inputs
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

// Define valid columns for sorting and valid order directions
$valid_sort_columns = ['inventoryID', 'itemID', 'name', 'current_inventory', 'spoilage', 'ending', 'usage_count', 'status', 'last_update', 'updated_by'];
$valid_order_directions = ['asc', 'desc'];

// Ensure valid sort column and direction
$sort = in_array($sort, $valid_sort_columns) ? $sort : 'inventoryID';
$order = in_array($order, $valid_order_directions) ? $order : 'asc';

// SQL query using prepared statements to prevent SQL injection
$sql = "SELECT inventoryID, itemID, name, uom, beginning, deliveries, transfers_in, transfers_out, spoilage, ending, last_update, updated_by, usage_count, status,(beginning + deliveries + transfers_in - transfers_out) AS current_inventory
        FROM daily_inventory
        WHERE 
            (name LIKE ? 
            OR itemID LIKE ? 
            OR uom LIKE ? 
            OR spoilage LIKE ? 
            OR ending LIKE ? 
            OR usage_count LIKE ? 
            OR status LIKE ? 
            OR updated_by LIKE ? 
            OR last_update LIKE ?)
        ORDER BY $sort $order";

$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param(
    'sssssssss',
    $search_param,
    $search_param,
    $search_param,
    $search_param,
    $search_param,
    $search_param,
    $search_param,
    $search_param,
    $search_param
);
$stmt->execute();
$result = $stmt->get_result();
?>

<link rel="stylesheet" href="itemsTable.css">

<!-- Table Structure -->
<table border="1">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Code</th>
            <th>Base Unit of Measurement</th>
            <th>Current Inventory</th>
            <th>Spoilage</th>
            <th>Usage</th>
            <th>Ending Inventory</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            $count = 1;
            while ($row = $result->fetch_assoc()) {
                // Calculate current inventory based on removed columns
                $current_inventory = $row["beginning"] + $row["deliveries"] + $row["transfers_in"] - $row["transfers_out"];

                // Determine status class based on stock level
                $status = $row['status'];
                $status_class = "";
                $status_style = "";
                if ($status == "in stock") {
                    $status_class = "in-stock";
                    $status_style = 'background-color: rgba(0, 151, 151, 0.15); color: #006D6D; font-weight: 600; padding: 5px 10px;';
                } elseif ($status == "low stock") {
                    $status_class = "low-stock";
                    $status_style = 'background-color: rgba(255, 156, 7, 0.15); color: #DB7600; padding: 5px 10px;';
                } elseif ($status == "out of stock") {
                    $status_class = "out-of-stock";
                    $status_style = 'background-color: rgba(242, 0, 0, 0.15); color: #B70000; padding: 5px 10px;';
                }

                // Styling for Ending column
                $ending_style = "";
                if ($status == "low stock") {
                    $ending_style = "color: #DB7600; font-weight: 700;";
                } elseif ($status == "out of stock") {
                    $ending_style = " color: #B70000; font-weight: 700;";
                } elseif ($status == "in stock") {
                    $ending_style = " color: #006D6D; font-weight: 700;";
                }

                echo "<tr>";
                echo "<td>" . $count++ . "</td>";
                echo "<td><strong>" . strtoupper(htmlspecialchars($row["name"])) . "</strong></td>";
                echo "<td>" . htmlspecialchars($row["itemID"]) . "</td>";
                echo "<td>" . strtoupper(htmlspecialchars($row["uom"])) . "</td>";

                // Display calculated current inventory
                echo "<td>" . htmlspecialchars($current_inventory) . "</td>";

                echo "<td>" . htmlspecialchars($row["spoilage"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["usage_count"]) . "</td>";
                echo "<td style='$ending_style'>" . htmlspecialchars($row["ending"]) . "</td>";

                // Display status with assigned class and style
                echo "<td class='$status_class' style='$status_style'>" . strtoupper(htmlspecialchars($row["status"])) . "</td>";


                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='12'>No items found</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php include 'Confirm_modal.php'; ?>
<?php include 'Edit_modal.php'; ?>

<?php
$stmt->close();
$conn->close();
?>