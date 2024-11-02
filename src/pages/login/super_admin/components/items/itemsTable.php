<?php
include '../../connection/database.php';

// Handle search and sort inputs
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'itemID';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

// Define valid columns for sorting and valid order directions
$valid_sort_columns = ['itemID', 'name', 'addedBy', 'date', 'shelfLife'];
$valid_order_directions = ['asc', 'desc'];

$sort = in_array($sort, $valid_sort_columns) ? $sort : 'itemID';
$order = in_array($order, $valid_order_directions) ? $order : 'asc';

// SQL query using prepared statements to prevent SQL injection
$sql = "SELECT itemID, name, addedBy, date, shelfLife 
        FROM items 
        WHERE (name LIKE ? OR addedBy LIKE ? OR itemID LIKE ?)
        ORDER BY $sort $order";

$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param('sss', $search_param, $search_param, $search_param);
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
            <th>Shelf Life (avg)</th>
            <th>Date Added</th>
            <th>Added By</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            $count = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $count++ . "</td>";
                echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["itemID"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["shelfLife"]) . " days</td>";

                // Format the date
                $formatted_date = date("F j, Y", strtotime($row["date"]));
                echo "<td>" . $formatted_date . "</td>";

                echo "<td>" . htmlspecialchars($row["addedBy"]) . "</td>";

                echo "<td>
                <div class='actions_icon'>
                    <a href='#' onclick=\"openConfirmModal('" . $row["itemID"] . "')\" data-icon-tooltip='Remove'>
                        <img src='../../assets/trash-2.svg' alt='Remove' class='remove_icon'>
                    </a>
                </div>
              </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No items found</td></tr>";
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