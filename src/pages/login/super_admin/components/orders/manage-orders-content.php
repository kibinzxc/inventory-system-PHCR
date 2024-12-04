<?php
include '../../connection/database.php';
?>
<link rel="stylesheet" href="archive.css" />
<style>
    .content-wrapper {
        margin-top: 20px;
    }

    .mg-card-container {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .mg-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 16px;
        width: 300px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background-color: #fff;
    }

    .mg-card-title {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .mg-card-content {
        font-size: 1.1rem;
        color: #555;
        margin-bottom: 12px;
        flex: 1;
    }

    .mg-card-total-price {
        font-size: 1.2rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 15px;
        text-align: center;
    }

    .mg-card-actions {
        display: flex;
        justify-content: space-between;
    }

    .mg-button {
        padding: 8px 12px;
        font-size: 1rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .mg-remove-button {
        background-color: #d3d3d3;
        color: #555;
        border: 1px solid #ccc;
    }

    .mg-accept-button {
        background-color: #006D6D;
        color: #fff;
        font-size: 1.2rem;
    }

    .mg-button:hover {
        opacity: 0.9;
    }

    /* Styling for the unordered list */
    .mg-card-content ul {
        padding-left: 20px;
        list-style-type: disc;
    }

    .mg-card-content li {
        margin-bottom: 10px;
    }
</style>

<div id="main-content">
    <div class="container">
        <div class="header">
            <h1>All Orders</h1>
            <div class="btn-wrapper">
                <a href="orders.php" class="btn"><img src="../../assets/external-link.svg" alt=""> Point-of-Sale</a>
                <a href="order-logs.php" class="btn"><img src="../../assets/file-text.svg" alt=""> Logs</a>
            </div>
        </div>
        <br>
        <div class="btncontents">
            <a href="items.php" class="active">New Orders</a>
            <a href="now-preparing.php">Now Preparing</a>
            <a href="items.php">Ready for Pickup</a>
            <a href="items.php">To be Delivered</a>
        </div>

        <div class="content-wrapper">
            <div class="mg-card-container">
                <?php

                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                // Query to fetch data
                $query = "SELECT orderID, orders, total_amount FROM float_orders where status = 'placed' ORDER BY transaction_date ASC";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $orderID = $row['orderID'];
                        $orders = json_decode($row['orders'], true);
                        $totalAmount = number_format((float)$row['total_amount'], 2, '.', '');

                        // Prepare the orders list (quantity, name, size) as an unordered list
                        $orderDetails = "<ul>";
                        foreach ($orders as $order) {
                            $orderDetails .= "<li>{$order['quantity']}x {$order['name']} ({$order['size']})</li>";
                        }
                        $orderDetails .= "</ul>";

                        // Card HTML
                        echo "
                        <div class='mg-card' data-order-id='$orderID'>
                            <div class='mg-card-title'>Order ID: $orderID</div>
                            <div class='mg-card-content'>$orderDetails</div>
                            <div class='mg-card-total-price'>Total w/ Delivery: ₱$totalAmount</div>
                            <div class='mg-card-actions'>
                                <button class='mg-button mg-remove-button' onclick='updateOrderStatus($orderID, \"declined\")'>Decline</button>
                                <button class='mg-button mg-accept-button' onclick='updateOrderStatus($orderID, \"preparing\")'>Accept</button>
                            </div>
                        </div>";
                    }
                } else {
                    echo "No orders found.";
                }

                mysqli_close($conn);
                ?>
            </div>
        </div>
    </div>
</div>
<?php include 'SuccessErrorModal.php'; ?>
<script src="SuccessErrorModal.js"></script>
<script>
    // JavaScript function to handle button clicks
    function updateOrderStatus(orderID, status) {

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_order_status.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status == 200) {
                var response = xhr.responseText;
                if (response.includes("success")) {
                    // Redirect to manage-orders.php on success
                    window.location.href = 'manage-orders.php?action=success&message=Order status updated';
                } else {
                    // Redirect to manage-orders.php on failure
                    window.location.href = 'manage-orders.php?action=error&reason=update_failed';
                }
            } else {
                window.location.href = 'manage-orders.php?action=error&reason=server_error';
            }
        };
        xhr.send('orderID=' + orderID + '&status=' + status);
    }
</script>