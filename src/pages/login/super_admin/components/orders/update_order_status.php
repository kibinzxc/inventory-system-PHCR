<?php
include '../../connection/database.php';

if (isset($_POST['orderID']) && isset($_POST['status'])) {
    $orderID = $_POST['orderID'];
    $status = $_POST['status'];

    // Update the status in the database
    $query = "UPDATE float_orders SET status = ? WHERE orderID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $status, $orderID);

    if ($stmt->execute()) {
        // Redirect to manage-orders.php with success message
        header("Location: manage-orders.php?action=success&message=Order status updated to '$status'.");
        exit();
    } else {
        // Redirect to manage-orders.php with error reason
        header("Location: manage-orders.php?action=error&reason=sql_failure&orderID=$orderID");
        exit();
    }

    $stmt->close();
    mysqli_close($conn);
}
