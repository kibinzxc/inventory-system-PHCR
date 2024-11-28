<?php
include '../../connection/database.php';
error_reporting(1);

if (isset($_POST['orderID'])) {
    $orderId = $_POST['orderID'];

    $updateOrderStatusSql = "UPDATE orders SET status = 'delivery' WHERE orderID = ? AND status = 'preparing'";
    $stmt = $conn->prepare($updateOrderStatusSql);
    $stmt->bind_param("i", $orderId);

    if ($stmt->execute()) {
        $sql = "SELECT * FROM orders WHERE orderID = ?";
        $stmt1 = $conn->prepare($sql);
        $stmt1->bind_param("i", $orderId);
        $stmt1->execute();
        $result = $stmt1->get_result();
        $rowz = $result->fetch_assoc();

        if ($rowz) {
            $uid = $rowz['uid'];

            $sql2 = "SELECT * FROM customerinfo WHERE uid = ?";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("i", $uid);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            $rows2 = $result2->fetch_assoc();

            if ($rows2) {
                $title = "Order ID#$orderId Status Update";
                $category = "Order status";
                $description = "Your order is now out for delivery. Our team is on the way to bring you a tasty meal. We appreciate your patience and hope you enjoy your food. If you have any questions or need assistance, feel free to contact us. Thank you for choosing our delivery service!";
                $image = "delivery.png";
                $status = "unread";

                $sql3 = "INSERT INTO msg_users (uid, title, category, description, image, status) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt3 = $conn->prepare($sql3);
                $stmt3->bind_param("isssss", $uid, $title, $category, $description, $image, $status);
                $stmt3->execute();
            }
        }

        header("Location: {$_SERVER['HTTP_REFERER']}");
    } else {
        echo "Error updating order status: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Order ID not provided.";
}

$conn->close();
