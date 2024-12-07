<?php
include '../../connection/database.php';

include '../../connection/database.php';

if (isset($_POST['orderID']) && isset($_POST['status'])) {
    $orderID = $_POST['orderID'];
    $status = $_POST['status'];

    // Get the uid using the orderID from the 'orders' table
    $query = "SELECT uid FROM orders WHERE orderID = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('i', $orderID);
    $stmt->execute();
    $stmt->bind_result($uid);
    $stmt->fetch();
    $stmt->close();

    // Check if uid is null or invalid
    if (is_null($uid)) {
        header("Location: manage-orders.php?action=error&reason=uid_not_found&orderID=$orderID");
        exit();
    }

    // Update the status in the database
    $query = "UPDATE float_orders SET status = ? WHERE orderID = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('si', $status, $orderID);

    if ($status === 'declined') {
        if ($stmt->execute()) {
            // Notification for declined status
            $title = "Order ID#$orderID Status Update";
            $category = "Order Status";
            $description = "We regret to inform you that your order with ID#$orderID has been 
            declined. This may be due to unavailability of items, payment issues, or other 
            operational constraints. We sincerely apologize for any inconvenience this may have caused. 
            Please feel free to reach out to us for assistance or further clarification. Thank you for understanding, and we hope to serve you better in the future.";
            $image = "declined.png";
            $notificationStatus = "unread";

            // Insert notification
            $sql3 = "INSERT INTO msg_users (uid, title, category, description, image, status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt3 = $conn->prepare($sql3);
            if (!$stmt3) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt3->bind_param("isssss", $uid, $title, $category, $description, $image, $notificationStatus);

            if ($stmt3->execute()) {
                // Remove the order from float_orders
                $removeOrder = "DELETE FROM float_orders WHERE orderID = ?";
                $stmtRemoveOrder = $conn->prepare($removeOrder);
                if (!$stmtRemoveOrder) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmtRemoveOrder->bind_param("i", $orderID);
                $stmtRemoveOrder->execute();
                $stmtRemoveOrder->close();
                //update status in orders table into declined
                $updateOrderStatus = "UPDATE orders SET status = ? WHERE orderID = ?";
                $stmtUpdateOrderStatus = $conn->prepare($updateOrderStatus);
                if (!$stmtUpdateOrderStatus) {
                    die("Prepare failed: " . $conn->error);
                }
                $declinedStatus = "declined";
                $stmtUpdateOrderStatus->bind_param("si", $declinedStatus, $orderID);
                $stmtUpdateOrderStatus->execute();
                $stmtUpdateOrderStatus->close();

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
                $stmt->execute();
                $stmt->close();




                header("Location: manage-orders.php?action=success&message=Order status updated.");
                exit();
            } else {
                die("Error executing stmt3: " . $stmt3->error);  // More detailed error if insert fails
            }
        } else {
            die("Error executing stmt: " . $stmt->error);  // More detailed error if update fails
        }
    } elseif ($status === 'preparing') {
        if ($stmt->execute()) {
            $title = "Order ID#$orderID Status Update";
            $category = "Order Status";
            $description = "Your order is now being prepared. We will notify you once it is ready. Thank you for your patience and for choosing our service!";
            $image = "preparing.png";
            $notificationStatus = "unread";

            // Insert notification
            $sql3 = "INSERT INTO msg_users (uid, title, category, description, image, status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt3 = $conn->prepare($sql3);
            if (!$stmt3) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt3->bind_param("isssss", $uid, $title, $category, $description, $image, $notificationStatus);

            if ($stmt3->execute()) {

                //update status in orders table into preparing
                $updateOrderStatus = "UPDATE orders SET status = ? WHERE orderID = ?";
                $stmtUpdateOrderStatus = $conn->prepare($updateOrderStatus);
                if (!$stmtUpdateOrderStatus) {
                    die("Prepare failed: " . $conn->error);
                }
                $preparingStatus = "preparing";
                $stmtUpdateOrderStatus->bind_param("si", $preparingStatus, $orderID);
                $stmtUpdateOrderStatus->execute();
                $stmtUpdateOrderStatus->close();

                header("Location: manage-orders.php?action=success&message=Order status updated.");
                exit();
            } else {
                die("Error executing stmt3: " . $stmt3->error);  // More detailed error if insert fails
            }
        } else {
            die("Error executing stmt: " . $stmt->error);  // More detailed error if update fails
        }
    } else {
        header("Location: manage-orders.php?action=error&reason=invalid_status&orderID=$orderID");
        exit();
    }

    $stmt->close();
    if (isset($stmt3)) $stmt3->close();
    mysqli_close($conn);
}
