<?php
include '../../connection/database.php';
error_reporting(0);

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Get the dates for the last week and two weeks ago
$startOfLastWeek = date('Y-m-d 00:00:00', strtotime('monday last week'));
$endOfLastWeek = date('Y-m-d 23:59:59', strtotime('sunday last week'));
$startOfTwoWeeksAgo = date('Y-m-d 00:00:00', strtotime('monday -2 weeks'));
$endOfTwoWeeksAgo = date('Y-m-d 23:59:59', strtotime('sunday -2 weeks'));

// Fetch daily sales data for the last week
$sql = "
    SELECT DATE(orderPlaced) as orderDate, SUM(totalPrice) as totalSales
    FROM orders
    WHERE orderPlaced BETWEEN ? AND ?
    GROUP BY DATE(orderPlaced)
    ORDER BY DATE(orderPlaced)
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $startOfLastWeek, $endOfLastWeek);
$stmt->execute();
$result = $stmt->get_result();

$salesDataLastWeek = [];
while ($row = $result->fetch_assoc()) {
    $salesDataLastWeek[$row['orderDate']] = $row['totalSales'];
}

$stmt->close();

// Fetch daily sales data for two weeks ago
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $startOfTwoWeeksAgo, $endOfTwoWeeksAgo);
$stmt->execute();
$result = $stmt->get_result();

$salesDataTwoWeeksAgo = [];
while ($row = $result->fetch_assoc()) {
    $salesDataTwoWeeksAgo[$row['orderDate']] = $row['totalSales'];
}

$stmt->close();
$conn->close();

// Prepare data for the chart
$dates = [];
$salesLastWeek = [];
$salesTwoWeeksAgo = [];
$currentDate = $startOfTwoWeeksAgo;
while (strtotime($currentDate) <= strtotime($endOfLastWeek)) {
    $formattedDate = date('F j', strtotime($currentDate)); // Format each date
    $dates[] = $formattedDate;
    $salesLastWeek[] = isset($salesDataLastWeek[$currentDate]) ? $salesDataLastWeek[$currentDate] : 0;
    $salesTwoWeeksAgo[] = isset($salesDataTwoWeeksAgo[$currentDate]) ? $salesDataTwoWeeksAgo[$currentDate] : 0;
    $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Sales Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        canvas {
            max-width: 100%;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3>Weekly Sales Forecasting</h3>
        <canvas id="salesChart"></canvas>
    </div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                        label: 'Last Week Sales (₱)',
                        data: <?php echo json_encode($salesLastWeek); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        fill: false
                    },
                    {
                        label: 'Two Weeks Ago Sales (₱)',
                        data: <?php echo json_encode($salesTwoWeeksAgo); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        fill: false
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>