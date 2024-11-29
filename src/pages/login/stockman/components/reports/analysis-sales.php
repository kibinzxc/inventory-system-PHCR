<?php
include '../../connection/database.php';
Error_reporting(1);

// Fetch all historical data for seasonality calculation (across all data, not just last week)
$sql = "
    SELECT DAYOFWEEK(transaction_date) AS weekday, AVG(total_amount) AS avg_sales
    FROM invoice
    GROUP BY DAYOFWEEK(transaction_date)
";
$result = $conn->query($sql);

$seasonality = [];
while ($row = $result->fetch_assoc()) {
    // Store the average sales for each weekday (1=Monday, 7=Sunday)
    $seasonality[$row['weekday']] = $row['avg_sales'];
}

$conn->close();

// Generate next week's dates (Monday to Sunday)
$nextWeekStart = date('Y-m-d', strtotime('monday next week'));
$nextWeekDates1 = [];
$nextWeekSales1 = [];

for ($i = 0; $i < 7; $i++) { // Loop Monday to Sunday (7 days)
    $date = date('Y-m-d', strtotime("$nextWeekStart +$i days"));
    $nextWeekDates1[$i + 1] = date('D', strtotime($date)); // Store day name (Mon, Tue, etc.) with unique number
    $weekday = date('N', strtotime($date)); // Get weekday (1=Monday, 7=Sunday)
    $nextWeekSales1[$i + 1] = $seasonality[$weekday] ?? 0; // Store sales for each day with unique number
}

// Calculate analysis data
$totalForecastedSales1 = array_sum($nextWeekSales1);
$averageForecastedSales1 = $totalForecastedSales1 / count($nextWeekSales1);
$maxForecastedSales1 = max($nextWeekSales1);
$minForecastedSales1 = min($nextWeekSales1);
$maxSalesDay1 = $nextWeekDates1[array_search($maxForecastedSales1, $nextWeekSales1)];
$minSalesDay1 = $nextWeekDates1[array_search($minForecastedSales1, $nextWeekSales1)];
?>


<style>
    h1,
    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    .analysis {
        margin-top: 20px;
    }

    .analysis p {
        font-size: 1.1em;
        line-height: 1.6;
    }
</style>
</head>

<body>
    <div class="container">

    </div>

    <div class="container analysis">
        <h2>Analysis</h2>
        <p><strong>Total Forecasted Sales for Next Week:</strong> ₱<?php echo number_format($totalForecastedSales1, 2); ?></p>
        <p><strong>Average Forecasted Sales per Day:</strong> ₱<?php echo number_format($averageForecastedSales1, 2); ?></p>
        <p><strong>Highest Forecasted Sales Day:</strong> <?php echo $maxSalesDay1; ?> (₱<?php echo number_format($maxForecastedSales1, 2); ?>)</p>
        <p><strong>Lowest Forecasted Sales Day:</strong> <?php echo $minSalesDay1; ?> (₱<?php echo number_format($minForecastedSales1, 2); ?>)</p>
    </div>