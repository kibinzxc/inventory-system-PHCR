<?php

date_default_timezone_set('Asia/Manila');

Error_reporting(1);
// Get the raw POST data (JSON format)
$data = json_decode(file_get_contents("php://input"), true);

// Extract the orders and other data
$orders = $data['orders'];
$cash = $data['cash'];
$change = $data['change'];

session_start();
$user_id = $_SESSION['user_id'];

include '../../connection/database.php';

// Get the user name
$query_user = "SELECT name FROM accounts WHERE uid = ?";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $row_user = $result_user->fetch_assoc();
    $user_name = $row_user['name'];
} else {
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit;
}

// Calculate the total amount
$total_amount = 0;
foreach ($orders as $order) {
    $total_amount += $order['price'] * $order['quantity'];
}

// Generate invID
$today = date('mdY');
$query = "SELECT invID FROM invoice WHERE invID LIKE '$today%' ORDER BY invID DESC LIMIT 1";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $last_invID = $row['invID'];
    $last_number = (int)substr($last_invID, -3);
    $next_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
} else {
    $next_number = '001';
}
$invID = $today . $next_number;

// Start a transaction
$conn->begin_transaction();

try {
    // Insert invoice data
    $stmt = $conn->prepare("INSERT INTO float_orders (orderID, orders, total_amount, amount_received, amount_change, order_type, mop, status, cashier) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $orderJson = json_encode($orders);
    $order_type = 'walk-in';
    $mop = 'cash';
    $cashier = $user_name;
    $status = 'preparing';
    $stmt->bind_param("ssddsssss", $invID, $orderJson, $total_amount, $cash, $change, $order_type, $mop, $status, $cashier);
    $stmt->execute();

    // Insert invoice data
    $stmtInvoice = $conn->prepare("INSERT INTO invoice (invID, orders, total_amount, amount_received, amount_change, order_type, mop, cashier) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $orderJson = json_encode($orders);
    $order_type = 'walk-in';
    $mop = 'cash';
    $cashier = $user_name;
    $stmtInvoice->bind_param("ssddssss", $invID, $orderJson, $total_amount, $cash, $change, $order_type, $mop, $cashier);
    $stmtInvoice->execute();


    // Initialize an array to track insufficient stocks
    $insufficientStocks = [];

    foreach ($orders as $order) {
        $itemName = $order['name'];
        $orderQuantity = $order['quantity'];
        $itemPrice = $order['price'];
        $itemSize = $order['size'];

        // Insert product usage data into the usage_reports table
        // $insertUsageQuery = "INSERT INTO usage_reports (invID, name, size, price, quantity, day_counted) 
        //                  VALUES (?, ?, ?, ?, ?, NOW())";
        // $stmtUsage = $conn->prepare($insertUsageQuery);
        // $stmtUsage->bind_param("sssdi", $invID, $itemName, $itemSize, $itemPrice, $orderQuantity);
        // $stmtUsage->execute();

        // Get ingredients JSON for the item
        $queryIngredients = "SELECT ingredients FROM products WHERE name = ?";
        $stmtIngredients = $conn->prepare($queryIngredients);
        $stmtIngredients->bind_param("s", $itemName);
        $stmtIngredients->execute();
        $resultIngredients = $stmtIngredients->get_result();

        if ($resultIngredients->num_rows > 0) {
            $row = $resultIngredients->fetch_assoc();
            $ingredients = json_decode($row['ingredients'], true);

            foreach ($ingredients as $ingredient) {
                $ingredientName = $ingredient['ingredient_name'];
                $ingredientQuantity = $ingredient['quantity'] * $orderQuantity; // Scale quantity
                $ingredientMeasurement = $ingredient['measurement'];

                // Get inventory measurement for this ingredient
                $queryInventory = "SELECT uom, usage_count, ending FROM daily_inventory WHERE name = ?";
                $stmtInventory = $conn->prepare($queryInventory);
                $stmtInventory->bind_param("s", $ingredientName);
                $stmtInventory->execute();
                $resultInventory = $stmtInventory->get_result();

                if ($resultInventory->num_rows > 0) {
                    $rowInventory = $resultInventory->fetch_assoc();
                    $inventoryMeasurement = $rowInventory['uom'];
                    $currentEnding = $rowInventory['ending'];

                    // Convert ingredient quantity to match inventory measurement
                    if ($ingredientMeasurement === 'grams' && $inventoryMeasurement === 'kg') {
                        $ingredientQuantity /= 1000; // Convert grams to kg
                    } elseif ($ingredientMeasurement === 'kg' && $inventoryMeasurement === 'grams') {
                        $ingredientQuantity *= 1000; // Convert kg to grams
                    }

                    // Check if the ending will become negative
                    if ($currentEnding - $ingredientQuantity < 0) {
                        // Add to the insufficient stocks array
                        $insufficientStocks[] = [
                            'product' => $itemName,
                            'ingredient' => $ingredientName
                        ];
                    } else {
                        // Update usage and ending inventory if stock is sufficient
                        // $updateUsage = "UPDATE daily_inventory SET usage_count = usage_count + ? WHERE name = ?";
                        // $stmtUsage = $conn->prepare($updateUsage);
                        // $stmtUsage->bind_param("ds", $ingredientQuantity, $ingredientName);
                        // $stmtUsage->execute();

                        // $updateEnding = "UPDATE daily_inventory SET ending = ending - ? WHERE name = ?";
                        // $stmtEnding = $conn->prepare($updateEnding);
                        // $stmtEnding->bind_param("ds", $ingredientQuantity, $ingredientName);
                        // $stmtEnding->execute();
                    }
                }
            }
        }
    }


    if (!empty($insufficientStocks)) {
        $insufficientGrouped = [];

        foreach ($insufficientStocks as $stock) {
            if (isset($insufficientGrouped[$stock['product']])) {
                $insufficientGrouped[$stock['product']][] = $stock['ingredient'];
            } else {
                $insufficientGrouped[$stock['product']] = [$stock['ingredient']];
            }
        }

        $insufficientList = [];
        foreach ($insufficientGrouped as $product => $ingredients) {
            $ingredientsList = implode(", ", $ingredients);
            $insufficientList[] = "Product: {$product}, Ingredients: {$ingredientsList}";
        }

        echo json_encode([
            'success' => false,
            'error' => 'Insufficient ingredient stock for these products: <br>',
            'ingredients' => $insufficientList,

        ]);
        $conn->rollback();
        exit;
    }

    $conn->commit();

    echo json_encode(['success' => true, 'invID' => $invID]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
