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
$valid_sort_columns = ['name', 'itemID', 'uom'];
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

// Set month number for SQL query
$monthNumber = str_pad($month, 2, "0", STR_PAD_LEFT); // Ensure two digits

// Search term setup
$searchTerm = "%" . $search . "%"; // Set wildcard search for LIKE queries
$sql = "
    SELECT 
        CONCAT('Month ', MONTH(inventory_date)) AS month_of_year,
        name,
        itemID,
        uom,
        (
            SELECT beginning
            FROM records_inventory ri_sub
            WHERE ri_sub.inventory_date = (
                SELECT MIN(inventory_date)
                FROM records_inventory ri_sub2
                WHERE MONTH(ri_sub2.inventory_date) = MONTH(ri.inventory_date)
                  AND YEAR(ri_sub2.inventory_date) = YEAR(ri.inventory_date)
                  AND ri_sub2.name = ri.name 
                  AND ri_sub2.itemID = ri.itemID
                  AND ri_sub2.uom = ri.uom
            )
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
            WHERE ri_sub.inventory_date = (
                SELECT MAX(inventory_date)
                FROM records_inventory ri_sub2
                WHERE MONTH(ri_sub2.inventory_date) = MONTH(ri.inventory_date)
                  AND YEAR(ri_sub2.inventory_date) = YEAR(ri.inventory_date)
                  AND ri_sub2.name = ri.name 
                  AND ri_sub2.itemID = ri.itemID
                  AND ri_sub2.uom = ri.uom
            )
            AND ri_sub.name = ri.name
            AND ri_sub.itemID = ri.itemID
            AND ri_sub.uom = ri.uom
        ) AS ending,
        SUM(usage_count) AS usage_count
    FROM records_inventory ri
    WHERE 
        (name LIKE ? OR itemID LIKE ? OR uom LIKE ?)
        AND MONTH(inventory_date) = ?
    GROUP BY month_of_year, name, itemID, uom
    ORDER BY $sort $order;
";




$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $monthNumber); // Bind all parameters
$stmt->execute();
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
            echo "<tr><td colspan='10'>No items found for Month of $monthName, Year $year</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$stmt->close();
$conn->close();
?>