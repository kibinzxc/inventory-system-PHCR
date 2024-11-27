<?php
require('../fpdf186/fpdf.php'); // Include the FPDF library
include '../../connection/database.php';
Error_reporting(1);

// Get the 'product' parameter from the URL
$selectedProduct = isset($_GET['product']) ? $_GET['product'] : '';

// Query to get details of the selected product
$productDetails = null;
if ($selectedProduct) {
    $productSql = "SELECT * FROM products WHERE name = ?";
    $productStmt = $conn->prepare($productSql);
    $productStmt->bind_param("s", $selectedProduct);
    $productStmt->execute();
    $productResult = $productStmt->get_result();

    if ($productResult->num_rows > 0) {
        $productDetails = $productResult->fetch_assoc();
    }
}

$currentDate = new DateTime();
$currentDate->setISODate($currentDate->format('Y'), $currentDate->format('W'));

// Calculate the start and end date for last week (Monday to Sunday 12:59 PM)
$startOfLastWeek = $currentDate->modify('last Monday')->format('Y-m-d');
$endOfLastWeek = $currentDate->modify('next Sunday')->setTime(23, 59)->format('Y-m-d H:i');

// Calculate the start and end date for the week before last (Monday to Sunday 12:59 PM)
$startOfWeekBeforeLast = $currentDate->modify('last Monday')->modify('-1 week')->format('Y-m-d');
$endOfWeekBeforeLast = $currentDate->modify('next Sunday')->setTime(23, 59)->format('Y-m-d H:i');

// Query to get the total quantity of orders for the selected product for last week
$sqlOrdersLastWeek = "
    SELECT SUM(quantity) AS total_quantity
    FROM usage_reports
    WHERE name = ? AND day_counted >= ? AND day_counted <= ?
";
$stmtOrdersLastWeek = $conn->prepare($sqlOrdersLastWeek);
$stmtOrdersLastWeek->bind_param("sss", $selectedProduct, $startOfLastWeek, $endOfLastWeek);
$stmtOrdersLastWeek->execute();
$resultOrdersLastWeek = $stmtOrdersLastWeek->get_result();
$rowLastWeek = $resultOrdersLastWeek->fetch_assoc();
$totalOrdersLastWeek = $rowLastWeek['total_quantity'] ?? 0;

$averageOrderCountLastWeek = ($totalOrdersLastWeek > 0) ? round($totalOrdersLastWeek / 7) : 0;

// Query to get the total quantity of orders for the selected product for the week before last
$sqlOrdersWeekBeforeLast = "
    SELECT SUM(quantity) AS total_quantity
    FROM usage_reports
    WHERE name = ? AND day_counted >= ? AND day_counted <= ?
";
$stmtOrdersWeekBeforeLast = $conn->prepare($sqlOrdersWeekBeforeLast);
$stmtOrdersWeekBeforeLast->bind_param("sss", $selectedProduct, $startOfWeekBeforeLast, $endOfWeekBeforeLast);
$stmtOrdersWeekBeforeLast->execute();
$resultOrdersWeekBeforeLast = $stmtOrdersWeekBeforeLast->get_result();
$rowWeekBeforeLast = $resultOrdersWeekBeforeLast->fetch_assoc();
$totalOrdersWeekBeforeLast = $rowWeekBeforeLast['total_quantity'] ?? 0;

// Calculate the percentage increase (or decrease)
$percentageChange = 0;
if ($totalOrdersWeekBeforeLast > 0) {
    $percentageChange = (($totalOrdersLastWeek - $totalOrdersWeekBeforeLast) / $totalOrdersWeekBeforeLast) * 100;
}

// Calculate how many products can be made with the existing ingredients
$productsPossible = PHP_INT_MAX;
if ($productDetails) {
    $ingredients = json_decode($productDetails['ingredients'], true);
    if ($ingredients) {
        foreach ($ingredients as $ingredient) {
            $ingredientName = $ingredient['ingredient_name'];
            $requiredQuantity = $ingredient['quantity'];
            $measurement = $ingredient['measurement'];

            // Map measurements to inventory units
            $unitMap = [
                'pcs' => 'pcs',
                'pc' => 'pcs',
                'grams' => 'kg',
                'bottle' => 'bt',
            ];
            $inventoryUnit = $unitMap[$measurement] ?? $measurement;

            // Convert grams to kg if necessary
            if ($measurement === 'grams') {
                $requiredQuantity /= 1000;
            }

            // Get the stock for the ingredient
            $inventorySql = "SELECT ending, uom FROM daily_inventory WHERE name = ? AND uom = ?";
            $inventoryStmt = $conn->prepare($inventorySql);
            $inventoryStmt->bind_param('ss', $ingredientName, $inventoryUnit);
            $inventoryStmt->execute();
            $inventoryResult = $inventoryStmt->get_result();

            if ($inventoryResult->num_rows > 0) {
                $inventoryRow = $inventoryResult->fetch_assoc();
                $availableStock = $inventoryRow['ending'];

                // Calculate how many products can be made with this ingredient
                $possibleProducts = floor($availableStock / $requiredQuantity);

                // Update the minimum number of products possible
                $productsPossible = min($productsPossible, $possibleProducts);
            } else {
                // If an ingredient is missing from inventory, the product cannot be made
                $productsPossible = 0;
                break;
            }
        }
    } else {
        $productsPossible = 0; // No ingredients, product cannot be made
    }
}

// Calculate days until product runs out
$averageOrdersPerDay = ($totalOrdersLastWeek > 0) ? round($totalOrdersLastWeek / 7) : 0;
$daysUntilOutOfStock = ($averageOrdersPerDay > 0) ? floor($productsPossible / $averageOrdersPerDay) : PHP_INT_MAX;

// Calculate the low stock threshold
$lowStockThreshold = $averageOrdersPerDay * 3; // Minimum stock to last for 3 days

// Query to get the top 5 fast-moving products from last week
$sqlFastMoving = "
    SELECT name, SUM(quantity) AS total_quantity
    FROM usage_reports
    WHERE day_counted >= ? AND day_counted <= ?
    GROUP BY name
    ORDER BY total_quantity DESC
    LIMIT 5
";
$stmtFastMoving = $conn->prepare($sqlFastMoving);
$stmtFastMoving->bind_param("ss", $startOfLastWeek, $endOfLastWeek);
$stmtFastMoving->execute();
$resultFastMoving = $stmtFastMoving->get_result();

$fastMovingProducts = [];
while ($row = $resultFastMoving->fetch_assoc()) {
    $fastMovingProducts[] = $row['name'];
}

// Query to get the top 5 slow-moving products from last week
$sqlSlowMoving = "
    SELECT name, SUM(quantity) AS total_quantity
    FROM usage_reports
    WHERE day_counted >= ? AND day_counted <= ?
    GROUP BY name
    ORDER BY total_quantity ASC
    LIMIT 5
";
$stmtSlowMoving = $conn->prepare($sqlSlowMoving);
$stmtSlowMoving->bind_param("ss", $startOfLastWeek, $endOfLastWeek);
$stmtSlowMoving->execute();
$resultSlowMoving = $stmtSlowMoving->get_result();

$slowMovingProducts = [];
while ($row = $resultSlowMoving->fetch_assoc()) {
    $slowMovingProducts[] = $row['name'];
}

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Add Pizza Hut logo
$pdf->Image('../../assets/logo-black.png', 75, 10, 50); // Adjust the path and size as needed
// Add "Date Generated" text
$pdf->SetXY(10, 15); // Move to the right of the logo
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Date Generated: ' . date('Y-m-d'), 0, 1);

// Move to the next line after the logo and date
$pdf->Ln(5);

if ($productDetails && !empty($productDetails['img'])) {
    $pdf->Image('../../assets/products/' . $productDetails['img'], 120, 40, 50);
    $pdf->Ln(10); // Move to the next line after the image
}

// Product Name
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Product Name: ' . $selectedProduct, 0, 1);

// Product Details
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Size: ' . $productDetails['size'], 0, 1);
$pdf->Cell(0, 10, 'Price: PHP ' . number_format($productDetails['price'], 2), 0, 1);

// Ingredients and Inventory Stocks (only if category is not beverages)

$pdf->Ln(10); // Add a line break
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Ingredients and Inventory Stocks:', 0, 1);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(95, 10, 'Ingredient Name & Quantity', 1, 0, 'L');
$pdf->Cell(95, 10, 'Inventory Stock', 1, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
if ($ingredients) {
    usort($ingredients, function ($a, $b) {
        return strcmp(strtolower($a['ingredient_name']), strtolower($b['ingredient_name']));
    });
    foreach ($ingredients as $ingredient) {
        $ingredientName = ucfirst(strtolower($ingredient['ingredient_name']));
        $pdf->Cell(95, 10, $ingredientName . ': ' . $ingredient['quantity'] . ' ' . $ingredient['measurement'], 1, 0, 'L');

        // Get the stock for the ingredient
        $inventorySql = "SELECT ending, uom FROM daily_inventory WHERE name = ?";
        $inventoryStmt = $conn->prepare($inventorySql);
        $inventoryStmt->bind_param('s', $ingredientName);
        $inventoryStmt->execute();
        $inventoryResult = $inventoryStmt->get_result();

        if ($inventoryResult->num_rows > 0) {
            $inventoryRow = $inventoryResult->fetch_assoc();
            $availableStock = $inventoryRow['ending'];
            $uom = $inventoryRow['uom'];

            // Check if the stock is enough
            $requiredQuantity = $ingredient['quantity'];
            if ($uom === 'grams') {
                $requiredQuantity /= 1000; // Convert grams to kg
            }
            $isStockEnough = $availableStock >= $requiredQuantity;

            if ($uom === 'kg') {
                // Convert ingredient quantity to kg if it's in grams
                $requiredQuantity = ($ingredient['measurement'] === 'grams') ? $ingredient['quantity'] / 1000 : $ingredient['quantity'];

                // Check stock availability
                $isStockEnough = $availableStock >= $requiredQuantity;

                if (!$isStockEnough) {
                    $pdf->SetTextColor(255, 0, 0); // Red color
                }
                $pdf->Cell(95, 10, number_format($availableStock, 2) . ' kg / ' . number_format($availableStock * 1000) . ' grams', 1, 0, 'C');
                $pdf->SetTextColor(0, 0, 0); // Reset to black
            } elseif ($uom === 'bt') {
                if (!$isStockEnough) {
                    $pdf->SetTextColor(255, 0, 0); // Red color
                }
                $pdf->Cell(95, 10, $availableStock . ' bottle' . ($availableStock > 1 ? 's' : ''), 1, 0, 'C');
            } else {
                if (!$isStockEnough) {
                    $pdf->SetTextColor(255, 0, 0); // Red color
                }
                $pdf->Cell(95, 10, $availableStock . ' ' . ($uom === 'pc' ? 'pcs' : $uom), 1, 0, 'C');
            }
            $pdf->SetTextColor(0, 0, 0); // Reset color to black
        } else {
            $pdf->SetTextColor(255, 0, 0); // Red color
            $pdf->Cell(95, 10, 'No stocks', 1, 0, 'C');
            $pdf->SetTextColor(0, 0, 0); // Reset color to black
        }
        $pdf->Ln();
    }
} else {
    $pdf->Cell(190, 10, 'No ingredient data available.', 1);
}

// Add a new page for orders and analysis
$pdf->AddPage();

// Add "Orders and Analysis" text
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Orders and Analysis', 0, 1, 'C');

// Product Details
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 5, 'Product Name: ' . $selectedProduct, 0, 1, 'C');

$pdf->Cell(0, 5, 'Size: ' . $productDetails['size'], 0, 1, 'C');
$pdf->Cell(0, 5, 'Price: PHP ' . number_format($productDetails['price'], 2), 0, 1, 'C');
$pdf->Ln(5);
// Orders and Analysis
$pdf->Cell(0, 10, 'Total Orders Last Week: ' . number_format($totalOrdersLastWeek), 0, 1);
$pdf->Cell(0, 10, 'Total Orders 2 Weeks Ago: ' . number_format($totalOrdersWeekBeforeLast), 0, 1);

// Comparison
if ($percentageChange > 0) {
    $pdf->Cell(0, 10, 'Comparison: Went up by ' . number_format($percentageChange, 2) . '%', 0, 1);
} elseif ($percentageChange < 0) {
    $pdf->Cell(0, 10, 'Comparison: Went down by ' . number_format($percentageChange, 2) . '%', 0, 1);
} else {
    $pdf->Cell(0, 10, 'Comparison: No change in orders compared to two weeks ago', 0, 1);
}
$pdf->SetTextColor(0, 0, 0); // Reset color to black

$pdf->Cell(0, 10, 'Average Orders per Day Last Week: ' . number_format($averageOrderCountLastWeek), 0, 1);
$pdf->Cell(0, 10, 'Product Status: ' . ($productDetails['status'] == 'available' ? 'Available' : 'Not Available'), 0, 1);
if ($productsPossible > 0) {
    $pdf->Cell(0, 10, "Product can be made for $productsPossible more orders based on current ingredient stocks.", 0, 1);
    if ($daysUntilOutOfStock !== PHP_INT_MAX) {
        $pdf->Cell(0, 10, "Based on the average daily orders last week, the product will last for $daysUntilOutOfStock day(s).", 0, 1);
    }
} else {
    $pdf->Cell(0, 10, "Product is unavailable due to insufficient stock of ingredients.", 0, 1);
}

// Display low stock threshold
$pdf->Cell(0, 10, 'Low Stock Threshold: ' . number_format($lowStockThreshold) . ' orders (to last for at least 3 days)', 0, 1);

// Required Stocks Table
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(95, 10, 'Ingredient Name', 1, 0, 'C');
$pdf->Cell(95, 10, 'Quantity Needed', 1, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
if ($ingredients) {
    foreach ($ingredients as $ingredient) {
        $ingredientName = ucfirst(strtolower($ingredient['ingredient_name']));
        $quantity = $ingredient['quantity'];
        $measurement = $ingredient['measurement'];

        // Convert grams to kg if necessary
        if ($measurement === 'grams') {
            $quantityInKg = $quantity / 1000;
            $quantityNeeded = $quantityInKg * $lowStockThreshold;
            $quantityNeededGrams = $quantity * $lowStockThreshold;
            $pdf->Cell(95, 10, $ingredientName, 1, 0, 'C');
            $pdf->Cell(95, 10, number_format($quantityNeeded, 2) . ' kg / ' . number_format($quantityNeededGrams) . ' grams', 1, 0, 'C');
        } else {
            $quantityNeeded = $quantity * $lowStockThreshold;
            $pdf->Cell(95, 10, $ingredientName, 1, 0, 'C');
            if ($measurement === 'bottle') {
                $pdf->Cell(95, 10, $quantityNeeded . ' bottles', 1, 0, 'C');
            } else {
                $pdf->Cell(95, 10, $quantityNeeded . ' ' . ($measurement === 'pc' ? 'pcs' : $measurement), 1, 0, 'C');
            }
        }
        $pdf->Ln();
    }
} else {
    $pdf->Cell(190, 10, 'No ingredient data available.', 1);
}

// Note on fast-moving or slow-moving
$pdf->Ln(10); // Add a line break
if (in_array($selectedProduct, $fastMovingProducts)) {
    $pdf->SetTextColor(0, 128, 0); // Green color
    $pdf->Cell(0, 10, 'Note: This product is one of the fast-moving products from last week.', 0, 1, 'C');
} elseif (in_array($selectedProduct, $slowMovingProducts)) {
    $pdf->SetTextColor(255, 165, 0); // Orange color
    $pdf->Cell(0, 10, 'Note: This product is one of the slow-moving products from last week.', 0, 1, 'C');
} else {
    $pdf->SetTextColor(0, 0, 0); // Black color
    $pdf->Cell(0, 10, 'Note: This product is neither fast-moving nor slow-moving.', 0, 1, 'C');
}
$pdf->Output();
