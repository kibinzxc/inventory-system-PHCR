<?php
include '../../connection/database.php';
error_reporting(1);

// Handle search, sort, order, and month/year inputs
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'order_count';
$order = isset($_GET['order']) ? $_GET['order'] : 'desc';
$month = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

// Valid columns for sorting and valid order directions
$valid_sort_columns = ['name', 'order_count'];
$valid_order_directions = ['asc', 'desc'];

// Ensure valid inputs
$sort = in_array($sort, $valid_sort_columns) ? $sort : 'order_count';
$order = in_array($order, $valid_order_directions) ? $order : 'desc';

// Validate month and year
if (!checkdate($month, 1, $year)) {
    $month = date('n');
    $year = date('Y');
}

// Set month number for SQL query
$monthNumber = str_pad($month, 2, "0", STR_PAD_LEFT);

// SQL Query to fetch product names and their monthly order count
$sql = "
    SELECT 
        name,
        SUM(quantity) AS order_count
    FROM usage_reports
    WHERE 
        name LIKE ?
        AND MONTH(day_counted) = ?
        AND YEAR(day_counted) = ?
    GROUP BY name
    ORDER BY $sort $order;
";


// Prepare and bind parameters
$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param("sss", $search_param, $monthNumber, $year);

// Execute the query and handle errors
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
            <th>Order Count</th>
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
                echo "<td>" . htmlspecialchars($row["order_count"]) . "</td>";
                echo "</tr>";
            }
        } else {
            $monthName = DateTime::createFromFormat('!m', $month)->format('F');
            echo "<tr><td colspan='3'>No items found for Month of $monthName, Year $year</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$stmt->close();
$conn->close();
?>