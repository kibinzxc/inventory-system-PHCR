<?php
include '../../connection/database.php';
Error_reporting(1);

// Handle search, sort, order, and month/year inputs
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';
$month = isset($_GET['month']) ? $_GET['month'] : ''; // Month parameter from URL
$year = isset($_GET['year']) ? $_GET['year'] : ''; // Year parameter from URL

// Valid columns for sorting and valid order directions
$valid_sort_columns = ['name', 'itemID', 'beginning', 'deliveries', 'transfers_in', 'transfers_out', 'spoilage', 'ending', 'usage_count'];
$valid_order_directions = ['asc', 'desc'];

// Ensure valid inputs
$sort = in_array($sort, $valid_sort_columns) ? $sort : 'name';
$order = in_array($order, $valid_order_directions) ? $order : 'asc';

// Validate month and year
if (!checkdate($month, 1, $year)) {
    // Invalid month or year, set to current month/year if invalid
    $month = date('n');
    $year = date('Y');
}

// SQL Query to fetch inventory data based on the selected month and year
$sql = "
    SELECT 
        name,
        itemID,
        uom,
        MIN(beginning) AS beginning,
        SUM(deliveries) AS deliveries,
        SUM(transfers_in) AS transfers_in,
        SUM(transfers_out) AS transfers_out,
        SUM(spoilage) AS spoilage,
        MAX(ending) AS ending,
        (
            MIN(beginning) + SUM(deliveries) + SUM(transfers_in) 
            - MAX(ending) - SUM(transfers_out) - SUM(spoilage)
        ) AS usage_count
    FROM records_inventory
    WHERE 
        (name LIKE ? OR itemID LIKE ? OR uom LIKE ?) 
        AND MONTH(inventory_date) = ? 
        AND YEAR(inventory_date) = ?
    GROUP BY name, itemID, uom
    ORDER BY $sort $order";

// Prepare the query and bind parameters
$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param('sssss', $search_param, $search_param, $search_param, $month, $year); // Bind parameters for search, month, and year

// Execute and handle errors
if (!$stmt->execute()) {
    echo "SQL Error: " . $stmt->error;
}

$result = $stmt->get_result();
?>

<link rel="stylesheet" href="itemsTable.css">
<table border="1">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Code</th>
            <th>Unit of Measurement</th>
            <th>Beginning Inventory</th>
            <th>Deliveries</th>
            <th>Transfers In</th>
            <th>Transfers Out</th>
            <th>Spoilage</th>
            <th>Ending Inventory</th>
            <th>Usage</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            $count = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $count++ . "</td>";
                echo "<td><strong>" . strtoupper(htmlspecialchars($row["name"])) . "</strong></td>";
                echo "<td>" . htmlspecialchars($row["itemID"]) . "</td>";
                echo "<td>" . strtoupper(htmlspecialchars($row["uom"])) . "</td>";
                echo "<td>" . htmlspecialchars($row["beginning"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["deliveries"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["transfers_in"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["transfers_out"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["spoilage"]) . "</td>";
                echo "<td><strong>" . htmlspecialchars($row["ending"]) . "<strong></td>";
                echo "<td>" . htmlspecialchars($row["usage_count"]) . "</td>";
                echo "</tr>";
            }
        } else {
            $monthName = DateTime::createFromFormat('!m', $month)->format('F');

            // Display the message with the month name
            echo "<tr><td colspan='10'>No items found for Month of $monthName, Year $year</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$stmt->close();
$conn->close();
?>