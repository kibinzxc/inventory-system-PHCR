<?php
include '../../connection/database.php';
error_reporting(0);

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'ingredient_name'; // Default to sorting by ingredient name
$order = isset($_GET['order']) ? $_GET['order'] : 'asc'; // Default to ascending order

// Function to calculate the average orders per day for the last week
function getAverageOrdersPerDay($conn, $productName)
{
    $currentDate = new DateTime();
    $currentDate->setISODate($currentDate->format('Y'), $currentDate->format('W'));
    $startOfLastWeek = $currentDate->modify('last Monday')->format('Y-m-d');
    $endOfLastWeek = $currentDate->modify('next Sunday')->setTime(23, 59)->format('Y-m-d H:i');

    $sql = "
        SELECT SUM(quantity) AS total_quantity
        FROM usage_reports
        WHERE name = ? AND day_counted >= ? AND day_counted <= ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $productName, $startOfLastWeek, $endOfLastWeek);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $totalOrdersLastWeek = $row['total_quantity'] ?? 0;

    return ($totalOrdersLastWeek > 0) ? round($totalOrdersLastWeek / 7) : 0;
}

// Initialize an empty array for ingredient thresholds
$ingredientThresholds = [];

// Get all products and filter by ingredient names
$sql = "SELECT * FROM products";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Get product ingredients
        $ingredients = json_decode($row['ingredients'], true);

        // Filter ingredients by search term
        foreach ($ingredients as $ingredient) {
            $ingredientName = strtolower($ingredient['ingredient_name']); // Ensure consistent case
            if ($search && strpos($ingredientName, strtolower($search)) === false) {
                continue; // Skip if ingredient name doesn't match search term
            }

            $ingredientQuantity = floatval($ingredient['quantity']);
            $ingredientMeasurement = $ingredient['measurement'];

            // Calculate the average orders per day and low stock threshold for the product
            $averageOrdersPerDay = getAverageOrdersPerDay($conn, $row['name']);
            $lowStockThreshold = $averageOrdersPerDay * 3;

            // Calculate the total quantity needed for the ingredient based on the low stock threshold
            $totalQuantityNeeded = $ingredientQuantity * $lowStockThreshold;

            // Add the quantity needed to the ingredient thresholds array
            if (!isset($ingredientThresholds[$ingredientName])) {
                $ingredientThresholds[$ingredientName] = ['quantity' => 0, 'measurement' => $ingredientMeasurement];
            }
            $ingredientThresholds[$ingredientName]['quantity'] += $totalQuantityNeeded;
        }
    }
} else {
    echo '<p>No products found.</p>';
}

$stmt->close();

// Sorting ingredients by name or threshold
if ($sort == 'ingredient_name') {
    // Sort by ingredient name (key) alphabetically
    uksort($ingredientThresholds, function ($a, $b) use ($order) {
        if ($order == 'asc') {
            return strcmp($a, $b); // Ascending alphabetical order
        } else {
            return strcmp($b, $a); // Descending alphabetical order
        }
    });
} elseif ($sort == 'low_stock_threshold') {
    // Sort by threshold (value) in ascending order, reverse for descending
    uasort($ingredientThresholds, function ($a, $b) use ($order) {
        if ($order == 'asc') {
            return $a['quantity'] <=> $b['quantity']; // Ascending order
        } else {
            return $b['quantity'] <=> $a['quantity']; // Descending order
        }
    });
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<link rel="stylesheet" href="itemsTable.css">
<style>
    table {
        color: #343434;
        width: 500px;
        margin: 0 auto;
        /* Center the table */
        margin-bottom: 50px;
    }

    tr {
        text-align: left;
    }

    tbody {
        text-align: left;
    }
</style>

<table id="ingredientsTable">
    <thead>
        <tr>
            <th>Ingredient Name</th>
            <th>Low Stock Threshold</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ingredientThresholds as $ingredientName => $data): ?>
            <tr>
                <td><strong><?php echo ucfirst($ingredientName); ?></strong></td>
                <td><strong>
                        <?php
                        $quantity = $data['quantity'];
                        $measurement = $data['measurement'];

                        // Convert grams to kg if the measurement is grams
                        if ($measurement === 'grams') {
                            $quantity = $quantity / 1000; // Convert to kg
                            $measurement = 'kg'; // Update measurement
                        }

                        // Format quantity and remove unnecessary .00
                        $quantity = rtrim(rtrim(number_format($quantity, 2), '0'), '.');

                        // Display formatted quantity with measurement
                        if ($measurement === 'kg') {
                            echo "$quantity kg";
                        } elseif ($measurement === 'pc' || $measurement === 'pcs') {
                            echo "$quantity pcs";
                        } elseif ($measurement === 'bottle') {
                            echo "$quantity bottle" . ($quantity > 1 ? 's' : '');
                        } else {
                            echo "$quantity $measurement";
                        }
                        ?>
                    </strong></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>