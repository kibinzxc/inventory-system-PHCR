<?php
include '../../connection/database.php';
error_reporting(1);

// Handle search, sort, order, and week inputs
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';
$week = isset($_GET['week']) ? (int)$_GET['week'] : 0;  // Week parameter from URL

// Valid columns for sorting and valid order directions
$valid_sort_columns = ['recordID', 'name', 'itemID', 'beginning', 'deliveries', 'transfers_in', 'transfers_out', 'spoilage', 'ending', 'usage_count'];
$valid_order_directions = ['asc', 'desc'];

// Ensure valid inputs
$sort = in_array($sort, $valid_sort_columns) ? $sort : 'name';
$order = in_array($order, $valid_order_directions) ? $order : 'asc';

// SQL Query to fetch inventory data based on the week parameter
$sql = "
    SELECT 
        CONCAT('Week ', 
            FLOOR((DATEDIFF(inventory_date, 
                DATE_ADD(LAST_DAY(DATE_SUB(inventory_date, INTERVAL 1 MONTH)), 
                    INTERVAL (9 - DAYOFWEEK(LAST_DAY(DATE_SUB(inventory_date, INTERVAL 1 MONTH)))) % 7 DAY)) 
                ) / 7) + 1
        ) AS week_of_month,
        name,
        itemID,
        uom,
        (
            SELECT beginning 
            FROM records_inventory ri_sub
            WHERE ri_sub.inventory_date = MIN(ri.inventory_date)
              AND ri_sub.name = ri.name
              AND ri_sub.itemID = ri.itemID
              AND ri_sub.uom = ri.uom
        ) AS beginning,
        SUM(deliveries) AS deliveries,
        SUM(transfers_in) AS transfers_in,
        SUM(transfers_out) AS transfers_out,
        SUM(spoilage) AS spoilage,
        (
            SELECT ending 
            FROM records_inventory ri_sub
            WHERE ri_sub.inventory_date = MAX(ri.inventory_date)
              AND ri_sub.name = ri.name
              AND ri_sub.itemID = ri.itemID
              AND ri_sub.uom = ri.uom
        ) AS ending,
        SUM(usage_count) AS usage_count
    FROM records_inventory ri
    WHERE 
        (name LIKE ? OR itemID LIKE ? OR uom LIKE ?)
        AND CONCAT('Week ', 
            FLOOR((DATEDIFF(inventory_date, 
                DATE_ADD(LAST_DAY(DATE_SUB(inventory_date, INTERVAL 1 MONTH)), 
                    INTERVAL (9 - DAYOFWEEK(LAST_DAY(DATE_SUB(inventory_date, INTERVAL 1 MONTH)))) % 7 DAY)) 
                ) / 7) + 1
        ) = ?
    GROUP BY week_of_month, name, itemID, uom
    ORDER BY $sort $order;
";


// Prepare the query and bind parameters
$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$week_param = "Week $week";  // Format the week for comparison in the query

$stmt->bind_param('ssss', $search_param, $search_param, $search_param, $week_param);

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
                echo "<td><strong>" . htmlspecialchars($row["ending"]) . "</strong></td>";
                echo "<td>" . htmlspecialchars($row["usage_count"]) . "</td>";
                echo "</tr>";
            }
        } else {
            // Determine the range for the given week number
            $currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('n'); // Use current month if not provided
            $currentYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');   // Use current year if not provided

            $startOfMonth = new DateTime("$currentYear-$currentMonth-01");

            // Find the first Monday of the month
            $firstMonday = clone $startOfMonth;
            $firstMonday->modify('first monday of this month');

            // Calculate the start and end dates for the given week
            $weekStart = clone $firstMonday;
            $weekStart->modify('+' . (($week - 1) * 7) . ' days');
            $weekEnd = clone $weekStart;
            $weekEnd->modify('+6 days'); // Always count 6 days forward for Sunday

            // Format the week range
            $weekRange = $weekStart->format('F j') . ' - ' . $weekEnd->format('F j, Y');

            echo "<tr><td colspan='12'>No items found for Week $week | $weekRange</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$stmt->close();
$conn->close();
?>