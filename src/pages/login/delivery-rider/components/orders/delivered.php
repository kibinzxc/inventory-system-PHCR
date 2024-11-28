<?php
include '../../connection/database.php';
error_reporting(0); // Disable error reporting to avoid displaying any errors

// Assuming the logged-in user ID is stored in the session
session_start();
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}
$userID = $_SESSION['user_id'];

// Fetch the rider's name based on the logged-in user ID
$riderNameSql = "SELECT name FROM accounts WHERE uid = ?";
$riderStmt = $conn->prepare($riderNameSql);
$riderStmt->bind_param("i", $userID);
$riderStmt->execute();
$riderResult = $riderStmt->get_result();
$rider = $riderResult->fetch_assoc();

if (!$rider) {
    die("Rider name not found.");
}
$riderName = $rider['name'];

if (isset($_POST['orderID']) && isset($_FILES['deliveryImage'])) {
    $orderId = $_POST['orderID'];
    $deliveryImage = $_FILES['deliveryImage'];

    // You can remove the move_uploaded_file part to avoid saving the image
    // if you just want to dump it without storing it
    // $targetDir = "../../uploads/";
    // $targetFile = $targetDir . basename($deliveryImage["name"]);
    // move_uploaded_file($deliveryImage["tmp_name"], $targetFile);

    // Continue with order status update and invoice creation
    $updateOrderStatusSql = "UPDATE orders SET status = 'delivered' WHERE orderID = ? AND status = 'delivery'";
    $stmt = $conn->prepare($updateOrderStatusSql);
    $stmt->bind_param("i", $orderId);

    if ($stmt->execute()) {
        // Fetch the order details
        $fetchOrderSql = "SELECT * FROM orders WHERE orderID = ?";
        $fetchOrderStmt = $conn->prepare($fetchOrderSql);
        $fetchOrderStmt->bind_param("i", $orderId);
        $fetchOrderStmt->execute();
        $orderResult = $fetchOrderStmt->get_result();
        $order = $orderResult->fetch_assoc();

        // Parse and format the items to include only qty, price, name, and size in the required format
        $orderItems = json_decode($order['items'], true);
        $formattedItems = array_map(function ($item) {
            return [
                'name' => $item['name'],
                'size' => $item['size'] ?? '',  // Use empty string if size is missing
                'price' => $item['price'],
                'quantity' => $item['qty']
            ];
        }, $orderItems);

        // Generate invID
        $dateToday = date('mdY');
        $fetchRecentInvIDSql = "SELECT invID FROM invoice ORDER BY invID DESC LIMIT 1";
        $fetchRecentInvIDStmt = $conn->prepare($fetchRecentInvIDSql);
        $fetchRecentInvIDStmt->execute();
        $recentInvIDResult = $fetchRecentInvIDStmt->get_result();
        $recentInvID = $recentInvIDResult->fetch_assoc()['invID'] ?? $dateToday . '000';
        $newInvID = $dateToday . str_pad((int)substr($recentInvID, -3) + 1, 3, '0', STR_PAD_LEFT);

        // Prepare invoice data
        $invoiceData = [
            'invID' => $newInvID,
            'orders' => json_encode($formattedItems),
            'total_amount' => $order['totalPrice'],
            'amount_received' => $order['totalPrice'],
            'amount_change' => 0,
            'order_type' => 'delivery',
            'mop' => 'cod',
            'cashier' => $riderName
        ];

        // Insert into invoice table
        $insertInvoiceSql = "INSERT INTO invoice (invID, orders, total_amount, amount_received, amount_change, order_type, mop, cashier) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $insertInvoiceStmt = $conn->prepare($insertInvoiceSql);
        $insertInvoiceStmt->bind_param(
            "ssddssss",
            $invoiceData['invID'],
            $invoiceData['orders'],
            $invoiceData['total_amount'],
            $invoiceData['amount_received'],
            $invoiceData['amount_change'],
            $invoiceData['order_type'],
            $invoiceData['mop'],
            $invoiceData['cashier']
        );

        if ($insertInvoiceStmt->execute()) {
            // No need to output success message
        } else {
            // No need to output error message
        }

        $insertInvoiceStmt->close();
    } else {
        // No need to output error message
    }

    $stmt->close();
} else {
    // No need to output error message
}

$conn->close();
