<?php
require('../fpdf186/fpdf.php'); // Include the FPDF library
include '../../connection/database.php';
error_reporting(1);

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Set current date and time
$currentDateTime = new DateTime();
$currentHour = (int) $currentDateTime->format('H');

if ($currentHour < 6) {
    $inventoryDate = $currentDateTime->modify('-1 day')->format('Y-m-d');
} else {
    $inventoryDate = $currentDateTime->format('Y-m-d');
}

// Fetch today's total sales from the invoice table
$today = $inventoryDate;
$sql = "
    SELECT SUM(total_amount) as totalSalesToday
    FROM invoice
    WHERE DATE(transaction_date) = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
$totalSalesToday = $result->fetch_assoc()['totalSalesToday'] ?? 0;
$stmt->close();

// Get the dates for the last week and two weeks ago
$startOfLastWeek = date('Y-m-d 00:00:00', strtotime('monday last week'));
$endOfLastWeek = date('Y-m-d 23:59:59', strtotime('sunday last week'));
$startOfTwoWeeksAgo = date('Y-m-d 00:00:00', strtotime('monday last week -7 days'));
$endOfTwoWeeksAgo = date('Y-m-d 23:59:59', strtotime('sunday last week -7 days'));

// Fetch weekly revenue and average sales for last week
$sql = "
    SELECT SUM(total_amount) as totalRevenue, COUNT(invID) as totalInvoices
    FROM invoice
    WHERE transaction_date BETWEEN ? AND ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $startOfLastWeek, $endOfLastWeek);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalRevenueLastWeek = $row['totalRevenue'] ?? 0;
$totalInvoicesLastWeek = $row['totalInvoices'] ?? 0;
$averageOrderValueLastWeek = $totalInvoicesLastWeek > 0 ? $totalRevenueLastWeek / $totalInvoicesLastWeek : 0;
$stmt->close();

// Fetch weekly revenue and average sales for two weeks ago
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $startOfTwoWeeksAgo, $endOfTwoWeeksAgo);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalRevenueTwoWeeksAgo = $row['totalRevenue'] ?? 0;
$totalInvoicesTwoWeeksAgo = $row['totalInvoices'] ?? 0;
$averageOrderValueTwoWeeksAgo = $totalInvoicesTwoWeeksAgo > 0 ? $totalRevenueTwoWeeksAgo / $totalInvoicesTwoWeeksAgo : 0;
$stmt->close();

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

// Calculate analysis data
$totalForecastedSales = array_sum($nextWeekSales);
$averageForecastedSales = $totalForecastedSales / count($nextWeekSales);
$maxForecastedSales = max($nextWeekSales);
$minForecastedSales = min($nextWeekSales);
$maxSalesDay = $nextWeekDates[array_search($maxForecastedSales, $nextWeekSales)];
$minSalesDay = $nextWeekDates[array_search($minForecastedSales, $nextWeekSales)];

// Create new PDF document
$pdf = new FPDF();
$pdf->AddPage();

// Set title
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Sales Analytics Report', 0, 1, 'C');
$pdf->Image('../../assets/logo-black.png', 10, 10, 50); // Adjust the path and size as needed

// Set subtitle
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Generated on ' . date('F j, Y'), 0, 1, 'C');

// Add a line break
$pdf->Ln(10);

// Set content font
$pdf->SetFont('Arial', '', 12);

// Add total sales today
$pdf->Cell(0, 10, 'Total Sales Today: PHP ' . number_format($totalSalesToday, 2), 0, 1, 'C');

// Add average order value last week and comparison to two weeks ago
$pdf->Cell(0, 10, 'Average Order Value Last Week: PHP ' . number_format($averageOrderValueLastWeek, 2), 0, 1, 'C');
$pdf->Cell(0, 10, 'Average Order Value Two Weeks Ago: PHP ' . number_format($averageOrderValueTwoWeeksAgo, 2), 0, 1, 'C');

// Add weekly revenue last week and two weeks ago
$pdf->Cell(0, 10, 'Weekly Revenue Last Week: PHP ' . number_format($totalRevenueLastWeek, 2), 0, 1, 'C');
$pdf->Cell(0, 10, 'Weekly Revenue Two Weeks Ago: PHP ' . number_format($totalRevenueTwoWeeksAgo, 2), 0, 1, 'C');

// Add forecasted sales analysis
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Total Forecasted Sales for Next Week: PHP ' . number_format($totalForecastedSales, 2), 0, 1, 'C');
$pdf->Cell(0, 10, 'Average Forecasted Sales per Day: PHP ' . number_format($averageForecastedSales, 2), 0, 1, 'C');
$pdf->Cell(0, 10, 'Highest Forecasted Sales Day: ' . $maxSalesDay . ' (PHP ' . number_format($maxForecastedSales, 2) . ')', 0, 1, 'C');
$pdf->Cell(0, 10, 'Lowest Forecasted Sales Day: ' . $minSalesDay . ' (PHP ' . number_format($minForecastedSales, 2) . ')', 0, 1, 'C');

// Output the PDF
// $pdf->Output('D', 'Sales_Analytics_Report.pdf');
$pdf->Output();
