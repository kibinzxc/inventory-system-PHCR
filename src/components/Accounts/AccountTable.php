<?php
include '../../connection/database.php';

// Handle search and sort inputs
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'uid';

// Define valid columns for sorting to prevent SQL injection
$valid_sort_columns = ['uid', 'name', 'email', 'position', 'userType'];
$sort = in_array($sort, $valid_sort_columns) ? $sort : 'uid';

// SQL query using prepared statements to prevent SQL injection
$sql = "SELECT uid, name, email, position, userType 
        FROM accounts 
        WHERE name LIKE ? OR email LIKE ? OR uid LIKE ? OR position LIKE ? OR userType LIKE ? 
        ORDER BY $sort";

$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param('sssss', $search_param, $search_param, $search_param, $search_param, $search_param);
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
            <th>Position</th>
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
                echo "<td>" . $row["name"] . "</td>";
                echo "<td>" . $row["email"] . "</td>";
                echo "<td>" . $row["position"] . "</td>";
                echo "<td>" . $row["userType"] . "</td>";
                echo "<td>
                        <div class='actions_icon'>
                            <a href='#' data-icon-tooltip='Edit'>
                                <img src='../../assets/edit.svg' alt='Edit' class='settings_icon'>
                            </a>
                            <a href='delete.php?uid=" . $row["uid"] . "' data-icon-tooltip='Remove'>
                                <img src='../../assets/trash-2.svg' alt='Remove' class='remove_icon'>
                            </a>
                        </div>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No accounts found</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$stmt->close();
$conn->close();
?>