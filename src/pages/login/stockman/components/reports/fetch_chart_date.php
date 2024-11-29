<?php
include '../../connection/database.php';
Error_reporting(1);

// Get the 'product' parameter from the URL
$selectedProduct = isset($_GET['product']) ? $_GET['product'] : '';

// Get current date and calculate start and end of last week (Monday to Sunday 11:59 PM)
$currentDate = new DateTime();
$currentDate->setISODate($currentDate->format('Y'), $currentDate->format('W'));
$startOfLastWeek = $currentDate->modify('last Monday')->format('Y-m-d');
$endOfLastWeek = $currentDate->modify('next Sunday')->setTime(23, 59)->format('Y-m-d H:i');

// Calculate the start and end dates for the week before last
$startOfWeekBeforeLast = (new DateTime($startOfLastWeek))->modify('-1 week')->format('Y-m-d');
$endOfWeekBeforeLast = (new DateTime($endOfLastWeek))->modify('-1 week')->setTime(23, 59)->format('Y-m-d H:i');

// Calculate the date range for the title (formatted as "Nov 24 - 27, 2024")
$startDateObj = new DateTime($startOfLastWeek);
$endDateObj = new DateTime($endOfLastWeek);
$dateRange = $startDateObj->format('M d') . ' - ' . $endDateObj->format('d, Y');

// Query to get the order count of the selected product for last week
$sqlOrdersLastWeek = "
    SELECT day_counted, SUM(quantity) AS total_quantity
    FROM usage_reports
    WHERE name = ? AND day_counted >= ? AND day_counted <= ?
    GROUP BY day_counted
    ORDER BY day_counted
";
$stmtOrdersLastWeek = $conn->prepare($sqlOrdersLastWeek);
$stmtOrdersLastWeek->bind_param("sss", $selectedProduct, $startOfLastWeek, $endOfLastWeek);
$stmtOrdersLastWeek->execute();
$resultOrdersLastWeek = $stmtOrdersLastWeek->get_result();

// Query to get the order count of the selected product for the week before last week
$sqlOrdersWeekBeforeLast = "
    SELECT day_counted, SUM(quantity) AS total_quantity
    FROM usage_reports
    WHERE name = ? AND day_counted >= ? AND day_counted <= ?
    GROUP BY day_counted
    ORDER BY day_counted
";
$stmtOrdersWeekBeforeLast = $conn->prepare($sqlOrdersWeekBeforeLast);
$stmtOrdersWeekBeforeLast->bind_param("sss", $selectedProduct, $startOfWeekBeforeLast, $endOfWeekBeforeLast);
$stmtOrdersWeekBeforeLast->execute();
$resultOrdersWeekBeforeLast = $stmtOrdersWeekBeforeLast->get_result();

// Prepare data for the chart
$labels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];  // Days of the week
$dataLastWeek = [0, 0, 0, 0, 0, 0, 0];    // Initialize order count data for last week
$dataWeekBeforeLast = [0, 0, 0, 0, 0, 0, 0]; // Initialize order count data for week before last

// Map days of the week to indices in the $data array
$dayMap = [
    'Monday' => 0,
    'Tuesday' => 1,
    'Wednesday' => 2,
    'Thursday' => 3,
    'Friday' => 4,
    'Saturday' => 5,
    'Sunday' => 6
];

// Process query results for last week and update the $dataLastWeek array
while ($row = $resultOrdersLastWeek->fetch_assoc()) {
    $dayOfWeek = (new DateTime($row['day_counted']))->format('l');  // Get day of the week (e.g., Monday)
    $dayIndex = $dayMap[$dayOfWeek];  // Get corresponding index for the day
    $dataLastWeek[$dayIndex] = $row['total_quantity'];  // Set order count for that day
}

// Process query results for the week before last week and update the $dataWeekBeforeLast array
while ($row = $resultOrdersWeekBeforeLast->fetch_assoc()) {
    $dayOfWeek = (new DateTime($row['day_counted']))->format('l');  // Get day of the week (e.g., Monday)
    $dayIndex = $dayMap[$dayOfWeek];  // Get corresponding index for the day
    $dataWeekBeforeLast[$dayIndex] = $row['total_quantity'];  // Set order count for that day
}
?>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Create a canvas for the chart -->
<canvas id="ordersChart" width="600" height="300"></canvas>

<script>
    var ctx6 = document.getElementById('ordersChart').getContext('2d');
    var ordersChart = new Chart(ctx6, {
        type: 'line', // Define chart type (line chart)
        data: {
            labels: <?php echo json_encode($labels); ?>, // Days of the week
            datasets: [{
                    label: 'Last Week', // Dataset for last week
                    data: <?php echo json_encode($dataLastWeek); ?>, // Order counts for last week
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                    tension: 0.4 // Smooth the line
                },
                {
                    label: 'Two Weeks Ago ',
                    data: <?php echo json_encode($dataWeekBeforeLast); ?>, // Order counts for the week before last
                    borderColor: 'rgba(255, 99, 132, 1)', // Different color for the second dataset
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                    tension: 0.4 // Smooth the line
                }
            ]
        },
        options: {
            responsive: true,
            animation: {
                duration: 1000, // Animation duration set to 1 second (1000ms)
                easing: 'easeOutQuad' // Smoothing effect for the animation
            },
            scales: {
                y: {
                    beginAtZero: true, // Start the y-axis at zero
                    title: {
                        display: true,
                        text: '<?php echo $selectedProduct . " Order Count"; ?> '
                    },
                    ticks: {
                        callback: function(value) {
                            return value % 1 === 0 ? value : ''; // Remove decimal places (i.e., show only integer values)
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: '<?php echo $dateRange; ?>' // Display the calculated date range for last week
                    }
                }
            }
        }
    });

    // Force chart to re-render with animation on page load
    window.onload = function() {
        ordersChart.update(); // This will trigger the animation when the page reloads
    };
</script>