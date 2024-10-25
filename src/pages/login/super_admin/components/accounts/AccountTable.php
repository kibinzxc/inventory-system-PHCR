<?php
include '../../connection/database.php';

// Handle search and sort inputs
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'uid';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc'; // Get order parameter

// Define valid columns for sorting and valid order directions to prevent SQL injection
$valid_sort_columns = ['uid', 'name', 'email', 'userType'];
$valid_order_directions = ['asc', 'desc'];

$sort = in_array($sort, $valid_sort_columns) ? $sort : 'uid';
$order = in_array($order, $valid_order_directions) ? $order : 'asc'; // Validate order

// SQL query using prepared statements to prevent SQL injection
$sql = "SELECT uid, name, email, userType 
        FROM accounts 
        WHERE name LIKE ? OR email LIKE ? OR uid LIKE ? OR userType LIKE ? 
        ORDER BY $sort $order"; // Include order in SQL

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
            <th>UID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
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
                echo "<td>" . $row["uid"] . "</td>";
                echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["userType"]) . "</td>";
                echo "<td>
                        <div class='actions_icon'>
                            <a href='#' onclick=\"openEditModal('" . $row['uid'] . "', '" . addslashes($row['name']) . "', '" . addslashes($row['email']) . "', '" . addslashes($row['userType']) . "')\" data-icon-tooltip='Edit'>
                                <img src='../../assets/edit.svg' alt='Edit' class='settings_icon'>
                            </a>
                            <a href='#' onclick=\"openConfirmModal('" . $row["uid"] . "')\" data-icon-tooltip='Remove'>
                                <img src='../../assets/trash-2.svg' alt='Remove' class='remove_icon'>
                            </a>
                        </div>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No accounts found</td></tr>"; // Updated colspan to match the table headers
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