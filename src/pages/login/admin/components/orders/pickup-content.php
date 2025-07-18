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
        font-size: 1rem;
        font-weight: bold;
        color: #343434;
        margin-bottom: 25px;
        text-align: center;
    }

    .mg-card-actions {
        display: flex;
        justify-content: center;

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
        text-align: center;
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
            <h2 style="color:#343434">All Orders | Ready for Pickup</h2>
            <div class="btn-wrapper">
                <!-- <a href="orders.php" class="btn"><img src="../../assets/external-link.svg" alt=""> Point-of-Sale</a> -->
                <a href="order-logs.php" class="btn"><img src="../../assets/file-text.svg" alt=""> Logs</a>
            </div>
        </div>
        <br>
        <div class="btncontents">
            <a href="manage-orders.php"><img src="../../assets/order.png" class="img-btn-link"> New Orders</a>
            <a href="now-preparing.php"><img src="../../assets/cooking-time.png" class="img-btn-link"> Now Preparing</a>
            <a href="pickup.php" class="active"><img src="../../assets/delivery-man.png" class="img-btn-link"> Ready for Pickup</a>
            <a href="tbd.php"><img src="../../assets/delivery.png" class="img-btn-link"> To be Delivered</a>
        </div>

        <div class="content-wrapper">
            <div class="mg-card-container">
                <?php

                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                // Query to fetch data
                $query = "SELECT orderID, orders, order_type FROM float_orders where status = 'ready for pickup' ORDER BY transaction_date ASC";
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
                            <div class='mg-card-total-price'>Waiting for Pickup</div>
                            <div class='mg-card-actions'>
                                <button class='mg-button mg-accept-button' onclick='downloadReceipt($orderID)'>Generate Receipt</button>
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
    function downloadReceipt(orderID) {
        const url = `generate_inv.php?invID=${orderID}`;
        const windowFeatures = "width=400,height=600,scrollbars=no,toolbar=no,location=no,status=no,menubar=no,resizable=no";
        window.open(url, "_blank", windowFeatures);
    }
</script>