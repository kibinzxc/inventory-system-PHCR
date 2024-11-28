<?php
include '../../connection/database.php';
error_reporting(1);

// Get all products
$sql = "SELECT * FROM products";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $allIngredientsAvailable = true;

        // Get product ingredients
        $ingredients = json_decode($row['ingredients'], true);

        foreach ($ingredients as $ingredient) {
            $ingredientName = strtolower($ingredient['ingredient_name']);
            $ingredientQuantity = floatval($ingredient['quantity']);
            $ingredientMeasurement = $ingredient['measurement'];

            // Check ingredient availability in the inventory
            $inventorySql = "SELECT * FROM daily_inventory WHERE name = ?";
            $inventoryStmt = $conn->prepare($inventorySql);
            $inventoryStmt->bind_param("s", $ingredientName);
            $inventoryStmt->execute();
            $inventoryResult = $inventoryStmt->get_result();
            $isIngredientAvailable = false;

            if ($inventoryResult->num_rows > 0) {
                $inventoryRow = $inventoryResult->fetch_assoc();
                $inventoryMeasurement = $inventoryRow['uom'];
                $availableStock = floatval($inventoryRow['ending']);
                $availableStockInKg = 0;
                $availableStockInPieces = 0;
                $availableStockInBottle = 0;

                // Convert available stock based on measurement unit
                if ($inventoryMeasurement == 'grams') {
                    $availableStockInKg = $availableStock / 1000;  // Convert grams to kg
                } elseif ($inventoryMeasurement == 'kg') {
                    $availableStockInKg = $availableStock;
                } elseif ($inventoryMeasurement == 'pc') {
                    $availableStockInPieces = $availableStock;
                } elseif ($inventoryMeasurement == 'bt') {
                    $availableStockInBottle = $availableStock;
                }

                // Check availability based on ingredient's measurement
                if ($ingredientMeasurement == 'pcs' && $availableStockInPieces >= $ingredientQuantity) {
                    $isIngredientAvailable = true;
                } elseif ($ingredientMeasurement == 'grams' && $availableStockInKg >= ($ingredientQuantity / 1000)) {
                    $isIngredientAvailable = true;
                } elseif ($ingredientMeasurement == 'bottle' && $availableStockInBottle >= $ingredientQuantity) {
                    $isIngredientAvailable = true;
                } elseif ($ingredientMeasurement == 'pc' && $availableStockInPieces >= $ingredientQuantity) {
                    $isIngredientAvailable = true;
                }
            }

            // If any ingredient is not available, set the flag to false
            if (!$isIngredientAvailable) {
                $allIngredientsAvailable = false;
                break;
            }
        }

        // Update product status based on ingredient availability
        $updateStatusSql = $allIngredientsAvailable ?
            "UPDATE products SET status = 'available' WHERE prodID = ?" :
            "UPDATE products SET status = 'not available' WHERE prodID = ?";
        $updateStatusStmt = $conn->prepare($updateStatusSql);
        $updateStatusStmt->bind_param("i", $row['prodID']);
        $updateStatusStmt->execute();
        $updateStatusStmt->close();
    }
} else {
    echo '<p>No products found.</p>';
}


$stmt->close();
$conn->close();
