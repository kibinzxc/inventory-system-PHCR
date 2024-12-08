<?php
include '../../connection/database.php';
error_reporting(1);

//set timezone to manila
date_default_timezone_set('Asia/Manila');

// Handle search, sort, and week inputs
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'order_count';
$order = isset($_GET['order']) ? $_GET['order'] : 'desc';
$week = isset($_GET['week']) ? (int)$_GET['week'] : 0; // Week parameter from URL

// Valid columns for sorting and valid order directions
$valid_sort_columns = ['name', 'order_count'];
$valid_order_directions = ['asc', 'desc'];

// Ensure valid inputs
$sort = in_array($sort, $valid_sort_columns) ? $sort : 'order_count';
$order = in_array($order, $valid_order_directions) ? $order : 'desc';

// SQL Query to fetch product names and their order count from usage_reports
$sql = "
    SELECT 
        p.name,
        SUM(ur.quantity) AS order_count
    FROM products p
    LEFT JOIN usage_reports ur ON p.name = ur.name
    WHERE 
        p.name LIKE ?
        AND CONCAT('Week ', 
            FLOOR((DATEDIFF(ur.day_counted, 
                DATE_ADD(LAST_DAY(DATE_SUB(ur.day_counted, INTERVAL 1 MONTH)), 
                    INTERVAL (9 - DAYOFWEEK(LAST_DAY(DATE_SUB(ur.day_counted, INTERVAL 1 MONTH)))) % 7 DAY)) 
                ) / 7) + 1
        ) = ?
    GROUP BY p.name
    ORDER BY $sort $order;
";

// Prepare the query and bind parameters
$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$week_param = "Week $week";
$stmt->bind_param('ss', $search_param, $week_param);

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
            // Determine the range for the given week number
            $currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
            $currentYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

            $startOfMonth = new DateTime("$currentYear-$currentMonth-01");
            $firstMonday = clone $startOfMonth;
            $firstMonday->modify('first monday of this month');
            $weekStart = clone $firstMonday;
            $weekStart->modify('+' . (($week - 1) * 7) . ' days');
            $weekEnd = clone $weekStart;
            $weekEnd->modify('+6 days');

            $weekRange = $weekStart->format('F j') . ' - ' . $weekEnd->format('F j, Y');

            echo "<tr><td colspan='3'>No items found for Week $week | $weekRange</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$stmt->close();
$conn->close();
?>