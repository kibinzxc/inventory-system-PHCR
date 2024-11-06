<?php
include '../../connection/database.php';

// Handle search, sort, and order inputs
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'inventoryID';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

// Define valid columns for sorting and valid order directions
$valid_sort_columns = ['inventoryID', 'itemID', 'name', 'qty', 'measurement', 'status', 'last_update', 'updated_by'];
$valid_order_directions = ['asc', 'desc'];

// Ensure valid sort column
$sort = in_array($sort, $valid_sort_columns) ? $sort : 'inventoryID';
// Ensure valid order direction
$order = in_array($order, $valid_order_directions) ? $order : 'asc';

// SQL query using prepared statements to prevent SQL injection
$sql = "SELECT inventoryID, itemID, name, qty, measurement, status, last_update, updated_by 
        FROM inventory 
        WHERE (name LIKE ? OR updated_by LIKE ? OR itemID LIKE ? OR inventoryID LIKE ?)
        ORDER BY $sort $order";

$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param('ssss', $search_param, $search_param, $search_param, $search_param);
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
            <th>QTY</th>
            <th>Measurement</th>
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
                // Capitalize measurement and status
                $measurement = strtoupper(htmlspecialchars($row["measurement"]));
                $status = strtoupper(htmlspecialchars($row["status"]));

                // Determine status class based on stock level
                $status_class = "";
                $status_style = ""; // Initialize status style
                if ($status == "IN STOCK") {
                    $status_class = "in-stock";
                    $status_style = 'background-color: rgba(0, 151, 151, 0.15); color: #006D6D; font-weight: 600; padding: 5px 10px; border-radius: 5px;';
                } elseif ($status == "LOW STOCK") {
                    $status_class = "low-stock";
                    $status_style = 'background-color: rgba(255, 156, 7, 0.15); color: #DB7600; padding: 5px 10px; border-radius: 5px;';
                } elseif ($status == "OUT OF STOCK") {
                    $status_class = "out-of-stock";
                    $status_style = 'background-color: rgba(242, 0, 0, 0.15); color: #B70000; padding: 5px 10px; border-radius: 5px;';
                }

                echo "<tr>";
                echo "<td>" . $count++ . "</td>";
                echo "<td><strong>" . htmlspecialchars($row["name"]) . "</strong></td>";
                echo "<td>" . htmlspecialchars($row["itemID"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["qty"]) . "</td>";
                echo "<td>" . $measurement . "</td>"; // Measurement in uppercase
                echo "<td><span class='$status_class' style='$status_style'>" . $status . "</span></td>"; // Status in uppercase with specific styles

                // Format the last update date
                $formatted_date = date("F j, Y g:i A", strtotime($row["last_update"]));
                echo "<td>" . $formatted_date . "</td>";

                echo "<td>" . htmlspecialchars($row["updated_by"]) . "</td>";

                echo "<td>
                <div class='actions_icon'>
                    <a href='#' onclick=\"openEditModal('" . $row['inventoryID'] . "', '" . addslashes($row['itemID']) . "','" . addslashes($row['name']) . "', '" . addslashes($row['qty']) . "', '" . addslashes($row['measurement']) . "')\" data-icon-tooltip='Edit'>
                        <img src='../../assets/edit.svg' alt='Edit' class='settings_icon'>
                    </a>
                    <a href='#' onclick=\"openConfirmModal('" . $row['inventoryID'] . "', '" . addslashes($row['name']) . "')\" data-icon-tooltip='Remove'>
                        <img src='../../assets/trash-2.svg' alt='Remove' class='remove_icon'>
                    </a>
                </div>
              </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No items found</td></tr>";
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