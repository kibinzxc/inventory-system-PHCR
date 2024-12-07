<?php
include '../../connection/database.php';
error_reporting(1);

// Start session to store user-specific data
session_start();

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>Error: User not logged in.</p>";
    exit;
}

$currentUid = $_SESSION['user_id']; // Current logged-in user's UID

// Get the name of the current user based on UID
$userQuery = "SELECT name FROM accounts WHERE uid = ?";
$stmtUser = $conn->prepare($userQuery);
$stmtUser->bind_param("i", $currentUid);
$stmtUser->execute();
$userResult = $stmtUser->get_result();

if ($userResult->num_rows === 0) {
    echo "<p>Error: Current user not found in accounts table.</p>";
    exit;
}

$user = $userResult->fetch_assoc();
$currentUsername = $user['name'];

// Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>Error: User not logged in.</p>";
    exit;
}

// Check if the current user already has a pending order in float_orders
$orderQuery = "SELECT * FROM float_orders WHERE cashier = ?";
$stmtOrder = $conn->prepare($orderQuery);
$stmtOrder->bind_param("s", $currentUsername);
$stmtOrder->execute();
$orderResult = $stmtOrder->get_result();

if ($orderResult->num_rows < 1) {
    // Redirect to accepted-orders.php if an order is found
    header("Location: online-orders.php");
    exit;
}


// Handle the "Not Delivered" button action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['not-yet'])) {
    $orderID = $_POST['orderID'];

    $conn->begin_transaction();

    try {
        // Update the status in `orders` table
        $updateOrdersSql = "UPDATE orders SET status = 'not delivered' WHERE orderID = ?";
        $stmtUpdateOrders = $conn->prepare($updateOrdersSql);
        $stmtUpdateOrders->bind_param("i", $orderID);
        $stmtUpdateOrders->execute();

        if ($stmtUpdateOrders->affected_rows === 0) {
            throw new Exception('Order not found or already updated.');
        }

        $deleteFloatOrdersSql = "DELETE FROM float_orders WHERE orderID = ?";
        $stmtDeleteFloatOrders = $conn->prepare($deleteFloatOrdersSql);
        $stmtDeleteFloatOrders->bind_param("i", $orderID);
        $stmtDeleteFloatOrders->execute();



        // Get the UID of the user who placed the order
        $getUidSql = "SELECT uid FROM orders WHERE orderID = ?";
        $stmtGetUid = $conn->prepare($getUidSql);
        $stmtGetUid->bind_param("i", $orderID);
        $stmtGetUid->execute();
        $uidResult = $stmtGetUid->get_result();
        $uidRow = $uidResult->fetch_assoc();
        $uid = $uidRow['uid'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            // Define the upload directory
            $uploadDir = '../../../super_admin/assets/pod/';

            $imgExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

            $imgName = 'proof' . $orderID . '.' . $imgExt; // 'proofORDERID.extension'

            $imgPath = $uploadDir . $imgName;


            // Move the uploaded image to the server
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $imgPath)) {
                throw new Exception('Error uploading the image.');
            }
        } else {
            // If no image was uploaded, ensure imgPath is set to null
            $imgPath = null;
        }

        $title = "Order ID#$orderID Status Update";
        $category = "Order status";
        $description = "We regret to inform you that the delivery of your order with ID#$orderID could not be completed because our delivery rider was unable to reach you. Please ensure that someone is available to receive the order at the delivery address, or kindly provide an alternate contact number or instructions. We apologize for the inconvenience and appreciate your prompt attention to this matter. If you need any assistance, please do not hesitate to contact us.";
        $image = "not-delivered.png";
        $status = "unread";

        // Insert notification
        $sql3 = "INSERT INTO msg_users (uid, title, category, description, image, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("isssss", $uid, $title, $category, $description, $image, $status);
        $stmt3->execute();
        $conn->commit();

        //insert all the data of orders with same orderID from float_orders to success_orders table, orderID uid name address items(orders) totalPrice payment, del instruct, orderplaced, status, orderdelivered
        $query = "SELECT * FROM orders WHERE orderID = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param('i', $orderID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        $orderID = $row['orderID'];
        $uid = $row['uid'];
        $name = $row['name'];
        $address = $row['address'];
        $items = $row['items'];
        $totalPrice = $row['totalPrice'];
        $payment = $row['payment'];
        $del_instruct = $row['del_instruct'];
        $orderPlaced = $row['orderPlaced'];
        $status = $row['status'];
        $query = "INSERT INTO success_orders (orderID, uid, name, address, items, totalPrice, payment, del_instruct, orderPlaced, status, img) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param('issssssssss', $orderID, $uid, $name, $address, $items, $totalPrice, $payment, $del_instruct, $orderPlaced, $status, $imgName);
        if ($stmt->execute()) {
            //delete float 
            $removeOrder = "DELETE FROM orders WHERE orderID = ?";
            $stmtRemoveOrder = $conn->prepare($removeOrder);
            $stmtRemoveOrder->bind_param("i", $orderID);
            $stmtRemoveOrder->execute();
            $stmtRemoveOrder->close();


            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            die("Error executing stmt3: " . $stmt3->error);  // More detailed error if insert fails
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['done'])) {
    $orderID = $_POST['orderID'];

    $conn->begin_transaction();

    try {
        // Update the status in `orders` table
        $updateOrdersSql = "UPDATE orders SET status = 'delivered' WHERE orderID = ?";
        $stmtUpdateOrders = $conn->prepare($updateOrdersSql);
        $stmtUpdateOrders->bind_param("i", $orderID);
        $stmtUpdateOrders->execute();

        if ($stmtUpdateOrders->affected_rows === 0) {
            throw new Exception('Order not found or already updated.');
        }

        // Update the status in `float_orders` table to 'delivered'
        $updateFloatOrdersSql = "UPDATE float_orders SET status = 'delivered' WHERE orderID = ?";
        $stmtUpdateFloatOrders = $conn->prepare($updateFloatOrdersSql);
        $stmtUpdateFloatOrders->bind_param("i", $orderID);
        $stmtUpdateFloatOrders->execute();

        if ($stmtUpdateFloatOrders->affected_rows === 0) {
            throw new Exception('Float order not found or already updated.');
        }

        // Get the UID of the user who placed the order
        $getUidSql = "SELECT uid FROM orders WHERE orderID = ?";
        $stmtGetUid = $conn->prepare($getUidSql);
        $stmtGetUid->bind_param("i", $orderID);
        $stmtGetUid->execute();
        $uidResult = $stmtGetUid->get_result();
        $uidRow = $uidResult->fetch_assoc();
        $uid = $uidRow['uid'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            // Define the upload directory
            $uploadDir = '../../../super_admin/assets/pod/';

            $imgExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

            $imgName = 'proof' . $orderID . '.' . $imgExt; // 'proofORDERID.extension'

            $imgPath = $uploadDir . $imgName;

            // Move the uploaded image to the server
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $imgPath)) {
                throw new Exception('Error uploading the image.');
            }
        } else {
            // If no image was uploaded, ensure imgPath is set to null
            $imgPath = null;
        }

        $title = "Order ID#$orderID Status Update";
        $category = "Order status";
        $description = "We are pleased to inform you that the delivery of your order with ID#$orderID has been successfully completed. Thank you for your purchase! If you have any questions or concerns, feel free to contact us.";
        $image = "delivered.png";
        $status = "unread";

        // Insert notification
        $sql3 = "INSERT INTO msg_users (uid, title, category, description, image, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("isssss", $uid, $title, $category, $description, $image, $status);
        $stmt3->execute();

        // Commit the transaction
        $conn->commit();

        // Insert order into success_orders table
        $query = "SELECT * FROM orders WHERE orderID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $orderID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        $orderID = $row['orderID'];
        $uid = $row['uid'];
        $name = $row['name'];
        $address = $row['address'];
        $items = $row['items'];
        $totalPrice = $row['totalPrice'];
        $payment = $row['payment'];
        $del_instruct = $row['del_instruct'];
        $orderPlaced = $row['orderPlaced'];
        $status = $row['status'];

        // Insert into success_orders table
        $query = "INSERT INTO success_orders (orderID, uid, name, address, items, totalPrice, payment, del_instruct, orderPlaced, status, img) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('issssssssss', $orderID, $uid, $name, $address, $items, $totalPrice, $payment, $del_instruct, $orderPlaced, $status, $imgName);
        if ($stmt->execute()) {

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
                $orderQty = $item['quantity'];

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

                            error_log("Checking stock for $ingredientName: available $availableQty, required $requiredQty");

                            if ($availableQty < $requiredQty) {
                                $insufficientStocks[] = $ingredientName;
                            } else {
                                $updateStockSql = "UPDATE daily_inventory SET usage_count = usage_count + ?, ending = ending - ? WHERE name = ?";
                                $stmtUpdateStock = $conn->prepare($updateStockSql);
                                $stmtUpdateStock->bind_param("dds", $requiredQty, $requiredQty, $ingredientName);
                                if ($stmtUpdateStock->execute()) {
                                    error_log("Stock updated for $ingredientName: -$requiredQty from ending, +$requiredQty to usage.");
                                } else {
                                    error_log("Failed to update stock for $ingredientName.");
                                }
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

            $insertInvoiceSql = "INSERT INTO invoice (invID, orders, total_amount, amount_received, amount_change, order_type, mop, cashier) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtInvoice = $conn->prepare($insertInvoiceSql);
            $orderItems = json_encode($items);
            $cashier = $currentUsername;
            $orderType = 'delivery';
            $mop = 'cod';
            $amountChange = 0; // Use a variable for the constant value
            $stmtInvoice->bind_param("ssddssss", $orderID, $orderItems, $totalPrice, $totalPrice, $amountChange, $orderType, $mop, $cashier);
            if ($stmtInvoice->execute()) {
                //delete float_orders and orders 
                $removeOrder = "DELETE FROM orders WHERE orderID = ?";
                $stmtRemoveOrder = $conn->prepare($removeOrder);
                $stmtRemoveOrder->bind_param("i", $orderID);
                $stmtRemoveOrder->execute();
                $stmtRemoveOrder->close();

                $removeFloatOrder = "DELETE FROM float_orders WHERE orderID = ?";
                $stmtRemoveFloatOrder = $conn->prepare($removeFloatOrder);
                $stmtRemoveFloatOrder->bind_param("i", $orderID);
                $stmtRemoveFloatOrder->execute();
                $stmtRemoveFloatOrder->close();

                //delete invoice_temp
                $removeInvoiceTemp = "DELETE FROM invoice_temp WHERE invID = ?";
                $stmtRemoveInvoiceTemp = $conn->prepare($removeInvoiceTemp);
                $stmtRemoveInvoiceTemp->bind_param("i", $orderID);
                $stmtRemoveInvoiceTemp->execute();
                $stmtRemoveInvoiceTemp->close();

                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            die("Error executing stmt3: " . $stmt3->error);  // More detailed error if insert fails
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}




// Fetch and display orders with 'ready for pickup' status
$query = "SELECT orderID, name, address, items, totalPrice, payment, del_instruct, orderPlaced, status FROM orders WHERE status = 'delivery' ORDER BY orderPlaced DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

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

    if (!empty($items)) {
        echo '<div class="order-items">';
        echo '<strong>Order Details:</strong>';
        echo '<ul>';
        foreach ($items as $item) {
            echo '<li>' . htmlspecialchars($item['quantity']) . 'x - ' . htmlspecialchars($item['name']) . ' (Size: ' . htmlspecialchars($item['size']) . ') - ₱' . number_format($item['price'], 2) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }

    echo '<div class="order-body">';
    echo '<p><strong>Name:</strong> ' . htmlspecialchars($name) . '</p>';
    echo '<p><strong>Address:</strong> ' . htmlspecialchars($address) . '</p>';
    echo '<p><strong>Total Price:</strong> ₱' . number_format($totalPrice, 2) . '</p>';
    echo '</div>';

    echo '<form method="POST" enctype="multipart/form-data" class="order-actions">';
    echo '<label for="image-' . $orderID . '" style="font-size: 1.2rem; color: #343434; display: block; margin-bottom: 10px;">Proof of Delivery</label>';
    echo '<input type="file" name="image" id="image-' . $orderID . '" accept="image/*" capture required onchange="handleFileChange(event, \'' . $orderID . '\')">';
    echo '<br>';
    echo '<img id="image-preview-' . $orderID . '" src="#" alt="Image Preview" style="display:none; margin-top: 15px; max-width: 100%; max-height: 200px; border: 1px solid #ccc; border-radius: 5px;">';
    echo '<input type="hidden" name="orderID" value="' . htmlspecialchars($orderID) . '">';
    echo '<div class="button-group">';
    echo '<button type="button" class="btn btn-secondary" id="not-delivered-btn-' . $orderID . '" style="display: none;" onclick="openModal(\'not-delivered-modal-' . $orderID . '\')">Not Delivered</button>';
    echo '<button type="button" class="btn btn-done" id="delivered-btn-' . $orderID . '" style="display: none;" onclick="openModal(\'delivered-modal-' . $orderID . '\')">Mark as Delivered</button>';
    echo '</div>';

    // Not Delivered Modal
    echo '<div id="not-delivered-modal-' . $orderID . '" class="modal">';
    echo '<div class="modal-content">';
    echo '<p>Are you sure you want to mark this order as "Not Delivered"?</p>';
    echo '<div class="modal-buttons">';
    echo '<button onclick="closeModal(\'not-delivered-modal-' . $orderID . '\')" class="btn btn-cancel2">Cancel</button>';
    echo '<input type="hidden" name="orderID" value="' . htmlspecialchars($orderID) . '">';
    echo '<button type="submit" name="not-yet" class="btn btn-secondary">Confirm</button>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Delivered Modal
    echo '<div id="delivered-modal-' . $orderID . '" class="modal">';
    echo '<div class="modal-content">';
    echo '<p>Are you sure you want to mark this order as "Delivered"?</p>';
    echo '<div class="modal-buttons">';
    echo '<button onclick="closeModal(\'delivered-modal-' . $orderID . '\')" class="btn btn-cancel2">Cancel</button>';
    echo '<input type="hidden" name="orderID" value="' . htmlspecialchars($orderID) . '">';
    echo '<button type="submit" name="done" class="btn btn-done">Confirm</button>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}



$stmt->close();
$conn->close();
