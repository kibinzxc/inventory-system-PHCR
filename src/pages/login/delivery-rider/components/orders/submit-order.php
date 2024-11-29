<?php
include '../../connection/database.php';
error_reporting(1);

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Fetch orders with 'delivery' status
$query = "SELECT orderID, name, address, items, totalPrice, payment, del_instruct, orderPlaced, status FROM orders WHERE status = 'delivery' ORDER BY orderPlaced DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_delivered'])) {
    $orderID = $_POST['orderID'];

    $conn->begin_transaction();
    try {
        // Update order status to "delivered"
        $updateOrderSql = "UPDATE orders SET status = 'delivered' WHERE orderID = ? AND status = 'delivery'";
        $stmtUpdateOrder = $conn->prepare($updateOrderSql);
        $stmtUpdateOrder->bind_param("i", $orderID);
        $stmtUpdateOrder->execute();

        if ($stmtUpdateOrder->affected_rows === 0) {
            throw new Exception('Order not found or already delivered.');
        }

        // Fetch order details
        $fetchOrderSql = "SELECT items, totalPrice FROM orders WHERE orderID = ?";
        $stmtFetchOrder = $conn->prepare($fetchOrderSql);
        $stmtFetchOrder->bind_param("i", $orderID);
        $stmtFetchOrder->execute();
        $resultOrder = $stmtFetchOrder->get_result();

        if ($resultOrder->num_rows === 0) {
            throw new Exception('Order details not found.');
        }

        $order = $resultOrder->fetch_assoc();
        $items = json_decode($order['items'], true);

        // Process ingredients and check stock
        $insufficientStocks = [];
        foreach ($items as $item) {
            $productName = $item['name'];
            $orderQty = $item['qty'];

            $ingredientsSql = "SELECT ingredients FROM products WHERE name = ?";
            $stmtIngredients = $conn->prepare($ingredientsSql);
            $stmtIngredients->bind_param("s", $productName);
            $stmtIngredients->execute();
            $resultIngredients = $stmtIngredients->get_result();

            if ($resultIngredients->num_rows > 0) {
                $rowIngredients = $resultIngredients->fetch_assoc();
                $ingredients = json_decode($rowIngredients['ingredients'], true);

                foreach ($ingredients as $ingredient) {
                    $ingredientName = $ingredient['ingredient_name'];
                    $requiredQty = $ingredient['quantity'] * $orderQty;

                    $stockSql = "SELECT ending, uom FROM daily_inventory WHERE name = ?";
                    $stmtStock = $conn->prepare($stockSql);
                    $stmtStock->bind_param("s", $ingredientName);
                    $stmtStock->execute();
                    $resultStock = $stmtStock->get_result();

                    if ($resultStock->num_rows > 0) {
                        $stock = $resultStock->fetch_assoc();
                        $availableQty = $stock['ending'];

                        if ($stock['uom'] === 'kg') {
                            $requiredQty /= 1000; // Convert grams to kilograms
                        }

                        if ($availableQty < $requiredQty) {
                            $insufficientStocks[] = $ingredientName;
                        } else {
                            $updateStockSql = "UPDATE daily_inventory SET usage_count = usage_count + ?, ending = ending - ? WHERE name = ?";
                            $stmtUpdateStock = $conn->prepare($updateStockSql);
                            $stmtUpdateStock->bind_param("dds", $requiredQty, $requiredQty, $ingredientName);
                            $stmtUpdateStock->execute();
                        }
                    } else {
                        $insufficientStocks[] = $ingredientName;
                    }
                }
            }
        }

        if (!empty($insufficientStocks)) {
            $conn->rollback();
            echo '<p>Insufficient stock for: ' . implode(', ', $insufficientStocks) . '</p>';
            exit;
        }

        // Generate invoice
        $invID = date('mdY') . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        $totalPrice = $order['totalPrice'];

        $insertInvoiceSql = "INSERT INTO invoice (invID, orders, total_amount, amount_received, amount_change, order_type, mop, cashier) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInvoice = $conn->prepare($insertInvoiceSql);
        $orderItems = json_encode($items);
        $cashier = 'Delivery Rider';
        $orderType = 'delivery';
        $mop = 'cod';
        $stmtInvoice->bind_param("ssddssss", $invID, $orderItems, $totalPrice, $totalPrice, 0, $orderType, $mop, $cashier);
        $stmtInvoice->execute();

        $conn->commit();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}

while ($row = $result->fetch_assoc()) {
    $orderID = $row['orderID'];
    $name = $row['name'];
    $address = $row['address'];
    $items = json_decode($row['items'], true);
    $totalPrice = $row['totalPrice'];
    $orderPlaced = $row['orderPlaced'];

    echo '<div class="order-card">';
    echo '<div class="order-header">';
    echo '<h2>Order #' . htmlspecialchars($orderID) . '</h2>';
    echo '<p><strong>Placed on:</strong> ' . date('F j, Y g:i A', strtotime($orderPlaced)) . '</p>';
    echo '</div>';
    echo '<div class="order-body">';
    echo '<p><strong>Name:</strong> ' . htmlspecialchars($name) . '</p>';
    echo '<p><strong>Address:</strong> ' . htmlspecialchars($address) . '</p>';
    echo '<p><strong>Total Price:</strong> ₱' . number_format($totalPrice, 2) . '</p>';
    echo '</div>';

    if (!empty($items)) {
        echo '<div class="order-items">';
        echo '<strong>Order Details:</strong>';
        echo '<ul>';
        foreach ($items as $item) {
            echo '<li>' . htmlspecialchars($item['name']) . ' (Size: ' . htmlspecialchars($item['size']) . ') - ₱' . number_format($item['price'], 2) . ' x ' . htmlspecialchars($item['qty']) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }

    echo '<form method="POST" class="order-actions">';
    echo '<input type="hidden" name="orderID" value="' . htmlspecialchars($orderID) . '">';
    echo '<button type="submit" name="mark_delivered" class="btn btn-done">Mark as Delivered</button>';
    echo '</form>';

    echo '</div><style>/* Basic Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body and Background */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    padding: 20px;
}

/* Main Container */
.container {
    max-width: 1200px;
    margin: 0 auto;
}

/* Order Card */
.order-card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.order-card:hover {
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
}

/* Header of Order Card */
.order-header {
    background-color: #007bff;
    color: white;
    padding: 20px;
}

.order-header h2 {
    font-size: 1.5rem;
    margin-bottom: 10px;
}

.order-header p {
    font-size: 1rem;
    font-weight: 300;
}

/* Order Body */
.order-body {
    padding: 20px;
    background-color: #fafafa;
}

.order-body p {
    font-size: 1rem;
    margin-bottom: 10px;
}

/* Order Items */
.order-items {
    margin-top: 20px;
}

.order-items ul {
    list-style: none;
}

.order-items li {
    font-size: 1rem;
    margin-bottom: 10px;
}

/* Buttons */
button {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-bottom:20px;
}

button:hover {
    background-color: #218838;
}

button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

/* Form for Actions */
.order-actions {
    margin-top: 20px;
    text-align: center;
}

/* Insufficient Stock Message */
.insufficient-stock {
    color: #ff0000;
    font-weight: bold;
    margin-top: 20px;
}
</style>';
}

$stmt->close();
$conn->close();
