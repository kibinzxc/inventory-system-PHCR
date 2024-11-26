<?php
session_start();
Error_reporting(1);

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
                <p class="sub-header">You have<span class="colored"> 2 unread </span> notifications.</p>
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
                    <div class="percentage-box">
                        <div class="percentage-body">
                            <?php if ($percentageChange > 0) { ?>
                                <div class="up">
                                    <!-- <a href="https://www.flaticon.com/free-icons/graph" title="graph icons">Graph icons created by Icon Hubs - Flaticon</a> -->
                                    <img src="../../assets/up.png" alt="" srcset="" style="width:25px;margin-right:5px;">
                                    <p class="percentage-up">+<?php echo number_format($percentageChange, 2); ?>%</p>
                                </div>
                            <?php } elseif ($percentageChange < 0) { ?>
                                <div class="down">
                                    <!-- <a href="https://www.flaticon.com/free-icons/trend" title="trend icons">Trend icons created by Amazona Adorada - Flaticon</a> -->
                                    <img src="../../assets/down.png" alt="" srcset="" style="width:25px; margin-right:5px;">
                                    <p class="percentage-down"><?php echo number_format($percentageChange, 2); ?>%</p>
                                </div>
                            <?php } else { ?>
                                <div class="neutral">
                                    <!-- <a href="https://www.flaticon.com/free-icons/growth" title="growth icons">Growth icons created by Prosymbols Premium - Flaticon</a> -->
                                    <img src="../../assets/neutral.png" alt="" srcset="" style="width:25px; margin-right:5px;">
                                    <p class="percentage-neutral"> 0.00%</p>
                                </div>
                            <?php } ?>
                        </div>
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
                        // Fetch total sales and orders for the week
                        $weekStart = date('Y-m-d', strtotime('monday this week')); // Start of the week (Monday)
                        $today = date('Y-m-d');

                        // Query for invoices in the last 7 days
                        $query = "SELECT orders, transaction_date FROM invoice WHERE DATE(transaction_date) BETWEEN ? AND ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('ss', $weekStart, $today);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        $totalSales = 0;
                        $totalOrders = 0;

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $orders = json_decode($row['orders'], true);

                                if (is_array($orders)) {
                                    foreach ($orders as $order) {
                                        $totalSales += $order['price'] * $order['quantity'];
                                        $totalOrders++;
                                    }
                                }
                            }
                        }

                        // Calculate Average Order Value
                        $averageOrderValue = ($totalOrders > 0) ? $totalSales / $totalOrders : 0; ?>
                        <p class="sales-amount"><?php echo '₱' . number_format($averageOrderValue, 2); ?></p>
                    </div>

                    <?php
                    $lastWeekStart = date('Y-m-d', strtotime('monday last week'));
                    $lastWeekEnd = date('Y-m-d', strtotime('sunday last week'));

                    // Query to fetch total_amount directly
                    $prevQuery = "SELECT total_amount FROM invoice WHERE DATE(transaction_date) BETWEEN ? AND ?";
                    $prevStmt = $conn->prepare($prevQuery);
                    $prevStmt->bind_param('ss', $lastWeekStart, $lastWeekEnd);
                    $prevStmt->execute();
                    $prevResult = $prevStmt->get_result();

                    $prevTotalSales = 0;

                    if ($prevResult->num_rows > 0) {
                        while ($row = $prevResult->fetch_assoc()) {
                            // Sum up the total_amount for all invoices within the last week
                            $prevTotalSales += $row['total_amount'];
                        }
                    }

                    // Calculate Average Order Value for last week
                    $prevAOV = $prevTotalSales > 0 ? $prevTotalSales / 1 : 0;  // No orders count needed as you're summing the total_amount directly

                    // Calculate percentage change in AOV
                    $aovPercentageChange = 0;
                    if ($prevAOV > 0) {
                        $aovPercentageChange = (($averageOrderValue - $prevAOV) / $prevAOV) * 100;
                    }
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
                    // Get the start of the current week (Monday) and the end of the current week (Sunday)
                    $thisWeekStart = date('Y-m-d', strtotime('monday this week'));
                    $thisWeekEnd = date('Y-m-d', strtotime('sunday this week'));

                    // Format the dates for display (e.g., "November 26, 2024 - December 02, 2024")
                    $this_week_sd = date("F d, Y", strtotime($thisWeekStart));  // Example: "November 26, 2024"
                    $this_week_ed = date("F d, Y", strtotime($thisWeekEnd));    // Example: "December 02, 2024"

                    // Display the date range
                    ?>
                    <p><?php echo 'From ' . $this_week_sd . ' - ' . $this_week_ed; ?></p>

                </div>
            </div>

            <?php

            // Define the date range for this week
            $thisWeekStart = date('Y-m-d', strtotime('monday this week'));
            $thisWeekEnd = date('Y-m-d', strtotime('sunday this week'));

            // Query to get total sales (total_amount) for this week
            $currentQuery = "SELECT total_amount FROM invoice WHERE DATE(transaction_date) BETWEEN ? AND ?";
            $currentStmt = $conn->prepare($currentQuery);
            $currentStmt->bind_param('ss', $thisWeekStart, $thisWeekEnd);
            $currentStmt->execute();
            $currentResult = $currentStmt->get_result();

            $currentTotalSales = 0;

            if ($currentResult->num_rows > 0) {
                while ($row = $currentResult->fetch_assoc()) {
                    // Sum up the total_amount for all invoices within this week
                    $currentTotalSales += $row['total_amount'];
                }
            }

            // Define the date range for last week
            $lastWeekStart = date('Y-m-d', strtotime('monday last week'));
            $lastWeekEnd = date('Y-m-d', strtotime('sunday last week'));

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

            // Calculate the percentage change in weekly revenue
            $revenuePercentage = 0;
            if ($prevTotalSales > 0) {
                $revenuePercentage = (($currentTotalSales - $prevTotalSales) / $prevTotalSales) * 100;
            }


            ?>

            <div class="card">
                <div class="card-body">
                    <div>
                        <h5>Weekly Revenue</h5>
                        <p class="sales-amount"><?php echo '₱' . number_format($currentTotalSales, 2); ?></p>
                    </div>
                    <div class="percentage-box">
                        <div class="percentage-body">
                            <?php if ($revenuePercentage > 0) { ?>
                                <div class="up">
                                    <!-- <a href="https://www.flaticon.com/free-icons/trend" title="trend icons">Trend icons created by Amazona Adorada - Flaticon</a> -->
                                    <img src="../../assets/up.png" alt="" style="width:25px;margin-right:5px;">
                                    <p class="percentage-up">+<?php echo number_format($revenuePercentage, 2); ?>%</p>
                                </div>
                            <?php } elseif ($revenuePercentage < 0) { ?>
                                <div class="down">
                                    <!-- <a href="https://www.flaticon.com/free-icons/trend" title="trend icons">Trend icons created by Amazona Adorada - Flaticon</a> -->
                                    <img src="../../assets/down.png" alt="" style="width:25px; margin-right:5px;">
                                    <p class="percentage-down"><?php echo number_format($revenuePercentage, 2); ?>%</p>
                                </div>
                            <?php } else { ?>
                                <div class="neutral">
                                    <!-- <a href="https://www.flaticon.com/free-icons/trend" title="trend icons">Trend icons created by Amazona Adorada - Flaticon</a> -->
                                    <img src="../../assets/neutral.png" alt="" style="width:25px; margin-right:5px;">
                                    <p class="percentage-neutral"> 0.00%</p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <?php
                    // Get the start of the current week (Monday) and the end of the current week (Sunday)
                    $thisWeekStart = date('Y-m-d', strtotime('monday this week'));
                    $thisWeekEnd = date('Y-m-d', strtotime('sunday this week'));

                    // Format the dates for display (e.g., "November 26, 2024 - December 02, 2024")
                    $this_week_sd = date("F d, Y", strtotime($thisWeekStart));  // Example: "November 26, 2024"
                    $this_week_ed = date("F d, Y", strtotime($thisWeekEnd));    // Example: "December 02, 2024"

                    // Display the date range
                    ?>
                    <p><?php echo 'From ' . $this_week_sd . ' - ' . $this_week_ed; ?></p>

                </div>
            </div>

            <?php
            // Database connection
            include("db_connection.php"); // Include your DB connection

            // Get current date
            $currentDate = date('Y-m-d');

            // Get the start of the week (Monday)
            $startOfWeek = date('Y-m-d', strtotime('last monday', strtotime($currentDate)));

            // Query to count the number of spoilage reports for the current week
            $query = "SELECT COUNT(*) AS total_reports FROM spoilage_reports WHERE date_reported BETWEEN '$startOfWeek' AND '$currentDate'";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_assoc($result);

            $totalReports = $row['total_reports'] ? $row['total_reports'] : 0; // Default to 0 if no data is found

            // Optional: Query for percentage change if required
            // This can be adjusted based on how you calculate the percentage change
            ?>
            <?php

            $currentDate = date('Y-m-d');

            // Get the start and end of the current week (Monday to Sunday)
            $startOfWeek = date('Y-m-d', strtotime('last monday', strtotime($currentDate)));
            $endOfWeek = date('Y-m-d', strtotime('next sunday', strtotime($currentDate)));

            // Get the start and end of last week (Monday to Sunday)
            $startOfLastWeek = date('Y-m-d', strtotime('last monday', strtotime('-1 week', strtotime($currentDate))));
            $endOfLastWeek = date('Y-m-d', strtotime('next sunday', strtotime('-1 week', strtotime($currentDate))));

            // Query to get the total number of spoilage reports for this week
            $queryThisWeek = "SELECT COUNT(*) AS total_reports FROM spoilage_reports WHERE date_reported BETWEEN '$startOfWeek' AND '$endOfWeek'";
            $resultThisWeek = mysqli_query($conn, $queryThisWeek);
            $rowThisWeek = mysqli_fetch_assoc($resultThisWeek);
            $totalReportsThisWeek = $rowThisWeek['total_reports'] ? $rowThisWeek['total_reports'] : 0;

            // Query to get the total number of spoilage reports for last week
            $queryLastWeek = "SELECT COUNT(*) AS total_reports FROM spoilage_reports WHERE date_reported BETWEEN '$startOfLastWeek' AND '$endOfLastWeek'";
            $resultLastWeek = mysqli_query($conn, $queryLastWeek);
            $rowLastWeek = mysqli_fetch_assoc($resultLastWeek);
            $totalReportsLastWeek = $rowLastWeek['total_reports'] ? $rowLastWeek['total_reports'] : 0;

            // Calculate percentage change
            if ($totalReportsLastWeek > 0) {
                $spoilagepercentageChange = (($totalReportsLastWeek - $totalReportsThisWeek) / $totalReportsLastWeek) * 100;
            } else {
                $spoilagepercentageChange = 0;
            }

            $spoilageRate = $totalReportsThisWeek;
            ?>

            <div class="card">
                <div class="card-body">
                    <div>
                        <h5>Spoilage Rate</h5>
                        <p class="sales-amount"><?php echo number_format($spoilageRate) . ' ' . ($spoilageRate == 1 ? 'Report' : 'Reports'); ?></p>
                    </div>
                    <div class="percentage-box">
                        <div class="percentage-body">
                            <?php if ($spoilagepercentageChange < 0) { // Spoilage rate is down, good sign 
                            ?>
                                <div class="up-spoilage">
                                    <!-- <a href="https://www.flaticon.com/free-icons/trend" title="trend icons">Trend icons created by Amazona Adorada - Flaticon</a> -->
                                    <img src="../../assets/neutral.png" alt="" style="width:25px;margin-right:5px;">
                                    <p class="percentage-up-spoilage">+<?php echo number_format(abs($percentageChange), 2); ?>%</p>
                                </div>
                            <?php } elseif ($spoilagepercentageChange > 0) { // Spoilage rate is up, bad sign 
                            ?>
                                <div class="down-spoilage">
                                    <!-- <a href="https://www.flaticon.com/free-icons/trend" title="trend icons">Trend icons created by Amazona Adorada - Flaticon</a> -->
                                    <img src="../../assets/trend.png" alt="" style="width:25px; margin-right:5px;">
                                    <p class="percentage-down-spoilage">-<?php echo number_format($spoilagepercentageChange, 2); ?>%</p>
                                </div>
                            <?php } else { // No change 
                            ?>
                                <div class="down-spoilage">
                                    <!-- <a href="https://www.flaticon.com/free-icons/trend" title="trend icons">Trend icons created by Amazona Adorada - Flaticon</a> -->
                                    <img src="../../assets/trend.png" alt="" style="width:25px; margin-right:5px;">
                                    <p class="percentage-down-spoilage">0.00%</p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <p>From <?php echo date('F d, Y', strtotime($startOfWeek)) . ' - ' . date('F d, Y', strtotime($endOfWeek)); ?></p>
                </div>
            </div>
        </div>

        <div class="same-column">
            <div class="table_container daily_sales">
                <?php include 'daily_sales.php'; ?>
            </div>
            <div class="table_container recent_orders">
                <h3>Recent Orders</h3>
                <?php include 'recent_orders.php'; ?>
            </div>
        </div>


    </div>
</div>