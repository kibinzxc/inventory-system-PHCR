<?php
include '../../connection/database.php';

// Handle search, sort, and order inputs
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

// Define valid columns for sorting and valid order directions
$valid_sort_columns = ['name', 'status'];
$valid_order_directions = ['asc', 'desc'];

// Ensure valid sort column and direction
$sort = in_array($sort, $valid_sort_columns) ? $sort : 'name';
$order = in_array($order, $valid_order_directions) ? $order : 'asc';

// SQL query using prepared statements to prevent SQL injection
$sql = "SELECT name, ingredients, status
        FROM products
        WHERE name LIKE ?
        ORDER BY $sort $order";

$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param('s', $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<link rel="stylesheet" href="itemsTable.css">

<table border="1">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Ingredients</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            $count = 1;
            while ($row = $result->fetch_assoc()) {
                // Decode the JSON ingredients
                $ingredients = json_decode($row["ingredients"], true);


                $ingredient_list = '';
                if (is_array($ingredients)) {
                    foreach ($ingredients as $ingredient) {
                        // Check if keys exist and use default values if missing
                        $ingredient_name = isset($ingredient['ingredient_name']) ? $ingredient['ingredient_name'] : 'Unknown';
                        $quantity = isset($ingredient['qty']) ? $ingredient['qty'] : 'N/A';
                        $measurement = isset($ingredient['measurement']) ? $ingredient['measurement'] : '';

                        $ingredient_list .= "<li>" . htmlspecialchars($ingredient_name) . ": " . htmlspecialchars($quantity) . " " . htmlspecialchars($measurement) . "</li>";
                    }
                } else {
                    $ingredient_list = "<li>Error decoding ingredients</li>";
                }

                $status = isset($row['status']) ? strtoupper(htmlspecialchars($row['status'])) : 'Unknown';

                echo "<tr>";
                echo "<td>" . $count++ . "</td>";
                echo "<td><strong>" . strtoupper(htmlspecialchars($row["name"])) . "</strong></td>";
                echo "<td style='text-align: left;'>" . "<ul style='padding-left: 20px;'>" . $ingredient_list . "</ul>" . "</td>";
                echo "<td><strong>" . $status . "</strong></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No products found</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$stmt->close();
$conn->close();
?>