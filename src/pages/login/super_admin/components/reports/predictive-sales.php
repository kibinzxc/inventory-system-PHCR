<?php
include '../../connection/database.php';

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

// Debug: Output the seasonality array
echo "<pre>Seasonality Data:\n";
print_r($seasonality);
echo "</pre>";

$conn->close();

// Generate next week's dates (Monday to Sunday)
$nextWeekStart = date('Y-m-d', strtotime('monday next week'));
$nextWeekDates = [];
$nextWeekSales = [];
for ($i = 0; $i < 7; $i++) { // Loop Monday to Sunday (7 days)
    $date = date('Y-m-d', strtotime("$nextWeekStart +$i days"));
    $nextWeekDates[] = date('D', strtotime($date)); // Show the short day name (Mon, Tue, etc.)
    $weekday = date('N', strtotime($date)); // Get weekday (1=Monday, 7=Sunday)
    $nextWeekSales[] = $seasonality[$weekday] ?? 0; // Apply seasonality factor for each day
}

// Debug: Output the forecast data
echo "<pre>Next Week Forecast:\n";
for ($i = 0; $i < 7; $i++) {
    echo $nextWeekDates[$i] . ": ₱" . number_format($nextWeekSales[$i], 2) . "\n";
}
echo "</pre>";
