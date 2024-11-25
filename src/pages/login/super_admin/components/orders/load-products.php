<?php
include '../../connection/database.php';
error_reporting(1);
$category = $_GET['category'] ?? 'pizza';
$search = $_GET['search'] ?? '';

// Query to fetch products based on category and search
$sql = "SELECT * FROM products WHERE category = ? AND STATUS = 'available'";

if (!empty($search)) {
    $sql .= " AND name LIKE ?";
}

$stmt = $conn->prepare($sql);
if (!empty($search)) {
    $searchParam = '%' . $search . '%';
    $stmt->bind_param("ss", $category, $searchParam);
} else {
    $stmt->bind_param("s", $category);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Initialize the availability flag
        $allIngredientsAvailable = true;

        echo '<div class="product-card">';

        $productImage = '../../assets/products/' . htmlspecialchars($row['img']);
        $buttonClass = 'btn-availability';
        $buttonText = 'Add to Order';
        $outOfStockClass = '';
        $grayscaleClass = '';
        $ingredientsClass = '';  // New variable for ingredients class
        $descriptionClass = '';  // New variable for description class

        // Check if the category is 'beverages'
        if ($category == 'beverages') {
            $ingredientsClass = 'bev';  // Add 'bev' to the class
            $descriptionClass = 'bev';  // Add 'bev' to the class
            $buttonClass2 .= ' bev2';  // Add 'bev' to the button class
        }

        if ($row['status'] == 'not available') {
            $outOfStockClass = 'out-of-stock-overlay';
            $grayscaleClass = 'grayscale';
            $buttonClass = 'btn-availability gray';
            $buttonText = 'Not Available';
        }

        // Product Image Div
        echo '<div class="product-image-container">';
        echo '<div class="product-image ' . $grayscaleClass . '">';
        echo '<img src="' . $productImage . '" alt="' . htmlspecialchars($row['name']) . '">';
        echo '</div>';

        // Out of Stock Overlay
        if ($row['status'] == 'not available') {
            echo '<div class="out-of-stock-overlay">';
            echo '<img src="../../assets/products/not-available.png" alt="Not Available" class="out-of-stock-img">';
            echo '</div>';
        }
        echo '</div>';

        echo '<div class="product-details">';
        echo '<h3 class="product-name">' . htmlspecialchars($row['name']) . '</h3>';
        // Add the 'bev' class to the product description paragraph
        echo '<p class="product-description ' . $descriptionClass . '">' . htmlspecialchars($row['slogan']) . '</p>';
        echo '<div class="product-size"><strong>Size:</strong><span class="size-value">' . htmlspecialchars($row['size']) . '</span></div>';
        echo '<div class="product-price"><span><strong>Price:</strong></span> <span class="price-value">₱' . htmlspecialchars($row['price']) . '</span></div>';

        echo '<div class="product-ingredients ' . $ingredientsClass . '">';  // Add the 'bev' class here
        echo '<h4>Ingredients:</h4>';
        $ingredients = json_decode($row['ingredients'], true);

        usort($ingredients, function ($a, $b) {
            return strcmp(strtolower($a['ingredient_name']), strtolower($b['ingredient_name']));
        });

        echo '<ul>';
        foreach ($ingredients as $ingredient) {
            $ingredientName = strtolower($ingredient['ingredient_name']);
            $ingredientQuantity = floatval($ingredient['quantity']);
            $ingredientMeasurement = $ingredient['measurement'];

            $inventorySql = "SELECT * FROM daily_inventory WHERE name = ?";
            $inventoryStmt = $conn->prepare($inventorySql);
            $inventoryStmt->bind_param("s", $ingredientName);
            $inventoryStmt->execute();
            $inventoryResult = $inventoryStmt->get_result();
            $isIngredientAvailable = false;
            $availableStockInKg = 0;
            $availableStockInPieces = 0;

            if ($inventoryResult->num_rows > 0) {
                $inventoryRow = $inventoryResult->fetch_assoc();
                $inventoryMeasurement = $inventoryRow['uom'];

                if ($inventoryMeasurement == 'grams') {
                    $availableStockInKg = floatval($inventoryRow['ending']) / 1000;
                } elseif ($inventoryMeasurement == 'kg') {
                    $availableStockInKg = floatval($inventoryRow['ending']);
                } elseif ($inventoryMeasurement == 'pc') {
                    $availableStockInPieces = intval($inventoryRow['ending']);
                } elseif ($inventoryMeasurement == 'bt') {
                    $availableStockInBottle = intval($inventoryRow['ending']);
                }

                if ($ingredientMeasurement == 'pcs' && $availableStockInPieces >= $ingredientQuantity) {
                    $isIngredientAvailable = true;
                } elseif ($ingredientMeasurement = 'grams' && $availableStockInKg >= ($ingredientQuantity / 1000)) {
                    $isIngredientAvailable = true;
                } elseif ($ingredientMeasurement = 'bottle' && $availableStockInBottle >= $ingredientQuantity) {
                    $isIngredientAvailable = true;
                } elseif ($ingredientMeasurement = 'pc' && $availableStockInPieces >= $ingredientQuantity) {
                    $isIngredientAvailable = true;
                }
            }

            $crossClass = '';
            if (!$isIngredientAvailable) {
                $crossClass = 'crossed-out';
                $allIngredientsAvailable = false;
            }

            echo '<li class="' . $crossClass . '">';
            echo ucwords($ingredientName) . ' ' . htmlspecialchars($ingredient['quantity']) . ' ' . htmlspecialchars($ingredient['measurement']);
            echo '</li>';
        }
        echo '</ul>';
        echo '</div>';

        echo '<div class="product-availability ' . $buttonClass2 . '">';
        echo '<button class="add-to-order-btn ' . $buttonClass . '" 
        data-name="' . htmlspecialchars($row['name']) . '" 
        data-size="' . htmlspecialchars($row['size']) . '"
        data-price="' . htmlspecialchars($row['price']) . '">';
        echo $buttonText;
        echo '</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        // Update product status based on ingredients availability
        $updateStatusSql = $allIngredientsAvailable ?
            "UPDATE products SET status = 'available' WHERE prodID = ?" :
            "UPDATE products SET status = 'not available' WHERE prodID = ?";
        $updateStatusStmt = $conn->prepare($updateStatusSql);
        $updateStatusStmt->bind_param("i", $row['prodID']);
        $updateStatusStmt->execute();
        $updateStatusStmt->close();
    }
} else {
    echo '<p>No products available in this category.</p>';
}

$stmt->close();
$conn->close();
