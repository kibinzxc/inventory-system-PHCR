<?php
include '../../connection/database.php';

// Handle search, sort, and order inputs
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

// Define valid columns for sorting and valid order directions
$valid_sort_columns = ['inventoryID', 'itemID', 'name', 'beginning', 'deliveries', 'transfers_in', 'transfers_out', 'spoilage', 'ending', 'notes', 'usage_count', 'status', 'last_update', 'updated_by'];
$valid_order_directions = ['asc', 'desc'];

// Ensure valid sort column
$sort = in_array($sort, $valid_sort_columns) ? $sort : 'inventoryID';
// Ensure valid order direction
$order = in_array($order, $valid_order_directions) ? $order : 'asc';

// SQL query using prepared statements to prevent SQL injection
$sql = "SELECT inventoryID, itemID, name, uom, beginning, deliveries, transfers_in, transfers_out, spoilage, ending, notes, last_update, updated_by, usage_count, status
        FROM inventory
WHERE 
            (name LIKE ? 
            OR itemID LIKE ? 
            OR uom LIKE ? 
            OR beginning LIKE ? 
            OR deliveries LIKE ? 
            OR transfers_in LIKE ? 
            OR transfers_out LIKE ? 
            OR spoilage LIKE ? 
            OR ending LIKE ? 
            OR usage_count LIKE ? 
            OR status LIKE ? 
            OR notes LIKE ? 
            OR updated_by LIKE ? 
            OR last_update LIKE ?)
        ORDER BY $sort $order";

$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param(
    'ssssssssssssss',
    $search_param,
    $search_param,
    $search_param,
    $search_param,
    $search_param,
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
            <th>Usage</th>
            <th>Ending Inv.</th>
            <th>Status</th>
            <th>Last Update</th>
            <th>Updated By</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            $count = 1;
            while ($row = $result->fetch_assoc()) {
                // Determine status class based on stock level
                $status = $row['status'];
                $status_class = "";
                $status_style = ""; // Initialize status style
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

                // Add emphasis to the Ending column if stock is low or out
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
                echo "<td>" . htmlspecialchars($row["beginning"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["deliveries"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["transfers_in"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["transfers_out"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["spoilage"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["usage_count"]) . "</td>";

                echo "<td style='$ending_style'>" . htmlspecialchars($row["ending"]) . "</td>";

                // Display status with assigned class and style
                echo "<td class='$status_class' style='$status_style'>" . strtoupper(htmlspecialchars($row["status"])) . "</td>";

                // Format the last update date
                $formatted_date = date("F j, Y g:i A", strtotime($row["last_update"]));
                echo "<td>" . $formatted_date . "</td>";

                echo "<td>" . htmlspecialchars($row["updated_by"]) . "</td>";

                echo "<td>
                <div class='actions_icon'>
                    <a href='#' onclick=\"openEditModal('" . $row['inventoryID'] . "', '" . addslashes($row['itemID']) . "','" . addslashes($row['name']) . "', '" . addslashes($row['uom']) . "', '" . addslashes($row['beginning']) . "', '" . addslashes($row['deliveries']) . "', '" . addslashes($row['transfers_in']) . "', '" . addslashes($row['transfers_out']) . "', '" . addslashes($row['spoilage']) . "', '" . addslashes($row['ending']) . "', '" . addslashes($row['usage_count']) . "', '" . addslashes($row['status']) . "')\" data-icon-tooltip='Edit'>
                        <img src='../../assets/edit.svg' alt='Edit' class='settings_icon'>
                    </a>
                    <a href='#' onclick=\"openConfirmModal('" . $row['inventoryID'] . "', '" . addslashes($row['name']) . "')\" data-icon-tooltip='Delete'>
                        <img src='../../assets/trash-2.svg' alt='Remove' class='remove_icon'>
                    </a>
                </div>
              </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='17'>No items found</td></tr>";
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