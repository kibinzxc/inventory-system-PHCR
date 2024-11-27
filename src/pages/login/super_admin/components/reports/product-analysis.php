<?php
include '../../connection/database.php';
Error_reporting(1);

// Query to get all product names
$sql = "SELECT name FROM products";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

// Get the 'product' parameter from the URL if it exists
$selectedProduct = isset($_GET['product']) ? $_GET['product'] : '';

// Query to get details of the selected product (if it exists)
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

// Set the locale to ensure Monday is the start of the week
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
$totalOrdersLastWeek = $rowLastWeek['total_quantity'];

$averageOrderCountLastWeek = 0;
if ($totalOrdersLastWeek > 0) {
    $averageOrderCountLastWeek = $totalOrdersLastWeek / 7; // Dividing by 7 days
}

// Round the average to the nearest whole number
$averageOrderCountLastWeek = round($averageOrderCountLastWeek);

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
$totalOrdersWeekBeforeLast = $rowWeekBeforeLast['total_quantity'];

// Calculate the percentage increase (or decrease)
$percentageChange = 0;
if ($totalOrdersWeekBeforeLast > 0) {
    $percentageChange = (($totalOrdersLastWeek - $totalOrdersWeekBeforeLast) / $totalOrdersWeekBeforeLast) * 100;
}

// Calculate the low stock threshold
$lowStockThreshold = $averageOrderCountLastWeek * 3; // Minimum stock to last for 3 days

?>

<link rel="stylesheet" href="product-analysis.css">

<!-- Product Selection Dropdown -->
<label for="product_name">Select a product:</label>
<select name="product_name" id="product_name" onchange="updateURL(event)">
    <option value="" disabled selected>Select a product</option> <!-- Disabled and cannot be selected -->
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $selected = ($row['name'] === $selectedProduct) ? 'selected' : '';
            echo "<option value='" . htmlspecialchars($row['name']) . "' $selected>" . htmlspecialchars($row['name']) . "</option>";
        }
    } else {
        echo "<option value=''>No products available</option>";
    }
    ?>
</select>

<script>
    function updateURL(event) {
        event.preventDefault();
        var product = document.getElementById('product_name').value;
        if (product) {
            history.pushState(null, null, "?product=" + encodeURIComponent(product));
            location.reload(); // Reload the page to fetch and display the details
        }
    }
</script>

<!-- Product Details Section -->
<?php if ($productDetails): ?>
    <div class="analysis products-chart">
        <?php include 'fetch_chart_date.php' ?>
    </div>
    <div class="analysis">
        <div class="analysis_card">
            <div class="analysis_image-container">
                <img src="../../assets/products/<?php echo htmlspecialchars($productDetails['img']); ?>" alt="<?php echo htmlspecialchars($productDetails['name']); ?>" class="analysis_image">
            </div>
            <div class="analysis_info">
                <h3 class="analysis_name"><?php echo htmlspecialchars($productDetails['name']); ?></h3>
                <div class="analysis_meta">
                    <p><strong>Size:</strong> <?php echo htmlspecialchars($productDetails['size']); ?></p>
                    <p><strong>Price:</strong> ₱<?php echo number_format($productDetails['price'], 2); ?></p>
                </div>
                <?php
                // Check if the category is not 'beverages' before displaying the ingredients section
                if ($productDetails['category'] !== 'beverages') {
                    echo "<h4 class='analysis_ingredients-title'>Ingredients:</h4>";
                    $ingredients = json_decode($productDetails['ingredients'], true);
                    if ($ingredients) {
                        echo "<ul class='analysis_ingredients-list'>";
                        usort($ingredients, function ($a, $b) {
                            return $a['ingredient_name'] <=> $b['ingredient_name'];
                        });
                        foreach ($ingredients as $ingredient) {
                            $ingredientName = ucfirst(strtolower($ingredient['ingredient_name'])); // Capitalize the ingredient name
                            echo "<li class='analysis_ingredient'><strong>" . htmlspecialchars($ingredientName) . "</strong>: " . htmlspecialchars($ingredient['quantity']) . " " . htmlspecialchars($ingredient['measurement']) . "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>No ingredient data available.</p>";
                    }
                }
                ?>
            </div>
        </div>

        <div class="analysis_details">
            <div class="analysis-container">
                <?php
                // Format the start and end of last week
                $startOfLastWeekFormatted = (new DateTime($startOfLastWeek))->format('F j, Y');
                $endOfLastWeekFormatted = (new DateTime($endOfLastWeek))->format('F j, Y');

                // Output the result
                $orderLabel = $totalOrdersLastWeek == 1 ? 'Order' : 'Orders';
                if ($totalOrdersLastWeek == 0) {
                    $totalOrdersLastWeekText = "0 Order";
                } else {
                    $totalOrdersLastWeekText = number_format($totalOrdersLastWeek) . " $orderLabel";
                }

                // Now echo the output
                echo "<p>Total Orders Last Week: <span style='font-weight: 600;'>$totalOrdersLastWeekText</span></p>";
                ?>
                <?php if ($totalOrdersWeekBeforeLast > 0): ?>
                    <p>Comparison with Two Weeks Ago:
                        <?php
                        // Check if the percentage change is positive or negative
                        if ($percentageChange > 0) {
                            echo '<span style="color: green; font-weight:600;">Went up by ' . number_format($percentageChange, 2) . '%</span>';
                        } elseif ($percentageChange < 0) {
                            echo '<span style="color: red;font-weight:600;">Went down by ' . number_format($percentageChange, 2) . '%</span>';
                        } else {
                            echo '<span style="font-weight:600;"> No change in orders compared to two weeks ago</span>';
                        }
                        ?>
                    </p>
                <?php else: ?>
                    <p><span style="font-weight:600;"> No orders in the previous week to compare</span></p>
                <?php endif; ?>

                <p>Average Orders per Day Last Week: <span style="font-weight: 600;"><?php echo round($averageOrderCountLastWeek); ?> Order<?php echo (round($averageOrderCountLastWeek) > 1) ? 's' : ''; ?></span></p>
                <?php
                $status = $productDetails['status'];
                if ($status == 'available') {
                    echo "<p>Product Status: <span style='color: green; font-weight: 600;'>Available</span></p>";
                } else {
                    echo "<p>Product Status: <span style='color: red; font-weight: 600;'>Not Available</span></p>";
                }
                ?>
                <?php

                // Get product details
                if ($selectedProduct && $productDetails) {
                    $ingredients = json_decode($productDetails['ingredients'], true);
                    $productsPossible = PHP_INT_MAX;

                    if ($ingredients) {
                        usort($ingredients, function ($a, $b) {
                            return $a['ingredient_name'] <=> $b['ingredient_name'];
                        });
                        foreach ($ingredients as $ingredient) {
                            $ingredientName = $ingredient['ingredient_name'];
                            $requiredQuantity = $ingredient['quantity'];
                            $measurement = $ingredient['measurement'];

                            // Map measurements to inventory units
                            $unitMap = [
                                'pcs' => 'pc',
                                'pc' => 'pc',
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

                    // Calculate average daily orders from last week
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

                    $averageOrdersPerDay = ($totalOrdersLastWeek > 0) ? round($totalOrdersLastWeek / 7) : 0;

                    // Calculate days until product runs out
                    $daysUntilOutOfStock = ($averageOrdersPerDay > 0) ? floor($productsPossible / $averageOrdersPerDay) : PHP_INT_MAX;

                    // Display results
                    if ($productsPossible > 0) {
                        echo "<p>Product can be made for <span style='font-weight: 600;'>$productsPossible more orders </span>based on current ingredient stocks.</p>";
                        if ($daysUntilOutOfStock !== PHP_INT_MAX) {
                            echo "<p>Based on the average daily orders last week, <span style='font-weight: 600;'>the product will last for $daysUntilOutOfStock day(s).</span></p>";
                        }
                    } else {
                        echo "<p>Product is unavailable due to<span style='font-weight: 600;'> insufficient stock of ingredients.</span></p>";
                    }
                } else {
                    echo "<p>Unable to calculate availability.</p>";
                }
                ?>
                <?php
                // Query to get the top 5 fast-moving products from last week
                $sqlFastMoving = "
                    SELECT name, SUM(quantity) AS total_quantity
                    FROM usage_reports
                    WHERE day_counted >= ? AND day_counted <= ?
                    GROUP BY name
                    ORDER BY total_quantity DESC
                    LIMIT 5
                ";
                $sqlSlowMoving = "
                    SELECT name, SUM(quantity) AS total_quantity
                    FROM usage_reports
                    WHERE day_counted >= ? AND day_counted <= ?
                    GROUP BY name
                    ORDER BY total_quantity ASC
                    LIMIT 5
                ";
                $stmtFastMoving = $conn->prepare($sqlFastMoving);
                $stmtFastMoving->bind_param("ss", $startOfLastWeek, $endOfLastWeek);
                $stmtFastMoving->execute();
                $resultFastMoving = $stmtFastMoving->get_result();

                $stmtSlowMoving = $conn->prepare($sqlSlowMoving);
                $stmtSlowMoving->bind_param("ss", $startOfLastWeek, $endOfLastWeek);
                $stmtSlowMoving->execute();
                $resultSlowMoving = $stmtSlowMoving->get_result();

                $fastMovingProducts = [];
                while ($row = $resultFastMoving->fetch_assoc()) {
                    $fastMovingProducts[] = $row['name'];
                }

                $slowMovingProducts = [];
                while ($row = $resultSlowMoving->fetch_assoc()) {
                    $slowMovingProducts[] = $row['name'];
                }

                // Check if the selected product is in the fast-moving list
                if (in_array($selectedProduct, $fastMovingProducts)) {
                    echo "<p><strong>Note:</strong> This product is one of the <span style='font-weight: 600; color: green;'>fast-moving</span> products from last week.</p>";
                } elseif (in_array($selectedProduct, $slowMovingProducts)) {
                    echo "<p><strong>Note:</strong> This product is one of the <span style='font-weight: 600; color: orange;'>slow-moving</span> products from last week.</p>";
                } else {
                    echo "<p><strong>Note:</strong> This product is neither fast-moving nor slow-moving.</p>";
                }

                // Display low stock threshold
                echo "<p>Low Stock Threshold: <span style='font-weight: 600;'>$lowStockThreshold orders</span> (to last for at least 3 days)</p>";
                ?>

            </div>
        </div>
    </div>
    <h3 style="text-align: center;">Ingredients Needed for Low Stock Threshold</h3>
    <div class="analysis">

        <table>
            <thead>
                <tr>
                    <th>Ingredient Name</th>
                    <th>Quantity Needed</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($ingredients) {
                    foreach ($ingredients as $ingredient) {
                        $ingredientName = ucfirst(strtolower($ingredient['ingredient_name'])); // Capitalize the ingredient name
                        $requiredQuantity = $ingredient['quantity'];
                        $measurement = $ingredient['measurement'];

                        // Calculate the quantity needed for the low stock threshold
                        $quantityNeeded = $requiredQuantity * $lowStockThreshold;

                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($ingredientName) . "</td>";
                        if ($measurement === 'grams') {
                            $quantityInKg = $quantityNeeded / 1000;
                            echo "<td>" . htmlspecialchars($quantityNeeded) . " grams / " . htmlspecialchars(number_format($quantityInKg, 2)) . " kg</td>";
                        } else {
                            echo "<td>" . htmlspecialchars($quantityNeeded) . " " . htmlspecialchars($measurement) . "</td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No ingredient data available.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>


<?php elseif ($selectedProduct): ?>
    <p>Product not found or details unavailable.</p>
<?php endif; ?>

<?php
$conn->close();
?>


<!-- Styles (could be in a separate CSS file) -->
<style>
    .analysis {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        margin-top: 20px;
        width: 100%;
        padding: 20px 170px;
        margin-bottom: 40px;
    }

    .analysis-container {
        font-size: 1.2rem;
    }

    .analysis_details {
        margin-left: 20px;
        display: flex;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0px 1px 4px 1px rgba(0, 0, 0, 0.25);
        padding: 30px;
        flex-grow: 1;
        max-width: 70%;
        justify-content: center;
        flex-direction: column;
        /* Make sure the image is on top of the name */
        align-items: center;
        height: 45vh;
        overflow-y: auto;

    }

    .analysis_card {
        display: flex;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0px 1px 4px 1px rgba(0, 0, 0, 0.25);
        padding: 30px;
        width: auto;
        max-width: 100%;
        justify-content: space-between;
        flex-direction: column;
        /* Make sure the image is on top of the name */
        align-items: center;
        height: 45vh;
        overflow-y: auto;
    }

    .analysis_image-container {
        flex-shrink: 0;
        max-width: 300px;
        margin-bottom: 5px;
        /* Add some space between image and name */
        text-align: center;
    }

    .analysis_image {
        width: 100%;
        max-width: 250px;
        height: auto;
        border-radius: 8px;
    }

    .analysis_info {
        flex: 1;
        text-align: center;
        /* Center the text in the info section */
    }

    .analysis_name {
        margin-bottom: 15px;
        font-size: 1.5rem;
        font-weight: 600;
        margin-top: 5px;
    }


    .analysis_meta {
        margin-top: 10px;
    }

    .analysis_meta p {
        font-size: 1.1rem;
        margin: 5px 0;
        text-align: left;
    }

    .analysis_ingredients-title {
        font-size: 1.2rem;
        margin-top: 20px;
    }

    .analysis_ingredients-list {
        list-style-type: none;
        padding-left: 0;
        text-align: left;
    }

    .analysis_ingredient {
        font-size: 1rem;
        margin: 8px 0;
    }

    .products-chart {
        margin: 0;
        margin-top: 10px;
        height: 50vh;
    }

    /* Responsive Layout */
    @media (max-width: 768px) {
        .analysis {
            flex-direction: column;
            align-items: center;
            padding: 0;
        }

        .analysis_card {
            flex-direction: column;
            align-items: center;
            text-align: center;
            height: 100%;
        }


        .analysis_image {
            width: 200px;
            margin-bottom: 20px;
        }

        .analysis_info {
            padding-left: 0;
        }
    }

    @media screen and (max-width: 1280px) {
        .analysis {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px;

        }


        .analysis_card {
            width: auto;
            height: auto;
        }

        .analysis_details {
            margin-top: 20px;
            height: auto;
            width: 100%;
        }

        .products-chart {
            height: auto;
            padding-bottom: 0;
        }
    }
</style>