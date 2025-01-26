<?php
include '../../connection/database.php';
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);  // Enable MySQLi error reporting
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
        color: #343434;
    }

    .mg-card-content {
        font-size: 1.1rem;
        color: #343434;
        margin-bottom: 12px;
        flex: 1;

    }

    .mg-card-total-price {
        font-size: 1.2rem;
        font-weight: bold;
        color: #343434;
        margin-bottom: 15px;
        text-align: left;
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
            <h2 style="color:#343434">All Orders | Now Preparing</h2>
            <div class="btn-wrapper">
                <a href="orders.php" class="btn"><img src="../../assets/external-link.svg" alt=""> Point-of-Sale</a>
                <a href="order-logs.php" class="btn"><img src="../../assets/file-text.svg" alt=""> Logs</a>
            </div>
        </div>
        <br>
        <div class="btncontents">
            <!-- <a href="https://www.flaticon.com/free-icons/food-delivery" title="food delivery icons">Food delivery icons created by HAJICON - Flaticon</a>
            <a href="https://www.flaticon.com/free-icons/submit" title="submit icons">Submit icons created by Vectors Tank - Flaticon</a>
            <a href="https://www.flaticon.com/free-icons/cooking-time" title="cooking time icons">Cooking time icons created by Freepik - Flaticon</a>
            <a href="https://www.flaticon.com/free-icons/delivery" title="delivery icons">Delivery icons created by monkik - Flaticon</a>
            <a href="https://www.flaticon.com/free-icons/email" title="email icons">Email icons created by Dewi Sari - Flaticon</a> -->
            <a href="manage-orders.php"><img src="../../assets/order.png" class="img-btn-link"> New Orders</a>
            <a href="now-preparing.php" class="active"><img src="../../assets/cooking-time.png" class="img-btn-link">
                Now Preparing</a>
            <a href="pickup.php"><img src="../../assets/delivery-man.png" class="img-btn-link"> Ready for Pickup</a>
            <a href="tbd.php"><img src="../../assets/delivery.png" class="img-btn-link"> To be Delivered</a>
        </div>

        <div class="content-wrapper">
            <div class="mg-card-container">
                <?php

                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                // Query to fetch data
                $query = "SELECT orderID, orders, order_type FROM float_orders where status = 'preparing' ORDER BY transaction_date ASC";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $orderID = $row['orderID'];
                        $orders = json_decode($row['orders'], true);
                        $orderType = ucfirst($row['order_type']);

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
                            <div class='mg-card-total-price'>Transaction: $orderType</div>
                            <div class='mg-card-actions'>
                                <button class='mg-button mg-remove-button' onclick='updateOrderStatus(\"$orderID\", \"cancelled\")'>Cancel</button>
                                <button class='mg-button mg-accept-button' onclick='updateOrderStatus(\"$orderID\", \"ready for pickup\")'>Done</button>

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
        console.log("orderID:", orderID, "status:", status); // Check if orderID is correct
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update-preparing.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status == 200) {
                var response = xhr.responseText.trim();
                if (!response) {
                    alert("Empty response from server.");
                    window.location.href = 'now-preparing.php?action=error&reason=empty_response';
                    return;
                }
                if (response.includes("success")) {
                    window.location.href = 'now-preparing.php?action=success&message=Order status updated';
                } else {
                    alert("Error: " + response);
                    window.location.href = 'now-preparing.php?action=error&reason=' + encodeURIComponent(response);
                }
            } else {
                window.location.href = 'now-preparing.php?action=error&reason=server_error';
            }

        };
        xhr.send('orderID=' + orderID + '&status=' + status);
    }
</script>