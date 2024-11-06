<?php
include '../../connection/database.php';

// Handle search and sort inputs
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'itemID';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc'; // Get order parameter

// Define valid columns for sorting and valid order directions to prevent SQL injection
$valid_sort_columns = ['itemID', 'name', 'qty', 'measurement', 'status'];
$valid_order_directions = ['asc', 'desc'];

$sort = in_array($sort, $valid_sort_columns) ? $sort : 'itemID';
$order = in_array($order, $valid_order_directions) ? $order : 'asc'; // Validate order

// SQL query using prepared statements to prevent SQL injection
$sql = "SELECT itemID, name, qty, measurement, status 
        FROM inventory 
        WHERE (name LIKE ? OR qty LIKE ? OR measurement LIKE ? OR status LIKE ?)
        ORDER BY $sort $order"; // Adjust the query for inventory

$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param('ssss', $search_param, $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<link rel="stylesheet" href="AccountTable.css">


<table border="1">
    <thead>
        <tr>
            <th>No.</th>
            <th>Item ID</th>
            <th>Name</th>
            <th>Quantity</th>
            <th>Measurement</th>
            <th>Status</th>
            <th>Actions</th> <!-- This column will be hidden on mobile -->
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            $count = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $count++ . "</td>";
                echo "<td>" . $row["itemID"] . "</td>";
                echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["qty"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["measurement"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["status"]) . "</td>";
                echo "<td>
                        <div class='actions_icon'>
                            <a href='#' onclick=\"openEditModal('" . $row['itemID'] . "', '" . addslashes($row['name']) . "', '" . addslashes($row['qty']) . "', '" . addslashes($row['measurement']) . "', '" . addslashes($row['status']) . "')\" data-icon-tooltip='Edit'>
                                <img src='../../assets/edit.svg' alt='Edit' class='settings_icon'>
                            </a>
                            <a href='#' onclick=\"openConfirmModal('" . $row["itemID"] . "')\" data-icon-tooltip='Remove'>
                                <img src='../../assets/trash-2.svg' alt='Remove' class='remove_icon'>
                            </a>
                        </div>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No inventory items found</td></tr>"; // Updated colspan to match the table headers
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