<?php
include '../../connection/database.php';
error_reporting(1);

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

// Get all products
$sql = "SELECT * FROM products";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$ingredientThresholds = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Get product ingredients
        $ingredients = json_decode($row['ingredients'], true);

        // Calculate the average orders per day and low stock threshold for the product
        $averageOrdersPerDay = getAverageOrdersPerDay($conn, $row['name']);
        $lowStockThreshold = $averageOrdersPerDay * 3;

        foreach ($ingredients as $ingredient) {
            $ingredientName = strtolower($ingredient['ingredient_name']);
            $ingredientQuantity = floatval($ingredient['quantity']);
            $ingredientMeasurement = $ingredient['measurement'];

            // Convert grams to kg if necessary
            if ($ingredientMeasurement === 'grams') {
                $ingredientQuantity /= 1000; // Convert to kg
                $ingredientMeasurement = 'kg'; // Update measurement
            }

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

// Update inventory status based on thresholds
foreach ($ingredientThresholds as $ingredientName => $data) {
    $threshold = $data['quantity'];
    $updateInventoryStatusSql = "
        UPDATE daily_inventory
        SET status = CASE
            WHEN ending = 0 THEN 'out of stock'
            WHEN ending > 0 AND ending <= ? THEN 'low stock'
            WHEN ending > ? THEN 'in stock'
            ELSE status
        END
        WHERE LOWER(name) = ?
    ";

    $updateInventoryStatusStmt = $conn->prepare($updateInventoryStatusSql);
    $updateInventoryStatusStmt->bind_param("dds", $threshold, $threshold, $ingredientName);
    $updateInventoryStatusStmt->execute();
    $updateInventoryStatusStmt->close();
}

$conn->close();
