<?php
include '../../connection/database.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'uid';

// SQL query with search and sorting
$sql = "SELECT uid, name, email, position, userType 
        FROM accounts 
        WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR uid LIKE '%$search%' OR position LIKE '%$search%' OR userType LIKE '%$search%' 
        ORDER BY $sort";

$result = $conn->query($sql);
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
                            <a href='#' data-icon-tooltip='Remove'>
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
$conn->close();
?>