<?php
include '../../connection/database.php';
Error_reporting(1);
// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Get current time
$currentDateTime = new DateTime();
$currentHour = $currentDateTime->format('H'); // Get current hour (24-hour format)

// Set the inventory date (previous day or current day based on time)
if ($currentHour < 6) {
    $inventoryDate = $currentDateTime->modify('-1 day')->format('Y-m-d');
} else {
    $inventoryDate = $currentDateTime->format('Y-m-d');
}

// Check if a 'date' parameter is passed in the URL
$selectedDate = isset($_GET['date']) ? $_GET['date'] : $inventoryDate; // Use passed date or default to current inventory date

// Query to check if a record exists for the given date
$query = "SELECT COUNT(*) AS recordCount FROM records_inventory WHERE inventory_date = '$selectedDate'";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$recordExists = $row['recordCount'] > 0;

// Handle search, sort, and order inputs
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

// Define valid columns for sorting and valid order directions
$valid_sort_columns = ['inventoryID', 'itemID', 'name', 'uom', 'submitted_by'];
$valid_order_directions = ['asc', 'desc'];

// Ensure valid sort column
$sort = in_array($sort, $valid_sort_columns) ? $sort : 'name';
// Ensure valid order direction
$order = in_array($order, $valid_order_directions) ? $order : 'asc';

// SQL query using prepared statements to prevent SQL injection, filtering by selected date
$sql = "SELECT recordID, itemID, name, uom, beginning, deliveries, transfers_in, transfers_out, spoilage, ending, usage_count, inventory_date, remarks, submitted_by
        FROM records_inventory
WHERE 
            (name LIKE ? 
            OR itemID LIKE ? 
            OR uom LIKE ? 
            OR submitted_by LIKE ?)
            AND inventory_date = ?  -- Filter by the selected date
        ORDER BY $sort $order";

$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param(
    'sssss',  // Adding another 's' for the date parameter
    $search_param,
    $search_param,
    $search_param,
    $search_param,
    $selectedDate  // Bind the selected date to the query
);
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
            <th>Base Unit of Measurement</th>
            <th>Beginning Inv.</th>
            <th>Deliveries</th>
            <th>Transfers In</th>
            <th>Transfers Out</th>
            <th>Spoilage</th>
            <th>Ending Inv.</th>
            <th>Usage</th>
            <th>Submitted By</th>

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
                echo "<td>" . htmlspecialchars($row["ending"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["usage_count"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["submitted_by"]) . "</td>";


                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='12'>No records found for the selected date</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php include 'Confirm_modal.php'; ?>
<?php include 'edit-archive.php'; ?>

<?php
$stmt->close();
$conn->close();
?>