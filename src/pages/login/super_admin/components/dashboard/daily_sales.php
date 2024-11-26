<?php
include '../../connection/database.php';

// Get the start and end of the current week
$startOfWeek = date('Y-m-d', strtotime('monday this week'));
$endOfWeek = date('Y-m-d', strtotime('sunday this week'));

// Get the start and end of the previous week
$startOfPrevWeek = date('Y-m-d', strtotime('monday last week'));
$endOfPrevWeek = date('Y-m-d', strtotime('sunday last week'));

$currentDate = date('Y-m-d'); // Today's date

// Query to fetch sales data for the current week
$queryCurrentWeek = "
    SELECT DATE(transaction_date) AS date, SUM(total_amount) AS total_sales
    FROM invoice
    WHERE DATE(transaction_date) BETWEEN '$startOfWeek' AND '$endOfWeek'
    GROUP BY DATE(transaction_date)
    ORDER BY DATE(transaction_date)
";

$queryPrevWeek = "
    SELECT DATE(transaction_date) AS date, SUM(total_amount) AS total_sales
    FROM invoice
    WHERE DATE(transaction_date) BETWEEN '$startOfPrevWeek' AND '$endOfPrevWeek'
    GROUP BY DATE(transaction_date)
    ORDER BY DATE(transaction_date)
";

$resultCurrentWeek = $conn->query($queryCurrentWeek);
$resultPrevWeek = $conn->query($queryPrevWeek);

// Initialize arrays for current week
$dates = [];
$salesCurrentWeek = [];
$days = [];

// Initialize array for previous week sales
$salesPrevWeek = [];

// Fill current week days
for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime("$startOfWeek +$i days"));
    $dates[] = $date;
    $salesCurrentWeek[$date] = 0; // Default to null for no data
    $days[] = date('D', strtotime($date)); // Short day names
}

// Fill sales data for current week
while ($row = $resultCurrentWeek->fetch_assoc()) {
    $salesCurrentWeek[$row['date']] = $row['total_sales'];
}

// Fill sales data for previous week (align by day)
for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime("$startOfPrevWeek +$i days"));
    $salesPrevWeek[$date] = 0; // Default sales for previous week
}

while ($row = $resultPrevWeek->fetch_assoc()) {
    $salesPrevWeek[$row['date']] = $row['total_sales'];
}

$conn->close();
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    #chart-container {
        width: 100%;
        height: 300px;
        margin-bottom: 10px;
    }

    canvas {
        width: 100% !important;
        height: 100% !important;
    }

    .sales-title {
        margin-top: 5px;
    }
</style>

<h3 class="sales-title">Weekly Sales Forecasting</h3>
<div id="chart-container">
    <canvas id="salesChart"></canvas>
</div>

<script>
    // Data from PHP
    const dates = <?php echo json_encode($dates); ?>; // Current week full dates
    const days = <?php echo json_encode($days); ?>; // Short day names
    const salesCurrent = <?php echo json_encode(array_values($salesCurrentWeek)); ?>; // Current week sales
    const salesPrev = <?php echo json_encode(array_values($salesPrevWeek)); ?>; // Previous week sales
    const currentDate = '<?php echo $currentDate; ?>'; // Current date

    // Identify the index of the current date
    const currentDateIndex = dates.indexOf(currentDate);

    // Format sales data to ensure values are numeric and fixed to 2 decimal places
    const formatSalesData = (salesData) => {
        return salesData.map(sale => {
            const numericSale = parseFloat(sale);
            return isNaN(numericSale) ? 0 : numericSale.toFixed(2); // Default to 0 if NaN
        });
    };

    // Check if the day is in the future
    const isFutureDay = (date) => {
        const current = new Date();
        const targetDate = new Date(date);
        return targetDate > current;
    };

    // Chart.js configuration
    const ctx = document.getElementById('salesChart').getContext('2d');

    // Check if canvas context is valid
    if (ctx) {
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: days, // Display short day names
                datasets: [{
                        label: 'Current Week',
                        data: formatSalesData(salesCurrent),
                        borderColor: '#009797',
                        backgroundColor: 'rgba(0, 151, 151, 0.2)',
                        borderWidth: 2,
                        pointRadius: (context) => {
                            const sale = context.raw;
                            const date = dates[context.dataIndex];
                            // Remove point if the sales value is 0 or if the date is in the future (current week only)
                            return (sale === 0 || isFutureDay(date)) ? 0 : 4;
                        },
                        pointBackgroundColor: (context) => {
                            const sale = context.raw;
                            const date = dates[context.dataIndex];
                            // Remove point if the sales value is 0 or if the date is in the future (current week only)
                            return (sale === 0 || isFutureDay(date)) ? 'transparent' : (dates[context.dataIndex] === currentDate ? '#006363' : '#009797');
                        },
                        pointBorderColor: (context) => {
                            const sale = context.raw;
                            const date = dates[context.dataIndex];
                            // Remove point if the sales value is 0 or if the date is in the future (current week only)
                            return (sale === 0 || isFutureDay(date)) ? 'transparent' : (dates[context.dataIndex] === currentDate ? '#006363' : '#009797');
                        },
                        spanGaps: true, // Prevent connecting lines across null values
                        tension: 0.4 // Smooth curve
                    },
                    {
                        label: 'Previous Week',
                        data: formatSalesData(salesPrev),
                        borderColor: '#DBAB03',
                        backgroundColor: 'rgba(219, 171, 3, 0.2)',
                        borderWidth: 2,
                        pointRadius: 4, // Always show a point for previous week, even if sales is 0
                        pointBackgroundColor: '#DBAB03', // Previous week circle color
                        pointBorderColor: '#DBAB03', // Previous week border color
                        tension: 0.4 // Smooth curve
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 2000 // Slower animation
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Days of the Week'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Total Sales (PHP)'
                        },
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                // Add ".00" to sales values
                                return value.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    } else {
        console.error("Canvas context not found.");
    }
</script>