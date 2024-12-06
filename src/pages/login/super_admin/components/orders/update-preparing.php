<?php
session_start();
include '../../connection/database.php';
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_POST['orderID']) && isset($_POST['status'])) {
    $orderID = $_POST['orderID'];
    $status = $_POST['status'];


    // Fetch order details, including order type and orders
    $query = "SELECT order_type, orders FROM float_orders WHERE orderID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $orderID);
    $stmt->execute();
    $stmt->bind_result($orderType, $ordersJson);
    $stmt->fetch();
    $stmt->close();

    //get the uid of the customer 
    $query = "SELECT uid FROM orders WHERE orderID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $orderID);
    $stmt->execute();
    $stmt->bind_result($uid);
    $stmt->fetch();
    $stmt->close();


    // If the order type is 'walk-in', proceed to insert usage data and update inventory
    if ($orderType == 'walk-in') {

        if ($status == 'cancelled') {
            // Handle the cancellation process
            $removeOrder = "DELETE FROM float_orders WHERE orderID = ?";
            $stmtRemoveOrder = $conn->prepare($removeOrder);
            $stmtRemoveOrder->bind_param("i", $orderID);
            $stmtRemoveOrder->execute();


            //delete from invoice 
            $removeOrder = "DELETE FROM invoice_temp WHERE invID = ?";
            $stmtRemoveOrder = $conn->prepare($removeOrder);
            $stmtRemoveOrder->bind_param("s", $orderID);
            $stmtRemoveOrder->execute();
            $stmtRemoveOrder->close();
            header("Location: now-preparing.php?action=success&message=Order has been cancelled.");
            exit();
        } else {
            // Normal "walk-in" order handling
            $orders = json_decode($ordersJson, true);
            // Loop through each order and insert usage report


            // Loop through each item in the order
            foreach ($orders as $order) {
                $itemName = $order['name'];
                $itemSize = $order['size'];
                $itemPrice = $order['price'];
                $orderQuantity = $order['quantity'];
                $insertUsageQuery = "INSERT INTO usage_reports (invID, name, size, price, quantity, day_counted) 
                             VALUES (?, ?, ?, ?, ?, NOW())";
                $stmtUsage = $conn->prepare($insertUsageQuery);
                $invID = $orderID;
                $stmtUsage->bind_param("sssdi", $invID, $itemName, $itemSize, $itemPrice, $orderQuantity);
                $stmtUsage->execute();

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
                                $updateUsage = "UPDATE daily_inventory SET usage_count = usage_count + ? WHERE name = ?";
                                $stmtUsage = $conn->prepare($updateUsage);
                                $stmtUsage->bind_param("ds", $ingredientQuantity, $ingredientName);
                                $stmtUsage->execute();

                                $updateEnding = "UPDATE daily_inventory SET ending = ending - ? WHERE name = ?";
                                $stmtEnding = $conn->prepare($updateEnding);
                                $stmtEnding->bind_param("ds", $ingredientQuantity, $ingredientName);
                                $stmtEnding->execute();



                                //get the data from invoice_temp and insert into invoice
                                $query = "SELECT * FROM invoice_temp WHERE invID = ?";
                                $stmt = $conn->prepare($query);
                                $stmt->bind_param('i', $orderID);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $row = $result->fetch_assoc();
                                $stmt->close();

                                if ($row) {
                                    $invID = $row['invID'];
                                    $orders = $row['orders'];
                                    $total_amount = $row['total_amount'];
                                    $amount_receive = $row['amount_received'];
                                    $amount_change = $row['amount_change'];
                                    $order_type = $row['order_type'];
                                    $mop = $row['mop'];
                                    $cashier = $row['cashier'];

                                    $query = "INSERT INTO invoice (invID, orders, total_amount, amount_received, amount_change, order_type, mop, cashier) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                                    $stmt = $conn->prepare($query);
                                    $stmt->bind_param('isdddsis', $invID, $orders, $total_amount, $amount_receive, $amount_change, $order_type, $mop, $cashier);
                                    if ($stmt->execute()) {
                                        //delete from invoice_temp
                                        $removeOrder = "DELETE FROM invoice_temp WHERE invID = ?";
                                        $stmtRemoveOrder = $conn->prepare($removeOrder);
                                        $stmtRemoveOrder->bind_param("i", $orderID);
                                        if ($stmtRemoveOrder->execute()) {

                                            // Remove the order from float_orders
                                            $removeOrder = "DELETE FROM float_orders WHERE orderID = ?";
                                            $stmtRemoveOrder = $conn->prepare($removeOrder);
                                            $stmtRemoveOrder->bind_param("i", $orderID);
                                            $stmtRemoveOrder->execute();
                                            $stmtRemoveOrder->close();
                                        } else {
                                            die("Error: No data found for invoice_temp with invID $orderID");
                                        }
                                    } else {
                                        die("Error: No data found for invoice_temp with invID $orderID");
                                    }
                                } else {
                                }
                            }
                        }
                    }
                }
            }

            // Close the usage statement
            $stmtUsage->close();
            header("Location: now-preparing.php?action=success&message=Order status updated successfully.");
            exit();
        }
    } elseif ($orderType == 'online') {


        if ($status == 'cancelled') {

            //update status on orders table into cancelled
            $updateOrderStatus = "UPDATE orders SET status = ? WHERE orderID = ?";
            $stmtUpdateOrderStatus = $conn->prepare($updateOrderStatus);
            $stmtUpdateOrderStatus->bind_param("si", $status, $orderID);
            $stmtUpdateOrderStatus->execute();


            $title = "Order ID#$orderID Status Update";
            $category = "Order status";
            $description = "We are sorry, but we had to cancel your order due to unforeseen circumstances. We understand this may be inconvenient, and we sincerely apologize for any trouble this may have caused. If you have any questions, need further clarification, or would like assistance with placing a new order, please do not hesitate to contact us. Thank you for your patience and understanding.";
            $image = "cancelled.png";
            $status = "unread";

            // Insert notification
            $sql3 = "INSERT INTO msg_users (uid, title, category, description, image, status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt3 = $conn->prepare($sql3);
            $stmt3->bind_param("isssss", $uid, $title, $category, $description, $image, $status);
            $stmt3->execute();



            //delete the order from float_orders
            $removeOrder = "DELETE FROM float_orders WHERE orderID = ?";
            $stmtRemoveOrder = $conn->prepare($removeOrder);
            $stmtRemoveOrder->bind_param("i", $orderID);
            $stmtRemoveOrder->execute();
            $stmtRemoveOrder->close();

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
            $query = "INSERT INTO success_orders (orderID, uid, name, address, items, totalPrice, payment, del_instruct, orderPlaced, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param('isssssssss', $orderID, $uid, $name, $address, $items, $totalPrice, $payment, $del_instruct, $orderPlaced, $status);
            if ($stmt->execute()) {
                //delete float 
                $removeOrder = "DELETE FROM orders WHERE orderID = ?";
                $stmtRemoveOrder = $conn->prepare($removeOrder);
                $stmtRemoveOrder->bind_param("i", $orderID);
                $stmtRemoveOrder->execute();
                $stmtRemoveOrder->close();


                header("Location: now-preparing.php?action=success&message=Order status updated to successfully.");
                exit();
            } else {
                die("Error executing stmt3: " . $stmt3->error);  // More detailed error if insert fails
            }
        } else {


            $user_id = $_SESSION['user_id'];


            // Fetch the name of the current user
            $queryUser = "SELECT name FROM accounts WHERE uid = ?";
            $stmtUser = $conn->prepare($queryUser);
            $stmtUser->bind_param('i', $user_id);
            $stmtUser->execute();
            $resultUser = $stmtUser->get_result();
            $userRow = $resultUser->fetch_assoc();
            $stmtUser->close();

            $cashier = $userRow['name'];

            // Fetch order details
            $query = "SELECT * FROM orders WHERE orderID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $orderID);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            $invID = $row['orderID'];
            $orders = $row['items'];
            $total_amount = $row['totalPrice'];
            $amount_receive = $row['totalPrice'];
            $amount_change = 0;
            $order_type = 'delivery';
            $mop = $row['payment'];

            // Insert into invoice_temp
            $query = "INSERT INTO invoice_temp (invID, orders, total_amount, amount_received, amount_change, order_type, mop, cashier) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('isdddsss', $invID, $orders, $total_amount, $amount_receive, $amount_change, $order_type, $mop, $cashier);
            $stmt->execute();
            $stmt->close();

            //update the status of the order
            $updateQuery = "UPDATE float_orders SET status = ? WHERE orderID = ?";
            $stmtUpdate = $conn->prepare($updateQuery);
            $stmtUpdate->bind_param('si', $status, $orderID);

            if ($stmtUpdate->execute()) {


                // Update order status
                $updateOrderStatus = "UPDATE orders SET status = ? WHERE orderID = ?";
                $stmtUpdateOrderStatus = $conn->prepare($updateOrderStatus);
                $stmtUpdateOrderStatus->bind_param("si", $status, $orderID);
                $stmtUpdateOrderStatus->execute();

                //insert message 
                $title = "Order ID#$orderID Status Update";
                $category = "Order status";
                $description = "Your order ID#$orderID is ready and currently waiting for a rider to pick it up for delivery. We will notify you as soon as the rider is on the way to your address. Thank you for your patience, and if you have any questions, please feel free to contact us.";
                $image = "waiting.png";
                $status = "unread";

                // Insert notification
                $sql3 = "INSERT INTO msg_users (uid, title, category, description, image, status) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt3 = $conn->prepare($sql3);
                $stmt3->bind_param("isssss", $uid, $title, $category, $description, $image, $status);
                $stmt3->execute();

                header("Location: now-preparing.php?action=success&message=Order status updated to '$status'.");
                exit();
            } else {
                // Redirect to manage-orders.php with error reason
                header("Location: now-preparing.php?action=error&reason=sql_failure&orderID=$orderID");
                exit();
            }

            // Close statements and database connection
            $stmtUpdate->close();
            mysqli_close($conn);
        }
    }
}
