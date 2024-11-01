<?php
include '../../connection/database.php';

// Handle search and sort inputs
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'ingredientsID';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc'; // Get order parameter

// Define valid columns for sorting and valid order directions to prevent SQL injection
$valid_sort_columns = ['ingredientsID', 'name', 'addedBy', 'date'];
$valid_order_directions = ['asc', 'desc'];

$sort = in_array($sort, $valid_sort_columns) ? $sort : 'ingredientsID';
$order = in_array($order, $valid_order_directions) ? $order : 'asc'; // Validate order

// SQL query using prepared statements to prevent SQL injection
$sql = "SELECT ingredientsID, name, addedBy, date 
        FROM ingredients 
        WHERE (name LIKE ? OR addedBy LIKE ? OR ingredientsID LIKE ?)
        ORDER BY $sort $order"; // Include order

$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param('sss', $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<link rel="stylesheet" href="IngredientsTable.css">

<table border="1">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Code</th>
            <th>Added By</th>
            <th>Date Added</th>
            <th>Actions</th><!-- This column will be hidden on mobile -->
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
                echo "<td>" . htmlspecialchars($row["ingredientsID"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["addedBy"]) . "</td>";

                // Format the date
                $formatted_date = date("F j, Y g:i A", strtotime($row["date"]));
                echo "<td>" . $formatted_date . "</td>"; // Output the formatted date

                echo "<td>
                <div class='actions_icon'>

                    <a href='#' onclick=\"openConfirmModal('" . $row["ingredientsID"] . "')\" data-icon-tooltip='Remove'>
                        <img src='../../assets/trash-2.svg' alt='Remove' class='remove_icon'>
                    </a>
                </div>
              </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No ingredients found</td></tr>"; // Updated colspan to match the table headers
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