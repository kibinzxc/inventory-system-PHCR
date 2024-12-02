<?php
include 'warning-modal.php';
error_reporting(0);

// Set the timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Get the current time
    $currentDateTime = new DateTime();
    $currentHour = $currentDateTime->format('H'); // Get current hour (24-hour format)

    // Set the greeting based on the current time
    if ($currentHour >= 5 && $currentHour < 12) {
        $greeting = 'Good Morning';
    } elseif ($currentHour >= 12 && $currentHour < 18) {
        $greeting = 'Good Afternoon';
    } else {
        $greeting = 'Good Evening';
    }

    // Set the inventory date (previous day or current day based on time)
    if ($currentHour < 6) {
        $inventoryDate = $currentDateTime->modify('-1 day')->format('Y-m-d');
    } else {
        $inventoryDate = $currentDateTime->format('Y-m-d');
    }

    // Get the user ID from the session
    $userId = $_SESSION['user_id'];

    // Connect to the database
    include '../../connection/database.php'; // Make sure to include your database connection file

    // Fetch the user name from the database
    $query = "SELECT name FROM accounts WHERE uid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the user exists, fetch their name
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $userName = $user['name'];
    } else {
        // If no user found, the script will stop here, and you can handle it accordingly (e.g., redirecting the user).
        exit('User not found.');
    }

    // Fetch total sales for today
    $salesQuery = "SELECT SUM(total_amount) AS total_sales FROM invoice WHERE DATE(transaction_date) = ?";
    $salesStmt = $conn->prepare($salesQuery);
    $salesStmt->bind_param('s', $inventoryDate);
    $salesStmt->execute();
    $salesResult = $salesStmt->get_result();

    $totalSales = 0;
    if ($salesResult->num_rows > 0) {
        $salesData = $salesResult->fetch_assoc();
        $totalSales = $salesData['total_sales'];
    }

    // Fetch total sales for the previous day
    $previousDate = $currentDateTime->modify('-1 day')->format('Y-m-d');
    $previousSalesQuery = "SELECT SUM(total_amount) AS total_sales FROM invoice WHERE DATE(transaction_date) = ?";
    $previousSalesStmt = $conn->prepare($previousSalesQuery);
    $previousSalesStmt->bind_param('s', $previousDate);
    $previousSalesStmt->execute();
    $previousSalesResult = $previousSalesStmt->get_result();

    $previousTotalSales = 0;
    if ($previousSalesResult->num_rows > 0) {
        $previousSalesData = $previousSalesResult->fetch_assoc();
        $previousTotalSales = $previousSalesData['total_sales'];
    }

    // Calculate percentage change
    $percentageChange = 0;
    if ($previousTotalSales > 0) {
        $percentageChange = (($totalSales - $previousTotalSales) / $previousTotalSales) * 100;
    }
} else {
    // If the user is not logged in, stop the script or redirect
    exit('You must be logged in to view this page.');
}
?>

<link rel="stylesheet" href="MainContent.css">

<div id="main-content">
    <div class="container">
        <div class="header">
            <div class="intro-header">
                <p class="headings">
                    <?php echo $greeting . '<span style="font-weight:bold;"> ' . $userName . '</span>'; ?>
                    <img src="../../assets/wave.png" alt="" style="width: 30px;">
                </p>
            </div>
            <!-- <div class="btn-wrapper">
                <a href="product-preview.php" class="btn"><img src="../../assets/instagram.svg" alt=""> Product Details</a>
                <a href="archive.php" class="btn" onclick="openArchiveModal()"><img src="../../assets/file-text.svg" alt=""> Archive</a>
            </div> -->
        </div>

        <div class="inventory-cards">
            <!-- Total Sales Card -->
            <div class="card">
                <div class="card-body">
                    <div>
                        <h5>Total Sales Today</h5>
                        <p class="sales-amount"><?php echo '₱' . number_format($totalSales, 2); ?></p>
                    </div>
                </div>
                <div class="card-footer">
                    <p><?php echo 'As of ' . date('F d, Y g:i A'); ?></p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div>
                        <h5>Average Order Value</h5>

                        <?php
                        // Fetch total sales and orders for last week
                        $lastWeekStart = date('Y-m-d', strtotime('monday last week'));
                        $lastWeekEnd = date('Y-m-d', strtotime('sunday last week'));

                        // Query for invoices for last week
                        $query = "SELECT total_amount FROM invoice WHERE DATE(transaction_date) BETWEEN ? AND ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('ss', $lastWeekStart, $lastWeekEnd);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        $totalSales = 0;
                        $totalOrders = 0;

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Sum up total sales
                                $totalSales += $row['total_amount'];
                                // Count total invoices (orders)
                                $totalOrders++;
                            }
                        }

                        // Calculate Average Order Value for last week
                        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

                        // echo "Total Sales: $totalSales<br>";
                        // echo "Total Orders: $totalOrders<br>";
                        // echo "Average Order Value: $averageOrderValue<br>";
                        ?>

                        <p class="sales-amount"><?php echo '₱' . number_format($averageOrderValue, 2); ?></p>
                    </div>


                    <?php
                    // Define the date range for last week (e.g., Nov 18, 2024 - Nov 24, 2024)
                    $lastWeekStart = date('Y-m-d', strtotime('monday last week'));
                    $lastWeekEnd = date('Y-m-d', strtotime('sunday last week'));

                    // Correct the date for the week before last week
                    $weekBeforeLastStart = date('Y-m-d', strtotime($lastWeekStart . ' -1 week'));
                    $weekBeforeLastEnd = date('Y-m-d', strtotime($lastWeekEnd . ' -1 week'));

                    // Query for total sales and order count for the week before last week
                    $prevQuery = "SELECT total_amount FROM invoice WHERE DATE(transaction_date) BETWEEN ? AND ?";
                    $prevStmt = $conn->prepare($prevQuery);
                    $prevStmt->bind_param('ss', $weekBeforeLastStart, $weekBeforeLastEnd);
                    $prevStmt->execute();
                    $prevResult = $prevStmt->get_result();

                    $prevTotalSales = 0;
                    $prevTotalOrders = 0;

                    if ($prevResult->num_rows > 0) {
                        while ($row = $prevResult->fetch_assoc()) {
                            // Sum up the total_amount and count the invoices as orders
                            $prevTotalSales += $row['total_amount'];
                            $prevTotalOrders++;
                        }
                    }

                    // Calculate Average Order Value (AOV) for the week before last week
                    $prevAOV = $prevTotalOrders > 0 ? $prevTotalSales / $prevTotalOrders : 0;

                    // Query for total sales and order count for last week
                    $lastWeekQuery = "SELECT total_amount FROM invoice WHERE DATE(transaction_date) BETWEEN ? AND ?";
                    $lastWeekStmt = $conn->prepare($lastWeekQuery);
                    $lastWeekStmt->bind_param('ss', $lastWeekStart, $lastWeekEnd);
                    $lastWeekStmt->execute();
                    $lastWeekResult = $lastWeekStmt->get_result();

                    $lastWeekTotalSales = 0;
                    $lastWeekTotalOrders = 0;

                    if ($lastWeekResult->num_rows > 0) {
                        while ($row = $lastWeekResult->fetch_assoc()) {
                            // Sum up the total_amount and count the invoices as orders
                            $lastWeekTotalSales += $row['total_amount'];
                            $lastWeekTotalOrders++;
                        }
                    }

                    // Calculate Average Order Value (AOV) for last week
                    $lastWeekAOV = $lastWeekTotalOrders > 0 ? $lastWeekTotalSales / $lastWeekTotalOrders : 0;

                    // Calculate percentage change in AOV between last week and the week before last week
                    $aovPercentageChange = ($prevAOV > 0) ? (($lastWeekAOV - $prevAOV) / $prevAOV) * 100 : 0;

                    // Output the results
                    // echo "Total Sales Week Before Last: $prevTotalSales<br>";
                    // echo "Total Orders Week Before Last: $prevTotalOrders<br>";
                    // echo "AOV Week Before Last: $prevAOV<br>";
                    // echo "Total Sales Last Week: $lastWeekTotalSales<br>";
                    // echo "Total Orders Last Week: $lastWeekTotalOrders<br>";
                    // echo "AOV Last Week: $lastWeekAOV<br>";
                    // echo "Percentage Change in AOV: $aovPercentageChange%<br>";
                    ?>


                    <div class="percentage-box">
                        <div class="percentage-body">
                            <?php if ($aovPercentageChange > 0) { ?>
                                <div class="up">
                                    <img src="../../assets/up.png" style="width:25px;margin-right:5px;">
                                    <p class="percentage-up">+<?php echo number_format($aovPercentageChange, 2); ?>%</p>
                                </div>
                            <?php } elseif ($aovPercentageChange < 0) { ?>
                                <div class="down">
                                    <img src="../../assets/down.png" style="width:25px;margin-right:5px;">
                                    <p class="percentage-down"><?php echo number_format($aovPercentageChange, 2); ?>%</p>
                                </div>
                            <?php } else { ?>
                                <div class="neutral">
                                    <img src="../../assets/neutral.png" style="width:25px;margin-right:5px;">
                                    <p class="percentage-neutral">0.00%</p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <?php
                    // Get the start and end date for last week
                    $this_week_sd = date("F d, Y", strtotime($lastWeekStart));  // Example: "November 26, 2024"
                    $this_week_ed = date("F d, Y", strtotime($lastWeekEnd));    // Example: "December 02, 2024"
                    ?>
                    <p><?php echo 'From ' . $this_week_sd . ' - ' . $this_week_ed; ?></p>
                </div>
            </div>

            <?php

            // Define the date range for last week (e.g., Nov 18, 2024 - Nov 24, 2024)
            $lastWeekStart = date('Y-m-d', strtotime('monday last week'));
            $lastWeekEnd = date('Y-m-d', strtotime('sunday last week'));

            // Correct the date for the week before last week
            // Subtract 2 weeks from the start of last week
            $weekBeforeLastStart = date('Y-m-d', strtotime($lastWeekStart . ' -1 week'));
            $weekBeforeLastEnd = date('Y-m-d', strtotime($lastWeekEnd . ' -1 week'));

            // Query to get total sales (total_amount) for last week
            $prevQuery = "SELECT total_amount FROM invoice WHERE DATE(transaction_date) BETWEEN ? AND ?";
            $prevStmt = $conn->prepare($prevQuery);
            $prevStmt->bind_param('ss', $lastWeekStart, $lastWeekEnd);
            $prevStmt->execute();
            $prevResult = $prevStmt->get_result();

            $prevTotalSales = 0;

            if ($prevResult->num_rows > 0) {
                while ($row = $prevResult->fetch_assoc()) {
                    // Sum up the total_amount for all invoices within last week
                    $prevTotalSales += $row['total_amount'];
                }
            }

            // Query to get total sales (total_amount) for the week before last week
            $prev2Query = "SELECT total_amount FROM invoice WHERE DATE(transaction_date) BETWEEN ? AND ?";
            $prev2Stmt = $conn->prepare($prev2Query);
            $prev2Stmt->bind_param('ss', $weekBeforeLastStart, $weekBeforeLastEnd);
            $prev2Stmt->execute();
            $prev2Result = $prev2Stmt->get_result();

            $prev2TotalSales = 0;

            if ($prev2Result->num_rows > 0) {
                while ($row = $prev2Result->fetch_assoc()) {
                    // Sum up the total_amount for all invoices within the week before last
                    $prev2TotalSales += $row['total_amount'];
                }
            }

            // Calculate the percentage change in weekly revenue (comparing last week to the week before last week)
            $revenuePercentage = 0;
            if ($prev2TotalSales > 0) {
                $revenuePercentage = (($prevTotalSales - $prev2TotalSales) / $prev2TotalSales) * 100;
            }

            ?>


            <div class="card">
                <div class="card-body">
                    <div>
                        <h5>Weekly Revenue</h5>
                        <p class="sales-amount"><?php echo '₱' . number_format($prevTotalSales, 2); ?></p>
                    </div>
                    <div class="percentage-box">
                        <div class="percentage-body">
                            <?php if ($revenuePercentage > 0) { ?>
                                <div class="up">
                                    <img src="../../assets/up.png" alt="" style="width:25px;margin-right:5px;">
                                    <p class="percentage-up">+<?php echo number_format($revenuePercentage, 2); ?>%</p>
                                </div>
                            <?php } elseif ($revenuePercentage < 0) { ?>
                                <div class="down">
                                    <img src="../../assets/down.png" alt="" style="width:25px; margin-right:5px;">
                                    <p class="percentage-down"><?php echo number_format($revenuePercentage, 2); ?>%</p>
                                </div>
                            <?php } else { ?>
                                <div class="neutral">
                                    <img src="../../assets/neutral.png" alt="" style="width:25px; margin-right:5px;">
                                    <p class="percentage-neutral"> 0.00%</p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <?php
                    // Get the start of last week and the end of last week for display
                    $lastWeekStart = date('Y-m-d', strtotime('monday last week'));
                    $lastWeekEnd = date('Y-m-d', strtotime('sunday last week'));

                    // Format the dates for display (e.g., "November 19, 2024 - November 25, 2024")
                    $last_week_sd = date("F d, Y", strtotime($lastWeekStart));
                    $last_week_ed = date("F d, Y", strtotime($lastWeekEnd));

                    // Display the date range for last week
                    ?>
                    <p><?php echo 'From ' . $last_week_sd . ' - ' . $last_week_ed; ?></p>
                </div>
            </div>
        </div>


        <div class="same-column-container">
            <div class="same-column">
                <div class="table_container daily_sales">
                    <?php include 'daily_sales.php'; ?>
                </div>
                <div class="table_container recent_orders">
                    <h3>Recent Orders</h3>
                    <?php include 'recent_orders.php'; ?>
                </div>
            </div>

            <div class="same-column">
                <div class="table_container daily_sales2">
                    <h3 class="h3-header"> Recent Inventory Updates</h3>
                    <?php include 'recent_updates.php'; ?>
                </div>
                <div class="table_container recent_orders2">
                    <h3>Current Week Top Products</h3>
                    <?php include 'top_products.php'; ?>
                </div>

            </div>
        </div>

    </div>
</div>