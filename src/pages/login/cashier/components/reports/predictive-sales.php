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
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div id="chart-container">
    <canvas id="salesForecastChart"></canvas>
</div>

<script>
    const nextWeekDates = <?php echo json_encode($nextWeekDates); ?>;
    const nextWeekSales = <?php echo json_encode($nextWeekSales); ?>;

    const ctx6 = document.getElementById('salesForecastChart').getContext('2d');
    new Chart(ctx6, {
        type: 'line',
        data: {
            labels: nextWeekDates,
            datasets: [{
                label: 'Forecasted Sales (₱)',
                data: nextWeekSales,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderWidth: 2,
                pointRadius: 4,
                tension: 0.4
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Sales (₱)'
                    },
                    ticks: {
                        callback: function(value) {
                            // Format the Y-axis labels as currency
                            return '₱' + value.toFixed(2);
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Days of Next Week'
                    }
                }
            }
        }
    });
</script>